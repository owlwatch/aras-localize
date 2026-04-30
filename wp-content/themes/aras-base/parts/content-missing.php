<?php

/**
 * The template part for displaying a message that posts cannot be found
 */
?>
<div class="cell post-not-found">
	<?php if (is_search()) : ?>
		<h2><?php esc_html_e('Sorry, no results.', 'aras'); ?></h2>
		<p><?php esc_html_e('Please try your search again using different terms.', 'aras'); ?></p>
	<?php else : ?>
		<h2><?php esc_html_e('Sorry, no results.', 'aras'); ?></h2>
		<p><?php esc_html_e('Please try your search again using different terms.', 'aras'); ?></p>
		<?php // get_search_form(); 
		?>
	<?php endif; ?>
</div>