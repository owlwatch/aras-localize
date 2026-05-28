<?php
/* The template for displaying search form */
?>

<form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>">
	<span class="screen-reader-text"><?php echo esc_html_x('Search for:', 'label', 'aras'); ?></span>
	<input aria-label="<?php echo esc_attr__('Search', 'aras'); ?>" type="search" class="search-field" placeholder="" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x('Search for:', 'label', 'aras'); ?>" />
	<input type="submit" class="search-submit button" value="<?php echo esc_attr_x('Search', 'submit button', 'aras'); ?>" />
</form>