<?php

$request_uri = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
if (strpos($request_uri, '/news/press-releases/') !== false && strpos($request_uri, 'link.aspx') !== false) {
	wp_redirect(home_url('/news'));
	exit;
}
if (strpos($request_uri, '/resources/all/') !== false && strpos($request_uri, 'link.aspx') !== false) {
	wp_redirect(home_url('/resources'));
	exit;
}

if (strpos($request_uri, '/page/') !== false) {
	if (strpos($request_uri, '/resources/page/') !== false) {
		wp_redirect(home_url('/resources'));
		exit;
	} elseif (strpos($request_uri, '/blog/author/') !== false) {
		wp_redirect(home_url('/blog'));
	} elseif (strpos($request_uri, '/blog/format/') !== false) {
		wp_redirect(home_url('/blog'));
	} elseif (strpos($request_uri, '/blog/tag/') !== false) {
		wp_redirect(home_url('/blog'));
	} else {
		wp_redirect(home_url(''));
		exit;
	}
}
/**
 * The template for displaying 404 (page not found) pages.
 *
 * For more info: https://codex.wordpress.org/Creating_an_Error_404_Page
 */

get_header(); ?>








<section id="error-page" class="short-hero hero-banner bg-dblue">
	<div class="grid-container">
		<div class="grid-x grid-padding-x align-top">
			<div class="cell small-12 medium-10 hero-content">
				<div class="hero-content-inner">
					<h1 role="heading" aria-level="1" class="hero-headline" style="margin-bottom: 2rem;">Sorry, we can't find that page.</h1>
					<h4 style="margin-bottom: 1rem;">Can we direct you somewhere else?</h4>
					<a aria-label="Blog" href="/blog/" style="margin-right: 15px; margin-bottom: 15px;" class="aras-button--outline">Blog</a>
					<a aria-label="Resources" href="/resources/" style="margin-right: 15px; margin-bottom: 15px;" class="aras-button--outline">Resources</a>
					<h4 style="margin-top: 3rem;">Additionally, you may search for content below.</h4>
					<form role="search" method="get" class="blog-search-form" action="<?php echo home_url('/'); ?>">
						<label>
							<span class="screen-reader-text"><?php echo _x('Search for:', 'label', 'jointswp') ?></span>
							<input type="hidden" name="post_type" value="*" />
							<input aria-label="search" type="search" class="search-field" placeholder="" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x('Search for:', 'jointswp') ?>" />
						</label>
						<input class="search-arrow-icon" type="submit" value=" " alt="Search" style="background-image:url(<?php echo get_template_directory_uri(); ?>/assets/images/icons/searchicon.svg)" />
					</form>
				</div>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>