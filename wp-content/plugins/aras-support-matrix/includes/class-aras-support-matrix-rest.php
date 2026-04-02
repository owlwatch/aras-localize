<?php

if (! defined('ABSPATH')) {
	exit;
}

class ArasSupportMatrixRest
{
	private $importer;

	public function __construct()
	{
		$this->importer = new ArasSupportMatrixImporter();
		add_action('rest_api_init', array($this, 'register_routes'));
		add_filter('rest_pre_serve_request', array($this, 'send_cors_headers'), 10, 4);
	}

	public function send_cors_headers($served, $result, $request, $server)
	{
		$route = $request instanceof WP_REST_Request ? $request->get_route() : '';

		if (strpos($route, '/aras-support-matrix/v1') !== 0) {
			return $served;
		}

		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
		header('Access-Control-Allow-Headers: Authorization, X-WP-Nonce, Content-Type');
		header('Access-Control-Expose-Headers: X-WP-Total, X-WP-TotalPages, Link');
		header('Vary: Origin', false);

		return $served;
	}

	public function register_routes()
	{
		register_rest_route(
			'aras-support-matrix/v1',
			'/matrix',
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array($this, 'matrix'),
				'permission_callback' => '__return_true',
			)
		);

		$this->register_collection_route('components', array($this, 'components'), array($this, 'save_component'));
		$this->register_item_route('components', array($this, 'component'), array($this, 'update_component'), array($this, 'delete_component'));

		$this->register_collection_route('releases', array($this, 'releases'), array($this, 'save_release'));
		$this->register_item_route('releases', array($this, 'release'), array($this, 'update_release'), array($this, 'delete_release'));
		$this->register_collection_route('entries', array($this, 'entries'), array($this, 'save_entry'));
		$this->register_item_route('entries', array($this, 'entry'), array($this, 'update_entry'), array($this, 'delete_entry'));
		$this->register_collection_route('notes', array($this, 'notes'), array($this, 'save_note'));
		$this->register_item_route('notes', array($this, 'note'), array($this, 'update_note'), array($this, 'delete_note'));
		$this->register_import_routes();
	}

	private function register_import_routes()
	{
		register_rest_route(
			'aras-support-matrix/v1',
			'/import/status',
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array($this, 'import_status'),
				'permission_callback' => array($this, 'can_edit'),
			)
		);

		register_rest_route(
			'aras-support-matrix/v1',
			'/import/start',
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array($this, 'start_import'),
				'permission_callback' => array($this, 'can_edit'),
			)
		);

		register_rest_route(
			'aras-support-matrix/v1',
			'/import/step',
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array($this, 'step_import'),
				'permission_callback' => array($this, 'can_edit'),
			)
		);
	}

	private function register_collection_route($resource, $get_callback, $post_callback)
	{
		register_rest_route(
			'aras-support-matrix/v1',
			'/' . $resource,
			array(
				array(
					'methods' => WP_REST_Server::READABLE,
					'callback' => $get_callback,
					'permission_callback' => '__return_true',
				),
				array(
					'methods' => WP_REST_Server::CREATABLE,
					'callback' => $post_callback,
					'permission_callback' => array($this, 'can_edit'),
				),
			)
		);
	}

	private function register_item_route($resource, $get_callback, $update_callback, $delete_callback)
	{
		register_rest_route(
			'aras-support-matrix/v1',
			'/' . $resource . '/(?P<id>\d+)',
			array(
				array(
					'methods' => WP_REST_Server::READABLE,
					'callback' => $get_callback,
					'permission_callback' => '__return_true',
				),
				array(
					'methods' => WP_REST_Server::EDITABLE,
					'callback' => $update_callback,
					'permission_callback' => array($this, 'can_edit'),
				),
				array(
					'methods' => WP_REST_Server::DELETABLE,
					'callback' => $delete_callback,
					'permission_callback' => array($this, 'can_edit'),
				),
			)
		);
	}

	public function can_edit()
	{
		return ArasSupportMatrixPlugin::current_user_can_manage();
	}

	public function import_status()
	{
		return rest_ensure_response($this->importer->get_status());
	}

	public function start_import(WP_REST_Request $request)
	{
		$file = ARAS_SUPPORT_MATRIX_PATH . 'data.csv';
		$reset = rest_sanitize_boolean($request->get_param('reset'));

		return rest_ensure_response(
			$this->importer->start_import(
				$file,
				array(
					'reset' => $reset,
				)
			)
		);
	}

	public function step_import()
	{
		return rest_ensure_response($this->importer->process_next_batch());
	}

	public function matrix(WP_REST_Request $request)
	{
		$release_id = absint($request->get_param('release'));
		$component_ids = array_filter(array_map('absint', (array) $request->get_param('components')));
		$include_drafts = ArasSupportMatrixPlugin::current_user_can_manage();

		$data = array(
			'components' => $this->get_components(),
			'releases' => $this->get_releases($include_drafts),
			'entries' => $this->get_entries($release_id, $component_ids, $include_drafts),
			'notes' => $this->get_notes(),
			'statuses' => $this->get_statuses(),
		);

		return rest_ensure_response($data);
	}

	public function components()
	{
		return rest_ensure_response($this->get_components());
	}

	public function component(WP_REST_Request $request)
	{
		return rest_ensure_response($this->map_component(get_post((int) $request['id'])));
	}

	public function save_component(WP_REST_Request $request)
	{
		$post_id = wp_insert_post(
			array(
				'post_type' => ArasSupportMatrixPostTypes::COMPONENT_POST_TYPE,
				'post_status' => $this->sanitize_publication_status((string) $request['publicationStatus']),
				'post_title' => sanitize_text_field($request['name']),
				'post_content' => wp_kses_post((string) $request['description']),
			),
			true
		);

		if (is_wp_error($post_id)) {
			return $post_id;
		}

		$this->save_component_groups($post_id, (array) $request['groups']);

		return rest_ensure_response($this->map_component(get_post($post_id)));
	}

	public function update_component(WP_REST_Request $request)
	{
		$post_id = (int) $request['id'];

		wp_update_post(
			array(
				'ID' => $post_id,
				'post_title' => sanitize_text_field($request['name']),
				'post_content' => wp_kses_post((string) $request['description']),
				'post_status' => $this->sanitize_publication_status((string) $request['publicationStatus']),
			)
		);

		$this->save_component_groups($post_id, (array) $request['groups']);

		return rest_ensure_response($this->map_component(get_post($post_id)));
	}

	public function delete_component(WP_REST_Request $request)
	{
		$component_id = (int) $request['id'];
		$this->delete_related_entries('component_post_id', $component_id);
		wp_delete_post($component_id, true);

		return rest_ensure_response(array('deleted' => true));
	}

	public function releases()
	{
		return rest_ensure_response($this->get_releases(ArasSupportMatrixPlugin::current_user_can_manage()));
	}

	public function release(WP_REST_Request $request)
	{
		return rest_ensure_response($this->map_release(get_post((int) $request['id'])));
	}

	public function save_release(WP_REST_Request $request)
	{
		$copy_from_release_id = absint($request['copyFromReleaseId']);
		$created_entries = array();
		$post_id = wp_insert_post(
			array(
				'post_type' => ArasSupportMatrixPostTypes::RELEASE_POST_TYPE,
				'post_status' => $this->sanitize_publication_status((string) $request['publicationStatus']),
				'post_title' => sanitize_text_field($request['name']),
			),
			true
		);

		if (is_wp_error($post_id)) {
			return $post_id;
		}

		$this->save_release_meta($post_id, $request);
		if ($copy_from_release_id) {
			$created_entries = $this->duplicate_release_entries($copy_from_release_id, $post_id);
		}

		return rest_ensure_response(
			array(
				'release' => $this->map_release(get_post($post_id)),
				'entries' => $created_entries,
			)
		);
	}

	public function update_release(WP_REST_Request $request)
	{
		$post_id = (int) $request['id'];

		wp_update_post(
			array(
				'ID' => $post_id,
				'post_title' => sanitize_text_field($request['name']),
				'post_status' => $this->sanitize_publication_status((string) $request['publicationStatus']),
			)
		);

		$this->save_release_meta($post_id, $request);

		return rest_ensure_response($this->map_release(get_post($post_id)));
	}

	private function duplicate_release_entries($source_release_id, $new_release_id)
	{
		$created_entries = array();
		$entries = get_posts(
			array(
				'post_type' => ArasSupportMatrixPostTypes::ENTRY_POST_TYPE,
				'post_status' => 'any',
				'numberposts' => -1,
				'meta_key' => 'release_post_id',
				'meta_value' => $source_release_id,
			)
		);

		foreach ($entries as $entry) {
			$new_entry_id = wp_insert_post(
				array(
					'post_type' => ArasSupportMatrixPostTypes::ENTRY_POST_TYPE,
					'post_status' => 'draft',
					'post_title' => $this->duplicate_entry_title($entry->ID, $new_release_id),
				),
				true
			);

			if (is_wp_error($new_entry_id)) {
				continue;
			}

			update_post_meta($new_entry_id, 'component_post_id', (int) get_post_meta($entry->ID, 'component_post_id', true));
			update_post_meta($new_entry_id, 'release_post_id', $new_release_id);
			update_post_meta($new_entry_id, 'component_version_number', (string) get_post_meta($entry->ID, 'component_version_number', true));
			update_post_meta($new_entry_id, 'component_release_number', (string) get_post_meta($entry->ID, 'component_release_number', true));
			update_post_meta($new_entry_id, 'entry_end_of_life_date', (string) get_post_meta($entry->ID, 'entry_end_of_life_date', true));
			update_post_meta($new_entry_id, 'note_post_id', (int) get_post_meta($entry->ID, 'note_post_id', true));
			update_post_meta($new_entry_id, 'notes', (string) get_post_meta($entry->ID, 'notes', true));

			$status_terms = wp_get_post_terms($entry->ID, ArasSupportMatrixPostTypes::STATUS_TAXONOMY, array('fields' => 'names'));
			if (! is_wp_error($status_terms) && ! empty($status_terms)) {
				wp_set_post_terms($new_entry_id, $status_terms, ArasSupportMatrixPostTypes::STATUS_TAXONOMY, false);
			}

			$mapped_entry = $this->map_entry(get_post($new_entry_id));
			if ($mapped_entry) {
				$created_entries[] = $mapped_entry;
			}
		}

		return $created_entries;
	}

	public function delete_release(WP_REST_Request $request)
	{
		$release_id = (int) $request['id'];
		$this->delete_related_entries('release_post_id', $release_id);
		wp_delete_post($release_id, true);

		return rest_ensure_response(array('deleted' => true));
	}

	public function entries()
	{
		return rest_ensure_response($this->get_entries(0, array(), ArasSupportMatrixPlugin::current_user_can_manage()));
	}

	public function notes()
	{
		return rest_ensure_response($this->get_notes());
	}

	public function note(WP_REST_Request $request)
	{
		return rest_ensure_response($this->map_note(get_post((int) $request['id'])));
	}

	public function save_note(WP_REST_Request $request)
	{
		$post_id = wp_insert_post(
			array(
				'post_type' => ArasSupportMatrixPostTypes::NOTE_POST_TYPE,
				'post_status' => 'publish',
				'post_title' => sanitize_text_field((string) $request['title']),
				'post_content' => wp_kses_post((string) $request['content']),
			),
			true
		);

		if (is_wp_error($post_id)) {
			return $post_id;
		}

		update_post_meta($post_id, 'note_type', $this->sanitize_note_type((string) $request['type']));

		return rest_ensure_response($this->map_note(get_post($post_id)));
	}

	public function update_note(WP_REST_Request $request)
	{
		$post_id = (int) $request['id'];

		wp_update_post(
			array(
				'ID' => $post_id,
				'post_title' => sanitize_text_field((string) $request['title']),
				'post_content' => wp_kses_post((string) $request['content']),
			)
		);

		update_post_meta($post_id, 'note_type', $this->sanitize_note_type((string) $request['type']));

		return rest_ensure_response($this->map_note(get_post($post_id)));
	}

	public function delete_note(WP_REST_Request $request)
	{
		$note_id = (int) $request['id'];
		$this->remove_note_references($note_id);
		wp_delete_post($note_id, true);

		return rest_ensure_response(array('deleted' => true));
	}

	public function entry(WP_REST_Request $request)
	{
		return rest_ensure_response($this->map_entry(get_post((int) $request['id'])));
	}

	public function save_entry(WP_REST_Request $request)
	{
		$post_id = wp_insert_post(
			array(
				'post_type' => ArasSupportMatrixPostTypes::ENTRY_POST_TYPE,
				'post_status' => $this->sanitize_publication_status((string) $request['publicationStatus']),
				'post_title' => $this->entry_title_from_request($request),
			),
			true
		);

		if (is_wp_error($post_id)) {
			return $post_id;
		}

		$this->save_entry_data($post_id, $request);

		return rest_ensure_response($this->map_entry(get_post($post_id)));
	}

	public function update_entry(WP_REST_Request $request)
	{
		$post_id = (int) $request['id'];

		wp_update_post(
			array(
				'ID' => $post_id,
				'post_title' => $this->entry_title_from_request($request),
				'post_status' => $this->sanitize_publication_status((string) $request['publicationStatus']),
			)
		);

		$this->save_entry_data($post_id, $request);

		return rest_ensure_response($this->map_entry(get_post($post_id)));
	}

	public function delete_entry(WP_REST_Request $request)
	{
		wp_delete_post((int) $request['id'], true);

		return rest_ensure_response(array('deleted' => true));
	}

	private function get_components()
	{
		$include_drafts = ArasSupportMatrixPlugin::current_user_can_manage();
		$posts = get_posts(
			array(
				'post_type' => ArasSupportMatrixPostTypes::COMPONENT_POST_TYPE,
				'post_status' => $include_drafts ? array('publish', 'draft') : 'publish',
				'numberposts' => -1,
				'orderby' => 'title',
				'order' => 'ASC',
			)
		);

		return array_values(array_filter(array_map(array($this, 'map_component'), $posts)));
	}

	private function get_releases($include_drafts = false)
	{
		$posts = get_posts(
			array(
				'post_type' => ArasSupportMatrixPostTypes::RELEASE_POST_TYPE,
				'post_status' => $include_drafts ? array('publish', 'draft') : 'publish',
				'numberposts' => -1,
				'orderby' => 'title',
				'order' => 'ASC',
			)
		);

		return array_values(array_filter(array_map(array($this, 'map_release'), $posts)));
	}

	private function get_entries($release_id = 0, array $component_ids = array(), $include_drafts = false)
	{
		$args = array(
			'post_type' => ArasSupportMatrixPostTypes::ENTRY_POST_TYPE,
			'post_status' => $include_drafts ? array('publish', 'draft') : 'publish',
			'numberposts' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
		);

		$meta_query = array();

		if ($release_id) {
			$meta_query[] = array(
				'key' => 'release_post_id',
				'value' => $release_id,
				'compare' => '=',
			);
		}

		if (! empty($component_ids)) {
			$meta_query[] = array(
				'key' => 'component_post_id',
				'value' => $component_ids,
				'compare' => 'IN',
			);
		}

		if (! empty($meta_query)) {
			$args['meta_query'] = $meta_query;
		}

		$posts = get_posts($args);

		return array_values(array_filter(array_map(array($this, 'map_entry'), $posts)));
	}

	private function get_notes()
	{
		$posts = get_posts(
			array(
				'post_type' => ArasSupportMatrixPostTypes::NOTE_POST_TYPE,
				'post_status' => 'publish',
				'numberposts' => -1,
				'orderby' => 'title',
				'order' => 'ASC',
			)
		);

		return array_values(array_filter(array_map(array($this, 'map_note'), $posts)));
	}

	private function get_statuses()
	{
		$terms = get_terms(
			array(
				'taxonomy' => ArasSupportMatrixPostTypes::STATUS_TAXONOMY,
				'hide_empty' => false,
			)
		);

		if (is_wp_error($terms)) {
			return array();
		}

		return array_map(
			function ($term) {
				return array(
					'id' => $term->term_id,
					'name' => $term->name,
					'slug' => $term->slug,
				);
			},
			$terms
		);
	}

	private function map_component($post)
	{
		if (! $post instanceof WP_Post) {
			return null;
		}

		$groups = wp_get_post_terms(
			$post->ID,
			ArasSupportMatrixPostTypes::COMPONENT_GROUP_TAXONOMY,
			array(
				'hide_empty' => false,
			)
		);

		$mapped_groups = array();

		if (! is_wp_error($groups)) {
			$mapped_groups = array_map(
				function ($term) {
					return array(
						'id' => (int) $term->term_id,
						'name' => $term->name,
						'slug' => $term->slug,
					);
				},
				$groups
			);
		}

		return array(
			'id' => $post->ID,
			'name' => $post->post_title,
			'description' => $post->post_content,
			'groups' => $mapped_groups,
			'publicationStatus' => $post->post_status,
		);
	}

	private function map_release($post)
	{
		if (! $post instanceof WP_Post) {
			return null;
		}

		$note = $this->resolve_note_for_post($post->ID);

		return array(
			'id' => $post->ID,
			'name' => $post->post_title,
			'buildNumber' => (string) get_post_meta($post->ID, 'build_number', true),
			'releaseDate' => (string) get_post_meta($post->ID, 'release_date', true),
			'endOfLifeDate' => (string) get_post_meta($post->ID, 'end_of_life_date', true),
			'noteId' => $note ? $note['id'] : null,
			'noteTitle' => $note ? $note['title'] : '',
			'notes' => $note ? $note['content'] : '',
			'note' => $note,
			'publicationStatus' => $post->post_status,
		);
	}

	private function map_entry($post)
	{
		if (! $post instanceof WP_Post) {
			return null;
		}

		$component_id = (int) get_post_meta($post->ID, 'component_post_id', true);
		$release_id = (int) get_post_meta($post->ID, 'release_post_id', true);
		$note = $this->resolve_note_for_post($post->ID);
		$status_terms = wp_get_post_terms($post->ID, ArasSupportMatrixPostTypes::STATUS_TAXONOMY);
		$status = (! is_wp_error($status_terms) && ! empty($status_terms)) ? $status_terms[0]->name : '';

		return array(
			'id' => $post->ID,
			'componentId' => $component_id,
			'componentName' => get_the_title($component_id),
			'innovatorReleaseId' => $release_id,
			'releaseName' => get_the_title($release_id),
			'componentVersionNumber' => (string) get_post_meta($post->ID, 'component_version_number', true),
			'componentReleaseNumber' => (string) get_post_meta($post->ID, 'component_release_number', true),
			'status' => $status,
			'publicationStatus' => $post->post_status,
			'endOfLifeDate' => (string) get_post_meta($post->ID, 'entry_end_of_life_date', true),
			'noteId' => $note ? $note['id'] : null,
			'noteTitle' => $note ? $note['title'] : '',
			'notes' => $note ? $note['content'] : '',
			'note' => $note,
		);
	}

	private function map_note($post)
	{
		if (! $post instanceof WP_Post) {
			return null;
		}

		return array(
			'id' => $post->ID,
			'title' => $post->post_title,
			'content' => $post->post_content,
			'type' => $this->sanitize_note_type((string) get_post_meta($post->ID, 'note_type', true)),
		);
	}

	private function save_component_groups($post_id, array $groups)
	{
		$group_term_ids = array();

		foreach ($groups as $group) {
			$term_id = 0;

			if (is_array($group)) {
				if (! empty($group['id'])) {
					$term_id = absint($group['id']);
				} elseif (! empty($group['name'])) {
					$term_id = $this->resolve_component_group_term_id($group['name']);
				}
			} elseif (is_numeric($group)) {
				$term_id = absint($group);
			} elseif (is_string($group)) {
				$term_id = $this->resolve_component_group_term_id($group);
			}

			if ($term_id) {
				$group_term_ids[] = $term_id;
			}
		}

		wp_set_post_terms(
			$post_id,
			array_values(array_unique($group_term_ids)),
			ArasSupportMatrixPostTypes::COMPONENT_GROUP_TAXONOMY,
			false
		);
	}

	private function resolve_component_group_term_id($name)
	{
		$sanitized_name = sanitize_text_field((string) $name);

		if ($sanitized_name === '') {
			return 0;
		}

		$existing_term = term_exists($sanitized_name, ArasSupportMatrixPostTypes::COMPONENT_GROUP_TAXONOMY);

		if (is_array($existing_term) && ! empty($existing_term['term_id'])) {
			return (int) $existing_term['term_id'];
		}

		if (is_int($existing_term)) {
			return $existing_term;
		}

		$created_term = wp_insert_term(
			$sanitized_name,
			ArasSupportMatrixPostTypes::COMPONENT_GROUP_TAXONOMY,
			array(
				'slug' => sanitize_title($sanitized_name),
			)
		);

		if (is_wp_error($created_term) || empty($created_term['term_id'])) {
			return 0;
		}

		return (int) $created_term['term_id'];
	}

	private function save_release_meta($post_id, WP_REST_Request $request)
	{
		update_post_meta($post_id, 'build_number', sanitize_text_field((string) $request['buildNumber']));
		update_post_meta($post_id, 'release_date', sanitize_text_field((string) $request['releaseDate']));
		update_post_meta($post_id, 'end_of_life_date', sanitize_text_field((string) $request['endOfLifeDate']));
		$this->save_note_reference($post_id, $request, true);
	}

	private function save_entry_data($post_id, WP_REST_Request $request)
	{
		$release_id = absint($request['innovatorReleaseId']);

		update_post_meta($post_id, 'component_post_id', absint($request['componentId']));
		update_post_meta($post_id, 'release_post_id', $release_id);
		update_post_meta($post_id, 'component_version_number', sanitize_text_field((string) $request['componentVersionNumber']));
		update_post_meta($post_id, 'component_release_number', sanitize_text_field((string) $request['componentReleaseNumber']));
		update_post_meta($post_id, 'entry_end_of_life_date', sanitize_text_field((string) $request['endOfLifeDate']));
		$this->save_note_reference($post_id, $request, false);

		$status = sanitize_text_field((string) $request['status']);
		if ($status !== '') {
			wp_set_post_terms($post_id, array($status), ArasSupportMatrixPostTypes::STATUS_TAXONOMY, false);
		}
	}

	private function entry_title_from_request(WP_REST_Request $request)
	{
		$component_name = get_the_title(absint($request['componentId']));
		$release_name = get_the_title(absint($request['innovatorReleaseId']));
		$version = sanitize_text_field((string) $request['componentVersionNumber']);

		return trim(sprintf('%s / %s / %s', $component_name, $release_name, $version), ' /');
	}

	private function delete_related_entries($meta_key, $meta_value)
	{
		$entries = get_posts(
			array(
				'post_type' => ArasSupportMatrixPostTypes::ENTRY_POST_TYPE,
				'post_status' => 'any',
				'numberposts' => -1,
				'meta_key' => $meta_key,
				'meta_value' => $meta_value,
			)
		);

		foreach ($entries as $entry) {
			wp_delete_post($entry->ID, true);
		}
	}

	private function remove_note_references($note_id)
	{
		$posts = get_posts(
			array(
				'post_type' => array(
					ArasSupportMatrixPostTypes::RELEASE_POST_TYPE,
					ArasSupportMatrixPostTypes::ENTRY_POST_TYPE,
				),
				'post_status' => 'any',
				'numberposts' => -1,
				'meta_key' => 'note_post_id',
				'meta_value' => $note_id,
			)
		);

		foreach ($posts as $post) {
			delete_post_meta($post->ID, 'note_post_id');
		}
	}

	private function sanitize_publication_status($status)
	{
		return $status === 'publish' ? 'publish' : 'draft';
	}

	private function sanitize_note_type($type)
	{
		return $type === 'warning' ? 'warning' : 'info';
	}

	private function save_note_reference($post_id, WP_REST_Request $request, $allow_rich_content = false)
	{
		$note_id = $this->resolve_note_id_from_request($request, $allow_rich_content);

		if ($note_id > 0) {
			update_post_meta($post_id, 'note_post_id', $note_id);
			delete_post_meta($post_id, 'notes');
			return;
		}

		delete_post_meta($post_id, 'note_post_id');
		delete_post_meta($post_id, 'notes');
	}

	private function resolve_note_id_from_request(WP_REST_Request $request, $allow_rich_content = false)
	{
		$note_id = absint($request['noteId']);

		if ($note_id > 0 && get_post_type($note_id) === ArasSupportMatrixPostTypes::NOTE_POST_TYPE) {
			return $note_id;
		}

		$new_note_title = sanitize_text_field((string) $request['newNoteTitle']);
		if ($new_note_title === '') {
			return 0;
		}

		$existing_note = $this->find_post_by_title(ArasSupportMatrixPostTypes::NOTE_POST_TYPE, $new_note_title);
		if ($existing_note instanceof WP_Post) {
			return $existing_note->ID;
		}

		$new_note_content = (string) $request['newNoteContent'];
		$created_note_id = wp_insert_post(
			array(
				'post_type' => ArasSupportMatrixPostTypes::NOTE_POST_TYPE,
				'post_status' => 'publish',
				'post_title' => $new_note_title,
				'post_content' => $allow_rich_content ? wp_kses_post($new_note_content) : sanitize_textarea_field($new_note_content),
			),
			true
		);

		if (is_wp_error($created_note_id)) {
			return 0;
		}

		update_post_meta($created_note_id, 'note_type', $this->sanitize_note_type((string) $request['newNoteType']));

		return (int) $created_note_id;
	}

	private function resolve_note_for_post($post_id)
	{
		$note_id = (int) get_post_meta($post_id, 'note_post_id', true);
		if ($note_id <= 0) {
			return null;
		}

		$note_post = get_post($note_id);
		if (! $note_post instanceof WP_Post || $note_post->post_type !== ArasSupportMatrixPostTypes::NOTE_POST_TYPE) {
			return null;
		}

		return $this->map_note($note_post);
	}

	private function get_latest_release_post()
	{
		$releases = get_posts(
			array(
				'post_type' => ArasSupportMatrixPostTypes::RELEASE_POST_TYPE,
				'post_status' => array('publish', 'draft'),
				'numberposts' => -1,
			)
		);

		if (! $releases) {
			return null;
		}

		usort(
			$releases,
			function ($left, $right) {
				$left_date = (string) get_post_meta($left->ID, 'release_date', true);
				$right_date = (string) get_post_meta($right->ID, 'release_date', true);

				return strcmp($right_date ?: $right->post_title, $left_date ?: $left->post_title);
			}
		);

		return $releases[0];
	}

	private function duplicate_entry_title($entry_id, $new_release_id)
	{
		$component_name = get_the_title((int) get_post_meta($entry_id, 'component_post_id', true));
		$release_name = get_the_title($new_release_id);
		$version = (string) get_post_meta($entry_id, 'component_version_number', true);

		return trim(sprintf('%s / %s / %s', $component_name, $release_name, $version), ' /');
	}

	private function find_post_by_title($post_type, $title)
	{
		$posts = get_posts(
			array(
				'post_type' => $post_type,
				'post_status' => 'any',
				'title' => $title,
				'numberposts' => 1,
			)
		);

		return $posts ? $posts[0] : null;
	}

}
