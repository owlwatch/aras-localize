<?php
/**
 * @package Aras\Competitors
 */
namespace Aras\Competitors;

class App {

	/**
	 * The instance of the class
	 *
	 * @var App
	 */
	private static $instance;

	/**
	 * acfService
	 * @var Aras\Compeitors\Service\ACF
	 */
	public $acfService;

	/**
	 * adminService
	 * @var Aras\Competitors\Service\Admin
	 */
	public $adminService;

	
	/**
	 * The constructor
	 */
	private function __construct() {
		// lets set up our services
		$this->acfService = new Service\ACF();
		$this->adminService = new Service\Admin();
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