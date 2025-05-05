<?php
namespace Aras\Swoogo\Service;

class Admin {
	
	public function __construct() {

		// add a post_row_actions filter to add our custom actions
		add_filter( 'post_row_actions', array( $this, 'addPostRowActions' ), 10, 2 );

		// lets also add a button on the edit page
		add_action( 'post_submitbox_misc_actions', array( $this, 'addSyncButton' ) );
	}

	public function addPostRowActions($actions, $post)
	{
		// check if this is a swoogo event
		if ( get_post_type($post->ID) == 'swoogo-event' && get_post_meta( $post->ID, 'swoogo_event', true ) ) {
			$actions['swoogo_sync'] = '<a href="' . admin_url( 'admin.php?page=swoogo-sync&post_id=' . $post->ID ) . '">Sync with Swoogo</a>';
		}

		return $actions;
	}

	public function addSyncButton()
	{
		global $post;
		if ( get_post_type($post->ID) == 'swoogo-event' && get_post_meta( $post->ID, 'swoogo_event', true ) ) {
			echo '<div class="misc-pub-section misc-pub-section-last"><a href="' . admin_url( 'admin.php?page=swoogo-sync&post_id=' . $post->ID ) . '" class="button button-primary">Sync with Swoogo</a></div>';
		}
	}
}