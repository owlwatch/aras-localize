<?php
namespace Aras\Verint\Service;

use function Aras\Verint\app;
use WP_REST_Request;

class Rest
{
	
	/**
	 * The constructor
	 */
	public function __construct() {
		// register our rest endpoints
		add_action( 'rest_api_init', [$this, 'registerRoutes' ] );
	}

	/**
	 * Register the routes
	 */
	public function registerRoutes() {
		register_rest_route( 'aras-verint/v1', '/api/(?P<path>.+)', array(
			'methods' => array( 'POST', 'GET', 'PUT', 'DELETE' ),
			'callback' => array( $this, 'api' ),
			'permission_callback' => function () {
				return is_super_admin();
			},
			'args' => array(
				'path' => array(
					'required' => true,
					'type' => 'string',
				),
			),
		));
		
		register_rest_route( 'aras-verint/v1', '/forum-urls', array(
			'methods' => array('GET'),
			'callback' => array( $this, 'forumUrls' ),
			'permission_callback' => function () {
				return is_super_admin();
			}
		));

		register_rest_route( 'aras-verint/v1', '/forum-content', array(
			'methods' => array('GET'),
			'callback' => array( $this, 'forumContent' ),
			'permission_callback' => function () {
				// return is_super_admin();
				return true;
			}
		));

		register_rest_route( 'aras-verint/v1', '/articles', array(
			'methods' => array('GET'),
			'callback' => array( $this, 'articles' ),
			'permission_callback' => function () {
				// return is_super_admin();
				return true;
			}
		));

		register_rest_route( 'aras-verint/v1', '/media-objects', array(
			'methods' => array('GET'),
			'callback' => function(){
				return app()->mediaCleanupService->getObjects();
			},
			'permission_callback' => function () {
				return is_super_admin();
			}
		));

		register_rest_route( 'aras-verint/v1', '/blog-export', array(
			'methods' => array('GET'),
			'callback' => function(){
				return $this->blogExport();
			},
			'permission_callback' => function () {
				// return is_super_admin();
				return true;
			}
		));

		register_rest_route( 'aras-verint/v1', '/banned-user-list', array(
			'methods' => array('GET'),
			'callback' => array( $this, 'bannedUserList' ),
			'permission_callback' => function () {
				// return is_super_admin();
				return true;
			}
		));

		register_rest_route( 'aras-verint/v1', '/delete-users', array(
			'methods' => array('GET'),
			'callback' => array( $this, 'deleteUsers' ),
			'args' => array(
				'dataset' => array(
					'required' => false,
					'type' => 'string',
				),
				'test' => array(
					'required' => false,
					'default' => true,
					'type' => 'boolean',
				),
				'all' => array(
					'required' => false,
					'type' => 'boolean',
				),
			),
			'permission_callback' => function () {
				// return is_super_admin();
				return true;
			}
		));

	}

	public function api( $request ) {
		$path = $request->get_param( 'path' );
		$body = $request->get_body() ?: [];
		$headers = $request->get_headers();
		$method = $request->get_method();

		// can we get the query parameters too?
		$query = $request->get_query_params();

		// lets combine the query parameters with the body
		if( $query ){
			$body = array_merge( $body , $query );
		}

		// get the api service
		$apiService = app()->apiService;

		// call the api service
		return $apiService->$method( $path, $body ?: [] );
	}

	public function forumUrls()
	{
		header('Content-Type: application/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=forum-urls.csv');

		// get the handle for output
		$output = fopen('php://output', 'w');
		// $urls = [];
		// lets get the forums
		$forumsPage = 0;
		do {
			error_log( 'forumsPage: ' . $forumsPage );
			$forumsResponse = app()->apiService->get('forums', ['PageIndex'=> $forumsPage++, 'PageSize' =>100]);
			foreach( $forumsResponse->Forums as $forum ){
				// $urls[] = $forum->Url;
				fputcsv($output, [$forum->Url], ",", '"', '\\');

				// we also want all the threads
				$threadsPage = 0;
				do {
					error_log( 'Forum '.$forum->Name .' threadsPage: ' . $threadsPage );
					$threadsResponse = app()->apiService->get('forums/'.$forum->Id.'/threads', ['PageIndex'=> $threadsPage++, 'PageSize' =>100]);
					foreach( $threadsResponse->Threads as $thread ){
						fputcsv($output, [$thread->Url], ",", '"', '\\');
					}
				}while( ($threadsResponse->PageIndex+1) * $threadsResponse->PageSize < $threadsResponse->TotalCount );
			}
		}while( ($forumsResponse->PageIndex+1) * $forumsResponse->PageSize < $forumsResponse->TotalCount );


		fclose($output);
		exit;

	}

	public function forumContent()
	{
		header('Content-Type: application/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=forum-content.csv');

		$forumsPage = 0;
		$output = fopen('php://output', 'w');
		$count = 0;
		
		do {
			error_log( 'forumsPage: ' . $forumsPage );
			$forumsResponse = app()->apiService->get('forums', ['PageIndex'=> $forumsPage++, 'PageSize' =>100]);
			foreach( $forumsResponse->Forums as $forum ){
				$data = $this->_sanitize_forum($forum);
				// output headers if we haven't yet
				if( $count == 0 ){
					$headers = array_keys($data);
					fputcsv($output, $headers, ",", '"', '\\');
				}
				fputcsv($output, $data, ",", '"', '\\');
				$threads = [];
				// we also want all the threads
				$threadsPage = 0;
				$count++;
				do {
					error_log( 'Forum '.$forum->Name .' threadsPage: ' . $threadsPage );
					$threadsResponse = app()->apiService->get('forums/'.$forum->Id.'/threads', ['PageIndex'=> $threadsPage++, 'PageSize' =>100]);
					foreach( $threadsResponse->Threads as $thread ){
						fputcsv($output, $this->_sanitize_thread($thread), ",", '"', '\\');
						$count++;
					}
					
				}while( ($threadsResponse->PageIndex+1) * $threadsResponse->PageSize < $threadsResponse->TotalCount );
			}
			
		}while( ($forumsResponse->PageIndex+1) * $forumsResponse->PageSize < $forumsResponse->TotalCount );


		fclose($output);
		exit;

	}

	public function blogExport()
	{
		header('Content-Type: application/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=blog-export.csv');

		$output = fopen('php://output', 'w');
		$headers = ['Id', 'Title', 'Url', 'Body', 'Excerpt', 'Date', 'Featured Image', 'All Images', 'Tags', 'Categories', 'Author Username', 'Author First Name', 'Author Last Name', 'Author Email'];
		fputcsv($output, $headers, ",", '"', '\\');

		// we are going to get these from wordpress posts
		// that are tagged with "Aras Labs" or in the category "Aras Labs"

		$args = [
			'post_type' => 'post',
			'posts_per_page' => -1,
			'tax_query' => [
				'relation' => 'OR',
				[
					'taxonomy' => 'post_tag',
					'field' => 'slug',
					'terms' => ['aras-labs']
				],
				[
					'taxonomy' => 'category',
					'field' => 'slug',
					'terms' => ['aras-labs']
				]
			]
		];
		$posts = get_posts( $args );
		foreach( $posts as $post ){
			$tags = get_the_tags( $post->ID );
			if( $tags ) $tags = array_map( function($t) { return $t->name; }, $tags );
			else $tags = [];
			$categories = get_the_category( $post->ID );
			if( $categories ) $categories = array_map( function($c) { return $c->name; }, $categories );
			else $categories = [];
			$author = get_userdata( $post->post_author );

			// find all the images in the post content
			// and copy them to a temporary directory that
			// we will zip up later
			// the temporary directory will be wp-content/uploads/aras-labs-images/

			$regex = '/<img[^>]+src="([^">]+\/(wp-content\/uploads\/[^">]+))"/i';
			$all_matches = preg_match_all($regex, $post->post_content, $matches);
			$images = $all_matches ? $matches[2] : [];
			if( has_post_thumbnail( $post->ID ) ){
				$url = get_the_post_thumbnail_url( $post->ID, 'full' );
				if( preg_match( '/wp-content\/uploads\/.+$/', $url, $matches ) ){
					$images[] = $matches[0];
				}
			}
			// copy the images to a temporary directory
			$tempDir = wp_upload_dir()['basedir'] . '/aras-labs-images/';
			if( !file_exists($tempDir) ){
				mkdir($tempDir, 0755, true);
			}
			foreach( $images as $image ){
				// copy the image to the temporary directory
				$imagePath = ABSPATH . $image;
				if( file_exists($imagePath) ){
					// we should retain the directory structure (which can be neste)
					// so we will create the directory structure in the temp dir
					$dir = dirname($image);
					$tempImageDir = $tempDir . str_replace(ABSPATH . 'wp-content/uploads/', '', $dir);
					if( !file_exists($tempImageDir) ){
						wp_mkdir_p($tempImageDir);
					}
					$tempImagePath = $tempImageDir .'/'. basename($image);
					copy($imagePath, $tempImagePath);
				}
			}

			$data = [
				$post->ID,
				$post->post_title,
				get_permalink( $post->ID ),
				$post->post_content,
				$post->post_excerpt,
				$post->post_date,
				get_the_post_thumbnail_url( $post->ID, 'full' ),
				implode(' | ', $images),
				implode(' | ', $tags),
				implode(' | ', $categories),
				$author->user_login,
				$author->first_name,
				$author->last_name,
				$author->user_email
			];
			fputcsv($output, $data, ",", '"', '\\');
		}
		exit;
	}

	public function articles()
	{
		header('Content-Type: application/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=articles.csv');

		$forumsPage = 0;
		$output = fopen('php://output', 'w');
		$count = 0;
		
		do {
			error_log( 'forumsPage: ' . $forumsPage );
			$forumsResponse = app()->apiService->get('forums', ['PageIndex'=> $forumsPage++, 'PageSize' =>100]);
			foreach( $forumsResponse->Forums as $forum ){
				fputcsv($output, $this->_sanitize_forum($forum), ",", '"', '\\');
				$threads = [];
				// we also want all the threads
				$threadsPage = 0;
				do {
					error_log( 'Forum '.$forum->Name .' threadsPage: ' . $threadsPage );
					$threadsResponse = app()->apiService->get('forums/'.$forum->Id.'/threads', ['PageIndex'=> $threadsPage++, 'PageSize' =>100]);
					foreach( $threadsResponse->Threads as $thread ){
						fputcsv($output, $this->_sanitize_thread($thread), ",", '"', '\\');
					}
					
				}while( ($threadsResponse->PageIndex+1) * $threadsResponse->PageSize < $threadsResponse->TotalCount );
			}
			
		}while( ($forumsResponse->PageIndex+1) * $forumsResponse->PageSize < $forumsResponse->TotalCount );
	}

	public function bannedUserList()
	{
		header('Content-Type: application/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=banned-and-disapproved-users.csv');

		$usersPage = 0;
		$output = fopen('php://output', 'w');
		$count = 0;
		
		do {
			error_log( 'forumsPage: ' . $usersPage );
			$usersResponse = app()->apiService->get('users', 
				[
					'AccountStatus' => 'Banned,Disapproved',
					'SortBy' => 'Posts',
					'SortOrder' => 'Descending',
					'PageIndex'=> $usersPage++,
					'PageSize' =>100
				]
			);
			foreach( $usersResponse->Users as $user ){
				// fputcsv($output, $this->_sanitize_forum($forum), ",", '"', '\\');
				$clean = [];
				$clean['Id'] = $user->Id;
				$clean['ContentId'] = $user->ContentId;
				$clean['Username'] = $user->Username;
				$clean['DisplayName'] = $user->DisplayName;
				$clean['Email'] = $user->PrivateEmail;
				$clean['Posts'] = $user->TotalPosts;
				$clean['Status'] = $user->AccountStatus;
				$clean['Date'] = $user->JoinDate;
				fputcsv($output, $clean, ",", '"', '\\');
			}
			
		}while( ($usersResponse->PageIndex+1) * $usersResponse->PageSize < $usersResponse->TotalCount );


		fclose($output);
		exit;

	}

	public function deleteUsers( WP_REST_Request $request )
	{
		$dataset = $request->get_param('dataset');
		$test = $request->get_param('test');
		$all = $request->get_param('all');

		$log = [];

		if( !$dataset ){
			wp_send_json_error( 'No dataset provided' );
			exit;
		}

		// action is based on the dataset name
		$action = $dataset == 'delete-user-and-content' ? 'delete' : 'merge';

		// lets get the users from the dataset
		// the dataset is a csv in the 'data' directory of the plugin
		$csvFile = ARAS_VERINT_PATH . '/data/' . $dataset . '.csv';
		if( !file_exists($csvFile) ){
			wp_send_json_error( 'Dataset not found: ' . $dataset );
			exit;
		}
		$users = [];
		if( ($handle = fopen($csvFile, 'r')) !== false ) {
			$header = fgetcsv($handle, 1000, ',');
			while( ($data = fgetcsv($handle, 1000, ',')) !== false ) {
				$user = array_combine($header, $data);
				$users[] = $user;
			}
			fclose($handle);
		}

		foreach( $users as $user ){
			$id = $user['ID'];
			$content_id = $user['Content ID'];
			$username = $user['Username'];
			$args = [
				'Username' => $username
			];
			if( $action == 'merge' ){
				$args['MergeToReassignedUser'] = true;
			}
			else {
				$args['DeleteAllContent'] = true;
			}
			$response = null;
			if( !$test ){
				try {
					$response = app()->apiService->delete('users/'.$id, $args);
				}catch( \Exception $e ){
					$response = $e->getMessage();
				}
			}

			$log[] = [
				'method' => "DELETE",
				'endpoint' => "https://www.aras.com/community/api.ashx/v2/users/{$id}.json",
				'args' => $args,
				'response' => $response
			];

			if( !$all ){
				break;
			}
		}

		return [
			'success' => true,
			'log' => $log,
			'test' => $test,
			'all' => $all,
			'dataset' => $dataset
		];

	}

	public function _sanitize_forum( $forum )
	{
		$clean = [];
		$clean['Id'] = $forum->Id;
		$clean['ContentId'] = $forum->ContentId;
		$clean['Title'] = $forum->Title;
		$clean['Url'] = $forum->Url;
		$clean['Body'] = $forum->description;
		$clean['ContentType'] = 'forum';
		$clean['Date'] = $forum->DateCreated;
		$clean['LatestPostDate'] = $forum->LatestPostDate;
		$clean['Tags'] = '';
		return $clean;
	}

	public function _sanitize_thread( $thread )
	{
		$clean = [];
		$clean['Id'] = $thread->Id;
		$clean['ContentId'] = $thread->ContentId;
		$clean['Title'] = $thread->Subject;
		$clean['Url'] = $thread->Url;
		$clean['Body'] = $thread->Body;
		$clean['ContentType'] = 'forum-thread';
		$clean['Date'] = $thread->Date;
		$clean['LatestPostDate'] = $thread->LatestPostDate;
		$clean['Tags'] = implode(' | ', array_map( function($t) { return $t->Value; }, $thread->Tags ) );
		return $clean;
	}

}