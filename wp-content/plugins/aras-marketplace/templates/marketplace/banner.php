<?php
$link = get_post_type_archive_link('mp-solution');
$logo = get_field('marketplace_logo', 'option');
?>
<section class="mp-banner">
	<div class="grid-container">
		<div class="grid-x grid-padding-x">
			<div class="cell hero-content mp-banner__content">
				<a href="<?php echo $link; ?>" class="card-link">
					<?php echo get_field('marketplace_title', 'option'); ?>
				</a>
			</div>
		</div>
	</div>
</section>