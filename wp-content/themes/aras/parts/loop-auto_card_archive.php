<?php
$term = '';
if (get_sub_field('content_item')) :
	$post = get_sub_field('content_item');
	if ($post) :
		setup_postdata($post);
		$post_type = get_post_type($post);
	endif;
else :
	$post_type = get_post_type();
endif;
?>

<?php if ($post_type == 'post') : ?>
	<?php
	if (get_field('post_cta_label', 'option')) {
		$cta_label = get_field('post_cta_label', 'option');
	} else {
		$cta_label = '';
	}
	?>

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
					<img class="card-image" src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholders/blog-placeholder.svg" alt="<?php the_title(''); ?>">
				<?php endif; ?>
			</a>
		<?php else : ?>
			<a class="card-image-container" aria-label="<?php the_title_attribute() ?>" title="<?php the_title_attribute() ?>" href="<?php the_permalink() ?>">
				<?php if (has_post_thumbnail('')) : ?>
					<?php
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
					<img class="card-image" src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholders/blog-placeholder.svg" alt="<?php the_title(''); ?>">
				<?php endif; ?>
			</a>
		<?php endif; ?>

		<div class="card-content-container">
			<h6 class="card-subhead">
				Blog
			</h6>
			<a aria-label="<?php the_title_attribute() ?>" class="card-headline-a" title="<?php the_title_attribute() ?>" href="<?php the_permalink() ?>">
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
					echo 'Read the Blog&nbsp;→';
				}
				?>
			</span>
			<div class="card-bottom">
				<a aria-label="<?php the_title_attribute() ?>" class="card-link" title="<?php the_title_attribute() ?>" href="<?php the_permalink() ?>">
					<?php if ($cta_label) {
						echo esc_html($cta_label) . '&nbsp;→';
					} else {
						echo 'Read the Blog&nbsp;→';
					}
					?>
				</a>
			</div>
		</div>

	</div>
<?php elseif ($post_type == 'resource') : ?>

	<?php
	$site_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$cta_label = '';
	$cat_label = '';
	$labelterms = get_the_terms(get_the_ID(), 'format');
	if ($labelterms && !is_wp_error($labelterms)) {
		$term = reset($labelterms);
		if (get_field('format_cta_label', $term)) {
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

		$cat_label = $term->name;
		if (str_contains($site_url, '/ja-jp/')) {
			$cat_label = get_field('cat_label_japanese', $term) ?: $cat_label;
		} elseif (str_contains($site_url, '/fr-fr/')) {
			$cat_label = get_field('cat_label_french', $term) ?: $cat_label;
		} elseif (str_contains($site_url, '/de-de/')) {
			$cat_label = get_field('cat_label_german', $term) ?: $cat_label;
		}
	}

	?>
	<div class="card-container">
		<?php if (get_field('external_url')) : ?>
			<a class="card-image-container" aria-label="<?php the_title_attribute() ?>" title="<?php the_title_attribute() ?>" href="<?php echo get_field('external_url'); ?>" target="_blank">
				<?php if (has_post_thumbnail('')) : ?>
					<?php
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
			<?php if (get_the_term_list(get_the_ID(), 'format')) : ?>
				<h6 class="card-subhead">
					<?php echo $cat_label; ?>
				</h6>
			<?php endif; ?>
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

<?php elseif ($post_type == 'event') : ?>
	<?php
	$site_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$labelterms = get_the_terms(get_the_ID(), 'event_type');
	$cta_label = '';
	if ($labelterms && !is_wp_error($labelterms)) {
		$term = array_shift($labelterms);
		if (get_field('format_cta_label', $term)) {
			$cta_label = get_field('format_cta_label', $term);
			if (str_contains($site_url, '/ja-jp/')) {
				$cta_label = get_field('cat_label_japanese', $term) ?: $cta_label;
			} elseif (str_contains($site_url, '/fr-fr/')) {
				$cta_label = get_field('format_cta_label_french', $term) ?: $cta_label;
			} elseif (str_contains($site_url, '/de-de/')) {
				$cta_label = get_field('format_cta_label_german', $term) ?: $cta_label;
			}
		} else {
			$cta_label = '';
		}
	}
	?>

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
			<h6 class="card-subhead">
				<?php if (get_the_term_list(get_the_ID(), 'event_type')) : ?><?php echo get_the_term_list(get_the_ID(), 'event_type', '', ', ', ''); ?><?php if (!get_field('hide_date_on_listing') || get_field('event_time')) : ?> | <?php endif; ?><?php endif; ?><?php if (get_field('event_date')) : ?><?php if (!get_field('hide_date_on_listing')) : ?><?php echo get_field('event_date'); ?><?php endif; ?><?php if (get_field('event_time')) : ?><?php if (!get_field('hide_date_on_listing')) : ?>,<?php endif; ?> <?php echo get_field('event_time'); ?><?php endif; ?><?php endif; ?>
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
							echo 'Register&nbsp;→';
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
									echo 'Register&nbsp;→';
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

<?php elseif ($post_type == 'news') : ?>
	<?php if (get_field('news_content_type') == 'external') {
		if (get_field('external_url')) {
			$cta = 'Read the Article';
		} else {
			$cta = 'Read Press Release';
		}
	} else {
		$cta = 'Read Press Release';
	} ?>
	<div class="card-container">
		<a class="card-image-container" aria-label="<?php the_title_attribute() ?>" title="<?php the_title_attribute() ?>" href="<?php the_permalink() ?>">
			<?php if (has_post_thumbnail('')) : ?>
				<?php
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
		<div class="card-content-container">
			<a aria-label="<?php the_title_attribute() ?>" class="card-headline-a" title="<?php the_title_attribute() ?>" href="<?php the_permalink() ?>">
				<?php if (get_field('hero_headline')) : ?>
					<h3 class="card-headline"><?php echo get_field('hero_headline'); ?></h3>
				<?php else : ?>
					<h3 class="card-headline"><?php the_title(''); ?></h3>
				<?php endif; ?>
			</a>
			<span class="card-link card-link--buffer">
				<?php echo $cta; ?>&nbsp;→
			</span>
			<div class="card-bottom">
				<a aria-label="<?php the_title_attribute() ?>" class="card-link" title="<?php the_title_attribute() ?>" href="<?php the_permalink() ?>">
					<?php echo $cta; ?>&nbsp;→
				</a>
			</div>
		</div>
	</div>

<?php else : ?>
	<div class="card-container">
		<a class="card-image-container" aria-label="<?php the_title_attribute() ?>" title="<?php the_title_attribute() ?>" href="<?php the_permalink() ?>">
			<?php if (has_post_thumbnail('')) : ?>
				<?php
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
		<div class="card-content-container">
			<a aria-label="<?php the_title_attribute() ?>" class="card-headline-a" title="<?php the_title_attribute() ?>" href="<?php the_permalink() ?>">
				<?php if (get_field('hero_headline')) : ?>
					<h3 class="card-headline"><?php echo get_field('hero_headline'); ?></h3>
				<?php else : ?>
					<h3 class="card-headline"><?php the_title(''); ?></h3>
				<?php endif; ?>
			</a>
			<span class="card-link card-link--buffer">
				Read More&nbsp;→
			</span>
			<div class="card-bottom">
				<a aria-label="<?php the_title_attribute() ?>" class="card-link" title="<?php the_title_attribute() ?>" href="<?php the_permalink() ?>">
					Read More&nbsp;→
				</a>
			</div>
		</div>
	</div>
<?php endif; ?>