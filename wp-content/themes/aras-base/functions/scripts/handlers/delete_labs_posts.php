<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function aras_delete_labs_posts()
{
	global $wpdb;
	// we want to find any posts that had the 'aras-labs' tag and
	// have a status of "archive" and delete them permanently
	// along with any thumbnail images or images in the content
	// that are not used anywhere else on the site
	$labs_term = get_term_by( 'slug', 'aras-labs', 'category' );
	if( !$labs_term ){
		wp_die('No aras-labs term found');
	}
	header('content-type: text/plain; charset=utf-8');
	$labs_query = new WP_Query([
		'post_type' => 'post',
		'post_status' => 'archive',
		'posts_per_page' => -1,
		'tax_query' => [
			[
				'taxonomy' => 'category',
				'field' => 'term_id',
				'terms' => $labs_term->term_id,
			],
		],
	]);

	$images_to_delete = [];

	while( $labs_query->have_posts() ){
		$labs_query->the_post();
		$post_id = get_the_ID();

		// get all image IDs from the content
		$content = get_the_content();
		preg_match('/<img[^>]+src=["\']([^"\']+\/wp-content\/uploads\/[^"\']+\.(jpg|jpeg|png|gif|webp))["\'][^>]*>/i', $content, $matches);
		if( isset( $matches[1] ) ){
			$image_url = $matches[1];
			$image_id = attachment_url_to_postid( $image_url );
			if( $image_id ){
				$images_to_delete[] = $image_id;
			}
		}

		$featured_image_id = get_post_thumbnail_id( $post_id );
		if( $featured_image_id ){
			$images_to_delete[] = $featured_image_id;
		}

		// delete the post permanently
		// wp_delete_post( $post_id, true );
		echo "to delete: {$post_id} - " . get_the_title( $post_id ) . "\n";
	}

	$images_to_delete = array_unique( $images_to_delete );
	foreach( $images_to_delete as $image_id ){
		$usage = get_posts([
			'post_type' => 'any',
			'post_status' => 'publish',
			'meta_query' => [
				[
					'key' => '_thumbnail_id',
					'value' => $image_id,
					'compare' => '=',
				],
			],
			'posts_per_page' => 1,
		]);

		$content_usage = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_content LIKE %s",
				'%' . $wpdb->esc_like( 'wp-image-' . $image_id ) . '%'
			)
		);

		if( empty( $usage ) && intval( $content_usage ) === 0 ){
			// wp_delete_attachment( $image_id, true );
			echo "to delete image: {$image_id}\n";
		}
		else {
			$usage_ids = implode(', ',array_map( function($post){
				return $post->ID;
			}, $usage ));
			echo "not deleting image (in use): {$image_id}, found in {$usage_ids} and content usage: {$content_usage}\n";
		}
	}
}
