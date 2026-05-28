<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
