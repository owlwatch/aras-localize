<?php
$link = get_post_type_archive_link('mp-solution');
$logo = get_field('marketplace_logo', 'option');
?>
<section class="archive-hero-banner mp-banner">
	<div class="grid-container">
		<div class="grid-x grid-padding-x">
			<div class="cell hero-content">
				<a href="<?php echo $link; ?>" class="mp-banner__logo">
					<?php echo wp_get_attachment_image($logo['ID'], 'medium'); ?>
					<span>
						<?php echo get_field('marketplace_title', 'option'); ?>
					</span>
				</a>
			</div>
		</div>
	</div>
</section>