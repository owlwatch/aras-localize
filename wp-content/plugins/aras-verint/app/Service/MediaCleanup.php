<?php

namespace Aras\Verint\Service;

use Aws\S3\Exception\S3Exception;
use Aws\Sdk;

class MediaCleanup
{
	/**
	 * @var \Aws\S3\S3Client $s3client
	 */
	public $s3Client = null;

	/**
	 * The constructor
	 */
	public function __construct()
	{
		add_action('init', [$this, 'init']);
		// add_action('init', [$this, 'testR2']);
		// add_action('aras_next3_media_audit', [$this, 'audit']);
		add_action('aras_next3_media_cleanup', [$this, 'cleanup']);
	}

	public function testR2()
	{
		$this->connect();
		$bucket = 'aras-next3offload-prod';

		$contents = $this->s3Client->listObjects([
			'Bucket' => $bucket,
		]);

		// error_log(print_r($contents, 1));

	}

	public function init()
	{
		$status = get_option('aras_next3_media_cleanup', false);
		// lets start the cleanup
		if( !$status ) {
			// we need to set the status to running
			if (! wp_next_scheduled('aras_next3_media_cleanup')) {
				error_log('scehdule aras_next3_media_cleanup');
				wp_schedule_single_event(time() - 4 * HOUR_IN_SECONDS, 'aras_next3_media_cleanup');
			}
		}

		if( isset( $_REQUEST['get-r2-keys'] )){
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($this->getKeys());
			exit;
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
			'posts_per_page' => 500,
			'post_status' => 'any',
			'meta_query' => [
				'relation' => 'AND',
				[
					'key' => '_next3_attached_file',

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
		foreach ($attachments->posts as $attachment) {
			// get the file path
			$file = get_attached_file($attachment->ID);
			// check if the file exists
			$exists = file_exists($file);
			// save the exists as the value for aras_next3_media_cleanup
			update_post_meta($attachment->ID, 'aras_next3_media_cleanup', $exists ? 'exists' : 'missing');
		}

		// check the query to see if there are any more pages left
		if ($attachments->max_num_pages > 1) {
			// we need to reschedule the event
			update_option('aras_next3_media_audit', 'waiting');
			// fire this immediately
			error_log('reschedule aras_next3_media_audit');
			// we need to reschedule the event
			wp_schedule_single_event(time() - (4 * HOUR_IN_SECONDS), 'aras_next3_media_audit');
		} else {
			// we are done, let's set the status to finished
			update_option('aras_next3_media_audit', 'finished');
		}
	}

	function cleanup_old()
	{

		update_option('	', 'running');
		// we need to retreive all the attachments that have
		// been offloaded via next3
		$attachments = new \WP_Query([
			'post_type' => 'attachment',
			'posts_per_page' => 10,
			'post_status' => 'any',
			'meta_query' => [
				'relation' => 'AND',
				[
					'key' => '_next3_attached_file',

					'value' => '',
					'compare' => '!=',
				],
				[
					'key' => 'aras_next3_media_cleanup',
					'value' => 'missing',
					'compare' => '=',
				],
			],
		]);

		// loop through the attachments
		foreach ($attachments->posts as $attachment) {
			// we need to copy the files back to the server
			error_log('attachment cleanup: ' . $attachment->ID);
			$source_file = next3_get_attached_file($attachment->ID, true);
			// okay, now lets retrieve the actual file from next3
			$source_url = next3_core()->action_ins->get_attatchment_url_preview($attachment->ID);
			error_log(print_r([
				'source_file' => $source_file,
				'source_url' => $source_url,
			], true));

			if (!empty($source_url)) {
				$result = next3_core()->action_ins->create_temp_file_from_url($source_url, pathinfo($source_file));
				error_log(print_r($result, true));
				// $source = $this->create_temp_file_from_url($source_url, pathinfo( $source_file ));
			}
		}

		// check the query to see if there are any more pages left
		if ($attachments->max_num_pages > 1) {
			// we need to reschedule the event
			update_option('aras_next3_media_cleanup', '');
			// fire this immediately
			error_log('reschedule aras_next3_media_cleanup');
			// we need to reschedule the event
			wp_schedule_single_event(time() - (4 * HOUR_IN_SECONDS), 'aras_next3_media_cleanup');
		} else {
			// we are done, let's set the status to finished
			update_option('aras_next3_media_cleanup', 'finished');
		}
	}

	function cleanup()
	{
		// lets set the status to running
		update_option('aras_next3_media_cleanup', 'running');

		$this->connect();
		// lets get the keys if we don't have them already
		$keys = get_option('aras_next3_media_cleanup_keys', false);
		if (!$keys) {
			error_log( 'get keys from s3');
			$keys = $this->getKeys();
			update_option('aras_next3_media_cleanup_keys', $keys);
		}

		// figure out where we stopped
		$offset = get_option('aras_next3_media_cleanup_offset', 0);
		$limit = 100;
		$total = count($keys);
		$keys = array_slice($keys, $offset, $limit);
		// loop through the keys
		foreach ($keys as $key) {
			// each key is a file in a bucket starting with wp-content/uploads/
			// and we want to add it to the wordpress directory
			// if it doesn't exist

			// check if the file exists
			$file = ABSPATH . $key;
			// check if the file exists
			$exists = file_exists($file);
			if( !$exists ){
				// we need to copy the files back to the server
				error_log('file cleanup: ' . $key);

				// create the directory if it doesn't exist using
				// wordpress file functions
				$dir = dirname($file);
				if (!file_exists($dir)) {
					// create the directory
					wp_mkdir_p($dir);
				}
				
				// lets retrieve the content directly from the s3 bucket
				$result = $this->s3Client->getObject([
					'Bucket' => 'aras-next3offload-prod',
					'Key' => $key,
					'SaveAs' => $file,
				]);
			}

			update_option('aras_next3_media_cleanup_offset', ++$offset);
		}

		if( $offset < $total ){
			// we need to reschedule the event
			update_option('aras_next3_media_cleanup', 'waiting');
			// fire this immediately
			error_log('reschedule aras_next3_media_cleanup');
			// we need to reschedule the event
			wp_schedule_single_event(time() - (4 * HOUR_IN_SECONDS), 'aras_next3_media_cleanup');
		} else {
			// we are done, let's set the status to finished
			update_option('aras_next3_media_cleanup', 'finished');
			// when we're done, lets cleanup our options
			delete_option('aras_next3_media_cleanup_keys');
			delete_option('aras_next3_media_cleanup_offset');
		}
	}

	public function getKeys()
	{
		
		// query s3 for all the objects
		$this->connect();
		$bucket = 'aras-next3offload-prod';
		$paginator = $this->s3Client->getPaginator('ListObjects', [
			'Bucket' => $bucket,
		]);

		// keys
		$keys= [];
		foreach( $paginator->search('Contents[].Key') as $key){
			$keys[] = $key;
		}
		return $keys;
	}



	function connect()
	{
		$provider = next3_core()->provider_ins->load('objects')->access();
		if (!$provider->check_configration()) {
			return;
		}

		$region = '';
		$data = $provider->get_access();

		$access_key = ($data['access_key']) ?? '';
		$secret_key = ($data['secret_key']) ?? '';
		$default_bucket = ($data['default_bucket']) ?? '';
		$default_region = ($data['default_region']) ?? '';

		$endpoint = ($data['endpoint']) ?? '';

		$args = [
			'version' => 'latest'
		];

		$settings_options = next3_options();
		$delivery_provider = ($settings_options['delivery']['provider']) ?? 'aws';
		if ($region == 'auto' && $delivery_provider == 'cloudflare') {
			$endpoint_id = ($settings_options['delivery']['provider_data'][$delivery_provider]) ?? '';
			if (!empty($endpoint_id)) {
				$endpoint = 'https://' . $endpoint_id . '.r2.cloudflarestorage.com';
			}
			$region = '';
		} else {
			$region = $default_region;
		}
		try {

			if (!empty($region)) {
				$args['region'] = $region;
			}
			if (!empty($endpoint)) {
				$args['endpoint'] = $endpoint;
			}
			$args = array_merge(array(
				'credentials' => array(
					'key'    => $access_key,
					'secret' => $secret_key,
				),
			), $args);
			$sdk = new Sdk($args);

			if (!empty($region)) {
				$this->s3Client = $sdk->createS3($args);
			} else {
				$this->s3Client = $sdk->createMultiRegionS3($args);
			}
		} catch (S3Exception $e) {
			$this->s3Client = false;
			throw $e;
		}
	}
}
