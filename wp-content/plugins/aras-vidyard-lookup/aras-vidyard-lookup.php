<?php

/**
 * Plugin Name: Aras Vidyard Lookup
 * Description: Adds a search tool to the WordPress admin to find posts by Vidyard ID.
 * Version: 1.0
 * Author: Your Name
 */

namespace Aras\Plugin;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class VidyardLookup
{

	private static $instance = null;

	private function __construct()
	{
		add_action('admin_menu', array($this, 'add_tools_page'));
	}

	public static function getInstance()
	{
		if (self::$instance == null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function add_tools_page()
	{
		add_management_page(
			'Vidyard Lookup',          // Page title
			'Vidyard Lookup',          // Menu title
			'manage_options',          // Capability
			'vidyard-lookup',          // Menu slug
			array($this, 'render_tools_page') // Callback function
		);
	}

	public function render_tools_page()
	{
		global $wpdb;

		// Check if a search term has been submitted
		$search_term = isset($_POST['vidyard_id']) ? sanitize_text_field($_POST['vidyard_id']) : '';

		echo '<div class="wrap">';
		echo '<h1>Vidyard Lookup</h1>';
		echo '<form method="post" style="margin-bottom: 1em">';
		echo '<input type="text" name="vidyard_id" placeholder="Vidyard Id" value="' . esc_attr($search_term) . '">';
		echo '<input type="submit" value="Search" class="button button-primary">';
		echo '</form>';

		if( $search_term && strlen( $search_term ) != 22 ){
			echo '<div class="notice notice-error">Please enter a valid Vidyard ID</div>';
		}

		if ($search_term && strlen( $search_term ) == 22 ) {
			// Prepare and execute the query
			$query = $wpdb->prepare("
                SELECT p.*, m.*, t.language_code
                FROM {$wpdb->posts} AS p
                JOIN {$wpdb->postmeta} AS m ON m.post_id = p.ID
                JOIN {$wpdb->prefix}icl_translations AS t ON p.ID = t.element_id
                WHERE (p.post_status = 'publish' OR p.post_status = 'draft')
                AND (p.post_content LIKE %s OR m.meta_value LIKE %s)
                AND t.element_type = CONCAT('post_', p.post_type)
                GROUP BY p.ID
                ORDER BY p.post_date
            ", '%' . $wpdb->esc_like($search_term) . '%', '%' . $wpdb->esc_like($search_term) . '%');

			$results = $wpdb->get_results($query);

			if ($results) {
				echo '<table class="widefat fixed" cellspacing="0">';
				echo '<thead>';
				echo '<tr>';
				echo '<th class="manage-column">Title</th>';
				echo '<th class="manage-column">Edit Link</th>';
				echo '<th class="manage-column">Language</th>';
				echo '<th class="manage-column">Post Type</th>';
				echo '<th class="manage-column">Date</th>';
				echo '</tr>';
				echo '</thead>';
				echo '<tbody>';

				foreach ($results as $post) {
					$post_type_object = get_post_type_object($post->post_type);
					$post_type_label = $post_type_object ? $post_type_object->labels->singular_name : $post->post_type;

					echo '<tr>';
					echo '<td><a href="' . get_permalink($post->ID) . '">' . esc_html($post->post_title) . '</a></td>';
					echo '<td><a class="button" href="' . get_edit_post_link($post->ID) . '">Edit</a></td>';
					echo '<td>' . esc_html($post->language_code) . '</td>';
					echo '<td>' . esc_html($post_type_label) . '</td>';
					echo '<td>' . esc_html($post->post_date) . '</td>';
					echo '</tr>';
				}

				echo '</tbody>';
				echo '</table>';
			} else {
				echo '<p>No posts found.</p>';
			}
		}

		echo '</div>';
	}
}

VidyardLookup::getInstance();
