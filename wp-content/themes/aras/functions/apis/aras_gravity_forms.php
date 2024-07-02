<?php
/* License Key form:
Form ID #14, Field ID #9, set validation format for MAC Address*/

add_filter('gform_field_validation_14_9', 'custom_mac_address_validation', 10, 4);
function custom_mac_address_validation($result, $value)
{
	// Regular expression for MAC Address format XX-XX-XX-XX-XX-XX
	$mac_pattern = '/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/';
	if ($result['is_valid'] && !preg_match($mac_pattern, $value)) {
		$result['is_valid'] = false;
		$result['message'] = 'Please use MacAddress format (XX-XX-XX-XX-XX-XX)';
	}
	return $result;
}


add_filter('gform_field_value', 'populate_fields', 10, 3);
function populate_fields($value, $field, $name)
{
	$webinar_id = '';
	if (get_post_meta(get_the_ID(), 'includes_gotowebinar', true)) {
		$webinar_id = get_post_meta(get_the_ID(), 'gotowebinar_webinar_key', true);
		$webinar_id = !empty($webinar_id) ? $webinar_id : $value;
	}
	if (get_post_meta(get_the_ID(), 'salesforce_campaign', true)) {
		$SecondaryCampaignID = get_post_meta(get_the_ID(), 'salesforce_campaign', true);
		$SecondaryCampaignID = !empty($SecondaryCampaignID) ? $SecondaryCampaignID : $value;
	} else {
		$SecondaryCampaignID = '';
	}
	$values = array(
		'visitor_id'     			=> isset($_COOKIE['visitor_id']) ? $_COOKIE['visitor_id'] : $value,
		'site_language'  			=> isset($_COOKIE['wp-wpml_current_language']) ? $_COOKIE['wp-wpml_current_language'] : $value,
		'DB_annual_revenue'  				=> isset($_COOKIE['DB_annual_revenue']) ? $_COOKIE['DB_annual_revenue'] : $value,
		'DB_Company_Facebook_url'  				=> isset($_COOKIE['DB_Company_Facebook_url']) ? $_COOKIE['DB_Company_Facebook_url'] : $value,
		'DB_Company_Twitter_url'  				=> isset($_COOKIE['DB_Company_Twitter_url']) ? $_COOKIE['DB_Company_Twitter_url'] : $value,
		'DB_executive_linkedIn_profile'  				=> isset($_COOKIE['DB_executive_linkedIn_profile']) ? $_COOKIE['DB_executive_linkedIn_profile'] : $value,
		'DB_executive_city'  				=> isset($_COOKIE['DB_executive_city']) ? $_COOKIE['DB_executive_city'] : $value,
		'DB_executive_state'  				=> isset($_COOKIE['DB_executive_state']) ? $_COOKIE['DB_executive_state'] : $value,
		'DB_executive_country'  				=> isset($_COOKIE['DB_executive_country']) ? $_COOKIE['DB_executive_country'] : $value,
		'DB_phone'  				=> isset($_COOKIE['DB_phone']) ? $_COOKIE['DB_phone'] : $value,
		'DB_email'  				=> isset($_COOKIE['DB_email']) ? $_COOKIE['DB_email'] : $value,
		'DB_executive_description'  				=> isset($_COOKIE['DB_executive_description']) ? $_COOKIE['DB_executive_description'] : $value,
		'DB_first_name'  				=> isset($_COOKIE['DB_first_name']) ? $_COOKIE['DB_first_name'] : $value,
		'DB_last_name'  				=> isset($_COOKIE['DB_last_name']) ? $_COOKIE['DB_last_name'] : $value,
		'DB_job_function'  				=> isset($_COOKIE['DB_job_function']) ? $_COOKIE['DB_job_function'] : $value,
		'DB_job_level'  				=> isset($_COOKIE['DB_job_level']) ? $_COOKIE['DB_job_level'] : $value,
		'DB_title'  				=> isset($_COOKIE['DB_title']) ? $_COOKIE['DB_title'] : $value,
		'DB_employee_count'  				=> isset($_COOKIE['DB_employee_count']) ? $_COOKIE['DB_employee_count'] : $value,
		'WebinarID' => $webinar_id,
		'SecondaryCampaignID' => $SecondaryCampaignID,
	);
	return isset($values[$name]) ? $values[$name] : $value;
}

add_filter('gform_field_value_autocountry', 'autocomplete_country_by_lang');
function autocomplete_country_by_lang($value)
{
	$site_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
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



add_filter('gform_field_value_visitor_id', 'populate_visitor_id');
function populate_visitor_id($value)
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



add_action('gform_after_submission', 'handle_form_submission', 10, 2);
function handle_form_submission($entry, $form)
{

	$current_page_id = get_queried_object_id();
	if (get_field('includes_gotowebinar', $current_page_id)) {
		if ($form['id'] == 10) {
			post_to_third_party_10($entry, $form);
		}
	}

	if ($form['id'] == 14) {
		post_to_third_party_14($entry, $form);
	}

	if ($form['id'] == 9) {
		post_to_third_party_9($entry, $form);
	}

	if ($form['id'] == 7) {
		gated_res_submission($entry, $form);
	} else {
		if (get_field('post_submission_action', $current_page_id) == 'update') {
			$redirect_url = $_SERVER['REQUEST_URI'];
			$post_id = $current_page_id;
			$redirect_url = add_query_arg('id', $post_id, $redirect_url);
			// Redirect to the new URL with the post_id parameter
			wp_redirect($redirect_url);
			exit();
		}
	}
}






function post_to_third_party_10($entry, $form)
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

function post_to_third_party_9($entry, $form)
{
	// Data validation
	$validated_data = array(
		'site_language' => sanitize_text_field(rgar($entry, '28')),
		'pardot_visitor_id' => sanitize_text_field(rgar($entry, '35')),
		'pardoturl' => sanitize_text_field(rgar($entry, '48')),
		'firstname' => sanitize_text_field(rgar($entry, '3')),
		'Last_Touch_Form_Name' => sanitize_text_field(rgar($entry, '38')),
		'lastname' => sanitize_text_field(rgar($entry, '5')),
		'assetname' => sanitize_text_field(rgar($entry, '18')),
		'email' => sanitize_email(rgar($entry, '39')),
		'company' => sanitize_text_field(rgar($entry, '13')),
		'phone' => sanitize_text_field(rgar($entry, '12')),
		'title' => sanitize_text_field(rgar($entry, '42')),
		'country' => sanitize_text_field(rgar($entry, '48.6')),
		'classid' => sanitize_text_field(rgar($entry, '47')),
		'wantcommunity' => sanitize_text_field(rgar($entry, '45')),
		'wantpsb' => sanitize_text_field(rgar($entry, '44')),
		'wantnewsletter' => sanitize_text_field(rgar($entry, '46')),
		'Aras_Cookie' => sanitize_text_field(rgar($entry, '29')),
		'referrer' => sanitize_text_field(rgar($entry, '30')),
		'DB_annual_revenue' => sanitize_text_field(rgar($entry, '17')),
		'DB_Company_Facebook_url' => sanitize_text_field(rgar($entry, '20')),
		'DB_executive_linkedIn_profile' => sanitize_text_field(rgar($entry, '23')),
		'DB_executive_country' => sanitize_text_field(rgar($entry, '24')),
		'DB_executive_city' => sanitize_text_field(rgar($entry, '25')),
		'DB_phone' => sanitize_text_field(rgar($entry, '27')),
		'DB_executive_state' => sanitize_text_field(rgar($entry, '26')),
	);
	$token_request_data = array(
		'grant_type' => 'password',
		'scope' => 'Innovator',
		'client_id' => 'IOMApp',
		'username' => 'web2lead',
		'password' => '4d87a136d94d89910de45c3af3120c31',
		'database' => 'MyInnovator'
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


function post_to_third_party_14($entry, $form)
{
	// Data validation
	$validated_data = array(
		'site_language' => sanitize_text_field(rgar($entry, '28')),
		'pardot_visitor_id' => sanitize_text_field(rgar($entry, '35')),
		'pardoturl' => sanitize_text_field(rgar($entry, '48')),
		'innovatorcampaignid' => sanitize_text_field(rgar($entry, '59')),
		'servermacaddress' => sanitize_text_field(rgar($entry, '9')),
		'innovatorversion' => sanitize_text_field(rgar($entry, '51')),
		'firstname' => sanitize_text_field(rgar($entry, '3')),
		'Last_Touch_Form_Name' => sanitize_text_field(rgar($entry, '38')),
		'lastname' => sanitize_text_field(rgar($entry, '5')),
		'assetname' => sanitize_text_field(rgar($entry, '18')),
		'email' => sanitize_email(rgar($entry, '39')),
		'phone' => sanitize_text_field(rgar($entry, '12')),
		'company' => sanitize_text_field(rgar($entry, '13')),
		'country' => sanitize_text_field(rgar($entry, '40.6')),
		'DB_annual_revenue' => sanitize_text_field(rgar($entry, '17')),
		'DB_Company_Facebook_url' => sanitize_text_field(rgar($entry, '20')),
		'DB_executive_linkedIn_profile' => sanitize_text_field(rgar($entry, '23')),
		'DB_executive_country' => sanitize_text_field(rgar($entry, '24')),
		'DB_executive_city' => sanitize_text_field(rgar($entry, '25')),
		'DB_phone' => sanitize_text_field(rgar($entry, '27')),
		'DB_executive_state' => sanitize_text_field(rgar($entry, '26')),
		'Aras_Cookie' => sanitize_text_field(rgar($entry, '29')),
		'referrer' => sanitize_text_field(rgar($entry, '30')),
	);
	$token_request_data = array(
		'grant_type' => 'password',
		'scope' => 'Innovator',
		'client_id' => 'IOMApp',
		'username' => 'web2lead',
		'password' => '4d87a136d94d89910de45c3af3120c31',
		'database' => 'MyInnovator'
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



function gated_res_submission($entry, $form)
{
	$current_page_id = get_queried_object_id();
	$email = '';
	$post_id = '';
	$redirect_url = $_SERVER['REQUEST_URI'];


	// Iterate through form fields to find email address
	foreach ($form['fields'] as $field) {
		// Check if the field type is email
		if ($field->type == 'email') {
			// Get the field value
			$email = rgar($entry, $field->id);

			// Check if the value is in email format
			if (is_email($email)) {
				// Append the email parameter to the URL
				$redirect_url = add_query_arg('pi_list_email', $email, $redirect_url);
			}
		}
	}

	// Check for post_submission_action
	if (get_field('post_submission_action', $current_page_id) == 'update') {
		$post_id = $current_page_id;
		// Append the post_id parameter to the URL
		$redirect_url = add_query_arg('id', $post_id, $redirect_url);
	}
	// Redirect to the new URL
	wp_redirect($redirect_url);
	exit;
}




//Define and schedule event for update_gotowebinar_refresh_token
function schedule_gotowebinar_api_call()
{
	if (!wp_next_scheduled('gotowebinar_api_call_event')) {
		wp_schedule_event(time(), 'gotowebinar_refresh_time', 'gotowebinar_api_call_event');
	}
}
add_action('after_setup_theme', 'schedule_gotowebinar_api_call');
function gotowebinar_update_interval($schedules)
{
	$schedules['gotowebinar_refresh_time'] = array(
		'interval' => 82800, // in seconds
		'display'  => __('Every 23 Hours'),
	);
	return $schedules;
}
add_filter('cron_schedules', 'gotowebinar_update_interval');
add_action('gotowebinar_api_call_event', 'update_gotowebinar_refresh_token');
function update_gotowebinar_refresh_token()
{
	error_log('running update_gotowebinar_refresh_token at interval of one day');

	$refresh_token_file_path = get_template_directory() . '/api_json/gotowebinar_refresh.json';
	if (file_exists($refresh_token_file_path)) {
		$refresh_token_data = file_get_contents($refresh_token_file_path);
		$refresh_token_json = json_decode($refresh_token_data, true);
		if (isset($refresh_token_json['refresh_token'])) {
			$refresh_token = $refresh_token_json['refresh_token'];
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
	} else {
		$response_body = wp_remote_retrieve_body($response);
		$data = json_decode($response_body, true);
		if (isset($data['refresh_token'])) {
			$tokens = array(
				'refresh_token' => $data['refresh_token'],
			);
			$go_to_webinar_refresh_file = get_template_directory() . '/api_json/gotowebinar_refresh.json';
			file_put_contents($go_to_webinar_refresh_file, json_encode($tokens));
		}
	}
}
