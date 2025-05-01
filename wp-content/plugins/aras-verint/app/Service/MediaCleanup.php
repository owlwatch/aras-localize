<?php
namespace Aras\Verint\Service;

class MediaCleanup
{
	/**
	 * The constructor
	 */
	public function __construct() {
		add_action('init', [$this, 'init']);
		add_action('aras_next3_media_audit', [$this, 'audit']);
	}

	public function init() {
		error_log('init media cleanup');
		// if we do not have a flag that the media cleanup
		// is running or finished, add a cron job
		$status = get_option('aras_next3_media_audit', false);
		error_log('aras_next3_media_audit: '.$status);
		if( !$status ) {
			// we want to schedule this to run once. we will reschedule
			// if it doesn't finish
			if( ! wp_next_scheduled('aras_next3_media_audit') ) {
				error_log('scehdule aras_next3_media_audit');
				wp_schedule_single_event( time(), 'aras_next3_media_audit' );
			}
		}
	}

	public function audit()
	{
		error_log('aras_next3_media_audit()');
		// let's set the status to running
		update_option('aras_next3_media_audit', 'running');

		// query attachments that have 'next3_get_attached_file_cache'
		// set but do not have a flag for aras_next3_media_cleanup
		$attachments = new \WP_Query([
			'post_type' => 'attachment',
			'posts_per_page' => 100,
			'post_status' => 'any',
			'meta_query' => [
				'relation' => 'AND',
				[
					'key' => 'next3_get_attached_file_cache',

					'value' => '',
					'compare' => '!=',
				],
				[
					'key' => 'aras_next3_media_cleanup',
					'value' => '',
					'compare' => 'NOT EXISTS',
				],
			],
		]);

		// loop through the attachments
		foreach( $attachments->posts as $attachment ) {
			// get the file path
			$file = get_attached_file($attachment->ID);
			// check if the file exists
			$exists = file_exists($file);
			// save the exists as the value for aras_next3_media_cleanup
			update_post_meta($attachment->ID, 'aras_next3_media_cleanup', $exists?'exists':'missing');
		}

		// check the query to see if there are any more pages left
		if( $attachments->max_num_pages > 1 ) {
			// we need to reschedule the event
			wp_schedule_single_event( time(), 'aras_next3_media_audit' );
		} else {
			// we are done, let's set the status to finished
			update_option('aras_next3_media_audit', 'finished');
		}

	}



}