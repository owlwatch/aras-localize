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
	header('content-type: text/plain; charset=utf-8');
	$out = fopen('php://output', 'w');
	// query all resources
	
	$resource_query = new WP_Query([
		'posts_per_page' => -1,
		'post_type' => 'resource',
		'suppress_filters' => true
	]);
	
	while( $resource_query->have_posts() ){
		$resource_query->the_post();
		$button = get_field('post_submission_button');
		
		fputcsv( $out, [
			get_the_title(),
			get_permalink(),
			get_edit_post_link(),
			get_the_date(),
			$button ? $button['url'] : 'no file'
		]);
	}
	fclose($out);
}