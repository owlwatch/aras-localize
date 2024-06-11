<?php if (get_field('news_content_type') == 'external') {
	if (get_field('external_url')) {
		if (get_field('news_archive_external_link_cta', 'option')) {
			$cta = get_field('news_archive_external_link_cta', 'option');
		} else {
			$cta = 'Read the Article';
		}
	} else {
		if (get_field('news_archive_internal_link_cta', 'option')) {
			$cta = get_field('news_archive_internal_link_cta', 'option');
		} else {
			$cta = 'Read Press Release';
		}
	}
} else {
	if (get_field('news_archive_internal_link_cta', 'option')) {
		$cta = get_field('news_archive_internal_link_cta', 'option');
	} else {
		$cta = 'Read Press Release';
	}
} ?>
<article class="cell small-12 news-item" id="post-<?php the_ID(); ?>" role="article">
	<?php if (get_field('news_content_type') == 'external') : ?>
		<?php if (get_field('external_url')) : ?>
			<?php $href = get_field('external_url'); ?>
			<?php $target = '_blank' ?>
		<?php else : ?>
			<?php $href = the_permalink() ?>
			<?php $target = '_self' ?>
		<?php endif; ?>
	<?php else : ?>
		<?php $href = get_the_permalink() ?>
		<?php $target = '_self' ?>
	<?php endif; ?>
	<?/*
	<?php if (has_post_thumbnail('')) : ?>
		<a aria-label="<?php the_title_attribute() ?>" title="<?php the_title_attribute() ?>" href="<?= "$href" ?>" target="<?= "$target" ?>">
					<?php //the_post_thumbnail('full');
					$image_id = get_post_thumbnail_id();
					$image_url = wp_get_attachment_image_src($image_id, 'full'); // 'full' size image
					if (get_post_meta($image_id, '_wp_attachment_image_alt', true) != '') {
						$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
					} else {
						$image_alt = get_the_title('');
					}
					$image_data = wp_get_attachment_metadata($image_id);
					$image_width = $image_data['width'];
					$image_height = $image_data['height'];
					$image_loading = 'lazy';
					$image_decoding = 'async';
					$image_srcset = wp_get_attachment_image_srcset($image_id, 'full'); // 'full' size image srcset
					?>
					<img src="<?php echo $image_url[0]; ?>" srcset="<?php echo esc_attr($image_srcset); ?>" alt="<?php echo $image_alt; ?>" width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" loading="<?php echo $image_loading; ?>" decoding="<?php echo $image_decoding; ?>" />
		</a>
	<?php else : ?>
		<a aria-label="<?php the_title_attribute() ?>" title="<?php the_title_attribute() ?>" href="<?= "$href" ?>" target="<?= "$target" ?>">
			<img src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholders/news-placeholder.svg" alt="<?php the_title(''); ?>">
		</a>
	<?php endif; ?>
	*/ ?>
	<div class="news-card-content">
		<h6 class="card-date">
			<?php echo get_the_time('F j, Y'); ?>
		</h6>
		<a aria-label="<?php the_title_attribute() ?>" title="<?php the_title_attribute() ?>" href="<?= "$href" ?>" target="<?= "$target" ?>">
			<h3 class="card-headline"><?php the_title(''); ?></h3>
		</a>
		<?/*
		<p class="card-subhead">
			<?php if (get_field('news_subtitle')) : ?>
				<?php echo get_field('news_subtitle'); ?>
			<?php endif; ?>
		</p>
		*/ ?>
		<a aria-label="<?php the_title_attribute() ?>" class="aras-button" title="<?php the_title_attribute() ?>" href="<?= "$href" ?>" target="<?= "$target" ?>">
			<?php echo $cta; ?>
		</a>
	</div>
</article> <!-- end article -->