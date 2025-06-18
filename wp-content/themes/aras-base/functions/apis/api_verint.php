<?php
namespace Aras;

class Verint {
	public function __construct()
	{
		add_action('init', [$this, 'init']);
	}

	public function init()
	{
		// check for test-verint query param
		if (isset($_GET['test-verint'])) {
			$this->testConnection();
		}
	}

	public function testConnection()
	{
		// Set the Verint API URL
		$verintApiUrl = 'https://aras.com/community/'; // Replace with your actual endpoint

		$user = get_field('verint_user', 'option');
		$key = get_field('verint_api_key', 'option');

		// Set the request headers
		$headers = [
			'Rest-User-Token' => base64_encode($key.':'.$user),
			'Content-Type' => 'application/json',
		];

		// Make the GET request
		$response = wp_remote_get($verintApiUrl.'api.ashx/v2/info.json', [
			'headers' => $headers,
			'timeout' => 15,
		]);

		if (is_wp_error($response)) {
			wp_die('Error: ' . $response->get_error_message());
			return;
		}

		header('content-type: text/javascript; charset=utf-8');
		echo wp_remote_retrieve_body($response);
		exit;
	}
}

new Verint();