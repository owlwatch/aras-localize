<?php
namespace Aras\SEO;
// Load Yoast's Abstract_Schema_Piece class (modify the path as needed)
use Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece;

class EventSchema extends Abstract_Schema_Piece {

    /**
     * Determines whether this schema piece should be added.
     *
     * @param WP_Post $context The context object (the post being rendered).
     * @return bool Whether or not to include the schema piece.
     */
    public function is_needed() {
        // Apply to event post types only
        return ( 'event' === get_post_type( $this->context->id ) );
    }

    /**
     * Generate the schema data.
     *
     * @param WP_Post $context The context object (the post being rendered).
     * @return array The schema data.
     */
    public function generate() {
        // Get the ACF values for the event
        $startDate = get_field( 'event_date', $this->context->id, false ); // Raw datetime from DB
        $startDate_iso = date( 'c', strtotime( $startDate ) ); // Convert to ISO 8601 format

        $description = get_field( 'event_time', $this->context->id );
		$url = get_permalink( $this->context->id );



        // Return the event schema
        return [
			'@id' => home_url().'#/schema/Event/'.$this->context->id,
            '@type' => 'Event',
            'name' => get_the_title( $this->context->id ),
            'startDate' => $startDate_iso, // Use ISO 8601 date
            'location' => [
                '@type' => 'VirtualLocation',
                'url' => $url,
            ],
            'description' => $description,
            'url' => get_permalink( $this->context->id ),
        ];
    }
}