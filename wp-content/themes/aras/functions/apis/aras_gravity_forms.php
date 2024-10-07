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

// Add the custom merge tag to the merge tag dropdown
add_filter( 'gform_merge_tags', 'aras_gf_add_format_term_merge_tag', 10, 2 );
function aras_gf_add_format_term_merge_tag( $merge_tags, $form_id ) {
    $merge_tags[] = array(
        'label' => 'Resource Format',  // Label for the merge tag
        'tag'   => '{format_term}' // Merge tag to insert
    );
    return $merge_tags;
}


add_filter( 'gform_replace_merge_tags', 'aras_gf_populate_format_taxonomy', 10, 3 );
function aras_gf_populate_format_taxonomy( $text, $form, $entry ) {
    // Check for the custom merge tag {format_term}
    if ( strpos( $text, '{format_term}' ) === false ) {
        return $text;
    }

    // Get the current post ID of the embedding page
    global $post;
    $post_id = $post->ID;

    // Get the terms of the 'format' taxonomy for the current post
    $terms = get_the_terms( $post_id, 'format' );

    if ( $terms && ! is_wp_error( $terms ) ) {
        // Get the first term from the 'format' taxonomy
        $first_term = $terms[0]->name;

        // Replace the {format_term} merge tag with the first term's name
        $text = str_replace( '{format_term}', $first_term, $text );
    }

    return $text;
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

	$resource_format = '';
	if( is_singular('resource') ){
		// Get the terms of the 'format' taxonomy for the current post
		$terms = get_the_terms( get_the_ID(), 'format' );

		if ( $terms && ! is_wp_error( $terms ) ) {
			// Get the first term from the 'format' taxonomy
			$resource_format = $terms[0]->name;
		}
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
		'Asset_Type_Downloaded' => $resource_format
	);
	$value = isset($values[$name]) ? $values[$name] : $value;
	return $value != $name ? trim($value) : '';
}

add_filter('gform_field_value_autocountry', 'autocomplete_country_by_lang');
function autocomplete_country_by_lang($value)
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


add_filter('gform_confirmation', 'custom_confirmation', 10, 3);
function custom_confirmation($confirmation, $form, $entry)
{
	GFCommon::log_debug('gform_confirmation');

	$current_page_id = get_queried_object_id();
	$redirect_url = $_SERVER['REQUEST_URI'];


	if (get_field('post_submission_action', $current_page_id) == 'update') {
		GFCommon::log_debug('post_submission_action is update');
		if (get_field('post_submission_content_behavior', $current_page_id) == 'newtab') {
			GFCommon::log_debug('post_submission_content_behavior is newtab');
			if ($form['id'] == 7) {
				GFCommon::log_debug('form is 7');
				$email = '';
				foreach ($form['fields'] as $field) {
					if ($field->type == 'email') {
						$email = rgar($entry, $field->id);
						if (is_email($email)) {
							$redirect_url = add_query_arg('pi_list_email', $email, $redirect_url);
						}
					}
				}
			}
			$redirect_url = add_query_arg('id', $current_page_id, $redirect_url);
			if (get_field('custom_confirmation_message', $current_page_id)) {
				$message = get_field('custom_confirmation_message', $current_page_id);
			} else {
				$message = 'To download your resource, click below.';
			}
			if (get_field('custom_confirmation_button_label', $current_page_id)) {
				$label = get_field('custom_confirmation_button_label', $current_page_id);
			} else {
				$label = 'DOWNLOAD';
			}
			$confirmation = '<p>' . $message . '</p><a class="aras-button" href="' . esc_url($redirect_url) . '" target="_blank">' . $label . '</a>';
			return $confirmation;
		}
	} else {
		return $confirmation;
	}


	if (have_rows('flexible_content', $current_page_id)) :
		while (have_rows('flexible_content', $current_page_id)) : the_row();
			if (get_row_layout() == 'split_content_section' || get_row_layout() == 'full_width_form_section') :
				$post_submission_id_set = false;

				if (get_sub_field('post_submission_action', $current_page_id) == 'update' && !$post_submission_id_set) {
					if (get_sub_field('post_submission_content_behavior', $current_page_id) == 'newtab') {

						if ($form['id'] == 7) {
							$email = '';
							foreach ($form['fields'] as $field) {
								if ($field->type == 'email') {
									$email = rgar($entry, $field->id);
									if (is_email($email)) {
										$redirect_url = add_query_arg('pi_list_email', $email, $redirect_url);
									}
								}
							}
						}
						$redirect_url = add_query_arg('id', $current_page_id, $redirect_url);
						if (get_sub_field('custom_confirmation_message', $current_page_id)) {
							$message = get_sub_field('custom_confirmation_message', $current_page_id);
						} else {
							$message = 'To download your resource, click below.';
						}
						if (get_sub_field('custom_confirmation_button_label', $current_page_id)) {
							$label = get_sub_field('custom_confirmation_button_label', $current_page_id);
						} else {
							$label = 'DOWNLOAD';
						}
						$confirmation = '<p>' . $message . '</p><a class="aras-button" href="' . esc_url($redirect_url) . '" target="_blank">' . $label . '</a>';
						return $confirmation;
					} else {
						return $confirmation;
					}
				} else {
					return $confirmation;
				}

				if (have_rows('right_content', $current_page_id)) :
					while (have_rows('right_content', $current_page_id)) : the_row();
						if (have_rows('form_block', $current_page_id)) :
							while (have_rows('form_block', $current_page_id)) : the_row();
								if (get_sub_field('post_submission_action', $current_page_id) == 'update'  && !$post_submission_id_set) {
									if (get_sub_field('post_submission_content_behavior', $current_page_id) == 'newtab') {
										if ($form['id'] == 7) {
											$email = '';
											foreach ($form['fields'] as $field) {
												if ($field->type == 'email') {
													$email = rgar($entry, $field->id);
													if (is_email($email)) {
														$redirect_url = add_query_arg('pi_list_email', $email, $redirect_url);
													}
												}
											}
										}
										$redirect_url = add_query_arg('id', $current_page_id, $redirect_url);
										if (get_sub_field('custom_confirmation_message', $current_page_id)) {
											$message = get_sub_field('custom_confirmation_message', $current_page_id);
										} else {
											$message = 'To download your resource, click below.';
										}
										if (get_sub_field('custom_confirmation_button_label', $current_page_id)) {
											$label = get_sub_field('custom_confirmation_button_label', $current_page_id);
										} else {
											$label = 'DOWNLOAD';
										}
										$confirmation = '<p>' . $message . '</p><a class="aras-button" href="' . esc_url($redirect_url) . '" target="_blank">' . $label . '</a>';
										return $confirmation;
									} else {
										return $confirmation;
									}
								} else {
									return $confirmation;
								}
							endwhile;
						endif;
					endwhile;
				endif;
				if (have_rows('left_content', $current_page_id)) :
					while (have_rows('left_content', $current_page_id)) : the_row();
						if (have_rows('form_block', $current_page_id)) :
							while (have_rows('form_block', $current_page_id)) : the_row();
								if (get_sub_field('post_submission_action', $current_page_id) == 'update'  && !$post_submission_id_set) {
									if (get_sub_field('post_submission_content_behavior', $current_page_id) == 'newtab') {
										if ($form['id'] == 7) {
											$email = '';
											foreach ($form['fields'] as $field) {
												if ($field->type == 'email') {
													$email = rgar($entry, $field->id);
													if (is_email($email)) {
														$redirect_url = add_query_arg('pi_list_email', $email, $redirect_url);
													}
												}
											}
										}
										$redirect_url = add_query_arg('id', $current_page_id, $redirect_url);
										if (get_sub_field('custom_confirmation_message', $current_page_id)) {
											$message = get_sub_field('custom_confirmation_message', $current_page_id);
										} else {
											$message = 'Thank you for your interest. To view your resource, click below.';
										}
										if (get_sub_field('custom_confirmation_button_label', $current_page_id)) {
											$label = get_sub_field('custom_confirmation_button_label', $current_page_id);
										} else {
											$label = 'VIEW RESOURCE';
										}
										$confirmation = '<p>' . $message . '</p><a class="aras-button" href="' . esc_url($redirect_url) . '" target="_blank">' . $label . '</a>';
										return $confirmation;
									} else {
										return $confirmation;
									}
								} else {
									return $confirmation;
								}
							endwhile;
						endif;
					endwhile;
				endif;
			endif;
		endwhile;
	endif;
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

		if (get_field('post_submission_action', $current_page_id) == 'redirect') {
			if (get_field('post-submission_redirect_url', $current_page_id)) {
				$redirect_url = get_field('post-submission_redirect_url', $current_page_id);
				wp_redirect($redirect_url);
				exit();
			}
		}

		if (get_field('post_submission_action', $current_page_id) == 'update') {
			if (get_field('post_submission_content_behavior', $current_page_id) != 'newtab') {
				$redirect_url = $_SERVER['REQUEST_URI'];
				$post_id = $current_page_id;
				$redirect_url = add_query_arg('id', $post_id, $redirect_url);
				wp_redirect($redirect_url);
				exit();
			}
		}
		if (have_rows('flexible_content', $current_page_id)) :
			while (have_rows('flexible_content', $current_page_id)) : the_row();
				if (get_row_layout() == 'split_content_section' || get_row_layout() == 'full_width_form_section') :
					$post_submission_id_set = false;

					if (get_sub_field('post_submission_action', $current_page_id) == 'redirect') {
						if (get_sub_field('post-submission_redirect_url', $current_page_id)) {
							$redirect_url = get_sub_field('post-submission_redirect_url', $current_page_id);
							wp_redirect($redirect_url);
							exit();
							$post_submission_id_set = true;
						}
					}

					if (get_sub_field('post_submission_action', $current_page_id) == 'update' && !$post_submission_id_set) {
						if (get_sub_field('post_submission_content_behavior', $current_page_id) != 'newtab') {
							$redirect_url = $_SERVER['REQUEST_URI'];
							$post_id = $current_page_id;
							$redirect_url = add_query_arg('id', $post_id, $redirect_url);
							wp_redirect($redirect_url);
							$post_submission_id_set = true;
						}
					}

					if (have_rows('right_content', $current_page_id)) :
						while (have_rows('right_content', $current_page_id)) : the_row();
							if (have_rows('form_block', $current_page_id)) :
								while (have_rows('form_block', $current_page_id)) : the_row();

									if (get_sub_field('post_submission_action', $current_page_id) == 'redirect') {
										if (get_sub_field('post-submission_redirect_url', $current_page_id)) {
											$redirect_url = get_sub_field('post-submission_redirect_url', $current_page_id);
											wp_redirect($redirect_url);
											exit();
											$post_submission_id_set = true;
										}
									}

									if (get_sub_field('post_submission_action', $current_page_id) == 'update'  && !$post_submission_id_set) {
										if (get_sub_field('post_submission_content_behavior', $current_page_id) != 'newtab') {
											$redirect_url = $_SERVER['REQUEST_URI'];
											$post_id = $current_page_id;
											$redirect_url = add_query_arg('id', $post_id, $redirect_url);
											wp_redirect($redirect_url);
											$post_submission_id_set = true;
										}
									}
								endwhile;
							endif;
						endwhile;
					endif;
					if (have_rows('left_content', $current_page_id)) :
						while (have_rows('left_content', $current_page_id)) : the_row();
							if (have_rows('form_block', $current_page_id)) :
								while (have_rows('form_block', $current_page_id)) : the_row();

									if (get_sub_field('post_submission_action', $current_page_id) == 'redirect') {
										if (get_sub_field('post-submission_redirect_url', $current_page_id)) {
											$redirect_url = get_sub_field('post-submission_redirect_url', $current_page_id);
											wp_redirect($redirect_url);
											exit();
											$post_submission_id_set = true;
										}
									}

									if (get_sub_field('post_submission_action', $current_page_id) == 'update'  && !$post_submission_id_set) {
										if (get_sub_field('post_submission_content_behavior', $current_page_id) != 'newtab') {
											$redirect_url = $_SERVER['REQUEST_URI'];
											$post_id = $current_page_id;
											$redirect_url = add_query_arg('id', $post_id, $redirect_url);
											wp_redirect($redirect_url);
											$post_submission_id_set = true;
										}
									}
								endwhile;
							endif;
						endwhile;
					endif;
				endif;
			endwhile;
		endif;
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
	$redirect_url = $_SERVER['REQUEST_URI'];



	if (get_field('post_submission_action', $current_page_id) == 'redirect') {
		if (get_field('post-submission_redirect_url', $current_page_id)) {
			$redirect_url = get_field('post-submission_redirect_url', $current_page_id);
			wp_redirect($redirect_url);
			exit();
		}
	}
	if (have_rows('flexible_content', $current_page_id)) :
		while (have_rows('flexible_content', $current_page_id)) : the_row();
			if (get_row_layout() == 'split_content_section' || get_row_layout() == 'full_width_form_section') :
				$post_submission_id_set = false;

				if (get_sub_field('post_submission_action', $current_page_id) == 'redirect') {
					if (get_sub_field('post-submission_redirect_url', $current_page_id)) {
						$redirect_url = get_sub_field('post-submission_redirect_url', $current_page_id);
						wp_redirect($redirect_url);
						exit();
						$post_submission_id_set = true;
					}
				}
				if (have_rows('right_content', $current_page_id)) :
					while (have_rows('right_content', $current_page_id)) : the_row();
						if (have_rows('form_block', $current_page_id)) :
							while (have_rows('form_block', $current_page_id)) : the_row();
								if (get_sub_field('post_submission_action', $current_page_id) == 'redirect') {
									if (get_sub_field('post-submission_redirect_url', $current_page_id)) {
										$redirect_url = get_sub_field('post-submission_redirect_url', $current_page_id);
										wp_redirect($redirect_url);
										exit();
										$post_submission_id_set = true;
									}
								}
							endwhile;
						endif;
					endwhile;
				endif;
				if (have_rows('left_content', $current_page_id)) :
					while (have_rows('left_content', $current_page_id)) : the_row();
						if (have_rows('form_block', $current_page_id)) :
							while (have_rows('form_block', $current_page_id)) : the_row();

								if (get_sub_field('post_submission_action', $current_page_id) == 'redirect') {
									if (get_sub_field('post-submission_redirect_url', $current_page_id)) {
										$redirect_url = get_sub_field('post-submission_redirect_url', $current_page_id);
										wp_redirect($redirect_url);
										exit();
										$post_submission_id_set = true;
									}
								}
							endwhile;
						endif;
					endwhile;
				endif;
			endif;
		endwhile;
	endif;



	foreach ($form['fields'] as $field) {
		if ($field->type == 'email') {
			$email = rgar($entry, $field->id);
			if (is_email($email)) {
				$redirect_url = add_query_arg('pi_list_email', $email, $redirect_url);
			}
		}
	}
	// Check for post_submission_action at the page level
	if (get_field('post_submission_action', $current_page_id) == 'update' && get_field('post_submission_content_behavior', $current_page_id) != 'newtab') {
		$redirect_url = add_query_arg('id', $current_page_id, $redirect_url);
	}
	// Check for post_submission_action within flexible content sections
	if (have_rows('flexible_content', $current_page_id)) {
		while (have_rows('flexible_content', $current_page_id)) {
			the_row();

			if (in_array(get_row_layout(), ['split_content_section', 'full_width_form_section'])) {
				if (check_and_add_redirect_param($current_page_id, $redirect_url)) {
					break;
				}
			}
		}
	}
	if (get_field('post_submission_action', $current_page_id) == 'update' && get_field('post_submission_content_behavior', $current_page_id) != 'newtab') {
		wp_redirect($redirect_url);
		exit;
	}
}
function check_and_add_redirect_param($current_page_id, &$redirect_url)
{
	$post_submission_id_set = false;
	if (get_sub_field('post_submission_action', $current_page_id) == 'update' && get_sub_field('post_submission_content_behavior', $current_page_id) != 'newtab' && !$post_submission_id_set) {
		$redirect_url = add_query_arg('id', $current_page_id, $redirect_url);
		$post_submission_id_set = true;
	}
	if (have_rows('right_content', $current_page_id)) {
		while (have_rows('right_content', $current_page_id)) {
			the_row();
			if (have_rows('form_block', $current_page_id)) {
				while (have_rows('form_block', $current_page_id)) {
					the_row();
					if (get_sub_field('post_submission_action', $current_page_id) == 'update' && get_sub_field('post_submission_content_behavior', $current_page_id) != 'newtab' && !$post_submission_id_set) {
						$redirect_url = add_query_arg('id', $current_page_id, $redirect_url);
						$post_submission_id_set = true;
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
					if (get_sub_field('post_submission_action', $current_page_id) == 'update' && get_sub_field('post_submission_content_behavior', $current_page_id) != 'newtab' && !$post_submission_id_set) {
						$redirect_url = add_query_arg('id', $current_page_id, $redirect_url);
						$post_submission_id_set = true;
					}
				}
			}
		}
	}
	return $post_submission_id_set;
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

// lets alter the form output
add_filter('gform_form_tag', function ($output, $form) {


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

	$validation_endpoint = rest_url('gf/v2/forms/'.$form['id'].'/submissions/validation');

	$attrs = [
		'data-marketo-id="'.esc_attr($data->marketo_form).'"',
		'data-form-title="' . esc_attr($form_title) . '"',
		'data-validation-endpoint="'.esc_attr($validation_endpoint).'"'
	];

	$form_tag = '<form '.implode(' ', $attrs);

	$output = str_replace('<form', $form_tag, $output);
	return $output;
}, 10, 2);

add_filter('gform_field_content', function ($field_content, $field, $value, $lead_id, $form_id) {
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
}, 10, 5);
