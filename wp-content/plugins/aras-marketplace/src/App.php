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
	 * The constructor
	 */
	private function __construct() {
		// lets set up our services
		$this->acfService = new Service\ACF();
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