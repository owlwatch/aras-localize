<?php

namespace Aras\Localize\API;

class LocalizeAPI
{
	const BASE_URL = 'https://api.localizejs.com/v2.0';
	const DEFAULT_TIMEOUT = 6;

	private $project_key;
	private $api_key;

	/**
	 * Constructor
	 *
	 * @param string $project_key
	 * @param string $api_key
	 */
	public function __construct($project_key, $api_key)
	{
		$this->project_key = $project_key;
		$this->api_key = $api_key;
	}

	/**
	 * Get project information including enabled languages
	 *
	 * @return array|false Project data or false on failure
	 */
	public function get_project()
	{
		$endpoint = self::BASE_URL . '/projects/' . rawurlencode($this->project_key);

		$response = wp_remote_get($endpoint, [
			'timeout' => self::DEFAULT_TIMEOUT,
			'headers' => $this->get_headers(),
		]);

		return $this->process_response($response);
	}

	/**
	 * Get translated phrases from Localize API
	 *
	 * @param array $phrases Array of phrases to translate
	 * @param string $target_language Target language code
	 * @return array Associative array mapping original phrases to translations
	 */
	public function get_phrases($phrases, $target_language)
	{
		if (empty($phrases) || empty($target_language)) {
			return [];
		}

		$translations = [];

		// Make individual API calls for each phrase since the API doesn't support batch search
		foreach ($phrases as $phrase) {
			if (empty($phrase)) {
				continue;
			}

			$translation = $this->get_single_phrase($phrase, $target_language);
			if ($translation !== null && $translation !== $phrase) {
				$translations[$phrase] = $translation;
			}
		}

		return $translations;
	}

	/**
	 * Get translation for a single phrase
	 *
	 * @param string $phrase The phrase to translate
	 * @param string $target_language Target language code
	 * @return string|null The translation or null if not found
	 */
	private function get_single_phrase($phrase, $target_language)
	{
		$endpoint = self::BASE_URL . '/projects/' . rawurlencode($this->project_key) . '/phrases';

		// Build query parameters for exact search
		$query_params = [
			'search' => $phrase,
			'exactMatch' => 'true',
			'type' => 'phrase',
			'languageCode' => $target_language,
			'limit' => 1, // We only need one result
			'state' => 'active'
		];

		$endpoint_with_params = $endpoint . '?' . http_build_query($query_params);

		$response = wp_remote_get($endpoint_with_params, [
			'timeout' => self::DEFAULT_TIMEOUT,
			'headers' => $this->get_headers(),
		]);

		$data = $this->process_response($response);

		if ($data === false || !isset($data['phrases']) || !is_array($data['phrases'])) {
			return null;
		}

		// Process the API response - look for exact match
		foreach ($data['phrases'] as $phrase_data) {
			// look in the translations and grab the first one if it exists
			if (isset($phrase_data['translations']) && is_array($phrase_data['translations']) && !empty($phrase_data['translations'])) {
				return $phrase_data['translations'][0]['value'] ?? null;
			}
		}

		return null;
	}

	/**
	 * Get default request headers
	 *
	 * @return array
	 */
	private function get_headers()
	{
		return [
			'Authorization' => 'Bearer ' . $this->api_key,
			'Accept' => 'application/json',
		];
	}

	/**
	 * Process API response and extract data
	 *
	 * @param array|WP_Error $response
	 * @return array|false
	 */
	private function process_response($response)
	{
		if (is_wp_error($response)) {
			error_log('Localize API error: ' . $response->get_error_message());
			return false;
		}

		$body = wp_remote_retrieve_body($response);
		if (empty($body)) {
			error_log('Localize API: Empty response body');
			return false;
		}

		$data = json_decode($body, true);
		if (!is_array($data)) {
			error_log('Localize API: Invalid JSON response');
			return false;
		}

		if (!isset($data['data'])) {
			error_log('Localize API: Missing data field in response');
			return false;
		}

		return $data['data'];
	}

	/**
	 * Create API instance from WordPress options
	 *
	 * @return LocalizeAPI|null
	 */
	public static function create_from_options()
	{
		$project_key = get_option('project_key');
		if (empty($project_key)) {
			return null;
		}

		$api_key = '';
		if (function_exists('get_field')) {
			$api_key = get_field('localize_api_key', 'option');
		}
		if (empty($api_key)) {
			$api_key = get_option('localize_api_key');
		}

		if (empty($api_key)) {
			return null;
		}

		return new self($project_key, $api_key);
	}
}
