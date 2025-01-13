<?php
namespace Aras\Swoogo\Service;

use Aras\Swoogo\App;

class Shortcodes
{
	/**
	 * The Agenda UI
	 *
	 * @var Aras\Swoogo\Service\ViteService
	 */
	private $agendaUI;

	/**
	 * The Agenda UI
	 *
	 * @var Aras\Swoogo\Service\ViteService
	 */
	public function __construct( ViteService $agendaUI )
	{
		$this->agendaUI = $agendaUI;
		add_shortcode( 'swoogo-agenda', array( $this, 'agenda' ) );
		add_shortcode( 'swoogo-speakers', array( $this, 'speakers' ) );
		add_shortcode( 'swoogo-sponsors', array( $this, 'sponsors' ) );
	}

	public function agenda( $atts )
	{
		return $this->shortcode('agenda', $atts);
	}

	public function speakers( $atts )
	{
		return $this->shortcode('speakers', $atts);
	}

	public function sponsors( $atts )
	{
		return $this->shortcode('sponsors', $atts, [
			'useSponsorLevels' => true
		]);
	}

	public function shortcode( $widget='agenda', $atts, $extra_defaults=[] )
	{
		$atts = shortcode_atts( array_merge( array(
			'event_id' => '',
		), $extra_defaults), $atts );

		$event_id = $atts['event_id'];

		$config = $atts;

		// get the event from the API
		$event = get_page_by_path( 'swoogo-event-' . $event_id, OBJECT, 'swoogo-event' );
		if( !$event ){
			// we need to add this to the list...
			$events = get_field( 'swoogo_synced_events', 'option' );
			if( !is_array( $events ) ){
				$events = [];
			}
			$exists = array_filter( $events, function( $e ) use ( $event_id ){
				return $e['event_id'] == $event_id;
			});
			if( !$exists ){
				$events[] = [
					'event_id' => $event_id,
				];
				update_field( 'swoogo_synced_events', $events, 'option' );
				App::getInstance()->syncService->syncEvent( $event_id );
			}
			else {
				return '';
			}
		}
		ob_start();
		$event = get_page_by_path( 'swoogo-event-' . $event_id, OBJECT, 'swoogo-event' );
		if( !$event ){
			echo '<p class="error">Event not found</a>';
		}
		$event_data = get_post_meta( $event->ID, 'swoogo_event', true );
		?><script type="application/json" data-aras-widget="swoogo-<?php echo $widget ?>" data-post-id="<?php echo $event->ID ?>" data-config="<?php echo esc_attr( json_encode($config) ) ?>"><?php
		echo json_encode($event_data);
		?></script><div class="swoogo-loading">
			<div>Loading...</div>
			<style>
				.swoogo-loading {
					display: flex;
					justify-content: center;
					align-items: center;
					height: 400px;
					animation: fading 1.5s infinite;
					font-weight: bold;
				}

				@keyframes fading {
					0% {
						background-color: rgba(0,0,0,0.02);
					}

					50% {
						background-color: rgba(0,0,0,0.06);
					}

					100% {
						background-color: rgba(0,0,0,0.02);
					}
				}
			</style>
		</div><?php
		$this->agendaUI->render('src/main.ts');
		return ob_get_clean();

	}
}