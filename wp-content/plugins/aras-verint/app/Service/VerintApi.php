<?php

namespace Aras\Verint\Service;

class VerintApi
{
	protected $token;
	protected $apiBase = 'https://www.aras.com/community/api.ashx/v2/';

	public function __construct(){
		if (!in_array('curl', get_loaded_extensions())) {
			throw new \Exception('You need to install cURL, see: http://curl.haxx.se/docs/install.html');
		}
	}

	public function request($url, $parameters = array(), $method = 'get')
	{
		if( !$this->token ){
			// get our token and secret
			$base_url = get_field('verint_base_url', 'option');
			$user = get_field('verint_user', 'option');
			$key = get_field('verint_api_key', 'option');
			
			if( !$user || !$key ){
				throw new \Exception("Error Processing Request", 1);
			}

			if( $base_url ){
				$this->apiBase = $base_url;
			}

			$this->token = base64_encode( $key . ':' . $user );
		}
		if( !preg_match('/https:/', $url ) ){
			$url = $this->apiBase . $url;
		}

		if( !preg_match('/\.json$/', $url ) ){
			$url .= '.json';
		}

		$method = strtolower($method);

		$ch = curl_init();
		$paramString = http_build_query($parameters);
		$headers = [
			'Rest-User-Token: ' . $this->token,
			'Content-Type: application/json',
			'Accept: application/json'
		];
		curl_setopt($ch, CURLOPT_URL, $url . ($method == 'get' && !empty($paramString) ? '?' . $paramString : ''));
		if ($method == 'post') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $paramString);
		} else if ($method == 'put') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			$headers[] ='Content-Length: ' . strlen($paramString);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $paramString);
		} else if ($method == 'delete') {
			curl_setopt($ch, CURLOPT_POST, 1);
			$headers[] = 'Rest-Method: DELETE';
		}
		curl_setopt($ch, CURLOPT_USERAGENT, 'Aras Verint API');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($ch);

		list($contentType, $charset) = explode(';', curl_getinfo($ch, CURLINFO_CONTENT_TYPE), 2);

		// get the headers too
		// $info = curl_getinfo( $ch );print_r( $info );
		curl_close($ch);
		
		if( $contentType != 'application/json' ){
			throw new \Exception($response);
		}

		return json_decode($response);
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
}