<?php
////We ball (send it now)
//update_salesforce_data();
//Define and schedule event for update_partners_from_file
function schedule_salesforce_api_call()
{
	if (!wp_next_scheduled('salesforce_api_call_event')) {
		wp_schedule_event(time(), 'salesforce_refresh_time', 'salesforce_api_call_event');
	}
}
add_action('after_setup_theme', 'schedule_salesforce_api_call');
function salesforce_update_interval($schedules)
{
	$schedules['salesforce_refresh_time'] = array(
		'interval' => 7201, // in seconds
		'display'  => __('Every Hour'),
	);
	return $schedules;
}
add_filter('cron_schedules', 'salesforce_update_interval');
add_action('salesforce_api_call_event', 'update_salesforce_data');

function update_salesforce_data()
{
	error_log('running update_salesforce_data at interval of one 7201s');

	// partners data
	$partners_url = 'https://aras1.my.salesforce.com/services/data/v60.0/ui-api/list-records/00BUM000000arH72AI';
	$p_query_params = array(
		'optionalFields' => '
			Partner_Integrations__c,Partner_Icon_For_Website__c,Aras_Website__c,Partners_URL_Link__c,Partner_Info__c,Partner_Name_For_Website__c,Industries_Partner__c,Partner_Solutions__c,Type_Partner__c,Regions_Partner__c,Name,Partner_Name_For_Website_Japan__c,Partner_Info_Japan__c',
		'sortBy' => 'Name',
		'pageSize' => '2000'
	);
	$partners_url = add_query_arg($p_query_params, $partners_url);
	$partners_data = fetch_data_from_salesforce_api($partners_url);
	if ($partners_data) {
		save_salesforce_data_to_file($partners_data, 'Salesforce_Partners_00BUM000000arH72AI.json');
	}

	// academic users data
	$academic_users_url = 'https://aras1.my.salesforce.com/services/data/v60.0/ui-api/list-records/00BUM000000bkYX2AY';
	$au_query_params = array(
		'optionalFields' => '
		Aras_Website__c,Academic_Logo__c,Academic_Website_Link__c,Academic_Description_for_Website__c,Name,',
		'sortBy' => 'Name',
		'pageSize' => '2000'
	);
	$academic_users_url = add_query_arg($au_query_params, $academic_users_url);
	$academic_users_data = fetch_data_from_salesforce_api($academic_users_url);
	if ($academic_users_url) {
		save_salesforce_data_to_file($academic_users_data, 'Salesforce_Academic_Users_00BUM000000bkYX2AY.json');
	}
}

// API CALL
function fetch_data_from_salesforce_api($url)
{
	$clientId = '3MVG9p1Q1BCe9GmDMzV_B995ILaTLlcH62_4FqAiYF2ABC0hVjOWWF1i8ix5_QdB0rb7w1c3ufX0sCzvgGxVV';
	$clientSecret = '9FF1DFB9ED3DFC62E5528995CB23C6F4F4101D2C654586F58E9C151392658679';
	$username = 'markopsintegrations@aras.com';
	$password = 'Pardotaras4!';
	$securityToken = 'EGeQjvdr1EatbBxECPtNG40mW';
	$loginUrl = 'https://login.salesforce.com/services/oauth2/token';
	$params = [
		'grant_type' => 'password',
		'client_id' => $clientId,
		'client_secret' => $clientSecret,
		'username' => $username,
		'password' => $password . $securityToken
	];
	$curl = curl_init($loginUrl);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
	$response = curl_exec($curl);
	$response = json_decode($response, true);
	curl_close($curl);
	if (!empty($response['access_token'])) {
		$access_token = $response['access_token'];
	} else {
		$access_token = 'failed-to-authenticate';
	}
	$headers = array(
		'Authorization' => 'Bearer ' . $access_token
	);
	$args = array(
		'headers' => $headers
	);
	$api_response = wp_remote_get($url, $args);
	// Check for errors
	if (!is_wp_error($api_response)) {
		$api_body = wp_remote_retrieve_body($api_response);
		$api_data = json_decode($api_body, true);
		return $api_data;
	}
	return null;
}
// Save API calls to files
function save_salesforce_data_to_file($data, $filename)
{
	$full_filename = get_template_directory() . '/api_json/' . $filename;
	$data['last_update_time'] = time();
	file_put_contents($full_filename, json_encode($data));
}
// Function to load data from file
function load_salesforce_data_from_file($filename)
{
	$full_filename = get_template_directory() . '/api_json/' . $filename;
	if (file_exists($full_filename)) {
		$data = file_get_contents($full_filename);
		return json_decode($data, true);
	}
	return null;
}


////We ball (send it now)
//update_partners_from_file();
//Define and schedule event for update_partners_from_file
function schedule_update_partners()
{
	if (!wp_next_scheduled('update_partners_event')) {
		wp_schedule_event(time(), 'partner_refresh_time', 'update_partners_event');
	}
}
add_action('after_setup_theme', 'schedule_update_partners');
function partners_update_interval($schedules)
{
	$schedules['partner_refresh_time'] = array(
		'interval' => 7201, // in seconds
		'display'  => __('Every Hour'),
	);
	return $schedules;
}
add_filter('cron_schedules', 'partners_update_interval');
add_action('update_partners_event', 'update_partners_from_file');

// Updates and creates Partners based on the file
function update_partners_from_file()
{
	error_log('running update_partners_from_file at interval of one 7201s');

	$partners_data = load_salesforce_data_from_file('Salesforce_Partners_00BUM000000arH72AI.json');
	// Check if decoding was successful
	if ($partners_data === null) {
		error_log("Error decoding JSON response");
		return;
	}

	//function to reformat categories for the ACF field
	function formatCatString($string)
	{
		$terms = explode(';', $string);
		$terms = array_map('trim', $terms);
		return implode(', ', $terms);
	}
	//Initialize arrays for the catrgorizations
	$unique_Industries_Partner__c = [];
	$unique_Partner_Solutions__c = [];
	$unique_Type_Partner__c = [];
	$unique_Regions_Partner__c = [];
	$unique_Partner_Integrations__c = [];

	foreach ($partners_data['records'] as $record) {
		$Partner_Name_For_Website__c = $record['fields']['Partner_Name_For_Website__c']['value'];
		$Partner_Icon_For_Website__c = $record['fields']['Partner_Icon_For_Website__c']['value'];
		$Partners_URL_Link__c = $record['fields']['Partners_URL_Link__c']['value'];
		$Partner_Info__c = $record['fields']['Partner_Info__c']['value'];
		$Industries_Partner__c = $record['fields']['Industries_Partner__c']['value'];
		$Partner_Solutions__c = $record['fields']['Partner_Solutions__c']['value'];
		$Type_Partner__c = $record['fields']['Type_Partner__c']['value'];
		$Regions_Partner__c = $record['fields']['Regions_Partner__c']['value'];
		$Partner_Integrations__c = $record['fields']['Partner_Integrations__c']['value'];
		//$formatted_Industries_Partner__c = formatCatString($Industries_Partner__c);
		//$formatted_Partner_Solutions__c = formatCatString($Partner_Solutions__c);
		//$formatted_Type_Partner__c = formatCatString($Type_Partner__c);
		//$formatted_Regions_Partner__c = formatCatString($Regions_Partner__c);
		//$formatted_Partner_Integrations__c = formatCatString($Partner_Integrations__c);
		//$Name = $record['fields']['Name']['value'];
		//$Aras_Website__c = $record['fields']['Aras_Website__c']['value'];
		$Partner_Name_For_Website_Japan__c = $record['fields']['Partner_Name_For_Website_Japan__c']['value'];
		$Partner_Info_Japan__c = $record['fields']['Partner_Info_Japan__c']['value'];

		// Split and remove duplicates for each filter list
		$terms_Industries = explode(';', $Industries_Partner__c);
		$unique_Industries_Partner__c = array_merge($unique_Industries_Partner__c, array_unique($terms_Industries));
		$terms_Solutions = explode(';', $Partner_Solutions__c);
		$unique_Partner_Solutions__c = array_merge($unique_Partner_Solutions__c, array_unique($terms_Solutions));
		$terms_Type = explode(';', $Type_Partner__c);
		$unique_Type_Partner__c = array_merge($unique_Type_Partner__c, array_unique($terms_Type));
		$terms_Regions = explode(';', $Regions_Partner__c);
		$unique_Regions_Partner__c = array_merge($unique_Regions_Partner__c, array_unique($terms_Regions));
		$terms_Integrations = explode(';', $Partner_Integrations__c);
		$unique_Partner_Integrations__c = array_merge($unique_Partner_Integrations__c, array_unique($terms_Integrations));

		//If data exists, update it. If not, make new data.
		if ($Partner_Icon_For_Website__c != '') {
			$existing_post = get_page_by_title($Partner_Name_For_Website__c, OBJECT, 'partners');
			if ($existing_post) {
				// Update ACF fields with array data
				update_field('partner_icon_for_website__c', $Partner_Icon_For_Website__c, $existing_post->ID);
				update_field('partners_url_link__c', $Partners_URL_Link__c, $existing_post->ID);
				update_field('partner_info__c', $Partner_Info__c, $existing_post->ID);
				update_field('industries_partner__c', $Industries_Partner__c, $existing_post->ID);
				update_field('partner_solutions__c', $Partner_Solutions__c, $existing_post->ID);
				update_field('type_partner__c', $Type_Partner__c, $existing_post->ID);
				update_field('regions_partner__c', $Regions_Partner__c, $existing_post->ID);
				update_field('partner_integrations__c', $Partner_Integrations__c, $existing_post->ID);

				update_field('partner_name_for_website_japan__c', $Partner_Name_For_Website_Japan__c, $existing_post->ID);
				update_field('partner_info_japan__c', $Partner_Info_Japan__c, $existing_post->ID);
			} else {
				//New partner means new post
				$post_args = array(
					'post_title' => $Partner_Name_For_Website__c,
					'post_type' => 'partners',
					'post_status' => 'publish',
				);
				$new_post_id = wp_insert_post($post_args);
				// Populate ACF fields with array data
				update_field('partner_icon_for_website__c', $Partner_Icon_For_Website__c, $new_post_id);
				update_field('partners_url_link__c', $Partners_URL_Link__c, $new_post_id);
				update_field('partner_info__c', $Partner_Info__c, $new_post_id);
				update_field('industries_partner__c', $Industries_Partner__c, $new_post_id);
				update_field('partner_solutions__c', $Partner_Solutions__c, $new_post_id);
				update_field('type_partner__c', $Type_Partner__c, $new_post_id);
				update_field('regions_partner__c', $Regions_Partner__c, $new_post_id);
				update_field('partner_integrations__c', $Partner_Integrations__c, $new_post_id);

				update_field('partner_name_for_website_japan__c', $Partner_Name_For_Website_Japan__c, $new_post_id);
				update_field('partner_info_japan__c', $Partner_Info_Japan__c, $new_post_id);
			}
		}
	}
	// Category arrays - remove duplicates again after merging all arrays
	$unique_Industries_Partner__c = array_unique($unique_Industries_Partner__c);
	$unique_Partner_Solutions__c = array_unique($unique_Partner_Solutions__c);
	$unique_Type_Partner__c = array_unique($unique_Type_Partner__c);
	$unique_Regions_Partner__c = array_unique($unique_Regions_Partner__c);
	$unique_Partner_Integrations__c = array_unique($unique_Partner_Integrations__c);
	// Now combine all the arrays into one mega-array
	$merged_partner_cat_array = array(
		'Industries_Partner__c' => $unique_Industries_Partner__c,
		'Partner_Solutions__c' => $unique_Partner_Solutions__c,
		'Type_Partner__c' => $unique_Type_Partner__c,
		'Regions_Partner__c' => $unique_Regions_Partner__c,
		'Partner_Integrations__c' => $unique_Partner_Integrations__c
	);
	// And now save the mega-array as a file to be accessed for the filters
	$api_folder = get_template_directory() . '/api_json/';
	file_put_contents($api_folder . 'Partner_Categories.json', json_encode($merged_partner_cat_array));


	// After processing array, delete 'partners' posts that don't have a matching item in the api array
	$all_partners = get_posts(array('post_type' => 'partners', 'posts_per_page' => -1));
	foreach ($all_partners as $partner) {
		$partner_name = $partner->post_title;
		$found = false;

		// Loop through each item in the array to check for a matching partner name
		foreach ($partners_data['records'] as $record) {
			if ($record['fields']['Partner_Name_For_Website__c']['value'] == $partner_name) {
				$found = true;
				break; // No need to continue looping once a match is found
			}
		}
		// If no matching partner name is found, delete the post
		if (!$found) {
			wp_delete_post($partner->ID, false);
		}
	}
}










////We ball (send it now)
//update_academic_users_from_file();
//Define and schedule event for update_academic_users_from_file
function schedule_update_academic_users()
{
	if (!wp_next_scheduled('update_academic_users_event')) {
		wp_schedule_event(time(), 'academic_users_refresh_time', 'update_academic_users_event');
	}
}
add_action('after_setup_theme', 'schedule_update_academic_users');
function academic_users_update_interval($schedules)
{
	$schedules['academic_users_refresh_time'] = array(
		'interval' => 7201, // in seconds
		'display'  => __('Every Hour'),
	);
	return $schedules;
}
add_filter('cron_schedules', 'academic_users_update_interval');
add_action('update_academic_users_event', 'update_academic_users_from_file');

// Updates and creates Partners based on the file
function update_academic_users_from_file()
{

	error_log('running update_academic_users_from_file at interval of one 7201s');

	$academic_users_data = load_salesforce_data_from_file('Salesforce_Academic_Users_00BUM000000bkYX2AY.json');
	// Check if decoding was successful
	if ($academic_users_data === null) {
		error_log("Error decoding JSON response");
		return;
	}
	foreach ($academic_users_data['records'] as $record) {
		$Name = $record['fields']['Name']['value'];
		$Aras_Website__c = $record['fields']['Aras_Website__c']['value'];
		$Academic_Logo__c = $record['fields']['Academic_Logo__c']['value'];
		$Academic_Website_Link__c = $record['fields']['Academic_Website_Link__c']['value'];
		$Academic_Description_for_Website__c = $record['fields']['Academic_Description_for_Website__c']['value'];

		if ($Aras_Website__c == true) {
			//If data exists, update it. If not, make new data.
			$existing_post = get_page_by_title($Name, OBJECT, 'sf-academic-users');
			if ($existing_post) {
				// Updte ACF fields with array data
				update_field('Academic_Logo__c', $Academic_Logo__c, $existing_post->ID);
				update_field('Academic_Website_Link__c', $Academic_Website_Link__c, $existing_post->ID);
				update_field('Academic_Description_for_Website__c', $Academic_Description_for_Website__c, $existing_post->ID);
			} else {
				//New partner means new post
				$post_args = array(
					'post_title' => $Name,
					'post_type' => 'sf-academic-users',
					'post_status' => 'publish',
				);
				$new_post_id = wp_insert_post($post_args);
				// Populate ACF fields with array data
				update_field('Academic_Logo__c', $Academic_Logo__c, $new_post_id);
				update_field('Academic_Website_Link__c', $Academic_Website_Link__c, $new_post_id);
				update_field('Academic_Description_for_Website__c', $Academic_Description_for_Website__c, $new_post_id);
			}
		}
	}
	// After processing array, delete 'sf-academic-users' posts that don't have a matching item in the api array
	$all_academic_users = get_posts(array('post_type' => 'sf-academic-users', 'posts_per_page' => -1));
	foreach ($all_academic_users as $academic_user) {
		$academic_user_name = $academic_user->post_title;
		$found = false;

		// Loop through each item in the array to check for a matching academic_user name
		foreach ($academic_users_data['records'] as $record) {
			$Name = $record['fields']['Name']['value'];
			if ($Name == $academic_user_name) {
				$found = true;
				break; // No need to continue looping once a match is found
			}
		}
		// If no matching partner name is found, delete the post
		if (!$found) {
			wp_delete_post($academic_user->ID, false);
		}
	}
}












//function clear_scheduled_events()
//{
//	$scheduled_hooks = array(
//		'partner_refresh_time',
//		'salesforce_refresh_time',
//		'academic_users_refresh_time',
//	);
//	foreach ($scheduled_hooks as $hook) {
//		wp_clear_scheduled_hook($hook);
//	}
//}
//clear_scheduled_events();
//
//function clear_all_scheduled_events()
//{
//	$scheduled_hooks = _get_cron_array();
//	$event_hooks = array_keys($scheduled_hooks);
//	foreach ($event_hooks as $hook) {
//		wp_clear_scheduled_hook($hook);
//	}
//}


// Hook into the post update and publish actions
add_action('save_post', 'update_salesforce_campaign_on_resource_publish');
function update_salesforce_campaign_on_resource_publish($post_id)
{
	if (get_field('create_salesforce_campaign', $post_id)) {
		// Check if the field 'salesforce_campaign' exists and is empty
		$salesforce_campaign = get_field('salesforce_campaign', $post_id);
		if (!$salesforce_campaign) {
			$title_of_page = get_the_title($post_id);
			$url_of_page = get_permalink($post_id);
			$language = isset($_COOKIE['wp-wpml_current_language']) ? $_COOKIE['wp-wpml_current_language'] : '';
			$format_terms = wp_get_post_terms($post_id, 'format');
			$format_resource_type = '';
			if (!empty($format_terms) && !is_wp_error($format_terms) && isset($format_terms[0])) {
				$format_resource_type = get_field('salesforce_campaign_resource_type', $format_terms[0]);
			}


			// Perform API call to Salesforce to create a new campaign
			$new_campaign_id = create_new_salesforce_campaign($title_of_page, $url_of_page, $language, $format_resource_type);
			// Update the 'salesforce_campaign' field with the new campaign ID
			update_field('salesforce_campaign', $new_campaign_id, $post_id);
		}
	}
}

function create_new_salesforce_campaign($title_of_page, $url_of_page, $language, $format_resource_type)
{
	$clientId = '3MVG9p1Q1BCe9GmDMzV_B995ILaTLlcH62_4FqAiYF2ABC0hVjOWWF1i8ix5_QdB0rb7w1c3ufX0sCzvgGxVV';
	$clientSecret = '9FF1DFB9ED3DFC62E5528995CB23C6F4F4101D2C654586F58E9C151392658679';
	$username = 'markopsintegrations@aras.com';
	$password = 'Pardotaras4!';
	$securityToken = 'EGeQjvdr1EatbBxECPtNG40mW';
	$loginUrl = 'https://login.salesforce.com/services/oauth2/token';
	$params = [
		'grant_type' => 'password',
		'client_id' => $clientId,
		'client_secret' => $clientSecret,
		'username' => $username,
		'password' => $password . $securityToken
	];
	$curl = curl_init($loginUrl);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
	$response = curl_exec($curl);
	$response = json_decode($response, true);
	curl_close($curl);
	if (!empty($response['access_token'])) {
		$access_token = $response['access_token'];
	} else {
		$access_token = 'failed-to-authenticate';
	}

	$sendName = "";
	if (strlen($title_of_page) > 40) {
		$sendName = substr($title_of_page, 0, 80);
	} else {
		$sendName = $title_of_page;
	}

	$campaignFriendlyName = "";
	$preConvertedFriendlyName = str_replace("-", " ", $title_of_page);
	if (strlen($preConvertedFriendlyName) > 40) {
		$campaignFriendlyName = substr($preConvertedFriendlyName, 0, 40);
	} else {
		$campaignFriendlyName = $preConvertedFriendlyName;
	}

	if ($language ==  'en') {
		$sendRegion = 'NA';
		$sendLanguage = 'EN';
	} elseif ($language ==  'fr-fr') {
		$sendRegion = 'EMEA';
		$sendLanguage = 'FR';
	} elseif ($language ==  'de-de') {
		$sendRegion = 'EMEA';
		$sendLanguage = 'DE';
	} elseif ($language ==  'ja-jp') {
		$sendRegion = 'AJP';
		$sendLanguage = 'JP';
	} else {
		$sendRegion = 'NA';
		$sendLanguage = 'EN';
	}

	$sendPortfolio = "Branding/PR";
	$sendChannel = "Web Asset";
	if (strpos($url_of_page, '/events/') !== false) {
		$sendPortfolio = "Aras Field Events";
		$sendChannel = 'Webinar';
	}

	$resourceType = "";
	if ($format_resource_type ==  'PDF') {
		$resourceType = "PDF";
	} elseif ($format_resource_type ==  'Infographic') {
		$resourceType = "Infographic";
	} elseif ($format_resource_type ==  'Video') {
		$resourceType = "video";
	}

	$url = 'https://aras1.my.salesforce.com/services/data/v60.0/sobjects/Campaign/';
	$query_params = array();
	$body_params = array(
		'Name' => $sendName,
		'Campaign_Friendly_Name__c' => $campaignFriendlyName,
		//'Asset_Name__c' => $title_of_page,
		'StartDate' => date('Y-m-d'),
		'Asset_Link__c'	=> $url_of_page,
		'Language__c' => $sendLanguage,
		'IsActive' => 'true',
		'RecordTypeId' => '0125f000000zEQbAAM',
		'Type' => $sendRegion,
		'Default_URL_Campaign__c' => 'true',
		'Portfolio__c' => $sendPortfolio,
		'Channel__c' => $sendChannel,
	);
	if (!empty($resourceType)) {
		$body_params['Asset_Type__c'] = $resourceType;
	}

	$body = wp_json_encode($body_params);
	$url = add_query_arg($query_params, $url);
	$headers = array(
		'Authorization' => 'Bearer ' . $access_token,
		'Content-Type' => 'application/json'
	);
	$args = array(
		'headers' => $headers,
		'body' => $body,
	);
	$response = wp_remote_post($url, $args);
	if (is_wp_error($response)) {
		$error_message = $response->get_error_message();
		return false;
	}
	$response_data = json_decode(wp_remote_retrieve_body($response), true);
	if (isset($response_data['success']) && $response_data['success'] === true) {
		return $response_data['id'];
	} else {
		return false;
	}
}
