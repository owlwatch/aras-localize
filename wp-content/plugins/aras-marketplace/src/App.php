<?php
/**
 * @package Aras\Marketplace
 */
namespace Aras\Marketplace;

class App {

	/**
	 * The instance of the class
	 *
	 * @var App
	 */
	private static $instance;

	/**
	 * acfService
	 * @var Aras\Marketplace\Service\ACF
	 */
	public $acfService;

	/**
	 * acpService
	 * @var Aras\Marketplace\Service\ACP
	 */
	public $acpService;

	/**
	 * templateService
	 * @var Aras\Marketplace\Service\Template
	 */
	public $templateService;

	/**
	 * ui
	 * @var Aras\Marketplace\Service\ViteService
	 */
	public $ui;

	/**
	 * The constructor
	 */
	private function __construct() {

		$this->ui = new Service\ViteService(
			ARAS_MARKETPLACE_PATH.'/ui/dist',
			ARAS_MARKETPLACE_URL.'ui/dist/',
			'http://localhost:6238'
		);
		
		// lets set up our core services
		$this->acfService = new Service\ACF();
		$this->acpService = new Service\ACP();
		$this->templateService = new Service\Template(
			$this->ui
		);

		
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

}