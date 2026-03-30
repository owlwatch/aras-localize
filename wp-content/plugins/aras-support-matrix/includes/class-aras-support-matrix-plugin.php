<?php

if (! defined('ABSPATH')) {
	exit;
}

class ArasSupportMatrixPlugin
{
	const MANAGE_CAPABILITY = 'manage_support_matrix';
	const ROLE = 'matrix-admin';

	private static $instance = null;

	private $post_types;
	private $rest;
	private $vite;
	private $importer;

	public static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct()
	{
		$this->post_types = new ArasSupportMatrixPostTypes();
		$this->rest = new ArasSupportMatrixRest();
		$this->importer = new ArasSupportMatrixImporter();
		$this->vite = new ArasSupportMatrixViteService(
			ARAS_SUPPORT_MATRIX_PATH . 'ui/dist',
			ARAS_SUPPORT_MATRIX_URL . 'ui/dist/',
			'http://aras.local:6240'
		);

		add_shortcode('aras_support_matrix', array($this, 'render_shortcode'));
		add_action('init', array($this, 'register_roles'));
		add_action('admin_menu', array($this, 'register_admin_page'));
		add_action('admin_menu', array($this, 'restrict_admin_menu'), 999);
		add_action('admin_init', array($this, 'restrict_admin_pages'));
		add_filter('login_redirect', array($this, 'redirect_matrix_admin_after_login'), 10, 3);
	}

	public static function activate()
	{
		self::sync_roles();
	}

	public static function deactivate()
	{
		$roles = array('administrator', 'editor');

		foreach ($roles as $role_name) {
			$role = get_role($role_name);

			if ($role instanceof WP_Role) {
				$role->remove_cap(self::MANAGE_CAPABILITY);
			}
		}
	}

	public function register_roles()
	{
		self::sync_roles();
	}

	private static function sync_roles()
	{
		add_role(
			self::ROLE,
			__('Matrix Admin', 'aras-support-matrix'),
			array(
				'read' => true,
				'upload_files' => false,
				self::MANAGE_CAPABILITY => true,
			)
		);

		$matrix_admin = get_role(self::ROLE);

		if ($matrix_admin instanceof WP_Role) {
			$matrix_admin->add_cap('read');
			$matrix_admin->add_cap(self::MANAGE_CAPABILITY);
			$matrix_admin->remove_cap('edit_posts');
		}

		$roles = array('administrator', 'editor');

		foreach ($roles as $role_name) {
			$role = get_role($role_name);

			if ($role instanceof WP_Role) {
				$role->add_cap(self::MANAGE_CAPABILITY);
			}
		}
	}

	public static function current_user_can_manage()
	{
		return current_user_can(self::MANAGE_CAPABILITY);
	}

	public function render_shortcode()
	{
		return $this->render_app(
			array(
				'restBase' => esc_url_raw(rest_url('aras-support-matrix/v1')),
				'nonce' => wp_create_nonce('wp_rest'),
				'isAdmin' => self::current_user_can_manage(),
				'initialTab' => 'public',
				'embedScriptUrl' => esc_url_raw(ARAS_SUPPORT_MATRIX_URL . 'ui/dist/embed.js'),
			)
		);
	}

	public function register_admin_page()
	{
		add_menu_page(
			__('Support Matrix', 'aras-support-matrix'),
			__('Support Matrix', 'aras-support-matrix'),
			self::MANAGE_CAPABILITY,
			'aras-support-matrix',
			array($this, 'render_admin_page'),
			'dashicons-screenoptions',
			58
		);
	}

	public function restrict_admin_menu()
	{
		if (! $this->is_limited_matrix_admin()) {
			return;
		}

		global $menu;

		$allowed_slugs = array(
			'profile.php',
			'aras-support-matrix',
		);

		foreach ($menu as $menu_item) {
			if (empty($menu_item[2]) || in_array($menu_item[2], $allowed_slugs, true)) {
				continue;
			}

			remove_menu_page($menu_item[2]);
		}
	}

	public function restrict_admin_pages()
	{
		if (! $this->is_limited_matrix_admin()) {
			return;
		}

		if ((defined('DOING_AJAX') && DOING_AJAX) || (defined('REST_REQUEST') && REST_REQUEST)) {
			return;
		}

		global $pagenow;

		$page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
		$allowed_pages = array(
			'profile.php',
			'admin-ajax.php',
			'async-upload.php',
		);

		if (in_array($pagenow, $allowed_pages, true)) {
			return;
		}

		if ($pagenow === 'admin.php' && $page === 'aras-support-matrix') {
			return;
		}

		wp_safe_redirect(admin_url('admin.php?page=aras-support-matrix'));
		exit;
	}

	public function redirect_matrix_admin_after_login($redirect_to, $requested_redirect_to, $user)
	{
		if (! ($user instanceof WP_User) || ! self::is_limited_matrix_admin_user($user)) {
			return $redirect_to;
		}

		if (! empty($requested_redirect_to) && $requested_redirect_to !== admin_url()) {
			return $redirect_to;
		}

		return admin_url('admin.php?page=aras-support-matrix');
	}

	private function is_limited_matrix_admin()
	{
		return self::is_limited_matrix_admin_user(wp_get_current_user());
	}

	private static function is_limited_matrix_admin_user($user)
	{
		if (! ($user instanceof WP_User)) {
			return false;
		}

		return in_array(self::ROLE, (array) $user->roles, true) && ! user_can($user, 'manage_options');
	}

	public function render_admin_page()
	{
		if (! self::current_user_can_manage()) {
			wp_die(esc_html__('You do not have permission to access the Support Matrix.', 'aras-support-matrix'));
		}

		$config = array(
			'restBase' => esc_url_raw(rest_url('aras-support-matrix/v1')),
			'nonce' => wp_create_nonce('wp_rest'),
			'isAdmin' => true,
			'initialTab' => 'admin',
			'embedScriptUrl' => esc_url_raw(ARAS_SUPPORT_MATRIX_URL . 'ui/dist/embed.js'),
		);

		echo $this->render_app($config); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function render_app($config)
	{
		ob_start();
		?>
		<div id="aras-support-matrix-app"></div>
		<script>
			window.ArasSupportMatrixConfig = <?php echo wp_json_encode($config); ?>;
		</script>
		<?php
		$this->vite->render('src/main.ts');

		return ob_get_clean();
	}
}
