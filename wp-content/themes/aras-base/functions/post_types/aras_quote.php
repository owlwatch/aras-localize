<?php

namespace Aras;
/**
 * Register the Quote post type
 */
function register_quote_post_type()
{
	add_action('init', function () {
		$labels = [
			'name' => __('Quotes', 'aras'),
			'singular_name' => __('Quote', 'aras'),
			'menu_name' => __('Quotes', 'aras'),
			'name_admin_bar' => __('Quote', 'aras'),
			'add_new' => __('Add New', 'aras'),
			'add_new_item' => __('Add New Quote', 'aras'),
			'new_item' => __('New Quote', 'aras'),
			'edit_item' => __('Edit Quote', 'aras'),
			'view_item' => __('View Quote', 'aras'),
			'all_items' => __('All Quotes', 'aras'),
			'search_items' => __('Search Quotes', 'aras'),
			'not_found' => __('No quotes found.', 'aras'),
			'not_found_in_trash' => __('No quotes found in Trash.', 'aras'),
			'parent_item_colon' => __('Parent Quote:', 'aras'),
			'archives' => __('Quote Archives', 'aras'),
			'attributes' => __('Quote Attributes', 'aras'),
			'insert_into_item' => __('Insert into quote', 'aras'),
			'uploaded_to_this_item' => __('Uploaded to this quote', 'aras'),
			'featured_image' => __('Featured Image', 'aras'),
			'set_featured_image' => __('Set featured image', 'aras'),
			'remove_featured_image' => __('Remove featured image', 'aras'),
			'use_featured_image' => __('Use as featured image', 'aras'),
			'filter_items_list' => __('Filter quotes list', 'aras'),
			'items_list_navigation' => __('Quotes list navigation', 'aras'),
			'items_list' => __('Quotes list', 'aras'),
			'item_published' => __('Quote published.', 'aras'),
			'item_published_privately' => __('Quote published privately.', 'aras'),
			'item_reverted_to_draft' => __('Quote reverted to draft.', 'aras'),
			'item_updated' => __('Quote updated.', 'aras'),
		];
		
		$args = [
			'labels' => $labels,
			'public' => true,
			'has_archive' => true,
			'show_in_rest' => true,
			'supports' => ['title', 'thumbnail', 'excerpt'],
			'menu_icon' => 'dashicons-format-quote',
			'rewrite' => ['slug' => 'quotes'],
			'capability_type' => 'post',
			'hierarchical' => false,
		];

		register_post_type('aras_quote', $args);

		// Register a taxonomy for Quote Categories
		$taxonomy_labels = [
			'name' => __('Quote Categories', 'aras'),
			'singular_name' => __('Quote Category', 'aras'),
			'menu_name' => __('Quote Categories', 'aras'),
			'all_items' => __('All Quote Categories', 'aras'),
			'edit_item' => __('Edit Quote Category', 'aras'),
			'view_item' => __('View Quote Category', 'aras'),
			'update_item' => __('Update Quote Category', 'aras'),
			'add_new_item' => __('Add New Quote Category', 'aras'),
			'new_item_name' => __('New Quote Category Name', 'aras'),
			'search_items' => __('Search Quote Categories', 'aras'),
			'popular_items' => __('Popular Quote Categories', 'aras'),
			'separate_items_with_commas' => __('Separate quote categories with commas', 'aras'),
			'add_or_remove_items' => __('Add or remove quote categories', 'aras'),
			'choose_from_most_used' => __('Choose from the most used quote categories', 'aras'),
			'not_found' => __('No quote categories found.', 'aras'),
		];
		$taxonomy_args = [
			'labels' => $taxonomy_labels,
			'hierarchical' => true,
			'public' => true,
			'show_in_rest' => true,
			'rewrite' => ['slug' => 'quote-category'],
			'show_admin_column' => true,
		];
		register_taxonomy('aras_quote_category', ['aras_quote'], $taxonomy_args);
	});
}
register_quote_post_type();