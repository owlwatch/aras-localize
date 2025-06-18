<?php

/**
 * The template part for displaying a message that posts cannot be found
 */
?>
<div class="cell post-not-found">
	<?php if (is_search()) : ?>
		<h2>Sorry, no results.</h2>
		<p>Please try your search again using different terms.</p>
	<?php else : ?>
		<h2>Sorry, no results.</h2>
		<p>Please try your search again using different terms.</p>
		<?php // get_search_form(); 
		?>
	<?php endif; ?>
</div>