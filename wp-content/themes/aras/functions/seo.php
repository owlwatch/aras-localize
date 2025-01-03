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


function enforce_alt_text_in_media_library() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Attach an event to the 'Insert into Post' button
            $(document).on('click', '.media-button-insert, .media-button-select', function() {
                var altText = $('.attachment-details .setting[data-setting="alt"]').find('input').val();
                
                if (!altText) {
                    alert('Please add alternative text for accessibility before inserting the image.');
                    return false; // Prevents the image from being inserted
                }
            });
        });
    </script>
    <?php
}
add_action('admin_footer', 'Aras\\SEO\\enforce_alt_text_in_media_library');

function add_custom_replacements($replacements, $args)
{
    if( !empty( $args->term_id ) ){
        $site_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $term = get_term( $args->term_id );
        $field_parts = ['cat','label'];
        
        switch( true ){
            case str_contains($site_url, '/ja-jp/'):
            $field_parts[] = 'japanese';
            break;
            case str_contains($site_url, '/fr-fr/'):
            $field_parts[] = 'french';
            break;
            case str_contains($site_url, '/de-de/'):
            $field_parts[] = 'german';
            break;
        }
        $field_name = implode('_', $field_parts);
        $replacements['%%term_title%%'] = get_field($field_name, $term) ?: $term->name;

        // remove "Aras" from the term_title replacement
        // but only for the "category" taxonomy
        $replacements['%%term_title_without_aras%%'] = str_replace('Aras ', '', $replacements['%%term_title%%']);
    }
    return $replacements;
}
add_action('wpseo_replacements', 'Aras\\SEO\\add_custom_replacements', 10, 2);

add_action('wpseo_register_extra_replacements', 'Aras\\SEO\\register_custom_replacements');
function register_custom_replacements()
{
    wpseo_register_var_replacement('%%term_title_without_aras%%', 'Aras\\SEO\\get_term_title_without_aras', 'advanced', 'Term Title without Aras');
}

function get_term_title_without_aras($var, $args)
{
    if( $args->term_id ){
        $site_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $term = get_term( $args->term_id);
        $field_parts = ['cat','label'];
        
        switch( true ){
            case str_contains($site_url, '/ja-jp/'):
            $field_parts[] = 'japanese';
            break;
            case str_contains($site_url, '/fr-fr/'):
            $field_parts[] = 'french';
            break;
            case str_contains($site_url, '/de-de/'):
            $field_parts[] = 'german';
            break;
        }
        $field_name = implode('_', $field_parts);
        $term_title = get_field($field_name, $term) ?: $term->name;

        // remove "Aras" from the term_title replacement
        // but only for the "category" taxonomy
        return str_replace('Aras ', '', $term_title);
    }
    return null;
}