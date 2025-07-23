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

		case 'find_posts_with_quote':
			aras_export_posts_with_quote();
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
		'ID',
		'Title',
		'Permalink',
		'Edit Link',
		'Date',
		'Last Modified',
		'File',
		'Formats',
		'Topics'
	]);
	
	while( $resource_query->have_posts() ){
		$resource_query->the_post();
		$button = get_field('post_submission_button');

		$formats = get_the_terms( get_the_ID(), 'format' );
		$all_formats = is_array( $formats ) ? implode(', ', array_map(function($term){
			return $term->name;
		}, $formats)) : '';

		$topics = get_the_terms( get_the_ID(), 'topic' );
		$all_topics = is_array($topics) ? implode(', ', array_map(function($term){
			return $term->name;
		}, $topics)) : '';

		
		
		fputcsv( $out, [
			get_the_ID(),
			get_the_title(),
			get_permalink(),
			get_edit_post_link(),
			get_the_date('Y-m-d H:i:s'),
			get_the_modified_date('Y-m-d H:i:s'),
			$button ? $button['url'] : 'no file',
			$all_formats,
			$all_topics
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