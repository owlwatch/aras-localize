<?php
namespace Aras\Verint\Service;

use function Aras\Verint\app;

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

		register_rest_route( 'aras-verint/v1', '/media-objects', array(
			'methods' => array('GET'),
			'callback' => function(){
				return app()->mediaCleanupService->getObjects();
			},
			'permission_callback' => function () {
				return is_super_admin();
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


		fclose($output);
		exit;

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

	public function _sanitize_forum( $forum )
	{
		$clean = [];
		$clean['Id'] = $forum->Id;
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