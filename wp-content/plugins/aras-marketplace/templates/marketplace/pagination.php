<?php
// return if there is no need for pagination
if ($GLOBALS['wp_query']->max_num_pages <= 1) {
	return;
}
?>
<div class="mp-pagination">
	<?php
	// output default wordpress number pagination
	the_posts_pagination([
		'prev_text' => __('Previous', 'aras-marketplace'),
		'next_text' => __('Next', 'aras-marketplace'),
		'screen_reader_text' => __('Posts navigation', 'aras-marketplace'),
	]);
	?>
</div>
