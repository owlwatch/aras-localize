<?php

if (! defined('ABSPATH')) {
	exit;
}

class ArasSupportMatrixPostTypes
{
	const COMPONENT_POST_TYPE = 'asm_component';
	const RELEASE_POST_TYPE = 'asm_release';
	const ENTRY_POST_TYPE = 'asm_entry';
	const STATUS_TAXONOMY = 'asm_support_status';
	const COMPONENT_GROUP_TAXONOMY = 'asm_component_group';

	public function __construct()
	{
		add_action('init', array($this, 'register'));
		add_action('init', array($this, 'seed_status_terms'));
	}

	public function register()
	{
		$this->register_taxonomies();
		$this->register_post_types();
		$this->register_meta();
	}

	public function register_taxonomies()
	{
		register_taxonomy(
			self::STATUS_TAXONOMY,
			self::ENTRY_POST_TYPE,
			array(
				'label' => __('Support Status', 'aras-support-matrix'),
				'public' => false,
				'show_ui' => false,
				'show_in_rest' => true,
				'hierarchical' => false,
				'rewrite' => false,
			)
		);

		register_taxonomy(
			self::COMPONENT_GROUP_TAXONOMY,
			self::COMPONENT_POST_TYPE,
			array(
				'label' => __('Component Groups', 'aras-support-matrix'),
				'public' => true,
				'show_ui' => false,
				'show_in_rest' => true,
				'hierarchical' => true,
			)
		);
	}

	public function register_post_types()
	{
		register_post_type(
			self::COMPONENT_POST_TYPE,
			array(
				'label' => __('Support Components', 'aras-support-matrix'),
				'public' => false,
				'show_ui' => false,
				'show_in_rest' => true,
				'menu_position' => 25,
				'menu_icon' => 'dashicons-screenoptions',
				'supports' => array('title', 'editor'),
			)
		);

		register_post_type(
			self::RELEASE_POST_TYPE,
			array(
				'label' => __('Innovator Releases', 'aras-support-matrix'),
				'public' => false,
				'show_ui' => false,
				'show_in_rest' => true,
				'menu_position' => 26,
				'menu_icon' => 'dashicons-update',
				'supports' => array('title'),
			)
		);

		register_post_type(
			self::ENTRY_POST_TYPE,
			array(
				'label' => __('Compatibility Entries', 'aras-support-matrix'),
				'public' => false,
				'show_ui' => false,
				'show_in_rest' => true,
				'menu_position' => 27,
				'menu_icon' => 'dashicons-yes-alt',
				'supports' => array('title'),
			)
		);
	}

	public function register_meta()
	{
		$meta_config = array(
			'single' => true,
			'show_in_rest' => true,
			'type' => 'string',
			'auth_callback' => function () {
				return current_user_can('edit_posts');
			},
		);

		register_post_meta(self::RELEASE_POST_TYPE, 'build_number', $meta_config);
		register_post_meta(self::RELEASE_POST_TYPE, 'release_date', $meta_config);
		register_post_meta(self::RELEASE_POST_TYPE, 'end_of_life_date', $meta_config);

		register_post_meta(self::ENTRY_POST_TYPE, 'component_post_id', array_merge($meta_config, array('type' => 'integer')));
		register_post_meta(self::ENTRY_POST_TYPE, 'release_post_id', array_merge($meta_config, array('type' => 'integer')));
		register_post_meta(self::ENTRY_POST_TYPE, 'component_version_number', $meta_config);
		register_post_meta(self::ENTRY_POST_TYPE, 'component_release_number', $meta_config);
		register_post_meta(self::ENTRY_POST_TYPE, 'entry_end_of_life_date', $meta_config);
		register_post_meta(self::ENTRY_POST_TYPE, 'notes', $meta_config);
	}

	public function seed_status_terms()
	{
		$statuses = array('Certified', 'Supported', 'End of Life');

		foreach ($statuses as $status) {
			if (! term_exists($status, self::STATUS_TAXONOMY)) {
				wp_insert_term($status, self::STATUS_TAXONOMY, array('slug' => sanitize_title($status)));
			}
		}
	}
}
