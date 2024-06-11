<?php
$site_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$current_url = esc_url(home_url(add_query_arg(array(), $wp->request)));
//if (get_the_term_list(get_the_ID(), 'category')) :
//	$nameterms = wp_get_post_terms(get_the_ID(), 'category');
//	$formatted_terms = array();
//	foreach ($nameterms as $term) {
//		$cat_slug = $term->slug;
//		if ($cat_slug != 'uncategorized' && $cat_slug != 'uncategorized-fr-fr' && $cat_slug != 'uncategorized-de-de' && $cat_slug != 'uncategorized-ja-jp') {
//			$cat_url = get_term_link($term);
//			$cat_name =  $term->name;
//			if (str_contains($site_url, '/ja-jp/')) {
//				$cat_url = str_replace('/en/', '/ja-jp/', $cat_url);
//				$cat_name = get_field('cat_label_japanese', 'format_' . $term->term_id);
//			} elseif (str_contains($site_url, '/fr-fr/')) {
//				$cat_url = str_replace('/en/', '/fr-fr/', $cat_url);
//				$cat_name = get_field('cat_label_french', 'format_' . $term->term_id);
//			} elseif (str_contains($site_url, '/de-de/')) {
//				$cat_url = str_replace('/en/', '/de-de/', $cat_url);
//				$cat_name = get_field('cat_label_german', 'format_' . $term->term_id);
//			}
//			if ($cat_name) {
//				$formatted_terms[] = '<a aria-label="' . esc_html($cat_name) . '" href="' . esc_url($cat_url) . '">' . esc_html($cat_name) . '</a>';
//			} else {
//				$formatted_terms[] = '<a aria-label="' . esc_html($term->name) . '" href="' . esc_url($cat_url) . '">' . esc_html($term->name) . '</a>';
//			}
//		}
//	}
//	$catlist = implode(', ', $formatted_terms);
//else :
//	$catlist = '';
//endif;
$cta_label = get_field('post_cta_label', 'option');
?>
<?php $perrow = ('small-12 medium-6 large-4'); ?>

<article class="cell cell-card blog-item-cell bordercard <?php echo $perrow; ?>" id="post-<?php the_ID(); ?>" role="article">

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
		<?php endif; ?>

		<div class="card-content-container">
			<?/*
			<h6 class="card-subhead"><?php if ($catlist) : ?><?php echo $catlist; ?> | <?php endif; ?><?php echo get_the_time('F j, Y'); ?></h6>
				*/ ?>
			<h6 class="card-subhead"><?php echo get_the_time('F j, Y'); ?> | <a aria-label="<?php the_author(); ?>" href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?></h6>

			<a aria-label="<?php the_title_attribute() ?>" class="card-headline-a" title="<?php the_title_attribute() ?>" href="<?php the_permalink() ?>">
				<?php if (get_field('hero_headline')) : ?>
					<h3 class="card-headline"><?php echo get_field('hero_headline'); ?></h3>
				<?php else : ?>
					<h3 class="card-headline"><?php the_title(''); ?></h3>
				<?php endif; ?>
			</a>
			<?/*
			<h6 class="card-subhead author">
				By <a aria-label="<?php the_author(); ?>" href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?> </a>
			</h6>
			*/ ?>
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
</article> <!-- end article -->