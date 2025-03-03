<?php
namespace Aras\Marketplace\Service;

class PasswordProtect
{
	/**
	 * If the marketplace is protected or not
	 *
	 * @var boolean
	 */
	private $isProtected = false;

	/**
	 * The password to login with
	 * 
	 * @var string
	 */
	private $password = '';

	public function __construct()
	{
		add_action('init', [$this, 'init']);
	}

	public function init()
	{

		if( is_user_logged_in() ){
			//return;
		}

		$this->isProtected = get_field('marketplace_private', 'option');

		if ($this->isProtected) {
			$this->password = get_field('marketplace_password', 'option');

			// check for the cookie
			if( !empty($_COOKIE[$this->password]) ) {
				$this->isProtected = false;
			}

			// check for the password in the request
			else if (isset($_GET[$this->password])) {
				setcookie( $this->password, '1', time() + 3600, '/');
				$this->isProtected = false;
			}

			
			if( $this->isProtected ){
				add_action('pre_get_posts', [$this, 'protectMarketplace']);
			}
		}
	}

	public function protectMarketplace($query)
	{
		if (is_admin() || ! $query->is_main_query()) {
			return;
		}

		if ($this->isProtected && $this->isProtectableRequest( $query ) ) {
			if (!isset($_COOKIE['marketplace_password']) || $_COOKIE['marketplace_password'] !== $this->password) {
				// simply 404
				$query->set_404();
				status_header(404);
			}
		}
	}

	public function isProtectableRequest( $query=null )
	{
		if( $query === null ){
			global $wp_query;
			$query = $wp_query;
		}
		return (
			is_post_type_archive('mp-solution') ||
			(is_singular() && $query->get('post_type') == 'mp-solution') ||
			is_tax('mp-solution-category') ||
			is_tax('mp-solution-type') || is_tax('mp-contributor')
		);
	}
}