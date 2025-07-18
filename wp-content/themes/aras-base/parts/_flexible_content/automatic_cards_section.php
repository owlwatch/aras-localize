<?php if (get_sub_field('show_for_this_language') != 'hide') : ?>
	<?php $modnum = get_row_index(); ?>
	<?php if (get_sub_field('anchor_link')) : ?>
		<?php $anchor = ('id="' . get_sub_field('anchor_link') . '"'); ?>
	<?php else : ?>
		<?php $anchor = ('id="autocardsection-' . $modnum . '"'); ?>
	<?php endif; ?>
	<?php
	$background_color = get_sub_field('background_color');
	$top_padding = get_sub_field('top_padding_amount');
	$bottom_padding = get_sub_field('bottom_padding_amount');
	$bg_color = '';
	$toppadding = '';
	$bottompadding = '';
	switch ($background_color) {
		case 'transparent':
			$bg_color = 'bg-transparent';
			break;
		case 'white':
			$bg_color = 'bg-white';
			break;
		case 'grey':
			$bg_color = 'bg-grey';
			break;
		case 'dblue':
			$bg_color = 'bg-dblue';
			break;
		case 'whitetogrey':
			$bg_color = 'bg-whitetogrey';
			break;
		case 'greytowarm':
			$bg_color = 'bg-greytowarm';
			break;
		default:
			$bg_color = 'bg-transparent';
	}
	switch ($top_padding) {
		case 'large':
			$toppadding = 'largetoppadding';
			break;
		case 'medium':
			$toppadding = 'mediumtoppadding';
			break;
		case 'small':
			$toppadding = 'smalltoppadding';
			break;
		case 'none':
			$toppadding = 'notoppadding';
			break;
		default:
			$toppadding = 'mediumtoppadding';
	}
	switch ($bottom_padding) {
		case 'large':
			$bottompadding = 'largebottompadding';
			break;
		case 'medium':
			$bottompadding = 'mediumbottompadding';
			break;
		case 'small':
			$bottompadding = 'smallbottompadding';
			break;
		case 'none':
			$bottompadding = 'nobottompadding';
			break;
		default:
			$bottompadding = 'mediumbottompadding';
	}
	?>
	<?php if (get_sub_field('cards_per_row') == 'two') : ?>
		<?php $perrow = ('small-12 medium-6 large-6'); ?>
	<?php elseif (get_sub_field('cards_per_row') == 'three') : ?>
		<?php $perrow = ('small-12 medium-4 large-4'); ?>
	<?php elseif (get_sub_field('cards_per_row') == 'four') : ?>
		<?php $perrow = ('small-12 medium-6 large-3'); ?>
	<?php elseif (get_sub_field('cards_per_row') == 'slider') : ?>
		<?php $perrow = ('card-slick'); ?>
	<?php else : ?>
		<?php $perrow = ('small-12 medium-4'); ?>
	<?php endif; ?>

	<?php if (get_sub_field('horizontal_alignment') == 'left') : ?>
		<?php $horiz = ('align-left'); ?>
	<?php elseif (get_sub_field('horizontal_alignment') == 'center') : ?>
		<?php $horiz = ('align-center'); ?>
	<?php else : ?>
		<?php $horiz = ('align-left'); ?>
	<?php endif; ?>

	<?php $card_styles = get_sub_field('card_style_options'); ?>
	<?php if ($card_styles && in_array('highlight', $card_styles)) : ?>
		<?php if (get_sub_field('highlight_style') == 'gradient') : ?>
			<?php $highlight = 'card-highlight-gradient' ?>
		<?php else : ?>
			<?php $highlight = 'card-highlight' ?>
		<?php endif; ?>
	<?php else : ?>
		<?php $highlight = '' ?>
	<?php endif; ?>
	<?php if ($card_styles && in_array('connector', $card_styles)) : ?>
		<?php $connector = 'card-connector' ?>
	<?php else : ?>
		<?php $connector = '' ?>
	<?php endif; ?>
	<?php if ($card_styles && in_array('images', $card_styles)) : ?>
		<?php $include_images = '' ?>
	<?php else : ?>
		<?php $include_images = 'hide-images' ?>
	<?php endif; ?>

	<?php if (get_sub_field('highlight_style') == 'gradient') : ?>
		<?php $highlight_type = 'highlight-gradient' ?>
	<?php else : ?>
		<?php $highlight_type = '' ?>
	<?php endif; ?>
	<?php $text_color = get_sub_field('text_color') ?: 'text-dark' ?>
	<section class="cards-section automatic-cards-section <?= "$toppadding $bottompadding $bg_color $text_color $connector $include_images $highlight_type" ?>" <?= "$anchor" ?>>
		<?php get_template_part('parts/_template_parts/background_visual'); ?>
		<div class="grid-container">
			<?php if (get_sub_field('content_before')) : ?>
				<div class="grid-x grid-padding-x <?php if (get_sub_field('content_before_position') == 'center') : ?>align-center<?php endif; ?>">
					<div class="cell small-12 medium-11 large-10 content-before">
						<div class="wysiwyg-content"><?php echo get_sub_field('content_before'); ?></div>
					</div>
				</div>
			<?php endif; ?>
			<?php if (get_sub_field('content_selection_type') == 'individual') : ?>
				<?php /* Slick Slider */ if ($perrow == 'card-slick') : ?>
					<?php if (have_rows('cards')) : ?>
						<div class="card-slider-slick <?php echo $horiz; ?>">
							<?php while (have_rows('cards')) : the_row(); ?>

								<?php if (get_sub_field('show_card_for_this_language') != 'hide') : ?>
									<article class="cell cell-card blog-item-cell bordercard <?php echo $perrow; ?>" id="post-<?php the_ID(); ?>" role="article">
										<?php get_template_part('parts/loop', 'auto_card_archive'); ?>
									</article>
								<?php endif; ?>

								<?php wp_reset_postdata(); ?>
							<?php endwhile; ?>
						</div>
						<?php if (get_sub_field('slider_arrows') == 'show') : ?>
							<div class="card-arrows"></div>
						<?php endif; ?>
					<?php endif; ?>
				<?php /* Standard Grid */ else : ?>
					<?php if (have_rows('cards')) : ?>
						<div class="grid-x grid-margin-x <?php echo $horiz; ?>">
							<?php while (have_rows('cards')) : the_row(); ?>
								<?php if (get_sub_field('show_card_for_this_language') != 'hide') : ?>
									<article class="cell cell-card blog-item-cell bordercard <?php echo $perrow; ?>" id="post-<?php the_ID(); ?>" role="article">
										<?php get_template_part('parts/loop', 'auto_card_archive'); ?>
									</article>
								<?php endif; ?>
								<?php wp_reset_postdata(); ?>
							<?php endwhile; ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>


			<?php elseif (get_sub_field('content_selection_type') == 'group') : ?>
				<? if (get_sub_field('number_of_items')) {
					$posts_per_page = get_sub_field('number_of_items');
				} else {
					$posts_per_page = '3';
				} ?>
				<?php if (get_sub_field('content_filtering_type') == 'content_type') : ?>
					<?/* Content filters by post type */ ?>
					<?php $content_type_selector = get_sub_field('content_type_selector');
					$post_type_array = array();
					if (is_array($content_type_selector) && in_array('blog', $content_type_selector)) {
						$post_type_array[] = 'post';
					}
					if (is_array($content_type_selector) && in_array('resource', $content_type_selector)) {
						$post_type_array[] = 'resource';
					}
					if (is_array($content_type_selector) && in_array('news', $content_type_selector)) {
						$post_type_array[] = 'news';
					}
					if (is_array($content_type_selector) && in_array('event', $content_type_selector)) {
						$post_type_array[] = 'event';
					}
					?>
					<?php $tax_query = array(); ?>
				<?php elseif (get_sub_field('content_filtering_type') == 'taxonomy') : ?>
					<?/* Content filters by taxonomy */ ?>
					<?php
					$post_type_array = array('post', 'resource', 'event', 'news');
					?>
					<?php
					$taxonomy_type_selector = get_sub_field('taxonomy_type_selector');
					$tax_query = array(
						'relation' => 'OR',
					);
					if (is_array($taxonomy_type_selector)) {
						if (in_array('category', $taxonomy_type_selector)) {
							$terms = get_sub_field('category_selector');
							$category_terms = array();
							foreach ($terms as $term) {
								$category_terms[] = $term->slug;
							}
							$tax_query[] = array(
								'taxonomy' => 'category',
								'field'    => 'slug',
								'terms'    => $category_terms,
							);
						}
						if (in_array('post_tag', $taxonomy_type_selector)) {
							$terms = get_sub_field('tag_selector');
							$tag_terms = array();
							foreach ($terms as $term) {
								$tag_terms[] = $term->slug;
							}
							$tax_query[] = array(
								'taxonomy' => 'post_tag',
								'field'    => 'slug',
								'terms'    => $tag_terms,
							);
						}
						if (in_array('featured-blog', $taxonomy_type_selector)) {
							$terms = get_sub_field('featured_blog_selector');
							$featured_blog_terms = array();
							foreach ($terms as $term) {
								$featured_blog_terms[] = $term->slug;
							}
							$tax_query[] = array(
								'taxonomy' => 'featured-blog',
								'field'    => 'slug',
								'terms'    => $featured_blog_terms,
							);
						}
						if (in_array('application', $taxonomy_type_selector)) {
							$terms = get_sub_field('resource_application_selector');
							$application_terms = array();
							foreach ($terms as $term) {
								$application_terms[] = $term->slug;
							}
							$tax_query[] = array(
								'taxonomy' => 'application',
								'field'    => 'slug',
								'terms'    => $application_terms,
							);
						}
						if (in_array('format', $taxonomy_type_selector)) {
							$terms = get_sub_field('resource_format_selector');
							$format_terms = array();
							foreach ($terms as $term) {
								$format_terms[] = $term->slug;
							}
							$tax_query[] = array(
								'taxonomy' => 'format',
								'field'    => 'slug',
								'terms'    => $format_terms,
							);
						}
						if (in_array('industry', $taxonomy_type_selector)) {
							$terms = get_sub_field('resource_industry_selector');
							$industry_terms = array();
							foreach ($terms as $term) {
								$industry_terms[] = $term->slug;
							}
							$tax_query[] = array(
								'taxonomy' => 'industry',
								'field'    => 'slug',
								'terms'    => $industry_terms,
							);
						}
						//if (in_array('location', $taxonomy_type_selector)) {
						//	$terms = get_sub_field('resource_location_selector');
						//	$location_terms = array();
						//	foreach ($terms as $term) {
						//		$location_terms[] = $term->slug;
						//	}
						//	$tax_query[] = array(
						//		'taxonomy' => 'location',
						//		'field'    => 'slug',
						//		'terms'    => $location_terms,
						//	);
						//}
						if (in_array('topic', $taxonomy_type_selector)) {
							$terms = get_sub_field('resource_topic_selector');
							$topic_terms = array();
							foreach ($terms as $term) {
								$topic_terms[] = $term->slug;
							}
							$tax_query[] = array(
								'taxonomy' => 'topic',
								'field'    => 'slug',
								'terms'    => $topic_terms,
							);
						}
						if (in_array('featured-resource', $taxonomy_type_selector)) {
							$terms = get_sub_field('featured_resource_selector');
							$featured_resource_terms = array();
							foreach ($terms as $term) {
								$featured_resource_terms[] = $term->slug;
							}
							$tax_query[] = array(
								'taxonomy' => 'featured-resource',
								'field'    => 'slug',
								'terms'    => $featured_resource_terms,
							);
						}

						if (in_array('event_region', $taxonomy_type_selector)) {
							$terms = get_sub_field('event_region_selector');
							$event_region_terms = array();
							foreach ($terms as $term) {
								$event_region_terms[] = $term->slug;
							}
							$tax_query[] = array(
								'taxonomy' => 'event_region',
								'field'    => 'slug',
								'terms'    => $event_region_terms,
							);
						}
						if (in_array('event_type', $taxonomy_type_selector)) {
							$terms = get_sub_field('event_type_selector');
							$event_type_terms = array();
							foreach ($terms as $term) {
								$event_type_terms[] = $term->slug;
							}
							$tax_query[] = array(
								'taxonomy' => 'event_type',
								'field'    => 'slug',
								'terms'    => $event_type_terms,
							);
						}
						if (in_array('news_type', $taxonomy_type_selector)) {
							$terms = get_sub_field('news_type_selector');
							$news_type_terms = array();
							foreach ($terms as $term) {
								$news_type_terms[] = $term->slug;
							}
							$tax_query[] = array(
								'taxonomy' => 'news_type',
								'field'    => 'slug',
								'terms'    => $news_type_terms,
							);
						}
					}
					?>

				<?php endif; ?>
				<?php
				$is_event_only = !empty($post_type_array) && count($post_type_array) === 1 && $post_type_array[0] === 'event';
				if ($is_event_only) {
					$current_date = date('Ymd');
					$args = array(
						'post_type'      => $post_type_array,
						'posts_per_page' => $posts_per_page,
						'post_status'    => 'publish',
						'order'          => 'ASC',
						'orderby' => 'meta_value',
						'meta_key' => 'event_date',
						'meta_query' => array(
							array(
								'key' => 'event_date',
								'value' => $current_date,
								'compare' => '>=',
								'type' => 'DATE'
							)
						)
					);
				} else {
					$args = array(
						'post_type'      => $post_type_array,
						'posts_per_page' => $posts_per_page,
						'post_status'    => 'publish',
						'tax_query'      => $tax_query,
						'orderby'        => 'date',
						'order'          => 'DESC',
						'suppress_filters' => false,
					);
				}
				$query = new WP_Query($args); ?>
				<?php /* Slick Slider */ if ($perrow == 'card-slick') : ?>
					<?php if ($query->have_posts()) : ?>
						<div class="card-slider-slick <?php echo $horiz; ?>">
							<?php while ($query->have_posts()) : $query->the_post(); ?>
								<article class="cell cell-card blog-item-cell bordercard <?php echo $perrow; ?>" id="post-<?php the_ID(); ?>" role="article">
									<?php get_template_part('parts/loop', 'auto_card_archive'); ?>
								</article>
							<?php endwhile;
							wp_reset_postdata();
							?>
						</div>
						<?php if (get_sub_field('slider_arrows') == 'show') : ?>
							<div class="card-arrows"></div>
						<?php endif; ?>
					<?php endif; ?>
				<?php /* Standard Grid */ else : ?>
					<?php if ($query->have_posts()) : ?>
						<div class="grid-x grid-margin-x <?php echo $horiz; ?>">
							<?php while ($query->have_posts()) : $query->the_post(); ?>
								<article class="cell cell-card blog-item-cell bordercard <?php echo $perrow; ?>" id="post-<?php the_ID(); ?>" role="article">
									<?php get_template_part('parts/loop', 'auto_card_archive'); ?>
								</article>
							<?php endwhile;
							wp_reset_postdata();
							?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php if (get_sub_field('buttons_after_cards')) : ?>
				<div class="card-buttons-section <?php if (get_sub_field('content_before_position') == 'center') : ?>grid-x grid-padding-x align-center<?php endif; ?>">
					<?php if (have_rows('buttons_after_cards')) : ?>
						<?php while (have_rows('buttons_after_cards')) : the_row(); ?>
							<?php $link = get_sub_field('button_link');
							if ($link) : $link_url = $link['url'];
								$link_title = $link['title'];
								$link_target = $link['target'] ? $link['target'] : '_self';
							?>
								<?php if (get_sub_field('button_style') == 'solid') : ?>
									<?php $buttontype = 'aras-button'; ?>

								<?php else : ?>
									<?php $buttontype = 'aras-button--outline'; ?>
								<?php endif; ?>
								<a aria-label="<?php echo esc_html($link_title); ?>" class="<?php echo $buttontype; ?>" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
									<?php echo esc_html($link_title); ?>
								</a>
							<?php endif; ?>
						<?php endwhile; ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</div>
	</section>
<?php endif; ?>