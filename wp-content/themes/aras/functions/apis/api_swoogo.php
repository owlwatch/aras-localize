<?php

namespace Aras;

class SwoogoApi
{

	protected $consumerKey;
	protected $consumerSecret;
	protected $accessToken;

	public function __construct($consumerKey, $consumerSecret)
	{

		if (!in_array('curl', get_loaded_extensions())) {
			throw new \Exception('You need to install cURL, see: http://curl.haxx.se/docs/install.html');
		}

		if (empty($consumerKey) || empty($consumerSecret)) {
			throw new \Exception('Make sure you are passing in the correct parameters');
		}

		$this->consumerKey = urlencode($consumerKey);
		$this->consumerSecret = urlencode($consumerSecret);
		if (!$this->accessToken) {
			$this->authorize();
		}
	}


	public function request($url, $parameters = array(), $method = 'get')
	{

		$method = strtolower($method);

		$ch = curl_init();
		$paramString = http_build_query($parameters);
		curl_setopt($ch, CURLOPT_URL, $url . ($method == 'get' && !empty($paramString) ? '?' . $paramString : ''));
		if ($method == 'post') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $paramString);
		} else if ($method == 'put') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($paramString)));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $paramString);
		} else if ($method == 'delete') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->accessToken));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($ch);

		list($contentType, $charset) = explode(';', curl_getinfo($ch, CURLINFO_CONTENT_TYPE), 2);
		
		if( $contentType == 'application/json' ){
			$response = json_decode($response);
		}

		curl_close($ch);

		// we should just 

		return $response;
	}

	/**
	 * Send request to oauth server to get our access token
	 */
	private function authorize()
	{

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERPWD, $this->consumerKey . ':' . $this->consumerSecret);
		curl_setopt($ch, CURLOPT_URL, 'https://www.swoogo.com/api/v1/oauth2/token.json');
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = json_decode(curl_exec($ch));
		if (empty($result->access_token)) {
			throw new \Exception(__CLASS__ . ': Unable to validate your consumer key and consumer secret. ' . print_r($result, 1));
		}
		$this->accessToken = $result->access_token;
	}
}

/**
 * Get an api wrapper
 *
 * @return SwoogoApi $api
 */
function get_swoogo_api()
{
	static $api;
	if (!isset($api)) {
		$key = get_field('swoogo_api_key', 'option');
		$secret = get_field('swoogo_api_secret', 'option');
		$api = new SwoogoApi($key, $secret);
	}
	return $api;
}

function get_custom_contact_fields()
{

	$api = get_swoogo_api();
	$fields = $api->request('https://api.swoogo.com/api/v1/contacts/fields.json', [
		'per-page' => 100
	]);

	return $fields;
}

function get_agenda($event_id)
{
	$cache_key = 'swoogo_agenda_' . $event_id;
	// check the cache
	$cached = wp_cache_get($cache_key, 'aras');
	if ($cached) {
		return $cached;
	}
	$api = get_swoogo_api();
	$response = $api->request(
		'https://api.swoogo.com/api/v1/sessions.json',
		[
			'event_id' => $event_id,
			'fields' => 'name,date,start_time,end_time,location,description,notes,id,webinar_url',
			'expand' => 'speakers,track',
			'per-page' => 200
		]
	);

	if (!$response) {
		return false;
	}

	$json = json_decode($response);
	return $json;
}


add_action('init', function () {

	if (empty($_REQUEST['test-swoogo'])) {
		return;
	}

	$event_id = $_REQUEST['test-swoogo'];

	header('content-type: application/json; charset=utf-8');
	$agenda = get_agenda($event_id);
	echo json_encode($agenda);
	exit;
});

add_action('init', function () {

	if (!isset($_REQUEST['get-swoogo-fields'])) {
		return;
	}
	header('content-type: application/json; charset=utf-8');
	echo json_encode( get_custom_contact_fields() );
	exit;
});

add_action('init', function(){
	if (empty($_REQUEST['swoogo-sync'])) {
		return;
	}
	$event_id = $_REQUEST['swoogo-sync'];
	// how to sync?
	
	header('content-type: application/json; charset=utf-8');
	echo json_encode( get_custom_contact_fields() );
	exit;
});