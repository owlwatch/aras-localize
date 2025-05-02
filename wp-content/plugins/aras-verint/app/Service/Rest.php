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

		register_rest_route( 'aras-verint/v1', '/media-objects', array(
			'methods' => array('GET'),
			'callback' => function(){
				return app()->mediaCleanupService->getObjects();
			},
			'permission_callback' => function () {
				return is_super_admin();
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

}