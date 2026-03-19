<?php

if (! defined('ABSPATH')) {
	exit;
}

class ArasSupportMatrixPlugin
{
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
		add_action('admin_menu', array($this, 'register_admin_page'));
	}

	public function render_shortcode()
	{
		return $this->render_app(
			array(
				'restBase' => esc_url_raw(rest_url('aras-support-matrix/v1')),
				'nonce' => wp_create_nonce('wp_rest'),
				'isAdmin' => current_user_can('edit_posts'),
				'initialTab' => 'public',
			)
		);
	}

	public function register_admin_page()
	{
		add_menu_page(
			__('Support Matrix', 'aras-support-matrix'),
			__('Support Matrix', 'aras-support-matrix'),
			'edit_posts',
			'aras-support-matrix',
			array($this, 'render_admin_page'),
			'dashicons-screenoptions',
			58
		);
	}

	public function render_admin_page()
	{
		$config = array(
			'restBase' => esc_url_raw(rest_url('aras-support-matrix/v1')),
			'nonce' => wp_create_nonce('wp_rest'),
			'isAdmin' => true,
			'initialTab' => 'admin',
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
