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