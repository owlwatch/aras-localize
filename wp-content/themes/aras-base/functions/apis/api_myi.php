<?php
// Schedule the hourly event
add_action('init', 'hourly_api_call_event');
function hourly_api_call_event()
{
	if (!wp_next_scheduled('update_myi_data_hourly_event')) {
		wp_schedule_event(time(), 'hourly', 'update_myi_data_hourly_event');
	}
}

add_action('init', function(){
	if( isset($_REQUEST['run_myi']) ) {
		update_myi_data_hourly( true );
	}
});
////We ball (send it now)
//update_myi_data_hourly();

// Hook update function to hourly event
add_action('update_myi_data_hourly_event', 'update_myi_data_hourly');

// Function to update roadmap data hourly
function update_myi_data_hourly( $debug = false )
{
	error_log('update_myi_data_hourly called at ' . date('Y-m-d H:i:s'));
	if( $debug ) {
		header('Content-Type: text/plain; charset=UTF-8');
		echo "update_myi_data_hourly called at " . date('Y-m-d H:i:s') . "\n";
	}
	// Roadmap data
	$roadmap_url = 'https://myinnovator.com/server/odata/method.MyI_Public_Roadmap_Query';
	$roadmap_data = fetch_data_from_myi_api($roadmap_url);
	if( $debug ) {
		echo "Fetching roadmap data from: $roadmap_url\n";
		print_r($roadmap_data);
	}
	if ($roadmap_data) {
		save_data_to_file($roadmap_data, 'MyI_Public_Roadmap_Query.json');
		//	echo "Roadmap data updated at " . date('Y-m-d H:i:s') . "\n";
	}

	// Roadmap release data
	$roadmap_release_url = 'https://myinnovator.com/server/odata/method.MyI_Public_Roadmap_Release_Query';
	$roadmap_release_data = fetch_data_from_myi_api($roadmap_release_url);
	if ($roadmap_release_data) {
		save_data_to_file($roadmap_release_data, 'MyI_Public_Roadmap_Release_Query.json');
		//echo "Roadmap release data updated at " . date('Y-m-d H:i:s') . "\n";
	}

	// Training data
	$training_url = 'https://myinnovator.com/server/odata/method.Web Get Class Data';
	$training_data = fetch_data_from_myi_api($training_url);
	if ($training_data) {
		save_data_to_file($training_data, 'Web_Get_Class_Data.json');
		//echo "Training data updated at " . date('Y-m-d H:i:s') . "\n";

		foreach ($training_data['Item'] as $record) :
			if (isset($record['Relationships']['Item'])) :
				$relationships_item = $record['Relationships']['Item'];
				if (is_array($relationships_item)) :
					foreach ($relationships_item as $relationship) :
						if (isset($relationship['state']) && $relationship['state'] == 'Planned') :
							$tempArray = [
								'pdf_id' => isset($relationship['_class_pdf']['Item']['id']['#text']) ? $relationship['_class_pdf']['Item']['id']['#text'] : '',
							];
							$training_PDF_IDs[] = $tempArray;
						endif;
					endforeach;
				endif;
			endif;
		endforeach;
		foreach ($training_data['Item'] as $record) :
			if (array_key_exists('Relationships', $record)) {
				if (isset($record['Relationships']['Item']['state']) == 'Planned') {
					$tempArray = [
						'pdf_id' => isset($record['Relationships']['Item']['_class_pdf']['Item']['id']['#text']) ? $record['Relationships']['Item']['_class_pdf']['Item']['id']['#text'] : '',
					];
					$training_PDF_IDs[] = $tempArray;
				}
			}
		endforeach;

		// Define the directory to store PDFs
		$pdf_directory = wp_upload_dir()['basedir'] . '/training/';
		// Delete existing PDFs
		$existing_pdfs = glob($pdf_directory . '*.pdf');
		foreach ($existing_pdfs as $pdf) {
			unlink($pdf);
		}

		// Fetch PDFs for each training class
		if (!empty($training_PDF_IDs)) {
			foreach ($training_PDF_IDs as $pdf_data) {
				$pdf_id = $pdf_data['pdf_id'];
				if ($pdf_id != '') {
					fetch_and_save_pdf($pdf_id);
				}
			}
		}
	}
	if( $debug ){
		exit;
	}
}

function fetch_and_save_pdf($pdf_id)
{
	if ($pdf_id != '') {
		$token_request_data = array(
			'grant_type' => 'password',
			'scope' => 'Innovator',
			'client_id' => 'IOMApp',
			'username' => 'web2lead',
			'password' => '4d87a136d94d89910de45c3af3120c31',
			'database' => 'MyInnovator'
		);
		// Use cURL to obtain access token
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://myinnovator.com/OAuthServer/connect/token');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_request_data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Execute the request to obtain access token
		$token_response = curl_exec($ch);
		curl_close($ch);

		// Decode the JSON response to obtain access token
		$token_response_data = json_decode($token_response, true);

		// Check if access token is obtained
		if (isset($token_response_data['access_token'])) {
			$access_token = $token_response_data['access_token'];


			$pdf_url = "https://myinnovator.com/server/odata/Document('$pdf_id')/viewable_file/\$value";
			// $pdf_url = 'https://myinnovator.com/?StartItem=Document%3A'.$pdf_id;
			$pdf_directory = wp_upload_dir()['basedir'] . '/training/';
			$pdf_file_name = $pdf_id . '.pdf';
			$headers = array(
				'Authorization' => 'Bearer ' . $access_token
			);
			$args = array(
				'headers' => $headers
			);

			$pdf_response = wp_remote_get($pdf_url, $args);

			// Check for errors
			if (!is_wp_error($pdf_response)) {
				$pdf_file_path = $pdf_directory . $pdf_file_name;
				$saved = file_put_contents($pdf_file_path, $pdf_response['body']);

				if ($saved !== false) {
					//	echo "PDF saved successfully at: " . $pdf_file_path;
				} else {
					//	echo "Error saving PDF.";
				}
			}
		}
	}
}



// API CALL
function fetch_data_from_myi_api($url)
{
	$token_request_data = array(
		'grant_type' => 'password',
		'scope' => 'Innovator',
		'client_id' => 'IOMApp',
		'username' => 'web2lead',
		'password' => '4d87a136d94d89910de45c3af3120c31',
		'database' => 'MyInnovator'
	);

	// Use cURL to obtain access token
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://myinnovator.com/OAuthServer/connect/token');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_request_data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	// Execute the request to obtain access token
	$token_response = curl_exec($ch);
	curl_close($ch);

	// Decode the JSON response to obtain access token
	$token_response_data = json_decode($token_response, true);

	// Check if access token is obtained
	if (isset($token_response_data['access_token'])) {
		$access_token = $token_response_data['access_token'];

		$headers = array(
			'Authorization' => 'Bearer ' . $access_token
		);
		$args = array(
			'headers' => $headers
		);
		$api_response = wp_remote_post($url, $args);

		// Check for errors
		if (!is_wp_error($api_response)) {
			$api_body = wp_remote_retrieve_body($api_response);
			$api_data = json_decode($api_body, true);
			return $api_data;
		}
		else {
			error_log('Error fetching data from MyI API: ' . $api_response->get_error_message());
		}
	}
	return null;
}

// Save API calls to files
function save_data_to_file($data, $filename)
{
	if( !is_dir( get_template_directory() . '/api_json/' ) ) {
		mkdir( get_template_directory() . '/api_json/', 0755, true );
	}
	$full_filename = get_template_directory() . '/api_json/' . $filename;
	$data['last_update_time'] = time();
	file_put_contents($full_filename, json_encode($data));
}
// Function to load data from file
function load_data_from_file($filename)
{
	$full_filename = get_template_directory() . '/api_json/' . $filename;
	if (file_exists($full_filename)) {
		$data = file_get_contents($full_filename);
		return json_decode($data, true);
	}
	return null;
}


//Example calls:
//$roadmap_data = load_data_from_file('MyI_Public_Roadmap_Query.json');
//$roadmap_release_data = load_data_from_file('MyI_Public_Roadmap_Release_Query.json');
//$training_data = load_data_from_file('Web_Get_Class_Data.json');

//				// Reset the hourly event
//				function reset_hourly_event()
//				{
//					wp_clear_scheduled_hook('update_myi_data_hourly_event');
//					hourly_api_call_event();
//				}
//				reset_hourly_event();