<?php

namespace Aras\Swoogo\Service;

class SwoogoApi
{
	protected $consumerKey;
	protected $consumerSecret;
	protected $accessToken;
	protected $apiBase = 'https://api.swoogo.com/api/v1/';
	protected $connected = false;

	public function __construct($consumerKey, $consumerSecret )
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
			try {
				$this->authorize();
				$this->connected = true;
			}catch( \Exception $e ){
				error_log( print_r( $e, 1 ) );
			}
		}
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
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = json_decode(curl_exec($ch));
		if (empty($result->access_token)) {
			throw new \Exception(__CLASS__ . ': Unable to validate your consumer key and consumer secret. ' . print_r($result, 1));
		}
		$this->accessToken = $result->access_token;
	}

	public function request($url, $parameters = array(), $method = 'get')
	{
		if( !preg_match('/https:/', $url ) ){
			$url = $this->apiBase . $url;
		}

		if( !preg_match('/\.json$/', $url ) ){
			$url .= '.json';
		}

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
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($ch);

		list($contentType, $charset) = explode(';', curl_getinfo($ch, CURLINFO_CONTENT_TYPE), 2);
		
		if( $contentType == 'application/json' ){
			$response = json_decode($response);
		}

		curl_close($ch);

		return $response;
	}

	public function get($endpoint, $parameters = array())
	{
		return $this->request($endpoint, $parameters, 'GET');
	}
	
	public function post($endpoint, $data=[])
	{
		return $this->request($endpoint, $data, 'POST');
	}

	public function put($endpoint, $data=[])
	{
		return $this->request($endpoint, $data, 'PUT');
	}

	public function delete($endpoint, $parameters = array())
	{
		return $this->request($endpoint, $parameters, 'DELETE');
	}

	public function isConnected()
	{
		return $this->connected;
	}
}