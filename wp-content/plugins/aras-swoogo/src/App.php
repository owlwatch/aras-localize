<?php
/**
 * @package Aras\Swoogo
 */
namespace Aras\Swoogo;

class App {

	/**
	 * acfService
	 * @var Aras\Swoogo\Service\ACF
	 */
	public $acfService;

	/**
	 * apiService
	 * @var Aras\Swoogo\Service\SwoogoApi
	 */
	public $apiService;

	/**
	 * syncService
	 * @var Aras\Swoogo\Service\Sync
	 */
	public $syncService;

	/**
	 * The instance of the class
	 *
	 * @var App
	 */
	private static $instance;

	/**
	 * Agenda UI
	 */
	private static $agendaUI;

	/**
	 * Shortcodes
	 * @var Aras\Swoogo\Service\Shortcodes
	 */
	public $shortcodes;

	/**
	 * The constructor
	 */
	private function __construct() {
		// lets set up our services
		$this->acfService = new Service\ACF();

		// get our token and secret
		$key = get_field('swoogo_api_key', 'option');
		$secret = get_field('swoogo_api_secret', 'option');

		if( !$key || !$secret ){
			return;
		}

		$this->apiService = new Service\SwoogoApi( $key, $secret);
		$this->syncService = new Service\Sync( $this->apiService );

		$this->agendaUI = new Service\ViteService(
			ARAS_SWOOGO_PATH.'/ui/swoogo-agenda/dist',
			ARAS_SWOOGO_URL.'ui/swoogo-agenda/dist/',
			'http://localhost:6237'
		);

		$this->shortcodes = new Service\Shortcodes(
			$this->agendaUI
		);

		// allow for tests
		add_action('init', [$this, 'init']);
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
		if( !empty( $_REQUEST['test-swoogo-sync-event'] ) ){
			$event_id = $_REQUEST['test-swoogo-sync-event'];
			$this->syncService->syncEvent( $event_id );
			exit;
		}	
	}

}