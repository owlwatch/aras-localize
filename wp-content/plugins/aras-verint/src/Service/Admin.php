<?php
namespace Aras\Verint\Service;

use function Aras\Verint\app;

class Admin {
	
	public function __construct() {

		// add action to add_submenu_page
		add_action( 'admin_menu', array( $this, 'addAdminMenu' ) );
	}

	/**
	 * Add the admin menu
	 */
	public function addAdminMenu() {
		// add a new sub menu page to the tools menu for "Verint Cleanup"
		add_submenu_page(
			'tools.php',
			__( 'Verint Cleanup', 'aras' ),
			__( 'Verint Cleanup', 'aras' ),
			'manage_options',
			'verint_cleanup',
			array( $this, 'renderCleanupPage' )
		);
	}

	/**
	 * Render the cleanup page
	 */
	public function renderCleanupPage() {
		?>
		<div id="verint-app"></div>
		<?php
		app()->ui->render('src/main.ts');
	}
}