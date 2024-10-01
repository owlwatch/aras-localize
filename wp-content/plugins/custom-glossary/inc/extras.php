<?php

namespace Custom\Glossary;

add_action('pre_get_posts', function ($query) {
	if (!is_admin() && $query->is_post_type_archive('glossary')) {
		$query->set('posts_per_page', -1);
		$query->set('order_by', 'name');
		$query->set('order', 'ASC');
	}
}, 10);

add_filter('excerpt_length', function ($length) {
	if ('glossary' === get_post_type()) {
		return 40;
	}
});

add_filter('the_excerpt', function ($excerpt) {
	if ('glossary' === get_post_type() && has_excerpt(get_the_ID())) {
		$excerpt .= ' ' . str_replace(['&ellipsis;', '…', '&#8230;', '...'], '', apply_filters('excerpt_more', '...'));
	}
	return $excerpt;
}, 9);

function remove_h2($content)
{
	$content = preg_replace('#<h2.*?</h2>#', '', $content);
	remove_filter('the_content', 'remove_h2', 9);
	return $content;
}

function filter_content_for_excerpt($excerpt)
{
	if ('glossary' == get_post_type()) {
		add_filter('the_content', __NAMESPACE__ . '\\remove_h2', 9);
	}
	return $excerpt;
}

add_filter('the_excerpt', __NAMESPACE__ . '\\filter_content_for_excerpt');

// let's add the script on the glossary page
add_action('wp_head', function () {
	if (is_post_type_archive('glossary')) {
		echo vite('main.ts');
	}
});

// extend the glossary to allow excerpt
add_action('init', function () {
	add_post_type_support('glossary', 'excerpt');
}, 20);
