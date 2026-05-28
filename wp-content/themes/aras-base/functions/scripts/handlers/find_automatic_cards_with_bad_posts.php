<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function aras_find_automatic_cards_with_bad_posts()
{
	global $wpdb;

	header('content-type: text/plain; charset=utf-8');
	header('Content-Disposition: attachment; filename="automatic_cards_with_bad_posts.csv"');
	$out = fopen('php://output', 'w');

	fputcsv( $out, [
		'Page ID',
		'Page Title',
		'Page Permalink',
		'Page Edit Link',
	]);

	$sql = "SELECT m.meta_id, m.meta_key, m.post_id, m.meta_value FROM {$wpdb->postmeta} m JOIN {$wpdb->posts} p ON m.post_id = p.ID AND p.post_status = 'publish' WHERE meta_key LIKE 'flexible_content%content_item' GROUP BY m.post_id, m.meta_key HAVING meta_value != ''";
	$results = $wpdb->get_results($sql);

	foreach( $results as $row ){
		$page_id = $row->post_id;
		$post_id = $row->meta_value;

		// Ensure the content item belongs to an automatic cards section.
		$flexible_content = get_post_meta( $page_id, 'flexible_content', true );
		if( !is_array( $flexible_content ) ){
			continue;
		}

		preg_match( '/^flexible_content_(\d+)/', $row->meta_key, $matches );
		if( !isset( $matches[1] ) ){
			continue;
		}

		$index = intval( $matches[1] );
		if( !isset( $flexible_content[$index] ) || $flexible_content[$index] !== 'automatic_cards_section' ){
			continue;
		}

		if( $post_id ){
			$post = get_post( $post_id );
			if( !$post || $post->post_status !== 'publish' ){
				fputcsv( $out, [
					$page_id,
					get_the_title( $page_id ),
					get_permalink( $page_id ),
					get_edit_post_link( $page_id ),
				]);
			}
		}
	}

	fclose($out);
}
