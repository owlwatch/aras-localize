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
	 * The constructor
	 */
	private function __construct() {
		
		// lets set up our core services
		$this->acfService = new Service\ACF();
		$this->acpService = new Service\ACP();
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