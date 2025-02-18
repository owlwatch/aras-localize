<?php

namespace Aras\Integration;

use GFCommon;

class GravityForms
{
	private static $instance;

	public static function getInstance()
	{
		if (! isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct()
	{
		add_filter('gform_field_validation_14_9', [$this, 'validate_mac_address'], 10, 4);
		add_filter('gform_merge_tags', [$this, 'add_format_term_merge_tag'], 10, 2);
		add_filter('gform_replace_merge_tags', [$this, 'populate_format_taxonomy'], 10, 7);
		add_filter('gform_pre_submission', [$this, 'clear_db_fields'], 10, 1);
		add_filter('gform_field_value', [$this, 'populate_fields'], 10, 3);
		add_filter('gform_field_value_autocountry', [$this, 'autocomplete_country_by_lang']);
		add_filter('gform_field_value_visitor_id', [$this, 'populate_visitor_id'], 10, 1);
		add_filter('gform_confirmation', [$this, 'gform_confirmation'], 10, 4);
		add_action('gform_after_submission', [$this, 'gform_after_submission'], 10, 2);
		add_filter('gform_form_tag', [$this, 'gform_form_tag'], 10, 2);
		add_filter('gform_field_content', [$this, 'gform_field_content'], 10, 5);
		add_filter( 'gform_form_args', [$this, 'gform_form_args'] );
	}

	public function gform_form_args($args)
	{
		// disable this for now...
		// $args['submission_method'] = \GFFormDisplay::SUBMISSION_METHOD_AJAX;
		return $args;
	}

	public function validate_mac_address($result, $value)
	{
		// Regular expression for MAC Address format XX-XX-XX-XX-XX-XX
		$mac_pattern = '/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/';
		if ($result['is_valid'] && !preg_match($mac_pattern, $value)) {
			$result['is_valid'] = false;
			$result['message'] = 'Please use MacAddress format (XX-XX-XX-XX-XX-XX)';
		}
		return $result;
	}

	public function add_format_term_merge_tag($merge_tags, $form_id)
	{
		$merge_tags[] = array(
			'label' => 'Resource Format',  // Label for the merge tag
			'tag'   => '{format_term}' // Merge tag to insert
		);
		return $merge_tags;
	}

	public function populate_format_taxonomy($text, $form, $entry)
	{
		// Check for the custom merge tag {format_term}
		if (strpos($text, '{format_term}') === false) {
			return $text;
		}

		// Get the current post ID of the embedding page
		global $post;
		$post_id = $post->ID;

		// Get the terms of the 'format' taxonomy for the current post
		$terms = get_the_terms($post_id, 'format');

		if ($terms && ! is_wp_error($terms)) {
			// Get the first term from the 'format' taxonomy
			$first_term = $terms[0]->name;

			// Replace the {format_term} merge tag with the first term's name
			$text = str_replace('{format_term}', $first_term, $text);
		}

		return $text;
	}

	public function populate_visitor_id($value)
	{
		// Iterate through $_COOKIE to find the cookie that meets the condition
		foreach ($_COOKIE as $name => $cookie) {
			if (strpos($name, 'visitor_id') !== false && strpos($name, '-hash') === false) {
				GFCommon::log_debug('Value from cookie => ' . $cookie);
				return $cookie;
			}
		}
		// If no matching cookie is found, return an empty string or handle the case accordingly
		return '';
	}

	public function clear_db_fields($form)
	{
		foreach ($form['fields'] as $field) {
			$field_id = $field->id;
			$field_value = rgpost("input_{$field_id}");
			if (preg_match('/^db_/i', $field_value)) {
				$_POST["input_{$field_id}"] = '';
			}
		}
	}

	public function populate_fields($value, $field, $name)
	{
		$webinar_id = '';
		if (get_post_meta(get_the_ID(), 'includes_gotowebinar', true)) {
			$webinar_id = get_post_meta(get_the_ID(), 'gotowebinar_webinar_key', true);
			$webinar_id = !empty($webinar_id) ? $webinar_id : $value;
		}
		$marketo_interactive_webinar_asset_id = '';
		if (get_post_meta(get_the_ID(), 'includes_marketo_interactive_webinar', true)) {
			$marketo_interactive_webinar_asset_id = get_post_meta(get_the_ID(), 'marketo_interactive_webinar_asset_id', true);
			$marketo_interactive_webinar_asset_id = $marketo_interactive_webinar_asset_id ?: $value;
		}
		if (get_post_meta(get_the_ID(), 'salesforce_campaign', true)) {
			$SecondaryCampaignID = get_post_meta(get_the_ID(), 'salesforce_campaign', true);
			$SecondaryCampaignID = !empty($SecondaryCampaignID) ? $SecondaryCampaignID : $value;
		} else {
			$SecondaryCampaignID = '';
		}

		$resource_format = '';
		if (is_singular('resource')) {
			// Get the terms of the 'format' taxonomy for the current post
			$terms = get_the_terms(get_the_ID(), 'format');

			if ($terms && ! is_wp_error($terms)) {
				// Get the first term from the 'format' taxonomy
				$resource_format = $terms[0]->name;
			}
		}

		$values = array(
			'visitor_id'                           => isset($_COOKIE['visitor_id']) ? $_COOKIE['visitor_id'] : $value,
			'site_language'                        => isset($_COOKIE['wp-wpml_current_language']) ? $_COOKIE['wp-wpml_current_language'] : $value,
			'DB_annual_revenue'                    => isset($_COOKIE['DB_annual_revenue']) ? $_COOKIE['DB_annual_revenue'] : $value,
			'DB_Company_Facebook_url'              => isset($_COOKIE['DB_Company_Facebook_url']) ? $_COOKIE['DB_Company_Facebook_url'] : $value,
			'DB_Company_Twitter_url'               => isset($_COOKIE['DB_Company_Twitter_url']) ? $_COOKIE['DB_Company_Twitter_url'] : $value,
			'DB_executive_linkedIn_profile'        => isset($_COOKIE['DB_executive_linkedIn_profile']) ? $_COOKIE['DB_executive_linkedIn_profile'] : $value,
			'DB_executive_city'                    => isset($_COOKIE['DB_executive_city']) ? $_COOKIE['DB_executive_city'] : $value,
			'DB_executive_state'                   => isset($_COOKIE['DB_executive_state']) ? $_COOKIE['DB_executive_state'] : $value,
			'DB_executive_country'                 => isset($_COOKIE['DB_executive_country']) ? $_COOKIE['DB_executive_country'] : $value,
			'DB_phone'                             => isset($_COOKIE['DB_phone']) ? $_COOKIE['DB_phone'] : $value,
			'DB_email'                             => isset($_COOKIE['DB_email']) ? $_COOKIE['DB_email'] : $value,
			'DB_executive_description'             => isset($_COOKIE['DB_executive_description']) ? $_COOKIE['DB_executive_description'] : $value,
			'DB_first_name'                        => isset($_COOKIE['DB_first_name']) ? $_COOKIE['DB_first_name'] : $value,
			'DB_last_name'                         => isset($_COOKIE['DB_last_name']) ? $_COOKIE['DB_last_name'] : $value,
			'DB_job_function'                      => isset($_COOKIE['DB_job_function']) ? $_COOKIE['DB_job_function'] : $value,
			'DB_job_level'                         => isset($_COOKIE['DB_job_level']) ? $_COOKIE['DB_job_level'] : $value,
			'DB_title'                             => isset($_COOKIE['DB_title']) ? $_COOKIE['DB_title'] : $value,
			'DB_employee_count'                    => isset($_COOKIE['DB_employee_count']) ? $_COOKIE['DB_employee_count'] : $value,
			'WebinarID'                            => $webinar_id,
			'SecondaryCampaignID'                  => $SecondaryCampaignID,
			'Asset_Type_Downloaded'                => $resource_format,
			'Marketo_Interactive_Webinar_Asset_ID' => $marketo_interactive_webinar_asset_id,
		);
		$value = isset($values[$name]) ? $values[$name] : $value;
		return $value != $name ? trim($value) : '';
	}

	public function autocomplete_country_by_lang($value)
	{
		$site_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		if (str_contains($site_url, '/en/')) {
			return 'United States';
		} elseif (str_contains($site_url, '/fr-fr/')) {
			return 'France';
		} elseif (str_contains($site_url, '/de-de/')) {
			return 'Germany';
		} elseif (str_contains($site_url, '/ja-jp/')) {
			return 'Japan';
		}
		return 'United States'; // Default
	}

	/**
	 * Handle third party integrations
	 *
	 * @param [type] $entry
	 * @param [type] $form
	 * @return void
	 */
	public function gform_after_submission($entry, $form)
	{

		$current_page_id = url_to_postid( $entry['source_url'] );

		if (get_field('includes_gotowebinar', $current_page_id)) {
			if ($form['id'] == 10) {
				$this->_post_to_third_party_10($entry, $form);
			}
		}

		if ($form['id'] == 14) {
			$this->_post_to_third_party_14($entry, $form);
		}
	
		if ($form['id'] == 9) {
			$this->_post_to_third_party_9($entry, $form);
		}
	}

	public function gform_confirmation($confirmation, $form, $entry, $ajax)
	{
		$post_id = url_to_postid( $entry['source_url'] );
		$post_submission = $this->_get_post_submission_config( $post_id );
		if( !$post_submission ){
			error_log( 'no post submission' );
			return $confirmation;
		}

		if( 'update' === $post_submission['action'] ){
			$redirect_url = $entry['source_url'];
			if( 'newtab' == $post_submission['content_behavior'] && 7 !== $form['id'] ){

				// to handle scenarios where we need to append the id
				$redirect_url = add_query_arg('id', $post_id, $redirect_url);
				foreach ($form['fields'] as $field) {
					if ($field->type == 'email') {
						$email = rgar($entry, $field->id);
						if (is_email($email)) {
							$redirect_url = add_query_arg('pi_list_email', $email, $redirect_url);
						}
					}
				}
				$message = $post_submission['custom_confirmation_message'];
				if( !$message ){
					$is_left = 'split_content_section:left' == $post_submission['found_location'];
					// if this is the left content, assume its view resource??
					if( $is_left ){
						$message = __('Thank you for your interest. To view your resource, click below.', 'aras');
					} 
					// default message
					else {
						$message = __('To download your resource, click below.', 'aras');
					}
				}
				$label = $post_submission['custom_confirmation_button_label'] ?: ($is_left ? __('VIEW RESOURCE', 'aras') : __('DOWNLOAD', 'aras') );
				$confirmation = '<p>'.$message.'</p><a class="aras-button" href="' . esc_url($redirect_url) . '" target="_blank">' . $label . '</a>';
				return $confirmation;
			}
			// if this is the asset download form, we are going to do that
			// weirdness where we append the id of the page and redirect
			if( 7 == $form['id']){
				$confirmation = [
					'redirect' => add_query_arg('id', $post_id, $redirect_url)
				];
				return $confirmation;
			}

			/**
			 * @todo This is how this should behave, but we are keeping the redirects
			 * in place for backwards compatability
			 */
			$use_standard_gravity_form_confirmation = true;
			if( $use_standard_gravity_form_confirmation ){
				$confirmation = $post_submission['content'];
				return $confirmation;
			}

			$confirmation = [
				'redirect' => add_query_arg('id', $post_id, $redirect_url)
			];

			return $confirmation;

		}
		else if( 'redirect' === $post_submission['action'] ){
			$confirmation = [
				'redirect' => $post_submission['redirect_url']
			];
			return $confirmation;
		}
	}

	public function gform_form_tag ($output, $form) {


		global $vxg_marketo;
		if (!isset($vxg_marketo)) {
			return $output;
		}
	
		// get the marketo form id
		$data_object = $vxg_marketo->get_data_object();
	
		$feeds = $data_object->get_feed_by_form($form['id'], true);
	
		if (empty($feeds)) {
			return $output;
		}
	
		$feed = $feeds[0];
		if (!isset($feed['meta'])) {
			return $output;
		}
	
		$data = json_decode($feed['data']);
		if (!$data || !isset($data->marketo_form)) {
			return $output;
		}
	
		$form_title = str_replace("'", "\\'", $form['title']);
	
		$validation_endpoint = rest_url('gf/v2/forms/' . $form['id'] . '/submissions/validation');
	
		$attrs = [
			'data-marketo-id="' . esc_attr($data->marketo_form) . '"',
			'data-form-title="' . esc_attr($form_title) . '"',
			'data-validation-endpoint="' . esc_attr($validation_endpoint) . '"'
		];
	
		$form_tag = '<form ' . implode(' ', $attrs);
	
		$output = str_replace('<form', $form_tag, $output);
		return $output;
	}

	public function gform_field_content($field_content, $field, $value, $lead_id, $form_id)
	{
		// Check if the field is set to be populated dynamically
		if (!empty($field->allowsPrepopulate)) {
			// Get the parameter name
			$parameter_name = is_array($field->inputName) ? reset($field->inputName) : $field->inputName;
			// Only proceed if the parameter name is not empty
			if (!empty($parameter_name)) {
				// Add the data-field-name attribute to the field container
				$field_content = preg_replace('/(<[^>]*\bclass=["\'][^"\']*ginput_container[^"\']*["\'][^>]*)(>)/', '$1 data-field-name="' . esc_attr($parameter_name) . '"$2', $field_content);
			}
		}
		return $field_content;
	}

	private function _retrieve_post_submission_config( $page_id, $sub_field=false, $found_location='post' )
	{
		$fn = $sub_field ? 'get_sub_field' : 'get_field';
		$config = [
			'action' => $fn('post_submission_action', $page_id),
			'content_behavior' => $fn('post-post_submission_content_behavior', $page_id),
			'redirect_url' => $fn('post-submission_redirect_url', $page_id),
			'custom_confirmation_message' => $fn('custom_confirmation_message', $page_id),
			'custom_confirmation_button_label' => $fn('custom_confirmation_button_label', $page_id),
			'content' => $fn('post_submission_content', $page_id),
			'found_location' => $found_location
		];

		return $config;
	}

	private function _get_post_submission_config( $page_id=null )
	{
		$post_submission = false;
		$current_page_id = $page_id ?: get_queried_object_id();

		if( get_field('post_submission_action', $current_page_id) ) {
			$post_submission = $this->_retrieve_post_submission_config( $current_page_id, false, 'post' );
		}

		if (have_rows('flexible_content', $current_page_id)) {
			while (have_rows('flexible_content', $current_page_id)) {
				the_row();
				if (get_row_layout() == 'split_content_section' || get_row_layout() == 'full_width_form_section') {
					
					if( get_sub_field('post_submission_action' ) ){
						$post_submission = $this->_retrieve_post_submission_config( $current_page_id, true, 'full_width_form_section' );
					}
					if (have_rows('right_content', $current_page_id)) {
						while (have_rows('right_content', $current_page_id)) {
							the_row();
							if (have_rows('form_block', $current_page_id)) {
								while (have_rows('form_block', $current_page_id) ) {
									the_row();
									if (get_sub_field('post_submission_action', $current_page_id)) {
										$post_submission = $this->_retrieve_post_submission_config( $current_page_id, true, 'split_content_section:right' );
									}
								}
							}
						}
					}
					if (have_rows('left_content', $current_page_id)) {
						while (have_rows('left_content', $current_page_id)) {
							the_row();
							if (have_rows('form_block', $current_page_id)) {
								while (have_rows('form_block', $current_page_id)) {
									the_row();
									if (get_sub_field('post_submission_action', $current_page_id)) {
										$post_submission = $this->_retrieve_post_submission_config( $current_page_id, true, 'split_content_section:left' );
									}
								}
							}
						}
					}
				}
			}
		}

		return $post_submission;
	}

	private function _post_to_third_party_9($entry, $form)
	{
		// Data validation
		$validated_data = array(
			'site_language'                 => sanitize_text_field(rgar($entry, '28')),
			'pardot_visitor_id'             => sanitize_text_field(rgar($entry, '35')),
			'pardoturl'                     => sanitize_text_field(rgar($entry, '48')),
			'firstname'                     => sanitize_text_field(rgar($entry, '3')),
			'Last_Touch_Form_Name'          => sanitize_text_field(rgar($entry, '38')),
			'lastname'                      => sanitize_text_field(rgar($entry, '5')),
			'assetname'                     => sanitize_text_field(rgar($entry, '18')),
			'email'                         => sanitize_email(rgar($entry, '39')),
			'company'                       => sanitize_text_field(rgar($entry, '13')),
			'phone'                         => sanitize_text_field(rgar($entry, '12')),
			'title'                         => sanitize_text_field(rgar($entry, '42')),
			'country'                       => sanitize_text_field(rgar($entry, '48.6')),
			'classid'                       => sanitize_text_field(rgar($entry, '47')),
			'wantcommunity'                 => sanitize_text_field(rgar($entry, '45')),
			'wantpsb'                       => sanitize_text_field(rgar($entry, '44')),
			'wantnewsletter'                => sanitize_text_field(rgar($entry, '46')),
			'Aras_Cookie'                   => sanitize_text_field(rgar($entry, '29')),
			'referrer'                      => sanitize_text_field(rgar($entry, '30')),
			'DB_annual_revenue'             => sanitize_text_field(rgar($entry, '17')),
			'DB_Company_Facebook_url'       => sanitize_text_field(rgar($entry, '20')),
			'DB_executive_linkedIn_profile' => sanitize_text_field(rgar($entry, '23')),
			'DB_executive_country'          => sanitize_text_field(rgar($entry, '24')),
			'DB_executive_city'             => sanitize_text_field(rgar($entry, '25')),
			'DB_phone'                      => sanitize_text_field(rgar($entry, '27')),
			'DB_executive_state'            => sanitize_text_field(rgar($entry, '26')),
		);
		$token_request_data = array(
			'grant_type' => 'password',
			'scope'      => 'Innovator',
			'client_id'  => 'IOMApp',
			'username'   => 'web2lead',
			'password'   => '4d87a136d94d89910de45c3af3120c31',
			'database'   => 'MyInnovator'
		);
		$token_response = wp_remote_post('https://myinnovator.com/OAuthServer/connect/token', array(
			'body' => $token_request_data
		));
		// Check for errors in obtaining token
		if (is_wp_error($token_response)) {
			// Log error
			GFCommon::log_debug('Error obtaining access token: ' . $token_response->get_error_message());
			return;
		}
		// Decode the JSON response to obtain access token
		$token_response_data = json_decode(wp_remote_retrieve_body($token_response), true);
		// Check if access token exists
		if (isset($token_response_data['access_token'])) {
			$access_token = $token_response_data['access_token'];
			// Endpoint URL
			$endpoint_url = 'https://myinnovator.com/server/odata/method.MyI_ExternalIntegration';
			// Prepare the request body
			$body = json_encode($validated_data);
			GFCommon::log_debug('Here is the body: ' . print_r($body, true));

			// Prepare the request headers
			$headers = array(
				'Authorization' => 'Bearer ' . $access_token,
				'Content-Type' => 'application/json',
			);

			// Request args
			$args = array(
				'headers' => $headers,
				'body' => $body,
			);
			// Send the POST request
			$response = wp_remote_post($endpoint_url, $args);
			GFCommon::log_debug(print_r($response, true));
			// Check for errors in request
			if (is_wp_error($response)) {
				// Log error
				GFCommon::log_debug('Error sending request: ' . $response->get_error_message());
				return;
			}
			// Log response
			GFCommon::log_debug('gform_after_submission: response => ' . print_r($response, true));
		} else {
			// Log error if access token is not found
			GFCommon::log_debug('Error obtaining access token: Access token not found in response');
		}
	}

	private function _post_to_third_party_10($entry, $form)
	{
		$webinar_key = sanitize_text_field(rgar($entry, '57'));
		// Data validation
		$validated_data = array(
			'firstName' => sanitize_text_field(rgar($entry, '3')),
			'lastName' => sanitize_text_field(rgar($entry, '5')),
			'email' => sanitize_text_field(rgar($entry, '39')),
		);
		$refresh_token_file_path = get_template_directory() . '/api_json/gotowebinar_refresh.json';
		if (file_exists($refresh_token_file_path)) {
			$refresh_token_data = file_get_contents($refresh_token_file_path);
			$refresh_token_json = json_decode($refresh_token_data, true);
			if (isset($refresh_token_json['refresh_token'])) {
				$refresh_token = $refresh_token_json['refresh_token'];
				// GFCommon::log_debug('THE REFRESH TOKEN IS ' . $refresh_token);
			} else {
				$refresh_token = '';
			}
		} else {
			$refresh_token = '';
		}
		$url = 'https://authentication.logmeininc.com/oauth/token';
		$headers = array(
			'Authorization' => 'Basic MzkyMDBmMDAtZTE0Ny00OTYxLWIzODctMzQ2NmY5MWZmYjM3Oko1Z2NEWHllNGcyR1J0ZzhxOWxnbkJwWA==',
			'Content-Type' => 'application/x-www-form-urlencoded'
		);
		$body = array(
			'grant_type' => 'refresh_token',
			'refresh_token' => $refresh_token,
		);
		$body_encoded = http_build_query($body);
		$response = wp_remote_post(
			$url,
			array(
				'headers' => $headers,
				'body' => $body_encoded
			)
		);
		if (is_wp_error($response)) {
			$access_token = '';
		} else {
			$response_body = wp_remote_retrieve_body($response);
			$data = json_decode($response_body, true);
			if (isset($data['access_token'])) {
				$access_token = $data['access_token'];
			} else {
				$access_token = '';
			}
		}
		//	GFCommon::log_debug('THE ACCESS TOKEN IS ' . $access_token);
		$endpoint_url = 'https://api.getgo.com/G2W/rest/v2/organizers/560872234068083717/webinars/' . $webinar_key . '/registrants?resendConfirmation=true';
		// Prepare the request body
		$body = json_encode($validated_data);
		// Prepare the request headers
		$headers = array(
			'Accept' => 'application/json',
			'Authorization' => 'Bearer ' . $access_token,
			'Content-Type' => 'application/json',
		);
		// Request args
		$args = array(
			'headers' => $headers,
			'body' => $body,
		);
		// Send the POST request
		$response = wp_remote_post($endpoint_url, $args);
		// Check for errors in request
		if (is_wp_error($response)) {
			// Log error
			//		GFCommon::log_debug('Error sending request: ' . $response->get_error_message());
			return;
		} else {
			//		GFCommon::log_debug('THE GOTOWEBINAR WAS CONSIDERED A SUCCESS:');
			//		GFCommon::log_debug(print_r($response, true));
		}
	}
	
	function _post_to_third_party_14($entry, $form)
	{
		// Data validation
		$validated_data = array(
			'site_language'                 => sanitize_text_field(rgar($entry, '28')),
			'pardot_visitor_id'             => sanitize_text_field(rgar($entry, '35')),
			'pardoturl'                     => sanitize_text_field(rgar($entry, '48')),
			'innovatorcampaignid'           => sanitize_text_field(rgar($entry, '59')),
			'servermacaddress'              => sanitize_text_field(rgar($entry, '9')),
			'innovatorversion'              => sanitize_text_field(rgar($entry, '51')),
			'firstname'                     => sanitize_text_field(rgar($entry, '3')),
			'Last_Touch_Form_Name'          => sanitize_text_field(rgar($entry, '38')),
			'lastname'                      => sanitize_text_field(rgar($entry, '5')),
			'assetname'                     => sanitize_text_field(rgar($entry, '18')),
			'email'                         => sanitize_email(rgar($entry, '39')),
			'phone'                         => sanitize_text_field(rgar($entry, '12')),
			'company'                       => sanitize_text_field(rgar($entry, '13')),
			'country'                       => sanitize_text_field(rgar($entry, '40.6')),
			'DB_annual_revenue'             => sanitize_text_field(rgar($entry, '17')),
			'DB_Company_Facebook_url'       => sanitize_text_field(rgar($entry, '20')),
			'DB_executive_linkedIn_profile' => sanitize_text_field(rgar($entry, '23')),
			'DB_executive_country'          => sanitize_text_field(rgar($entry, '24')),
			'DB_executive_city'             => sanitize_text_field(rgar($entry, '25')),
			'DB_phone'                      => sanitize_text_field(rgar($entry, '27')),
			'DB_executive_state'            => sanitize_text_field(rgar($entry, '26')),
			'Aras_Cookie'                   => sanitize_text_field(rgar($entry, '29')),
			'referrer'                      => sanitize_text_field(rgar($entry, '30')),
		);
		$token_request_data = array(
			'grant_type' => 'password',
			'scope'      => 'Innovator',
			'client_id'  => 'IOMApp',
			'username'   => 'web2lead',
			'password'   => '4d87a136d94d89910de45c3af3120c31',
			'database'   => 'MyInnovator'
		);
		$token_response = wp_remote_post('https://myinnovator.com/OAuthServer/connect/token', array(
			'body' => $token_request_data
		));
		// Check for errors in obtaining token
		if (is_wp_error($token_response)) {
			// Log error
			// GFCommon::log_debug('Error obtaining access token: ' . $token_response->get_error_message());
			return;
		}
		// Decode the JSON response to obtain access token
		$token_response_data = json_decode(wp_remote_retrieve_body($token_response), true);
		// Check if access token exists
		if (isset($token_response_data['access_token'])) {
			$access_token = $token_response_data['access_token'];
			// Endpoint URL
			$endpoint_url = 'https://myinnovator.com/server/odata/method.MyI_ExternalIntegration';
			// Prepare the request body
			$body = json_encode($validated_data);
			GFCommon::log_debug('Here is the body: ' . print_r($body, true));

			// Prepare the request headers
			$headers = array(
				'Authorization' => 'Bearer ' . $access_token,
				'Content-Type' => 'application/json',
			);

			// Request args
			$args = array(
				'headers' => $headers,
				'body' => $body,
			);
			// Send the POST request
			$response = wp_remote_post($endpoint_url, $args);
			GFCommon::log_debug(print_r($response, true));
			// Check for errors in request
			if (is_wp_error($response)) {
				// Log error
				GFCommon::log_debug('Error sending request: ' . $response->get_error_message());
				return;
			}
			// Log response
			GFCommon::log_debug('gform_after_submission: response => ' . print_r($response, true));
		} else {
			// Log error if access token is not found
			GFCommon::log_debug('Error obtaining access token: Access token not found in response');
		}
	}
}

GravityForms::getInstance();