<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function aras_convert_glossary_h1s()
{
	@set_time_limit(0);

	header('content-type: text/plain; charset=utf-8');
	header('cache-control: no-store, no-cache, must-revalidate, max-age=0');

	$dry_run_raw = $_REQUEST['dry_run'] ?? $_REQUEST['aras_dry_run'] ?? '0';
	$dry_run = filter_var( wp_unslash( (string) $dry_run_raw ), FILTER_VALIDATE_BOOLEAN );

	$ids = get_posts([
		'post_type' => 'glossary',
		'post_status' => 'any',
		'posts_per_page' => -1,
		'fields' => 'ids',
		'orderby' => 'ID',
		'order' => 'ASC',
		'suppress_filters' => true,
	]);

	$total = count( $ids );
	$processed = 0;
	$updated = 0;
	$unchanged = 0;
	$errors = 0;

	echo "mode\t" . ( $dry_run ? 'dry-run' : 'live' ) . "\n";
	echo "total\t{$total}\n\n";
	@ob_flush();
	@flush();

	foreach ( $ids as $post_id ) {
		$processed++;
		$post = get_post( $post_id );
		if ( ! $post ) {
			$errors++;
			echo "{$processed}/{$total}\terror\t{$post_id}\tpost_not_found\n";
			@ob_flush();
			@flush();
			continue;
		}

		$title = str_replace(["\r", "\n", "\t"], ' ', get_the_title( $post_id ));
		$permalink = get_permalink( $post_id ) ?: '';
		$wpml_language_details = apply_filters( 'wpml_post_language_details', null, $post_id );
		if ( $permalink && is_array( $wpml_language_details ) && ! empty( $wpml_language_details['language_code'] ) ) {
			$wpml_permalink = apply_filters( 'wpml_permalink', $permalink, $wpml_language_details['language_code'], true );
			if ( is_string( $wpml_permalink ) && $wpml_permalink !== '' ) {
				$permalink = $wpml_permalink;
			}
		}

		$original = (string) $post->post_content;
		if ( stripos( $original, '<h1' ) === false ) {
			$unchanged++;
			echo "{$processed}/{$total}\tskip\t{$post_id}\t{$title}\t{$permalink}\tno_h1\n";
			@ob_flush();
			@flush();
			continue;
		}

		$updated_content = preg_replace( '/<\s*h1(\b[^>]*)>/i', '<h2$1>', $original, -1, $open_count );
		$updated_content = preg_replace( '/<\s*\/\s*h1\s*>/i', '</h2>', $updated_content, -1, $close_count );
		$replacements = (int) $open_count + (int) $close_count;

		if ( $replacements === 0 || $updated_content === $original ) {
			$unchanged++;
			echo "{$processed}/{$total}\tskip\t{$post_id}\t{$title}\t{$permalink}\tno_effective_change\n";
			@ob_flush();
			@flush();
			continue;
		}

		if ( $dry_run ) {
			$updated++;
			echo "{$processed}/{$total}\twould_update\t{$post_id}\t{$title}\t{$permalink}\t{$replacements}_tag_changes\n";
			@ob_flush();
			@flush();
			continue;
		}

		$result = wp_update_post([
			'ID' => $post_id,
			'post_content' => $updated_content,
		], true);

		if ( is_wp_error( $result ) ) {
			$errors++;
			echo "{$processed}/{$total}\terror\t{$post_id}\t{$title}\t{$permalink}\t" . $result->get_error_message() . "\n";
			@ob_flush();
			@flush();
			continue;
		}

		$updated++;
		echo "{$processed}/{$total}\tupdated\t{$post_id}\t{$title}\t{$permalink}\t{$replacements}_tag_changes\n";
		@ob_flush();
		@flush();
	}

	echo "\nsummary\n";
	echo "processed\t{$processed}\n";
	echo ( $dry_run ? 'would_update' : 'updated' ) . "\t{$updated}\n";
	echo "unchanged\t{$unchanged}\n";
	echo "errors\t{$errors}\n";
}
