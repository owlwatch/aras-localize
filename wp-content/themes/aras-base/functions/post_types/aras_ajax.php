<?php

/*Set the amount of posts per page on post types*/
function custom_posts_per_page($query)
{
	if (is_admin() || !$query->is_main_query()) {
		return;
	}
	if ($query->is_search()) {
		$query->set('posts_per_page', -1);
	}
	if (is_post_type_archive('post')) {
		// Standard post type ('post')
		$query->set('posts_per_page', 12);
	}
	if (is_post_type_archive('resource')) {
		// Custom post type ('resource')
		$query->set('posts_per_page', 12);
	}
}
add_action('pre_get_posts', 'custom_posts_per_page');



//AJAX LOAD MORE POSTS
add_action('wp_ajax_load_more_posts', 'load_more_posts');
add_action('wp_ajax_nopriv_load_more_posts', 'load_more_posts');

function load_more_posts()
{
	// Retrieve AJAX parameters
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$lang_switcher = isset($_POST['language']) ? $_POST['language'] : '';
	$category_switcher = isset($_POST['category']) ? $_POST['category'] : '';
	$tag_switcher = isset($_POST['tag']) ? $_POST['tag'] : '';
	$author_switcher = isset($_POST['author']) ? $_POST['author'] : '';
	$site_url = isset($_POST['site_url']) ? $_POST['site_url'] : '';

	$args = array(
		'posts_per_page' => 12,
		'post_type' => 'post',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post_status'    => 'publish',
		'suppress_filters' => true,
		'paged' => $page,
	);

	// Add language filter to query if specified
	if ($lang_switcher == 'de-de' || $lang_switcher == 'fr-fr' || $lang_switcher == 'ja-jp' || $lang_switcher == 'en') {
		$args['meta_query'] = array(
			array(
				'key' => 'post_lang_code',
				'value' => $lang_switcher,
				'compare' => 'IN',
			)
		);
	} elseif ($lang_switcher == 'en__de-de' || $lang_switcher == 'en__fr-fr' || $lang_switcher == 'en__ja-jp') {
		$lang_codes = explode('__', $lang_switcher);
		$args['meta_query'] = array(
			'relation' => 'OR', // Use OR relation as we want posts matching either of the language codes
			array(
				'key'     => 'post_lang_code',
				'value'   => $lang_codes[0], // First language code
				'compare' => 'IN',
			),
			array(
				'key'     => 'post_lang_code',
				'value'   => $lang_codes[1], // Second language code
				'compare' => 'IN',
			),
		);
	} else {
		if (str_contains($site_url, '/ja-jp/')) {
			$args['meta_query'] = array(
				array(
					'key' => 'post_lang_code',
					'value' => 'ja-jp',
					'compare' => 'IN',
				)
			);
		} elseif (str_contains($site_url, '/fr-fr/')) {
			$args['meta_query'] = array(
				'relation' => 'OR',
				array(
					'key'     => 'post_lang_code',
					'value'   => 'en',
					'compare' => 'IN',
				),
				array(
					'key'     => 'post_lang_code',
					'value'   => 'fr-fr',
					'compare' => 'IN',
				),
			);
		} elseif (str_contains($site_url, '/de-de/')) {
			$args['meta_query'] = array(
				'relation' => 'OR',
				array(
					'key'     => 'post_lang_code',
					'value'   => 'en',
					'compare' => 'IN',
				),
				array(
					'key'     => 'post_lang_code',
					'value'   => 'de-de',
					'compare' => 'IN',
				),
			);
		} else {
			$args['meta_query'] = array(
				array(
					'key' => 'post_lang_code',
					'value' => 'en',
					'compare' => 'IN',
				)
			);
		}
	}

	// Add category filter to query if specified
	if ($category_switcher) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'field' => is_numeric($category_switcher) ? 'term_id' : 'slug',
				'terms' => $category_switcher,
			)
		);
	}

	// Add tag filter to query if specified
	if ($tag_switcher) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'post_tag',
				'field' => is_numeric($tag_switcher) ? 'term_id' : 'slug',
				'terms' => $tag_switcher,
			)
		);
	}

	// Add author filter to query if specified
	if ($author_switcher) {
		$user = get_user_by('login', $author_switcher);
		if ($user) {
			$author_id = $user->ID;
			$args['author'] = $author_id;
		}
	}

	$posts_query = new WP_Query($args);
	if ($posts_query->have_posts()) :
		while ($posts_query->have_posts()) :
			$posts_query->the_post();
			get_template_part('parts/loop', 'archive');
		endwhile;
		wp_reset_postdata();
	endif;

	// Always remember to exit after processing AJAX request
	wp_die();
}











//AJAX LOAD MORE Resources
function load_more_resources()
{
	$page = $_POST['page'];
	$args = array(
		'post_type'      => 'resource',
		'posts_per_page' => 12,
		'paged'          => $page,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post_status'    => 'publish',
	);
	// Add exclusion of posts with term ID 1672
	$tax_query_exclude = array(
		array(
			'taxonomy' => 'featured-resource',
			'field'    => 'id',
			'terms'    => array(1672),
			'operator' => 'NOT IN',
		),
	);
	$args['tax_query'] = $tax_query_exclude;

	$query = new WP_Query($args);

	if ($query->have_posts()) :
		while ($query->have_posts()) :
			$query->the_post();

			get_template_part('parts/loop', 'archive-resources');

		endwhile;
		wp_reset_postdata();
	endif;
	die();
}
add_action('wp_ajax_load_more_resources', 'load_more_resources');
add_action('wp_ajax_nopriv_load_more_resources', 'load_more_resources');


// LOAD MORE RESOURCES BY CUSTOM TAXONOMY
function load_more_resources_by_taxonomy()
{
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$tax_queries = isset($_POST['tax_queries']) ? $_POST['tax_queries'] : array();

	$args = array(
		'post_type'      => 'resource',
		'posts_per_page' => 12,
		'paged'          => $page,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post_status'    => 'publish',
	);



	// Existing tax query
	$main_tax_query = array();
	if (!empty($tax_queries)) {
		foreach ($tax_queries as $tax_query) {
			$main_tax_query[] = array(
				'taxonomy' => $tax_query['taxtype'],
				'field'    => 'slug',
				'terms'    => $tax_query['taxterm'],
			);
		}
	}

	// Add exclusion of posts with term ID 1672
	$tax_query_exclude = array(
		array(
			'taxonomy' => 'featured-resource',
			'field'    => 'id',
			'terms'    => array(1672),
			'operator' => 'NOT IN',
		),
	);

	if (!empty($main_tax_query)) {
		// Merge main tax query with exclusion tax query
		$args['tax_query'] = array_merge($main_tax_query, $tax_query_exclude);
	} else {
		$args['tax_query'] = $tax_query_exclude;
	}


	$query = new WP_Query($args);
	if ($query->have_posts()) :
		while ($query->have_posts()) :
			$query->the_post();
			get_template_part('parts/loop', 'archive-resources');
		endwhile;
		wp_reset_postdata();
	endif;
	die();
}
add_action('wp_ajax_load_more_resources_by_taxonomy', 'load_more_resources_by_taxonomy');
add_action('wp_ajax_nopriv_load_more_resources_by_taxonomy', 'load_more_resources_by_taxonomy');





//AJAX LOAD MORE NEWS
function load_more_news()
{
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$args = array(
		'post_type'      => 'news',
		'posts_per_page' => 12,
		'paged'          => $page,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post_status'    => 'publish',
		'tax_query' => array(
			array(
				'taxonomy' => 'news_type',
				'field' => 'slug',
				'terms' => 'media-coverage',
				'operator' => 'NOT IN',
			),
		),
	);

	$query = new WP_Query($args);

	if ($query->have_posts()) :
		while ($query->have_posts()) :
			$query->the_post();

			get_template_part('parts/loop', 'archive-news');

		endwhile;
		wp_reset_postdata();
	endif;
	die();
}
add_action('wp_ajax_load_more_news', 'load_more_news');
add_action('wp_ajax_nopriv_load_more_news', 'load_more_news');

//LOAD MORE NEWS BY TYPE
function load_more_type_news()
{
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$category_id = $_POST['category_id'];
	$args = array(
		'post_type'      => 'news',
		'posts_per_page' => 12,
		'paged'          => $page,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post_status'    => 'publish',
		'tax_query' => array(
			array(
				'taxonomy' => 'news_type',
				'field'    => 'slug',
				'terms'    => $category_id,
			),
		),
	);

	$query = new WP_Query($args);

	if ($query->have_posts()) :
		while ($query->have_posts()) :
			$query->the_post();

			get_template_part('parts/loop', 'archive-news');

		endwhile;
		wp_reset_postdata();
	endif;
	die();
}
add_action('wp_ajax_load_more_type_news', 'load_more_type_news');
add_action('wp_ajax_nopriv_load_more_type_news', 'load_more_type_news');
