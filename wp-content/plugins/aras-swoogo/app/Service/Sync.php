<?php
namespace Aras\Swoogo\Service;

class Sync
{

	/**
	 * The Swoogo API
	 *
	 * @var Aras\Swoogo\Service\SwoogoApi
	 */
	private $api;

	/**
	 * Initialize the class
	 *
	 * @param Aras\Swoogo\Service\SwoogoApi $api
	 */
	public function __construct( $api )
	{
		$this->api = $api;

		if( !$this->api->isConnected() ){
			return;
		}

		// add a cron jobs to sync events
		add_action( 'aras_swoogo_sync_events', array( $this, 'syncEvents' ) );

		if( !wp_next_scheduled( 'aras_swoogo_sync_events' ) ) {
			wp_schedule_event( time(), 'hourly', 'aras_swoogo_sync_events' );
		}
	}

	/**
	 * Sync all the events
	 *
	 * @return void
	 */
	public function syncEvents()
	{

		$events = get_field( 'swoogo_synced_events', 'option' );
		foreach( $events as $event ){
			// get the event from the API
			$this->syncEvent( $event['event_id'] );
		}
	}

	/**
	 * Get all the swoogo fields for a given type
	 *
	 * @param string $type
	 * @return array
	 */
	public function getSwoogoFields( $type='')
	{
		$fields = [];
		$page = 1;
		do {
			$response = $this->api->get( $type.'/fields',[
				'per-page' => 100,
				'page' => $page++,
			]);
			$fields = array_merge( $fields, $response->items );
		}while( $response->_meta->currentPage < $response->_meta->pageCount );

		return $fields;
	}

	/**
	 * Sync the event with the API
	 *
	 * @param int $id
	 * @return int|null
	 */
	public function syncEvent( $id )
	{

		if( !$this->api->isConnected() ){
			return null;
		}
		
		// get the event from the API
		$event = new \stdClass;
		$event->sessionFields = $this->getSwoogoFields('sessions');
		$event->contactFields = $this->getSwoogoFields('contacts');
		$event->sponsorFields = $this->getSwoogoFields('sponsors');

		$event->details = $this->api->get('events/'.$id, []);
		
		// lets get all the fields for the sessions
		$fields = array_map( function($field){
			return $field->attribute;
		}, $event->sessionFields);

		$fields = array_merge( $fields, ['id','name','description','date','start_time','end_time','notes','badge_name'] );

		// lets get the sessions for this event
		$sessions = [];
		$page = 1;
		do {
			$sessionResponse = $this->api->get('sessions', [
				'event_id' => $id,
				'page' => $page++,
				'fields' => implode(',',$fields),
				'expand' => 'speakers,track,location,translations',
			]);
			$sessions = array_merge( $sessions,  $sessionResponse->items );
		}while( $sessionResponse->_meta->currentPage < $sessionResponse->_meta->pageCount );
		
		// lets get the sponsors for this event
		// lets get all the fields for the sessions
		$fields = array_map( function($field){
			return $field->attribute;
		}, $event->sponsorFields);

		$fields = array_merge( $fields, ['id','logo_id','direct_link','name','website','description','level'] );
		$sponsors = [];
		$page = 1;
		do {
			$sponsorResponse = $this->api->get('sponsors', [
				'event_id' => $id,
				'page' => $page++,
				'fields' => implode(',',$fields),
			]);
			$sponsors = array_merge( $sponsors,  $sponsorResponse->items );
		}while( $sponsorResponse->_meta->currentPage < $sponsorResponse->_meta->pageCount );

		$event->sponsors = $sponsors;
		
		// lets dedupe all the speakers and tracks
		$speakers = [];
		foreach( $sessions as $session ){
			if( !empty( $session->speakers ) ){
				foreach( $session->speakers as $speaker ){
					if( !isset( $speakers[ $speaker->id ] ) ){
						$speakers[ $speaker->id ] = $speaker;
					}
				}
				$session->speaker_ids = array_map( function($s){
					return $s->id;
				}, $session->speakers );
			}
		}

		$tracks = [];
		foreach( $sessions as $session ){
			if( !empty( $session->track ) ){
				$track = $session->track;
				if( !isset( $tracks[ $track->id ] ) ){
					$tracks[ $track->id ] = $track;
				}

				$session->track_id = $session->track->id;
			}
		}

		$locations = [];
		foreach( $sessions as $session ){
			if( !empty( $session->location ) ){
				$location = $session->location;
				if( !isset( $locations[ $location->id ] ) ){
					$locations[ $location->id ] = $location;
				}
				$session->location_id = $location->id;
			}
		}

		$event->sessions = $sessions;
		$event->speakers = array_values($speakers);
		$event->tracks = array_values($tracks);
		$event->locations = array_values($locations);

		// use "swoogo-event-" . $event->id as the database key
		// and save the event as json in post_content
		$post_name = 'swoogo-event-' . $event->details->id;
		$post = get_page_by_path($post_name, OBJECT, 'swoogo-event');

		if ($post) {
			// Update the existing post
			wp_update_post([
				'ID' => $post->ID,
				'post_content' => '',
			]);
			$post_id = $post->ID;
		} else {
			// Create a new post
			$post_id = wp_insert_post([
				'post_title' => $event->details->name,
				'post_name' => $post_name,
				'post_content' => '',
				'post_status' => 'publish',
				'post_type' => 'swoogo-event',
			]);
		}
		update_post_meta( $post_id, 'swoogo_event', $event );

		return $post_id;
	}
	
}