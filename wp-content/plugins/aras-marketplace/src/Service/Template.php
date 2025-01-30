<?php

namespace Aras\Marketplace\Service;

use Aras\Marketplace\Service\ViteService;

/**
 * This class is responsible for adding archive and single templates
 * in the plugins "templates" directory
 */
class Template {

	/**
	 * @var Aras\Marketplace\Service\ViteService
	 */
	public $ui;

	/**
	 * The constructor
	 */
	public function __construct( ViteService $ui)
	{
		$this->ui = $ui;
		add_filter( 'template_include', array( $this, 'template_include' ) );
	}

	public function template_include( $template )
	{

		// if the theme template is specific "mp-", just use that
		if( strpos( basename($template), 'mp-' ) !== false ){
			return $template;
		}

		$post_type = 'mp-solution';
		
		// Plugin template folder path
		$plugin_templates_path = ARAS_MARKETPLACE_PATH . '/templates/';

		// Handle archive for the custom post type
		if (is_post_type_archive($post_type) && !locate_template("archive-{$post_type}.php")) {
			$plugin_template = $plugin_templates_path . "archive-{$post_type}.php";
			if (file_exists($plugin_template)) {
				return $plugin_template;
			}
		}

		// Handle single view for the custom post type
		if (is_singular($post_type) && !locate_template("single-{$post_type}.php")) {
			$plugin_template = $plugin_templates_path . "single-{$post_type}.php";
			if (file_exists($plugin_template)) {
				return $plugin_template;
			}
		}

		if( is_tax() && strpos( get_queried_object()->taxonomy, 'mp-' ) === 0 ){
			$taxonomy = get_queried_object()->taxonomy;
			$plugin_template = $plugin_templates_path . "taxonomy-{$taxonomy}.php";
			if (file_exists($plugin_template)) {
				return $plugin_template;
			}

			// if not, we should just include our post type archive
			$plugin_template = $plugin_templates_path . "archive-{$post_type}.php";
			if (file_exists($plugin_template)) {
				return $plugin_template;
			}
		}

		return $template;
	}

	public static function get_template_part( $slug, $name = null, $args=[] )
	{
		$templates = array();
		$name      = (string) $name;
		if ( '' !== $name ) {
			$templates[] = "{$slug}-{$name}.php";
		}

		else {
			$templates[] = "{$slug}.php";
		}

		if ( ! locate_template( $templates, true, false, $args ) ) {
			// look for our own version of the template
			$plugin_templates_path = ARAS_MARKETPLACE_PATH . 'templates/';
			$plugin_template = $plugin_templates_path . $templates[0];
			if (file_exists($plugin_template)) {
				include $plugin_template;
			}
			else {
				// whats the point of this return?
				return false;
			}
		}
	}

	public function enqueue_style()
	{
		add_action('wp_footer', function(){
			echo $this->ui->render('src/main.ts');
		});
	}

	public static function getYoutubeThumbnailUrlFromVideoUrl($video_url)
	{
		$video_id = self::getYoutubeVideoIdFromUrl($video_url);
		return "https://img.youtube.com/vi/{$video_id}/maxresdefault.jpg";
	}

	public static function getYoutubeVideoIdFromUrl($url)
	{
		$pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
		preg_match($pattern, $url, $matches);
		return (isset($matches[1])) ? $matches[1] : false;
	}

}