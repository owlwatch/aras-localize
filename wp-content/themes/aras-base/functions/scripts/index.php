<?php

add_action('init', function(){
	// perform our requested scripts
	if( !is_super_admin() ){
		return;
	}

	if( !isset($_REQUEST['aras_script']) ){
		return;
	}

	
	switch( $_REQUEST['aras_script'] ){
		case 'export_resources':
			aras_export_resources();
			break;

		case 'find_posts_with_quote':
			aras_export_posts_with_quote();
			break;	

		case 'find_automatic_cards_with_bad_posts':
			aras_find_automatic_cards_with_bad_posts();
			break;

		case 'delete_labs_posts':
			aras_delete_labs_posts();
			break;
	}

	exit;
});

function aras_export_resources()
{
	header('content-type: text/csv; charset=utf-8');
	$out = fopen('php://output', 'w');
	// query all resources
	
	$resource_query = new WP_Query([
		'posts_per_page' => -1,
		'post_type' => 'resource',
		'suppress_filters' => true
	]);

	fputcsv( $out, [
		'title',
		'permalink',
		'language',
		'salesforce campaign',
		'file',
		'special resource',
		'publish date',
		'page template',
		'format',
		'applications',
		'topics',
		'industries'
	]);
	
	while( $resource_query->have_posts() ){
		$resource_query->the_post();
		$button = get_field('post_submission_button');
		$post_id = get_the_ID();

		$formats = get_the_terms( $post_id, 'format' );
		$all_formats = is_array( $formats ) ? implode(', ', array_map(function($term){
			return $term->name;
		}, $formats)) : '';

		$applications = get_the_terms( $post_id, 'application' );
		$all_applications = is_array($applications) ? implode(', ', array_map(function($term){
			return $term->name;
		}, $applications)) : '';

		$topics = get_the_terms( $post_id, 'topic' );
		$all_topics = is_array($topics) ? implode(', ', array_map(function($term){
			return $term->name;
		}, $topics)) : '';

		$industries = get_the_terms( $post_id, 'industry' );
		$all_industries = is_array($industries) ? implode(', ', array_map(function($term){
			return $term->name;
		}, $industries)) : '';

		$special_resource_terms = get_the_terms( $post_id, 'featured-resource' );
		$all_special_resources = is_array($special_resource_terms) ? implode(', ', array_map(function($term){
			return $term->name;
		}, $special_resource_terms)) : '';

		$language = '';
		$wpml_language_details = apply_filters('wpml_post_language_details', null, $post_id);
		if( is_array($wpml_language_details) && !empty($wpml_language_details['language_code']) ){
			$language = $wpml_language_details['language_code'];
		}
		elseif( !empty($_COOKIE['wp-wpml_current_language']) ){
			$language = sanitize_text_field($_COOKIE['wp-wpml_current_language']);
		}

		
		
		fputcsv( $out, [
			get_the_title(),
			get_permalink(),
			$language,
			get_field('salesforce_campaign', $post_id),
			$button ? $button['url'] : 'no file',
			$all_special_resources,
			get_the_date('Y-m-d H:i:s'),
			get_page_template_slug( $post_id ),
			$all_formats,
			$all_applications,
			$all_topics,
			$all_industries
		]);
	}
	fclose($out);
}

function aras_export_posts_with_quote()
{
	global $wpdb;

	header('content-type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename="posts_with_quote.csv"');
	$out = fopen('php://output', 'w');
	
	fputcsv( $out, [
		'ID',
		'Title',
		'Permalink',
		'Edit Link',
		'Date',
		'Last Modified',
	]);
	
	// Step 1: Find post IDs with a matching meta_key
	$like = $wpdb->esc_like('quote_content');
	$post_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
			"%{$like}%"
		)
	);

	// if( !empty( $post_ids ) ){
	// 	fclose($out);
	// 	die( print_R( $post_ids, true ) );
	// }

	// Step 2: Query those posts
	$quote_query = new WP_Query([
		'post_type'      => 'any',
		'post__in'       => $post_ids,
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	]);

	while( $quote_query->have_posts() ){
		$quote_query->the_post();
		
		fputcsv( $out, [
			get_the_ID(),
			get_the_title(),
			get_permalink(),
			get_edit_post_link(),
			get_the_date('Y-m-d H:i:s'),
			get_the_modified_date('Y-m-d H:i:s')
		]);
		
	}
	fclose($out);
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
	
	// Step 1: Find post IDs with a matching meta_key
	$like = $wpdb->esc_like('content_item');

	$sql = "SELECT m.meta_id, m.meta_key, m.post_id, m.meta_value FROM {$wpdb->postmeta} m JOIN {$wpdb->posts} p ON m.post_id = p.ID AND p.post_status = 'publish' WHERE meta_key LIKE 'flexible_content%content_item' GROUP BY m.post_id, m.meta_key HAVING meta_value != ''";

	$results = $wpdb->get_results($sql);

	// we need to find the post_ids of the bad posts (meta_value is either a deleted post_id or a post_id that is not published)
	$bad_post_ids = [];
	foreach( $results as $row ){
		$page_id = $row->post_id;
		$post_id = $row->meta_value;

		// we need to get the flexible_content field to make sure that the content_item is actually an automatic card
		// otherwise it could be a quote or other content item
		$flexible_content = get_post_meta( $page_id, 'flexible_content', true );
		if( !is_array( $flexible_content ) ){
			continue;
		}

		// check the meta_key to get the flexible_content_{index}
		preg_match( '/^flexible_content_(\d+)/', $row->meta_key, $matches );
		if( !isset( $matches[1] ) ){
			continue;
		}
		$index = intval( $matches[1] ); // meta_key is 1-based, array is 0-based
		if( !isset( $flexible_content[$index] ) ){
			continue;
		}
		$layout = $flexible_content[$index];
		if( $layout !== 'automatic_cards_section' ){
			continue;
		}
		if( $post_id ){
			$post = get_post( $post_id );
			if( !$post || $post->post_status !== 'publish' ){
				$bad_post_ids[] = [
					'meta_id' => $row->meta_id,
					'meta_key' => $row->meta_key,
					'page_id' => $page_id,
					'permalink' => get_permalink( $page_id ),
					'post_id' => $post_id,
					'post_exists' => $post ? true : false,
					'post_status' => $post ? $post->post_status : 'n/a',
				];
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

function aras_delete_labs_posts()
{
	global $wpdb;
	// we want to find any posts that had the 'aras-labs' tag and
	// have a status of "archived" and delete them permanently
	// along with any thumbnail images or images in the content
	// that are not used anywhere else on the site
	$labs_term = get_term_by( 'slug', 'aras-labs', 'category' );
	if( !$labs_term ){
		wp_die('No aras-labs term found');
	}
	header('content-type: text/plain; charset=utf-8');
	$labs_query = new WP_Query([
		'post_type' => 'post',
		'post_status' => 'archived',
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
		// we need to find it by src=".*/wp-content/uploads/.*(jpg|png|gif|jpeg|webp)$"
		preg_match('/<img[^>]+src=["\']([^"\']+\/wp-content\/uploads\/[^"\']+\.(jpg|jpeg|png|gif|webp))["\'][^>]*>/i', $content, $matches);
		if( isset( $matches[1] ) ){
			$image_url = $matches[1];
			// get the attachment ID from the URL
			$image_id = attachment_url_to_postid( $image_url );
			if( $image_id ){
				$images_to_delete[] = $image_id;
			}
		}

		// get the featured image ID
		$featured_image_id = get_post_thumbnail_id( $post_id );
		if( $featured_image_id ){
			$images_to_delete[] = $featured_image_id;
		}

		// delete the post permanently
		// wp_delete_post( $post_id, true );
		echo "to delete: {$post_id} - " . get_the_title( $post_id ) . "\n";

	}

	// now we need to delete the images that are not used anywhere else
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

		// also check if the image is used in any post content
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