<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function aras_archive_deprecated_pages()
{
	$csv_path = get_template_directory() . '/data/deprecated-pages.csv';
	if( !file_exists( $csv_path ) ){
		wp_die( 'Deprecated pages CSV not found: ' . esc_html( $csv_path ) );
	}

	header('content-type: text/plain; charset=utf-8');
	header('Content-Disposition: attachment; filename="deprecated-pages-archived.txt"');

	$handle = fopen( $csv_path, 'r' );
	if( !$handle ){
		wp_die( 'Unable to open deprecated pages CSV.' );
	}

	$totals = [
		'processed' => 0,
		'archived' => 0,
		'already_archived' => 0,
		'missing' => 0,
		'errors' => 0,
	];

	while( ( $row = fgetcsv( $handle ) ) !== false ){
		$url = trim( $row[0] ?? '' );
		if( $url === '' ){
			continue;
		}

		$totals['processed']++;
		$post_id = url_to_postid( $url );
		if( !$post_id ){
			$totals['missing']++;
			echo "missing\t{$url}\n";
			continue;
		}

		$post = get_post( $post_id );
		if( !$post ){
			$totals['missing']++;
			echo "missing\t{$url}\t{$post_id}\n";
			continue;
		}

		if( $post->post_status === 'archive' ){
			$totals['already_archived']++;
			echo "already_archived\t{$url}\t{$post_id}\t{$post->post_type}\n";
			continue;
		}

		$updated = wp_update_post([
			'ID' => $post_id,
			'post_status' => 'archive',
		], true );

		if( is_wp_error( $updated ) ){
			$totals['errors']++;
			echo "error\t{$url}\t{$post_id}\t" . $updated->get_error_message() . "\n";
			continue;
		}

		$totals['archived']++;
		echo "archived\t{$url}\t{$post_id}\t{$post->post_type}\t{$post->post_status}\n";
	}

	fclose( $handle );

	echo "\nsummary\n";
	foreach( $totals as $label => $count ){
		echo "{$label}\t{$count}\n";
	}
}
