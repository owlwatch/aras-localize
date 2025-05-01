<?php
/**
 * @package Aras\Verint
 */
namespace Aras\Verint;

class App {

	/**
	 * The instance of the class
	 *
	 * @var App
	 */
	private static $instance;

	/**
	 * acfService
	 * @var Aras\Verint\Service\ACF
	 */
	public $acfService;

	/**
	 * apiService
	 * @var Aras\Verint\Service\VerintApi
	 */
	public $apiService;

	/**
	 * The Reset API Service
	 * @var Aras\Verint\Service\Rest
	 */
	public $restService;

	/**
	 * The Media Cleanup Service
	 * @var Aras\Verint\Service\MediaCleanup
	 */
	public $mediaCleanupService;

	/**
	 * Agenda UI
	 */
	public $ui;

	/**
	 * Admin Service
	 */
	public $adminService;

	/**
	 * The constructor
	 */
	private function __construct() {
		// lets set up our services
		$this->acfService = new Service\ACF();
		$this->adminService = new Service\Admin();

		$this->restService = new Service\Rest();

		$this->apiService = new Service\VerintApi();
		$this->mediaCleanupService = new Service\MediaCleanup();
		
		$this->ui = new Service\ViteService(
			ARAS_SWOOGO_PATH.'/ui/dist',
			ARAS_SWOOGO_URL.'ui/dist/',
			'http://localhost:6238'
		);

		add_action('init', [$this, 'init']);
		// $this->init();
	}

	/**
	 * Get the instance of the class
	 *
	 * @return App
	 */
	public static function getInstance() {

		if ( self::$instance == null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function init()
	{
		if( isset($_GET['test-verint2']) ){
			$this->testConnection();
		}
	}

	public function testConnection()
	{
		try {
			$response = $this->apiService->get('info');
			header('Content-Type: application/json; charset=utf-8');
			print_r( json_encode($response) );
		}
		catch( \Exception $e ){
			wp_die( $e->getMessage() );
		}
		exit;
	}

}