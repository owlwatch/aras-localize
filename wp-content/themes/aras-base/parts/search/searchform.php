<?php
/* The template for displaying search form */
?>

<form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>">
	<span class="screen-reader-text"><?php echo _x('Search for:', 'label', 'jointswp') ?></span>
	<input aria-label="search" type="search" class="search-field" placeholder="" value="<?php echo get_search_query() ?>" name="s" title="Search for:" />
	<input type="submit" class="search-submit button" value="<?php echo esc_attr_x('Search', '') ?>" />
</form>