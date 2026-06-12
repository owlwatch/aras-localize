<?php
/**
 * Plugin Name: Aras - Localize and WPML
 * Description: Runs LocalizeJS alongside WPML by using WPML for language routing/selection while forcing selected frontend languages to render the default-language content.
 * Version: 1.0.0
 * Author: Aras
 * Text Domain: aras-localize-and-wpml
 */

if (!defined('ABSPATH')) {
	exit;
}

final class Aras_Localize_And_WPML
{
	private const OPTION_NAME = 'aras_localize_and_wpml_languages';
	private const OPTION_GROUP = 'aras_localize_and_wpml';
	private const MENU_SLUG = 'aras-localize-and-wpml';
	private const LANGUAGE_CODE_ALIASES = [
		'zh-hant' => 'zh-tw',
	];

	private static ?Aras_Localize_And_WPML $instance = null;

	/** @var array<int,string>|null */
	private ?array $selected_languages = null;

	/** @var array<string,mixed>|null */
	private ?array $available_languages = null;

	/** @var string|null */
	private ?string $current_language = null;

	/** @var array<int,int> */
	private array $source_post_cache = [];

	private bool $meta_redirect_guard = false;
	private bool $title_redirect_guard = false;

	public static function get_instance(): Aras_Localize_And_WPML
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct()
	{
		add_action('plugins_loaded', [$this, 'normalize_inbound_language_request'], 1);
		add_action('init', [$this, 'register_hooks'], 20);
		add_action('admin_menu', [$this, 'register_settings_page']);
		add_action('admin_init', [$this, 'register_settings']);
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'add_plugin_action_links']);
	}

	public function register_hooks(): void
	{
		if ($this->is_localize_plugin_available()) {
			remove_action('wp_enqueue_scripts', 'add_localizejs_script', 1);
		}

		add_action('wp_enqueue_scripts', [$this, 'conditionally_enqueue_localize_scripts'], 1);
		add_filter('wpml_language_codes_map', [$this, 'filter_wpml_language_codes_map']);
		add_filter('wpml_get_language_from_url', [$this, 'filter_wpml_language_from_url'], 20, 2);
		add_filter('wpml_active_languages', [$this, 'filter_wpml_active_languages'], 20, 2);
		add_filter('acf/settings/current_language', [$this, 'force_acf_language_to_default'], 20);
		add_filter('the_posts', [$this, 'replace_posts_with_source_language_content'], 20, 2);
		add_filter('get_post_metadata', [$this, 'redirect_post_meta_to_source_language'], 20, 4);
		add_filter('the_title', [$this, 'replace_title_with_source_language'], 20, 2);
	}

	public function normalize_inbound_language_request(): void
	{
		if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
			return;
		}

		$this->normalize_server_request_value('REQUEST_URI');
		$this->normalize_server_request_value('PATH_INFO');
		$this->normalize_server_request_value('REDIRECT_URL');
	}

	public function register_settings_page(): void
	{
		add_options_page(
			__('Localize and WPML', 'aras-localize-and-wpml'),
			__('Localize and WPML', 'aras-localize-and-wpml'),
			'manage_options',
			self::MENU_SLUG,
			[$this, 'render_settings_page']
		);
	}

	public function register_settings(): void
	{
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_NAME,
			[
				'type' => 'array',
				'sanitize_callback' => [$this, 'sanitize_selected_languages'],
				'default' => [],
			]
		);

		add_settings_section(
			'aras_localize_and_wpml_languages_section',
			__('LocalizeJS Languages', 'aras-localize-and-wpml'),
			[$this, 'render_languages_section'],
			self::MENU_SLUG
		);

		add_settings_field(
			self::OPTION_NAME,
			__('Languages rendered from the default language', 'aras-localize-and-wpml'),
			[$this, 'render_languages_field'],
			self::MENU_SLUG,
			'aras_localize_and_wpml_languages_section'
		);
	}

	/**
	 * @param mixed $value
	 * @return array<int,string>
	 */
	public function sanitize_selected_languages($value): array
	{
		if (!is_array($value)) {
			return [];
		}

		$allowed = array_keys($this->get_available_languages());
		$selected = [];

		foreach ($value as $language) {
			if (!is_string($language)) {
				continue;
			}

			$language = strtolower(trim($language));
			if ($language !== '' && in_array($language, $allowed, true)) {
				$selected[] = $language;
			}
		}

		return array_values(array_unique($selected));
	}

	public function render_languages_section(): void
	{
		$default_language = $this->get_default_language();
		?>
		<p>
			<?php esc_html_e('Select the WPML languages that should render the default-language content on the server and be translated on the frontend by LocalizeJS.', 'aras-localize-and-wpml'); ?>
		</p>
		<p>
			<?php
			echo esc_html(
				sprintf(
					/* translators: %s: default language code */
					__('Current WPML default language: %s', 'aras-localize-and-wpml'),
					strtoupper($default_language)
				)
			);
			?>
		</p>
		<?php if (!$this->is_localize_plugin_available()) : ?>
			<p>
				<strong><?php esc_html_e('The Localize Integration plugin is not active. Selected languages will not receive the LocalizeJS frontend scripts until it is enabled.', 'aras-localize-and-wpml'); ?></strong>
			</p>
		<?php endif; ?>
		<?php
	}

	public function render_languages_field(): void
	{
		$languages = $this->get_available_languages();
		$selected = $this->get_selected_languages();
		$default_language = $this->get_default_language();

		if (empty($languages)) {
			echo '<p>' . esc_html__('No WPML languages were found.', 'aras-localize-and-wpml') . '</p>';
			return;
		}

		echo '<fieldset>';
		foreach ($languages as $code => $language) {
			$is_default = $code === $default_language;
			$checked = in_array($code, $selected, true);
			$label = $language['native_name'] ?: strtoupper($code);
			$description = [];

			if (!empty($language['translated_name']) && $language['translated_name'] !== $label) {
				$description[] = $language['translated_name'];
			}

			if ($is_default) {
				$description[] = __('Default language', 'aras-localize-and-wpml');
			}
			?>
			<label style="display:block; margin-bottom:8px;">
				<input
					type="checkbox"
					name="<?php echo esc_attr(self::OPTION_NAME); ?>[]"
					value="<?php echo esc_attr($code); ?>"
					<?php checked($checked); ?>
					<?php disabled($is_default); ?>
				/>
				<?php echo esc_html($label . ' (' . $code . ')'); ?>
				<?php if (!empty($description)) : ?>
					<span style="opacity:.8;"><?php echo esc_html(' - ' . implode(' / ', $description)); ?></span>
				<?php endif; ?>
			</label>
			<?php
		}
		echo '</fieldset>';
	}

	public function render_settings_page(): void
	{
		if (!current_user_can('manage_options')) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e('Aras Localize and WPML', 'aras-localize-and-wpml'); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields(self::OPTION_GROUP);
				do_settings_sections(self::MENU_SLUG);
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * @param array<int,string> $links
	 * @return array<int,string>
	 */
	public function add_plugin_action_links(array $links): array
	{
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url(admin_url('options-general.php?page=' . self::MENU_SLUG)),
			esc_html__('Settings', 'aras-localize-and-wpml')
		);

		array_unshift($links, $settings_link);
		return $links;
	}

	public function conditionally_enqueue_localize_scripts(): void
	{
		if (!$this->should_use_source_language_content() || !$this->is_localize_plugin_available()) {
			return;
		}

		add_localizejs_script();

		$current_language = $this->get_current_language();
		if ($current_language !== '') {
			wp_add_inline_script(
				'localizeFallback',
				'const OVERRIDE_LANG = ' . wp_json_encode($current_language) . ';',
				'before'
			);
		}
	}

	/**
	 * @param array<int,\WP_Post> $posts
	 * @return array<int,\WP_Post>
	 */
	public function replace_posts_with_source_language_content(array $posts): array
	{
		if (!$this->should_use_source_language_content() || empty($posts)) {
			return $posts;
		}

		foreach ($posts as $post) {
			if (!$post instanceof WP_Post) {
				continue;
			}

			$source_post = $this->get_source_post($post->ID);
			if (!$source_post instanceof WP_Post || $source_post->ID === $post->ID) {
				continue;
			}

			$post->post_title = $source_post->post_title;
			$post->post_excerpt = $source_post->post_excerpt;
			$post->post_content = $source_post->post_content;
			$post->post_content_filtered = $source_post->post_content_filtered;
		}

		return $posts;
	}

	/**
	 * @param mixed $value
	 * @param int $object_id
	 * @param string $meta_key
	 * @param bool $single
	 * @return mixed
	 */
	public function redirect_post_meta_to_source_language($value, int $object_id, string $meta_key, bool $single)
	{
		if (!$this->should_use_source_language_content() || $this->meta_redirect_guard) {
			return $value;
		}

		if ($object_id <= 0 || $this->should_skip_meta_key($meta_key)) {
			return $value;
		}

		$source_post_id = $this->get_source_post_id($object_id);
		if ($source_post_id === null || $source_post_id === $object_id) {
			return $value;
		}

		$this->meta_redirect_guard = true;
		$source_value = get_metadata('post', $source_post_id, $meta_key, $single);
		$this->meta_redirect_guard = false;

		return $source_value;
	}

	public function replace_title_with_source_language(string $title, int $post_id = 0): string
	{
		if (
			!$this->should_use_source_language_content()
			|| $this->title_redirect_guard
			|| $post_id <= 0
		) {
			return $title;
		}

		$source_post = $this->get_source_post($post_id);
		if (!$source_post instanceof WP_Post || $source_post->ID === $post_id) {
			return $title;
		}

		$this->title_redirect_guard = true;
		$source_title = get_the_title($source_post);
		$this->title_redirect_guard = false;

		return is_string($source_title) && $source_title !== '' ? $source_title : $title;
	}

	public function force_acf_language_to_default($current_language)
	{
		if (!$this->should_use_source_language_content()) {
			return $current_language;
		}

		return $this->get_default_language();
	}

	/**
	 * @param array<string,string> $language_codes_map
	 * @return array<string,string>
	 */
	public function filter_wpml_language_codes_map(array $language_codes_map): array
	{
		foreach (self::LANGUAGE_CODE_ALIASES as $internal_code => $public_code) {
			if (isset($language_codes_map[$internal_code])) {
				$language_codes_map[$internal_code] = $public_code;
			}
		}

		return $language_codes_map;
	}

	/**
	 * @param mixed $languages
	 * @param mixed $args
	 * @return mixed
	 */
	public function filter_wpml_active_languages($languages, $args = null)
	{
		if (!is_array($languages)) {
			return $languages;
		}

		$normalized_languages = [];

		foreach ($languages as $code => $language) {
			if (!is_string($code) || !is_array($language)) {
				$normalized_languages[$code] = $language;
				continue;
			}

			$public_code = $this->normalize_language_code($code);

			$language['code'] = $public_code;

			if (isset($language['language_code']) && is_string($language['language_code'])) {
				$language['language_code'] = $this->normalize_language_code($language['language_code']);
			}

			if (isset($language['url']) && is_string($language['url'])) {
				$language['url'] = $this->normalize_language_url($language['url']);
			}

			if (isset($language['translated_url']) && is_string($language['translated_url'])) {
				$language['translated_url'] = $this->normalize_language_url($language['translated_url']);
			}

			$normalized_languages[$public_code] = $language;
		}

		return $normalized_languages;
	}

	/**
	 * @param mixed $language
	 * @param mixed $url
	 * @return mixed
	 */
	public function filter_wpml_language_from_url($language, $url)
	{
		if (!is_string($language) || $language === '') {
			return $language;
		}

		return $this->denormalize_language_code($language);
	}

	private function should_use_source_language_content(): bool
	{
		if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
			return false;
		}

		$current_language = $this->get_current_language();
		if ($current_language === '') {
			return false;
		}

		return in_array($current_language, $this->get_selected_languages(), true);
	}

	private function is_localize_plugin_available(): bool
	{
		return function_exists('add_localizejs_script');
	}

	/**
	 * @return array<int,string>
	 */
	private function get_selected_languages(): array
	{
		if ($this->selected_languages !== null) {
			return $this->selected_languages;
		}

		$selected = get_option(self::OPTION_NAME, []);
		if (!is_array($selected)) {
			$selected = [];
		}

		$selected = array_map(
			static fn($language) => is_string($language) ? strtolower(trim($language)) : '',
			$selected
		);
		$selected = array_values(array_filter(array_unique($selected)));
		$selected = array_values(array_diff($selected, [$this->get_default_language()]));

		$this->selected_languages = $selected;
		return $this->selected_languages;
	}

	/**
	 * @return array<string,array<string,string>>
	 */
	private function get_available_languages(): array
	{
		if ($this->available_languages !== null) {
			return $this->available_languages;
		}

		$languages = apply_filters(
			'wpml_active_languages',
			null,
			'skip_missing=0&orderby=code'
		);

		if (!is_array($languages)) {
			$languages = [];
		}

		$normalized = [];
		foreach ($languages as $code => $language) {
			if (!is_string($code) || !is_array($language)) {
				continue;
			}

			$normalized[strtolower($code)] = [
				'native_name' => isset($language['native_name']) ? (string) $language['native_name'] : strtoupper($code),
				'translated_name' => isset($language['translated_name']) ? (string) $language['translated_name'] : '',
			];
		}

		$this->available_languages = $normalized;
		return $this->available_languages;
	}

	private function get_current_language(): string
	{
		if ($this->current_language !== null) {
			return $this->current_language;
		}

		$current_language = apply_filters('wpml_current_language', null);
		$this->current_language = is_string($current_language)
			? $this->normalize_language_code($current_language)
			: '';

		return $this->current_language;
	}

	private function get_default_language(): string
	{
		$default_language = apply_filters('wpml_default_language', null);
		if (is_string($default_language) && $default_language !== '') {
			return strtolower($default_language);
		}

		return 'en';
	}

	private function get_source_post_id(int $post_id): ?int
	{
		if (isset($this->source_post_cache[$post_id])) {
			return $this->source_post_cache[$post_id];
		}

		$post_type = get_post_type($post_id);
		if (!$post_type) {
			$this->source_post_cache[$post_id] = $post_id;
			return $post_id;
		}

		$source_post_id = apply_filters(
			'wpml_object_id',
			$post_id,
			$post_type,
			true,
			$this->get_default_language()
		);

		if (!is_numeric($source_post_id) || (int) $source_post_id <= 0) {
			$source_post_id = $post_id;
		}

		$this->source_post_cache[$post_id] = (int) $source_post_id;
		return $this->source_post_cache[$post_id];
	}

	private function get_source_post(int $post_id): ?WP_Post
	{
		$source_post_id = $this->get_source_post_id($post_id);
		if ($source_post_id === null || $source_post_id <= 0) {
			return null;
		}

		$source_post = get_post($source_post_id);
		return $source_post instanceof WP_Post ? $source_post : null;
	}

	private function should_skip_meta_key(string $meta_key): bool
	{
		if ($meta_key === '') {
			return false;
		}

		$prefixes = [
			'_icl',
			'icl_',
			'_wpml',
			'wpml_',
			'_edit_',
		];

		foreach ($prefixes as $prefix) {
			if (str_starts_with($meta_key, $prefix)) {
				return true;
			}
		}

		$exact_keys = [
			'post_lang_code',
		];

		return in_array($meta_key, $exact_keys, true);
	}

	private function normalize_language_code(string $language_code): string
	{
		$language_code = strtolower(trim($language_code));

		return self::LANGUAGE_CODE_ALIASES[$language_code] ?? $language_code;
	}

	private function denormalize_language_code(string $language_code): string
	{
		$language_code = strtolower(trim($language_code));

		$reverse_aliases = array_flip(self::LANGUAGE_CODE_ALIASES);

		return $reverse_aliases[$language_code] ?? $language_code;
	}

	private function normalize_language_url(string $url): string
	{
		foreach (self::LANGUAGE_CODE_ALIASES as $internal_code => $public_code) {
			$url = preg_replace(
				'~/' . preg_quote($internal_code, '~') . '([/?#]|$)~i',
				'/' . $public_code . '$1',
				$url
			);
		}

		return is_string($url) ? $url : '';
	}

	private function normalize_server_request_value(string $server_key): void
	{
		if (empty($_SERVER[$server_key]) || !is_string($_SERVER[$server_key])) {
			return;
		}

		foreach (self::LANGUAGE_CODE_ALIASES as $internal_code => $public_code) {
			$_SERVER[$server_key] = preg_replace(
				'~(^|/)' . preg_quote($public_code, '~') . '([/?#]|$)~i',
				'$1' . $internal_code . '$2',
				$_SERVER[$server_key]
			);
		}
	}
}

Aras_Localize_And_WPML::get_instance();
