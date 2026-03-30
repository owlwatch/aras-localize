<?php

if (! defined('ABSPATH')) {
	exit;
}

class ArasSupportMatrixImporter
{
	const CHUNK_SIZE = 200;
	const DELETE_BATCH_SIZE = 250;
	const MAINTENANCE_INTERVAL = 100;
	const NONCE_ACTION = 'aras_support_matrix_import';
	const STATE_OPTION = 'aras_support_matrix_import_state';

	public function get_status()
	{
		$state = $this->get_state();

		if (! $state) {
			return $this->default_status();
		}

		return $this->format_status($state);
	}

	public function start_import($file, array $args = array())
	{
		$args = wp_parse_args(
			$args,
			array(
				'reset' => false,
			)
		);

		if (! file_exists($file)) {
			throw new RuntimeException(sprintf('CSV file not found: %s', $file));
		}

		$total_rows = $this->count_csv_rows($file);
		$headers = $this->read_headers($file);
		$offset = $this->read_data_offset($file);

		$state = array(
			'status' => 'running',
			'phase' => $args['reset'] ? 'reset' : 'import',
			'file' => $file,
			'headers' => $headers,
			'offset' => $offset,
			'row_count' => 0,
			'total_rows' => $total_rows,
			'counts' => array(
				'releases' => 0,
				'components' => 0,
				'entries' => 0,
				'updated_entries' => 0,
			),
			'release_cache' => array(),
			'component_cache' => array(),
			'reset' => (bool) $args['reset'],
			'reset_post_type_index' => 0,
			'started_at' => time(),
			'finished_at' => 0,
			'message' => $args['reset'] ? 'Resetting existing data.' : 'Preparing import.',
			'last_error' => '',
		);

		$this->save_state($state);

		return $this->format_status($state);
	}

	public function process_next_batch()
	{
		$state = $this->get_state();

		if (! $state) {
			throw new RuntimeException('No import is currently active.');
		}

		if ($state['status'] !== 'running') {
			return $this->format_status($state);
		}

		$this->begin_memory_optimized_import();

		try {
			if ($state['phase'] === 'reset') {
				$state = $this->process_reset_batch($state);
			}

			if ($state['status'] === 'running' && $state['phase'] === 'import') {
				$state = $this->process_import_batch($state);
			}
		} catch (Throwable $exception) {
			$state['status'] = 'error';
			$state['finished_at'] = time();
			$state['last_error'] = $exception->getMessage();
			$state['message'] = 'Import failed.';
			$this->save_state($state);
		} finally {
			$this->end_memory_optimized_import();
		}

		return $this->format_status($state);
	}

	public function import_csv($file, array $args = array())
	{
		$status = $this->start_import($file, $args);

		while ($status['status'] === 'running') {
			$status = $this->process_next_batch();
		}

		if ($status['status'] === 'error') {
			throw new RuntimeException($status['lastError'] ?: 'Import failed.');
		}

		return $status['counts'];
	}

	private function process_reset_batch(array $state)
	{
		$post_types = array(
			ArasSupportMatrixPostTypes::ENTRY_POST_TYPE,
			ArasSupportMatrixPostTypes::RELEASE_POST_TYPE,
			ArasSupportMatrixPostTypes::COMPONENT_POST_TYPE,
		);

		while ($state['reset_post_type_index'] < count($post_types)) {
			$post_type = $post_types[$state['reset_post_type_index']];
			$post_ids = get_posts(
				array(
					'post_type' => $post_type,
					'post_status' => 'any',
					'numberposts' => self::DELETE_BATCH_SIZE,
					'fields' => 'ids',
					'orderby' => 'ID',
					'order' => 'ASC',
					'no_found_rows' => true,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
				)
			);

			if (empty($post_ids)) {
				$state['reset_post_type_index']++;
				continue;
			}

			foreach ($post_ids as $post_id) {
				wp_delete_post((int) $post_id, true);
				clean_post_cache((int) $post_id);
			}

			$state['message'] = sprintf('Resetting existing %s records.', $post_type);
			$this->save_state($state);

			return $state;
		}

		$state['phase'] = 'import';
		$state['message'] = 'Existing data cleared. Starting import.';
		$this->save_state($state);

		return $state;
	}

	private function process_import_batch(array $state)
	{
		$handle = fopen($state['file'], 'r');

		if (! $handle) {
			throw new RuntimeException(sprintf('Could not open CSV file: %s', $state['file']));
		}

		if (fseek($handle, (int) $state['offset']) !== 0) {
			fclose($handle);
			throw new RuntimeException('Could not seek to the saved CSV offset.');
		}

		$processed_in_batch = 0;
		$reached_end_of_file = false;

		try {
			while ($processed_in_batch < self::CHUNK_SIZE && ($row = fgetcsv($handle)) !== false) {
				$state['offset'] = ftell($handle);
				$state['row_count']++;
				$processed_in_batch++;

				$record = $this->map_row($state['headers'], $row);

				if (! $record) {
					if ($processed_in_batch % self::MAINTENANCE_INTERVAL === 0) {
						$this->perform_import_maintenance();
					}
					continue;
				}

				$release_id = $this->upsert_release($record, $state['release_cache'], $state['counts']);
				$component_id = $this->upsert_component($record, $state['component_cache'], $state['counts']);
				$this->upsert_entry($record, $release_id, $component_id, $state['counts']);

				if ($processed_in_batch % self::MAINTENANCE_INTERVAL === 0) {
					$this->perform_import_maintenance();
				}
			}

			$reached_end_of_file = feof($handle);
		} finally {
			fclose($handle);
		}

		if ($reached_end_of_file) {
			$state['status'] = 'completed';
			$state['phase'] = 'done';
			$state['finished_at'] = time();
			$state['message'] = 'Import complete.';
		} else {
			$state['message'] = sprintf(
				'Imported %d of %d rows.',
				(int) $state['row_count'],
				(int) $state['total_rows']
			);
		}

		$this->save_state($state);

		return $state;
	}

	private function count_csv_rows($file)
	{
		$handle = fopen($file, 'r');

		if (! $handle) {
			throw new RuntimeException(sprintf('Could not open CSV file: %s', $file));
		}

		$count = 0;
		fgetcsv($handle);

		while (fgetcsv($handle) !== false) {
			$count++;
		}

		fclose($handle);

		return $count;
	}

	private function read_headers($file)
	{
		$handle = fopen($file, 'r');

		if (! $handle) {
			throw new RuntimeException(sprintf('Could not open CSV file: %s', $file));
		}

		$headers = fgetcsv($handle);
		fclose($handle);

		if (! is_array($headers)) {
			throw new RuntimeException('Could not read CSV headers.');
		}

		return $headers;
	}

	private function read_data_offset($file)
	{
		$handle = fopen($file, 'r');

		if (! $handle) {
			throw new RuntimeException(sprintf('Could not open CSV file: %s', $file));
		}

		fgetcsv($handle);
		$offset = ftell($handle);
		fclose($handle);

		if (! is_int($offset)) {
			throw new RuntimeException('Could not determine CSV data offset.');
		}

		return $offset;
	}

	private function get_state()
	{
		$state = get_option(self::STATE_OPTION, array());

		return is_array($state) ? $state : array();
	}

	private function save_state(array $state)
	{
		update_option(self::STATE_OPTION, $state, false);
	}

	private function default_status()
	{
		return array(
			'status' => 'idle',
			'phase' => 'idle',
			'progress' => 0,
			'processedRows' => 0,
			'totalRows' => 0,
			'counts' => array(
				'releases' => 0,
				'components' => 0,
				'entries' => 0,
				'updated_entries' => 0,
			),
			'message' => '',
			'lastError' => '',
			'startedAt' => 0,
			'finishedAt' => 0,
			'reset' => false,
		);
	}

	private function format_status(array $state)
	{
		$total_rows = max(0, (int) ($state['total_rows'] ?? 0));
		$processed_rows = max(0, (int) ($state['row_count'] ?? 0));
		$progress = $total_rows > 0 ? min(100, (int) floor(($processed_rows / $total_rows) * 100)) : 0;

		if (($state['status'] ?? '') === 'completed') {
			$progress = 100;
		}

		return array(
			'status' => (string) ($state['status'] ?? 'idle'),
			'phase' => (string) ($state['phase'] ?? 'idle'),
			'progress' => $progress,
			'processedRows' => $processed_rows,
			'totalRows' => $total_rows,
			'counts' => is_array($state['counts'] ?? null) ? $state['counts'] : $this->default_status()['counts'],
			'message' => (string) ($state['message'] ?? ''),
			'lastError' => (string) ($state['last_error'] ?? ''),
			'startedAt' => (int) ($state['started_at'] ?? 0),
			'finishedAt' => (int) ($state['finished_at'] ?? 0),
			'reset' => ! empty($state['reset']),
		);
	}

	private function begin_memory_optimized_import()
	{
		wp_suspend_cache_addition(true);
		wp_defer_term_counting(true);
		wp_defer_comment_counting(true);
	}

	private function end_memory_optimized_import()
	{
		$this->perform_import_maintenance();
		wp_suspend_cache_addition(false);
		wp_defer_term_counting(false);
		wp_defer_comment_counting(false);
	}

	private function perform_import_maintenance()
	{
		global $wpdb;

		if (isset($wpdb->queries) && is_array($wpdb->queries)) {
			$wpdb->queries = array();
		}

		if (function_exists('wp_cache_flush')) {
			wp_cache_flush();
		}

		if (function_exists('gc_collect_cycles')) {
			gc_collect_cycles();
		}
	}

	private function map_row($headers, $row)
	{
		if (! is_array($headers) || ! is_array($row)) {
			return null;
		}

		$data = array_combine($headers, $row);

		if (! is_array($data)) {
			return null;
		}

		$release = $this->parse_release((string) $data['release_build']);
		$component_release = $this->parse_component_release((string) $data['cell_value']);
		$eol_date = $this->parse_month_year((string) $data['eol_date']);
		$status = sanitize_text_field((string) $data['status']);
		$component_name = sanitize_text_field((string) $data['component']);

		if ($release['name'] === '' || $component_name === '' || $status === '') {
			return null;
		}

		return array(
			'release_name' => $release['name'],
			'build_number' => $release['build_number'],
			'end_of_life_date' => $eol_date,
			'component_name' => $component_name,
			'component_version_number' => $component_release['version'],
			'component_release_number' => $component_release['release_number'],
			'status' => $status,
		);
	}

	private function parse_release($value)
	{
		$value = trim($value);
		$name = preg_replace('/\s*\|\s*\(Build.+$/', '', $value);
		$build = '';

		if (preg_match('/\(Build\s+([^)]+)\)/i', $value, $matches)) {
			$build = trim($matches[1]);
		}

		return array(
			'name' => trim((string) $name),
			'build_number' => $build,
		);
	}

	private function parse_component_release($value)
	{
		$value = trim($value);
		$value = preg_replace('/\[\d+\]/', '', $value);

		if ($value === '') {
			return array(
				'version' => '',
				'release_number' => '',
			);
		}

		if (preg_match('/^(.*?)\s*\|\s*\(([^)]+)\)$/', $value, $matches)) {
			return array(
				'version' => trim($matches[1]),
				'release_number' => trim($matches[2]),
			);
		}

		if (preg_match('/^(.*?)\s*\(([^)]+)\)$/', $value, $matches)) {
			return array(
				'version' => trim($matches[1]),
				'release_number' => trim($matches[2]),
			);
		}

		return array(
			'version' => $value,
			'release_number' => $value,
		);
	}

	private function parse_month_year($value)
	{
		$value = trim($value);
		$value = preg_replace('/\s+\d+$/', '', $value);
		$value = preg_replace('/\[\d+\]/', '', $value);

		if ($value === '') {
			return '';
		}

		$date = DateTime::createFromFormat('M-Y', $value);

		return $date ? $date->format('Y-m-01') : '';
	}

	private function upsert_release(array $record, array &$cache, array &$counts)
	{
		$key = $record['release_name'];

		if (isset($cache[$key])) {
			return $cache[$key];
		}

		$post = $this->find_post_by_title(ArasSupportMatrixPostTypes::RELEASE_POST_TYPE, $record['release_name']);

		if (! $post) {
			$post_id = wp_insert_post(
				array(
					'post_type' => ArasSupportMatrixPostTypes::RELEASE_POST_TYPE,
					'post_status' => 'publish',
					'post_title' => $record['release_name'],
				)
			);
			$counts['releases']++;
		} else {
			$post_id = $post->ID;
		}

		update_post_meta($post_id, 'build_number', $record['build_number']);
		update_post_meta($post_id, 'end_of_life_date', $record['end_of_life_date']);
		clean_post_cache($post_id);

		$cache[$key] = $post_id;

		return $post_id;
	}

	private function upsert_component(array $record, array &$cache, array &$counts)
	{
		$key = $record['component_name'];

		if (isset($cache[$key])) {
			return $cache[$key];
		}

		$post = $this->find_post_by_title(ArasSupportMatrixPostTypes::COMPONENT_POST_TYPE, $record['component_name']);

		if (! $post) {
			$post_id = wp_insert_post(
				array(
					'post_type' => ArasSupportMatrixPostTypes::COMPONENT_POST_TYPE,
					'post_status' => 'publish',
					'post_title' => $record['component_name'],
				)
			);
			$counts['components']++;
		} else {
			$post_id = $post->ID;
		}

		$cache[$key] = $post_id;
		clean_post_cache($post_id);

		return $post_id;
	}

	private function upsert_entry(array $record, $release_id, $component_id, array &$counts)
	{
		$existing = get_posts(
			array(
				'post_type' => ArasSupportMatrixPostTypes::ENTRY_POST_TYPE,
				'post_status' => 'publish',
				'numberposts' => 1,
				'no_found_rows' => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'meta_query' => array(
					array(
						'key' => 'component_post_id',
						'value' => $component_id,
					),
					array(
						'key' => 'release_post_id',
						'value' => $release_id,
					),
					array(
						'key' => 'component_version_number',
						'value' => $record['component_version_number'],
					),
					array(
						'key' => 'component_release_number',
						'value' => $record['component_release_number'],
					),
				),
			)
		);

		if ($existing) {
			$post_id = $existing[0]->ID;
			$counts['updated_entries']++;
		} else {
			$post_id = wp_insert_post(
				array(
					'post_type' => ArasSupportMatrixPostTypes::ENTRY_POST_TYPE,
					'post_status' => 'publish',
					'post_title' => sprintf(
						'%s / %s / %s',
						$record['component_name'],
						$record['release_name'],
						$record['component_version_number'] ?: $record['status']
					),
				)
			);
			$counts['entries']++;
		}

		update_post_meta($post_id, 'component_post_id', $component_id);
		update_post_meta($post_id, 'release_post_id', $release_id);
		update_post_meta($post_id, 'component_version_number', $record['component_version_number']);
		update_post_meta($post_id, 'component_release_number', $record['component_release_number']);
		update_post_meta($post_id, 'entry_end_of_life_date', $record['end_of_life_date']);
		update_post_meta($post_id, 'notes', '');
		wp_set_post_terms($post_id, array($record['status']), ArasSupportMatrixPostTypes::STATUS_TAXONOMY, false);
		clean_post_cache($post_id);
	}

	private function find_post_by_title($post_type, $title)
	{
		$posts = get_posts(
			array(
				'post_type' => $post_type,
				'post_status' => 'publish',
				'title' => $title,
				'numberposts' => 1,
				'no_found_rows' => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		return $posts ? $posts[0] : null;
	}
}

if (defined('WP_CLI') && WP_CLI) {
	WP_CLI::add_command(
		'aras-support-matrix import',
		function ($args, $assoc_args) {
			$importer = new ArasSupportMatrixImporter();
			$file = $assoc_args['file'] ?? ARAS_SUPPORT_MATRIX_PATH . 'data.csv';
			$reset = ! empty($assoc_args['reset']);

			try {
				$result = $importer->import_csv($file, array('reset' => $reset));
			} catch (Throwable $exception) {
				WP_CLI::error($exception->getMessage());
			}

			WP_CLI::success(
				sprintf(
					'Import complete. Releases: %d, Components: %d, New entries: %d, Updated entries: %d',
					$result['releases'],
					$result['components'],
					$result['entries'],
					$result['updated_entries']
				)
			);
		}
	);
}
