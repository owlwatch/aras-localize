<?php

if (! defined('ABSPATH')) {
	exit;
}

if (! class_exists('WP_CLI_Command')) {
	return;
}

class Aras_CLI_Scripts_Command extends WP_CLI_Command
{
	/**
	 * Archive posts listed in the deprecated pages CSV by setting post_status to archive.
	 *
	 * ## OPTIONS
	 *
	 * [--csv=<path>]
	 * : Absolute path to a CSV file. Defaults to <theme>/data/deprecated-pages.csv.
	 *
	 * ## EXAMPLES
	 *
	 *     wp aras scripts archive_deprecated_pages
	 *     wp aras scripts archive_deprecated_pages --csv=/tmp/deprecated-pages.csv
	 *
	 * @when after_wp_load
	 */
	public function archive_deprecated_pages($args, $assoc_args)
	{
		$csv_path = $this->get_csv_path($assoc_args);
		$totals = [
			'processed' => 0,
			'archived' => 0,
			'already_archived' => 0,
			'missing' => 0,
			'errors' => 0,
		];

		$this->process_csv_urls($csv_path, function ($url) use (&$totals) {
			$totals['processed']++;
			$post_id = url_to_postid($url);
			if (! $post_id) {
				$totals['missing']++;
				WP_CLI::log("missing\t{$url}");
				return;
			}

			$post = get_post($post_id);
			if (! $post) {
				$totals['missing']++;
				WP_CLI::log("missing\t{$url}\t{$post_id}");
				return;
			}

			if ($post->post_status === 'archive') {
				$totals['already_archived']++;
				WP_CLI::log("already_archived\t{$url}\t{$post_id}\t{$post->post_type}");
				return;
			}

			$updated = wp_update_post([
				'ID' => $post_id,
				'post_status' => 'archive',
			], true);

			if (is_wp_error($updated)) {
				$totals['errors']++;
				WP_CLI::warning("error\t{$url}\t{$post_id}\t" . $updated->get_error_message());
				return;
			}

			$totals['archived']++;
			WP_CLI::log("archived\t{$url}\t{$post_id}\t{$post->post_type}\t{$post->post_status}");
		});

		$this->print_summary($totals);
		WP_CLI::success('Archive operation completed.');
	}

	/**
	 * Restore posts listed in the deprecated pages CSV by publishing and setting Yoast noindex.
	 *
	 * ## OPTIONS
	 *
	 * [--csv=<path>]
	 * : Absolute path to a CSV file. Defaults to <theme>/data/deprecated-pages.csv.
	 *
	 * ## EXAMPLES
	 *
	 *     wp aras scripts restore_deprecated_pages
	 *     wp aras scripts restore_deprecated_pages --csv=/tmp/deprecated-pages.csv
	 *
	 * @when after_wp_load
	 */
	public function restore_deprecated_pages($args, $assoc_args)
	{
		$csv_path = $this->get_csv_path($assoc_args);
		$totals = [
			'processed' => 0,
			'restored' => 0,
			'already_published' => 0,
			'noindexed' => 0,
			'missing' => 0,
			'errors' => 0,
		];

		$this->process_csv_urls($csv_path, function ($url) use (&$totals) {
			$totals['processed']++;
			$post_id = url_to_postid($url);
			if (! $post_id) {
				$totals['missing']++;
				WP_CLI::log("missing\t{$url}");
				return;
			}

			$post = get_post($post_id);
			if (! $post) {
				$totals['missing']++;
				WP_CLI::log("missing\t{$url}\t{$post_id}");
				return;
			}

			if ($post->post_status !== 'publish') {
				$updated = wp_update_post([
					'ID' => $post_id,
					'post_status' => 'publish',
				], true);

				if (is_wp_error($updated)) {
					$totals['errors']++;
					WP_CLI::warning("error\t{$url}\t{$post_id}\t" . $updated->get_error_message());
					return;
				}

				$totals['restored']++;
			} else {
				$totals['already_published']++;
			}

			$noindex_updated = update_post_meta($post_id, '_yoast_wpseo_meta-robots-noindex', '1');
			if ($noindex_updated !== false) {
				$totals['noindexed']++;
			}

			WP_CLI::log("restored\t{$url}\t{$post_id}\t{$post->post_type}\tnoindex=1");
		});

		$this->print_summary($totals);
		WP_CLI::success('Restore operation completed.');
	}

	private function get_csv_path($assoc_args)
	{
		$csv_path = isset($assoc_args['csv']) ? $assoc_args['csv'] : get_template_directory() . '/data/deprecated-pages.csv';
		if (! file_exists($csv_path)) {
			WP_CLI::error('Deprecated pages CSV not found: ' . $csv_path);
		}

		return $csv_path;
	}

	private function process_csv_urls($csv_path, $callback)
	{
		$handle = fopen($csv_path, 'r');
		if (! $handle) {
			WP_CLI::error('Unable to open deprecated pages CSV.');
		}

		while (($row = fgetcsv($handle)) !== false) {
			$url = trim($row[0] ?? '');
			if ($url === '') {
				continue;
			}

			call_user_func($callback, $url);
		}

		fclose($handle);
	}

	private function print_summary($totals)
	{
		WP_CLI::log('');
		WP_CLI::log('summary');
		foreach ($totals as $label => $count) {
			WP_CLI::log("{$label}\t{$count}");
		}
	}
}
