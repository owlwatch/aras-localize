<?php
/* The template for displaying search form */
?>

<form role="search" method="get" class="nav-search-form" action="<?php echo home_url('/'); ?>">
	<label>
		<span class="screen-reader-text"><?php echo _x('Search for:', 'label', 'jointswp') ?></span>
		<input type="hidden" name="post_type" value="*" />
		<input aria-label="search" type="search" class="search-field" placeholder="<?php echo esc_attr_x('Search...', 'jointswp') ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x('Search for:', 'jointswp') ?>" />
	</label>
	<input class="search-arrow-icon" type="submit" value=" " alt="Search" style="background-image:url(<?php echo get_template_directory_uri(); ?>/assets/images/icons/searchicon.svg)" />
</form>