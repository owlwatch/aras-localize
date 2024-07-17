<?php
$site_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$current_url = esc_url(home_url(add_query_arg(array(), $wp->request)));

$labelterms = get_the_terms(get_the_ID(), 'format');
if ($labelterms && !is_wp_error($labelterms)) {
	$term = array_shift($labelterms);
	$cta_label = get_field('format_cta_label', $term);
	if (str_contains($site_url, '/ja-jp/')) {
		$cta_label = get_field('format_cta_label_japanese', $term) ?: $cta_label;
	} elseif (str_contains($site_url, '/fr-fr/')) {
		$cta_label = get_field('format_cta_label_french', $term) ?: $cta_label;
	} elseif (str_contains($site_url, '/de-de/')) {
		$cta_label = get_field('format_cta_label_german', $term) ?: $cta_label;
	}
} else {
	$cta_label = '';
}
?>
<?php if (get_the_term_list(get_the_ID(), 'format')) :
	$nameterms = wp_get_post_terms(get_the_ID(), 'format');
	$formatted_terms = array();
	foreach ($nameterms as $term) {
		$format_slug = $term->slug;
		$format_name = $term->name;
		$term_link = add_query_arg('format', $format_slug, $current_url);
		if (str_contains($site_url, '/ja-jp/')) {
			$format_name = get_field('cat_label_japanese', 'format_' . $term->term_id);
		} elseif (str_contains($site_url, '/fr-fr/')) {
			$format_name = get_field('cat_label_french', 'format_' . $term->term_id);
		} elseif (str_contains($site_url, '/de-de/')) {
			$format_name = get_field('cat_label_german', 'format_' . $term->term_id);
		}
		if ($format_name) {
			$formatted_terms[] = '<a aria-label="' . esc_html($format_name) . '"  href="' . esc_url($term_link) . '">' . esc_html($format_name) . '</a>';
		} else {
			$formatted_terms[] = '<a aria-label="' . esc_html($term->name) . '"  href="' . esc_url($term_link) . '">' . esc_html($term->name) . '</a>';
		}
	}
	$formatlist = implode(', ', $formatted_terms);
else :
	$formatlist = '';
endif;
?>
<?php $perrow = ('small-12 medium-6 large-4'); ?>

<article class="cell cell-card blog-item-cell bordercard <?php echo $perrow; ?> " id="post-<?php the_ID(); ?>" role="article">
	<div class="card-container">

		<?php if (get_field('external_url')) : ?>
			<a class="card-image-container" aria-label="<?php the_title_attribute() ?>" title="<?php the_title_attribute() ?>" href="<?php echo get_field('external_url'); ?>" target="_blank">
				<?php if (has_post_thumbnail('')) : ?>
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
					<img class="card-image" src="<?php echo $image_url[0]; ?>" srcset="<?php echo esc_attr($image_srcset); ?>" alt="<?php echo $image_alt; ?>" width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" loading="<?php echo $image_loading; ?>" decoding="<?php echo $image_decoding; ?>" />
				<?php else : ?>
					<img class="card-image" src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholders/resource-placeholder.svg" alt="<?php the_title(''); ?>">
				<?php endif; ?>
			</a>
		<?php else : ?>
			<a class="card-image-container" aria-label="<?php the_title_attribute() ?>" title="<?php the_title_attribute() ?>" href="<?php the_permalink() ?>">
				<?php if (has_post_thumbnail('')) : ?>
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
					<img class="card-image" src="<?php echo $image_url[0]; ?>" srcset="<?php echo esc_attr($image_srcset); ?>" alt="<?php echo $image_alt; ?>" width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" loading="<?php echo $image_loading; ?>" decoding="<?php echo $image_decoding; ?>" />
				<?php else : ?>
					<img class="card-image" src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholders/resource-placeholder.svg" alt="<?php the_title(''); ?>">
				<?php endif; ?>
			</a>
		<?php endif; ?>

		<div class="card-content-container">

			<h6 class="card-subhead"><?php if ($formatlist) : ?><?php echo $formatlist; ?> | <?php endif; ?><?php echo get_the_time('F j, Y'); ?>
			</h6>
			<?php if (get_field('external_url')) : ?>
				<a aria-label="<?php the_title_attribute() ?>" class="card-headline-a" title="<?php the_title_attribute() ?>" href="<?php echo get_field('external_url'); ?>" target="_blank">
				<?php else : ?>
					<a aria-label="<?php the_title_attribute() ?>" class="card-headline-a" title="<?php the_title_attribute() ?>" href="<?php the_permalink() ?>">
					<?php endif; ?>
					<?php if (get_field('hero_headline')) : ?>
						<h3 class="card-headline"><?php echo get_field('hero_headline'); ?></h3>
					<?php else : ?>
						<h3 class="card-headline"><?php the_title(''); ?></h3>
					<?php endif; ?>
					</a>
					<span class="card-link card-link--buffer">
						<?php if ($cta_label) {
							echo esc_html($cta_label) . '&nbsp;→';
						} else {
							echo 'Read More&nbsp;→';
						}
						?>
					</span>
					<div class="card-bottom">
						<?php if (get_field('external_url')) : ?>
							<a aria-label="<?php the_title_attribute() ?>" class="card-link" title="<?php the_title_attribute() ?>" href="<?php echo get_field('external_url'); ?>" target="_blank">
							<?php else : ?>
								<a aria-label="<?php the_title_attribute() ?>" class="card-link" title="<?php the_title_attribute() ?>" href="<?php the_permalink() ?>">
								<?php endif; ?>
								<?php if ($cta_label) {
									echo esc_html($cta_label) . '&nbsp;→';
								} else {
									echo 'Read More&nbsp;→';
								}
								?>
								<?php if (get_field('external_url')) : ?>
								</a>
							<?php else : ?>
							</a>
						<?php endif; ?>
					</div>
		</div>
	</div>
</article> <!-- end article -->