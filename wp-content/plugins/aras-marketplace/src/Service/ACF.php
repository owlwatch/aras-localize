<?php

namespace Aras\Marketplace\Service;

class ACF {

	/**
	 * The constructor
	 */
	public function __construct() {

		// set up our configuration
		// add_filter( 'acf/settings/save_json', array( $this, 'setSaveJson' ), 20 );
		add_filter( 'acf/json/save_paths', array( $this, 'setSaveJsonPaths'), 20, 2 );
		add_filter( 'acf/settings/load_json', array( $this, 'setLoadJson' ), 10 );
	}

	/**
	 * Set the load json path
	 */
	public function setLoadJson( $paths = []) {
		$paths[] = ARAS_MARKETPLACE_PATH . '/config/acf';
		return $paths;
	}

	/**
	 * Set the save json path
	 */
	public function setSaveJsonPaths( $paths, $post ) {
		// we only want to save if this matches a certain field group
		// check the post title for Swoogo: prefix
		if ( preg_match( '/marketplace/i', $post['title'] ) ) {
			$paths = [ARAS_MARKETPLACE_PATH . '/config/acf'];
		}
		return $paths;
	}



}