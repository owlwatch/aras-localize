<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
