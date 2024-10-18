<?php

namespace Aras\SEO;

function seo_partner_content($content)
{
	if (is_singular('partners')) {
		// get the field partner_info__c
		$info = get_field('partner_info__c');
		$desc = wp_html_excerpt($info, 162, '...');
		return $desc;
	}
	return $content;
}

add_filter('wpseo_metadesc', 'Aras\\SEO\\seo_partner_content');

function add_custom_event_schema_piece( $graph_pieces, $context ) {
    // Check if we're dealing with the "event" post type
    if ( 'event' === get_post_type( $context->id ) ) {
		require_once( __DIR__ . '/seo/EventSchema.php' );
        // Add the custom Event_Schema class to the graph
        $graph_pieces[] = new EventSchema();
    }

    return $graph_pieces;
}

add_filter( 'wpseo_schema_graph_pieces', 'Aras\\SEO\\add_custom_event_schema_piece', 10, 2 );
