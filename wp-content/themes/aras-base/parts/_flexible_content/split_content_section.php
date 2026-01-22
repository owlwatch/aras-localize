<?php if (get_sub_field('show_for_this_language') != 'hide') : ?>
	<?php $modnum = (get_row_index()); ?>
	<?php if (get_sub_field('anchor_link')) : ?>
		<?php $anchor = ('id="' . get_sub_field('anchor_link') . '"'); ?>
	<?php else : ?>
		<?php $anchor = ('id="split-content-section-' . $modnum . '"'); ?>
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
			$bg_color = 'bg-white';
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
	<?php $vertical_alignment = get_sub_field('vertical_alignment');
	switch ($vertical_alignment) {
		case 'top':
			$vert = 'align-top';
			break;
		case 'middle':
			$vert = 'align-middle';
			break;
		case 'bottom':
			$vert = 'align-bottom';
			break;
		default:
			$vert = 'align-top';
	}
	?>
	<?php if (get_sub_field('order_on_mobile') == 'rightleft') {
		$leftmobile = 'small-order-2';
		$rightmobile = 'small-order-1';
	} else {
		$leftmobile = 'small-order-1';
		$rightmobile = 'small-order-2';
	} ?>
	<?php if (get_sub_field('column_sizes') == 'largeleft') {
		$leftsize = 'medium-8';
		$rightsize = 'medium-4';
	} elseif (get_sub_field('column_sizes') == 'largeright') {
		$leftsize = 'medium-4';
		$rightsize = 'medium-8';
	} else {
		$leftsize = 'medium-6';
		$rightsize = 'medium-6';
	} ?>
	<?php $text_color = get_sub_field('text_color') ?: 'text-dark' ?>
	<section class="content-section <?= "$toppadding $bottompadding $bg_color $text_color" ?>" <?= "$anchor" ?>>
		<?php get_template_part('parts/_template_parts/background_visual'); ?>
		<div class="grid-container">
			<div class="grid-x grid-margin-x <?= "$vert" ?>">

				<?php if (have_rows('left_content')) : ?>
					<?php while (have_rows('left_content')) : the_row(); ?>
						<?php $rownum = (get_row_index()); ?>
						<?php if (get_sub_field('content_type') == 'text') :
							//TEXT BLOCK
						?>
							<?php if (have_rows('text_block')) : ?>
								<?php while (have_rows('text_block')) : the_row(); ?>
									<?php if (get_sub_field('link_alignment') == 'left') : ?>
										<?php $lalign = 'text-left'; ?>
									<?php elseif (get_sub_field('link_alignment') == 'center') : ?>
										<?php $lalign = 'text-center'; ?>
									<?php else : ?>
										<?php $lalign = 'text-left'; ?>
									<?php endif; ?>
									<?php if (get_sub_field('link_type') == 'button') : ?>
										<?php $ltype = 'aras-button'; ?>
									<?php elseif (get_sub_field('link_type') == 'link') : ?>
										<?php $ltype = 'aras-link'; ?>
									<?php else : ?>
										<?php $ltype = 'aras-button'; ?>
									<?php endif; ?>
									<div class="cell text-block wysiwyg-content <?= "$leftsize $leftmobile" ?> small-12 medium-order-1">
										<?php if (get_sub_field('text_content')) : ?>
											<?php echo get_sub_field('text_content'); ?>
										<?php endif; ?>
										<?php if (get_sub_field('link_type_type') == 'popup') : ?>
											<?php if (get_sub_field('popup_label') && get_sub_field('popup_content')) : ?>
												<div class="<?php echo $lalign; ?>">
													<button aria-label="<?php echo get_sub_field('popup_label'); ?>" class="<?php echo $ltype; ?>" data-open="left_popup_<?php echo $modnum; ?>_<?php echo $rownum; ?>">
														<?php echo get_sub_field('popup_label'); ?>
													</button>
												</div>
											<?php endif; ?>
										<?php else : ?>
											<?php $link = get_sub_field('text_block_link');
											if ($link) : $link_url = $link['url'];
												$link_title = $link['title'];
												$link_target = $link['target'] ? $link['target'] : '_self';
											?>
												<div class="<?php echo $lalign; ?>">
													<a aria-label="<?php echo esc_attr($link_title); ?>" class="<?php echo $ltype; ?>" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
														<?php echo esc_html($link_title); ?>
													</a>
												</div>
											<?php endif; ?>
										<?php endif; ?>
									</div>
								<?php endwhile; ?>
							<?php endif; ?>
						<?php elseif (get_sub_field('content_type') == 'image') :
							//IMAGE BLOCK
						?>
							<?php if (have_rows('image_block')) : ?>
								<?php while (have_rows('image_block')) : the_row(); ?>
									<?php $image_options = get_sub_field('image_options'); ?>
									<?php if ($image_options && in_array('greyscale', $image_options)) : ?>
										<?php $greyscale = 'greyscale' ?>
									<?php else : ?>
										<?php $greyscale = '' ?>
									<?php endif; ?>
									<?php if ($image_options && in_array('overlay', $image_options)) : ?>
										<?php $overlay = 'overlay' ?>
									<?php else : ?>
										<?php $overlay = '' ?>
									<?php endif; ?>
									<?php if ($image_options && in_array('shadow', $image_options)) : ?>
										<?php $shadow = 'shadow' ?>
									<?php else : ?>
										<?php $shadow = '' ?>
									<?php endif; ?>
									<?php $image = get_sub_field('image');
									if (!empty($image)) : ?>
										<div class="cell image-block small-12 medium-order-1 <?= "$leftsize $leftmobile" ?> >">
											<div class="image-container <?= "$greyscale $overlay $shadow" ?>">
												<?php if ($overlay == 'overlay') : ?>
													<img class="image-overlay" src="<?php echo get_template_directory_uri(); ?>/assets/images/orange_overlay.svg" alt="orange overlay layer" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>" />
												<?php endif; ?>
												<?php $zoomable = in_array('zoomable', $image_options) ?: false; ?>
												<?php if( $zoomable ){ ?>
												<a href="<?php echo esc_url($image['url']); ?>" data-pswp-width="<?php echo $image['width']; ?>" data-pswp-height="<?php echo $image['height']; ?>" class="zoomable-image">
												<?php } ?>
												<img class="split-image" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>">
												<?php if( $zoomable ){ ?>
												</a>
												<?php } ?>
											</div>
										</div>
									<?php endif; ?>
								<?php endwhile; ?>
							<?php endif; ?>
						<?php elseif (get_sub_field('content_type') == 'video') :
							//VIDEO BLOCK
						?>
							<?php if (have_rows('video_block')) : ?>
								<?php while (have_rows('video_block')) : the_row(); ?>
									<?php $block_settings = get_sub_field('block_settings'); ?>
									<?php if ($block_settings && in_array('shadow', $block_settings)) : ?>
										<?php $shadow = 'shadow' ?>
									<?php else : ?>
										<?php $shadow = '' ?>
									<?php endif; ?>
									<?php if ((get_sub_field('video_display') != '') && (get_sub_field('poster_image') != '')) : ?>
										<?php $pop_image_options = get_sub_field('popup_image_settings'); ?>
										<?php if ($pop_image_options && in_array('greyscale', $pop_image_options)) : ?>
											<?php $greyscale = 'greyscale' ?>
										<?php else : ?>
											<?php $greyscale = '' ?>
										<?php endif; ?>
										<?php if ($pop_image_options && in_array('overlay', $pop_image_options)) : ?>
											<?php $overlay = 'overlay' ?>
										<?php else : ?>
											<?php $overlay = '' ?>
										<?php endif; ?>
										<?php if ($pop_image_options && in_array('icon', $pop_image_options)) : ?>
											<?php $icon = 'icon' ?>
										<?php else : ?>
											<?php $icon = '' ?>
										<?php endif; ?>
									<?php else : ?>
										<?php $greyscale = '' ?>
										<?php $overlay = '' ?>
										<?php $icon = '' ?>
									<?php endif; ?>

									<?php $video_features = get_sub_field('video_settings'); ?>
									<?php if ($video_features && in_array('autoplay', $video_features)) : ?>
										<?php $autoplay = '1' ?>
									<?php else : ?>
										<?php $autoplay = '0' ?>
									<?php endif; ?>
									<?php if ($video_features && in_array('loop', $video_features)) : ?>
										<?php $loop = '1' ?>
									<?php else : ?>
										<?php $loop = '0' ?>
									<?php endif; ?>
									<?php if ($video_features && in_array('controls', $video_features)) : ?>
										<?php $controls = '1' ?>
									<?php else : ?>
										<?php $controls = '0' ?>
									<?php endif; ?>

									<?php $image = get_sub_field('poster_image');
									if (!empty($image)) : ?>
										<?php $poster = ($image['url']); ?>
									<?php else : ?>
										<?php $poster = (''); ?>
									<?php endif; ?>
									<div class="cell video-block small-12 medium-order-1 <?= "$leftsize $leftmobile" ?>">
										<?php if (get_sub_field('video_display')) :	?>
											<div class="video-container <?= "$shadow $greyscale $overlay $icon" ?>">

												<?php $image = get_sub_field('poster_image');
												if (!empty($image)) : ?>
													<button aria-label="open video in pop-up container" class="" data-open="left_popup_<?php echo $modnum; ?>_<?php echo $rownum; ?>">
														<?php if ($overlay == 'overlay') : ?>
															<img class="video-overlay" src="<?php echo get_template_directory_uri(); ?>/assets/images/orange_overlay.svg" alt="orange overlay layer" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>" />
														<?php endif; ?>
														<?php if ($icon == 'icon') : ?>
															<img class="video-icon" src="<?php echo get_template_directory_uri(); ?>/assets/images/video-icon.svg" alt="video play icon" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>" />
														<?php endif; ?>
														<img class="video-poster" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>" />
													</button>
												<?php endif; ?>
											</div>
										<?php else :
											//Video is inline
										?>
											<div class="video-container <?= "$shadow" ?>">
												<?php if (get_sub_field('video_type') == 'id') : ?>
													<img style="width: 100%; margin: auto; display: block;" class="vidyard-player-embed" src="https://play.vidyard.com/<?php echo get_sub_field('vidyard_id'); ?>.jpg" data-uuid="<?php echo get_sub_field('vidyard_id'); ?>" data-v="4" data-type="inline" data-autoplay="<?= "$autoplay" ?>" data-loop="<?= "$loop" ?>" data-controls="<?= "$controls" ?>" />
												<?php else : ?>
													<?php
													$iframe = get_sub_field('video_link');
													preg_match('/src="(.+?)"/', $iframe, $matches);
													$src = $matches[1];
													$params = array(
														'controls'  => $controls,
														'hd'        => 1,
														'autohide'  => 1,
														'loop'  		=> $loop,
														'autoplay'  => $autoplay,
														'mute'  		=> $autoplay,
													);
													$new_src = add_query_arg($params, $src);
													$iframe = str_replace($src, $new_src, $iframe);
													$attributes = 'frameborder="0"';
													$iframe = str_replace('></iframe>', ' ' . $attributes . '></iframe>', $iframe);
													echo $iframe;
													?>
												<?php endif; ?>
											</div>
										<?php endif; ?>
									</div>
								<?php endwhile; ?>
							<?php endif; ?>

						<?php elseif (get_sub_field('content_type') == 'form') :
							//FORM BLOCK
						?>


							<?php if (have_rows('form_block')) : ?>
								<div class="cell form-block small-12 medium-order-1 <?= "$rightsize $rightmobile" ?>">
									<?php while (have_rows('form_block')) : the_row(); ?>

										<div id="" class="hero-form-container form-left">
											<?php if (get_sub_field('form_shortcode')) : ?>
												<div class="hero-form bg-white">
													<?php if (get_sub_field('form_headline')) : ?>
														<h4 class="hero-form-headline"><?php echo get_sub_field('form_headline')  ?></h4>
													<?php endif; ?>
													<?php $gravity_form_id = get_sub_field('form_shortcode');
													echo do_shortcode('[gravityform ajax="true" id="' . $gravity_form_id . '" title="false" description="false"]'); ?>
												</div>
												<?php get_template_part('parts/_template_parts/gform_variables'); ?>
											<?php endif; ?>
											<?php if (get_sub_field('content_below_form')) : ?>
												<div class="hero-form-end">
													<?php echo get_sub_field('content_below_form'); ?>
												</div>
											<?php endif; ?>
										</div>
									<?php endwhile; ?>
								</div>
							<?php endif; ?>


						<?php endif; ?>
					<?php endwhile; ?>
				<?php endif; ?>


				<?/* RIGHT CONTENT IS A DUPLICATE OF LEFT CONTENT
			--'left' is changed to 'right' except for in text-left
			Make any section changes to Left first then duplicate and change.
			*/ ?>


				<?php if (have_rows('right_content')) : ?>
					<?php while (have_rows('right_content')) : the_row(); ?>
						<?php $rownum = (get_row_index()); ?>
						<?php if (get_sub_field('content_type') == 'text') :
							//TEXT BLOCK
						?>
							<?php if (have_rows('text_block')) : ?>
								<?php while (have_rows('text_block')) : the_row(); ?>
									<?php if (get_sub_field('link_alignment') == 'left') : ?>
										<?php $lalign = 'text-left'; ?>
									<?php elseif (get_sub_field('link_alignment') == 'center') : ?>
										<?php $lalign = 'text-center'; ?>
									<?php else : ?>
										<?php $lalign = 'text-left'; ?>
									<?php endif; ?>
									<?php if (get_sub_field('link_type') == 'button') : ?>
										<?php $ltype = 'aras-button'; ?>
									<?php elseif (get_sub_field('link_type') == 'link') : ?>
										<?php $ltype = 'aras-link'; ?>
									<?php else : ?>
										<?php $ltype = 'aras-button'; ?>
									<?php endif; ?>
									<div class="cell text-block wysiwyg-content small-12 medium-order-2 <?= "$rightsize $rightmobile" ?>">
										<?php if (get_sub_field('text_content')) : ?>
											<?php echo get_sub_field('text_content'); ?>
										<?php endif; ?>
										<?php if (get_sub_field('link_type_type') == 'popup') : ?>
											<?php if (get_sub_field('popup_label') && get_sub_field('popup_content')) : ?>
												<div class="<?php echo $lalign; ?>">
													<button aria-label="<?php echo get_sub_field('popup_label'); ?>" class="<?php echo $ltype; ?>" data-open="right_popup_<?php echo $modnum; ?>_<?php echo $rownum; ?>">
														<?php echo get_sub_field('popup_label'); ?>
													</button>
												</div>
											<?php endif; ?>
										<?php else : ?>
											<?php $link = get_sub_field('text_block_link');
											if ($link) : $link_url = $link['url'];
												$link_title = $link['title'];
												$link_target = $link['target'] ? $link['target'] : '_self';
											?>
												<div class="<?php echo $lalign; ?>">
													<a aria-label="<?php echo esc_attr($link_title); ?>" class="<?php echo $ltype; ?>" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
														<?php echo esc_html($link_title); ?>
													</a>
												</div>
											<?php endif; ?>
										<?php endif; ?>
									</div>
								<?php endwhile; ?>
							<?php endif; ?>
						<?php elseif (get_sub_field('content_type') == 'image') :
							//IMAGE BLOCK
						?>
							<?php if (have_rows('image_block')) : ?>
								<?php while (have_rows('image_block')) : the_row(); ?>
									<?php $image_options = get_sub_field('image_options'); ?>
									<?php if ($image_options && in_array('greyscale', $image_options)) : ?>
										<?php $greyscale = 'greyscale' ?>
									<?php else : ?>
										<?php $greyscale = '' ?>
									<?php endif; ?>
									<?php if ($image_options && in_array('overlay', $image_options)) : ?>
										<?php $overlay = 'overlay' ?>
									<?php else : ?>
										<?php $overlay = '' ?>
									<?php endif; ?>
									<?php if ($image_options && in_array('shadow', $image_options)) : ?>
										<?php $shadow = 'shadow' ?>
									<?php else : ?>
										<?php $shadow = '' ?>
									<?php endif; ?>
									<?php $image = get_sub_field('image');
									if (!empty($image)) : ?>
										<div class="cell image-block small-12 medium-order-2 <?= "$rightsize $rightmobile" ?>">
											<div class="image-container <?= "$greyscale $overlay $shadow" ?>">
												<?php if ($overlay == 'overlay') : ?>
													<img class="image-overlay" src="<?php echo get_template_directory_uri(); ?>/assets/images/orange_overlay.svg" alt="orange overlay layer" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>" />
												<?php endif; ?>
												<?php $zoomable = in_array('zoomable', $image_options) ?: false; ?>
												<?php if( $zoomable ){ ?>
												<a href="<?php echo esc_url($image['url']); ?>" data-pswp-width="<?php echo $image['width']; ?>" data-pswp-height="<?php echo $image['height']; ?>" class="zoomable-image">
												<?php } ?>
												<img class="split-image" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>">
												<?php if( $zoomable ){ ?>
												</a>
												<?php } ?>
											</div>
										</div>
									<?php endif; ?>
								<?php endwhile; ?>
							<?php endif; ?>
						<?php elseif (get_sub_field('content_type') == 'video') :
							//VIDEO BLOCK
						?>
							<?php if (have_rows('video_block')) : ?>
								<?php while (have_rows('video_block')) : the_row(); ?>
									<?php $block_settings = get_sub_field('block_settings'); ?>
									<?php if ($block_settings && in_array('shadow', $block_settings)) : ?>
										<?php $shadow = 'shadow' ?>
									<?php else : ?>
										<?php $shadow = '' ?>
									<?php endif; ?>
									<?php if ((get_sub_field('video_display') != '') && (get_sub_field('poster_image') != '')) : ?>
										<?php $pop_image_options = get_sub_field('popup_image_settings'); ?>
										<?php if ($pop_image_options && in_array('greyscale', $pop_image_options)) : ?>
											<?php $greyscale = 'greyscale' ?>
										<?php else : ?>
											<?php $greyscale = '' ?>
										<?php endif; ?>
										<?php if ($pop_image_options && in_array('overlay', $pop_image_options)) : ?>
											<?php $overlay = 'overlay' ?>
										<?php else : ?>
											<?php $overlay = '' ?>
										<?php endif; ?>
										<?php if ($pop_image_options && in_array('icon', $pop_image_options)) : ?>
											<?php $icon = 'icon' ?>
										<?php else : ?>
											<?php $icon = '' ?>
										<?php endif; ?>
									<?php else : ?>
										<?php $greyscale = '' ?>
										<?php $overlay = '' ?>
										<?php $icon = '' ?>
									<?php endif; ?>
									<?php $video_features = get_sub_field('video_settings'); ?>
									<?php if ($video_features && in_array('autoplay', $video_features)) : ?>
										<?php $autoplay = '1' ?>
									<?php else : ?>
										<?php $autoplay = '0' ?>
									<?php endif; ?>
									<?php if ($video_features && in_array('loop', $video_features)) : ?>
										<?php $loop = '1' ?>
									<?php else : ?>
										<?php $loop = '0' ?>
									<?php endif; ?>
									<?php if ($video_features && in_array('controls', $video_features)) : ?>
										<?php $controls = '1' ?>
									<?php else : ?>
										<?php $controls = '0' ?>
									<?php endif; ?>
									<?php $image = get_sub_field('poster_image');
									if (!empty($image)) : ?>
										<?php $poster = ($image['url']); ?>
									<?php else : ?>
										<?php $poster = (''); ?>
									<?php endif; ?>
									<div class="cell video-block small-12 medium-order-2 <?= "$rightsize $rightmobile" ?>">
										<?php if (get_sub_field('video_display')) :
											//Video is a popup
										?>
											<div class="video-container <?= "$shadow $greyscale $overlay $icon" ?>">

												<?php $image = get_sub_field('poster_image');
												if (!empty($image)) : ?>
													<button aria-label="open video in pop-up container" class="" data-open="right_popup_<?php echo $modnum; ?>_<?php echo $rownum; ?>">
														<?php if ($overlay == 'overlay') : ?>
															<img class="video-overlay" src="<?php echo get_template_directory_uri(); ?>/assets/images/orange_overlay.svg" alt="orange overlay layer" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>" />
														<?php endif; ?>
														<?php if ($icon == 'icon') : ?>
															<img class="video-icon" src="<?php echo get_template_directory_uri(); ?>/assets/images/video-icon.svg" alt="video play icon" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>" />
														<?php endif; ?>
														<img class="video-poster" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>" />
													</button>
												<?php endif; ?>
											</div>
										<?php else :
											//Video is inline
										?>
											<div class="video-container <?= "$shadow" ?>">
												<?php if (get_sub_field('video_type') == 'id') : ?>
													<img style="width: 100%; margin: auto; display: block;" class="vidyard-player-embed" alt="vidyard video player" src="https://play.vidyard.com/<?php echo get_sub_field('vidyard_id'); ?>.jpg" data-uuid="<?php echo get_sub_field('vidyard_id'); ?>" data-v="4" data-type="inline" data-autoplay="<?= "$autoplay" ?>" data-loop="<?= "$loop" ?>" data-controls="<?= "$controls" ?>" />
												<?php else : ?>
													<?php
													$iframe = get_sub_field('video_link');
													preg_match('/src="(.+?)"/', $iframe, $matches);
													$src = $matches[1];
													$params = array(
														'controls'  => $controls,
														'hd'        => 1,
														'autohide'  => 1,
														'loop'  		=> $loop,
														'autoplay'  => $autoplay,
														'mute'  		=> $autoplay,
													);
													$new_src = add_query_arg($params, $src);
													$iframe = str_replace($src, $new_src, $iframe);
													$attributes = 'frameborder="0"';
													$iframe = str_replace('></iframe>', ' ' . $attributes . '></iframe>', $iframe);
													echo $iframe;
													?>
												<?php endif; ?>
											</div>
										<?php endif; ?>
									</div>
								<?php endwhile; ?>
							<?php endif; ?>

						<?php elseif (get_sub_field('content_type') == 'form') :
							//FORM BLOCK
						?>


							<?php if (have_rows('form_block')) : ?>
								<div class="cell form-block small-12 medium-order-1 <?= "$leftsize $leftmobile" ?>">
									<?php while (have_rows('form_block')) : the_row(); ?>
										<div id="" class="hero-form-container">
											<?php if (get_sub_field('form_shortcode')) : ?>
												<div class="hero-form bg-white">
													<?php if (get_sub_field('form_headline')) : ?>
														<h4 class="hero-form-headline"><?php echo get_sub_field('form_headline')  ?></h4>
													<?php endif; ?>
													<?php $gravity_form_id = get_sub_field('form_shortcode');
													echo do_shortcode('[gravityform ajax="true" id="' . $gravity_form_id . '" title="false" description="false"]'); ?>
												</div>
												<?php get_template_part('parts/_template_parts/gform_variables'); ?>
											<?php endif; ?>
											<?php if (get_sub_field('content_below_form')) : ?>
												<div class="hero-form-end">
													<?php echo get_sub_field('content_below_form'); ?>
												</div>
											<?php endif; ?>
										</div>
									<?php endwhile; ?>
								</div>
							<?php endif; ?>

						<?php endif; ?>
					<?php endwhile; ?>
				<?php endif; ?>

			</div>
		</div>
	</section>



	<?/* BELOW CONTENT IS FOR POP-UPS
Split into Left and Right, Text and Video
*/ ?>

	<?php if (have_rows('left_content')) : ?>
		<?php while (have_rows('left_content')) : the_row(); ?>
			<?php $rownum = (get_row_index()); ?>
			<?php if (get_sub_field('content_type') == 'text') : ?>
				<?php if (have_rows('text_block')) : ?>
					<?php while (have_rows('text_block')) : the_row(); ?>
						<?php if (get_sub_field('link_type_type') == 'popup') : ?>
							<?php if (get_sub_field('popup_label') && get_sub_field('popup_content')) : ?>
								<div class="reveal medium" id="left_popup_<?php echo $modnum; ?>_<?php echo $rownum; ?>" data-reveal data-reset-on-close="true">
									<?php echo get_sub_field('popup_content'); ?>
									<button aria-label="close pop-up container" class="close-button" data-close aria-label="Close modal" type="button">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					<?php endwhile; ?>
				<?php endif; ?>
			<?php endif; ?>
			<?php if (get_sub_field('content_type') == 'video') : ?>
				<?php if (have_rows('video_block')) : ?>
					<?php while (have_rows('video_block')) : the_row(); ?>
						<?php if (get_sub_field('video_display')) :
							//Video is a popup 
						?>
							<div class="reveal large" id="left_popup_<?php echo $modnum; ?>_<?php echo $rownum; ?>" data-reveal data-reset-on-close="true">
								<?php if (get_sub_field('video_type') == 'id') : ?>
									<img style="width: 100%; margin: auto; display: block;" class="vidyard-player-embed" alt="vidyard video player" src="https://play.vidyard.com/<?php echo get_sub_field('vidyard_id'); ?>.jpg" data-uuid="<?php echo get_sub_field('vidyard_id'); ?>" data-v="4" data-type="inline" data-autoplay="<?= "$autoplay" ?>" data-loop="<?= "$loop" ?>" data-controls="<?= "$controls" ?>" />
								<?php else : ?>
									<?php
									$iframe = get_sub_field('video_link');
									preg_match('/src="(.+?)"/', $iframe, $matches);
									$src = $matches[1];
									// Add extra parameters to src and replace HTML.
									$params = array(
										'controls'  => $controls,
										'hd'        => 1,
										'autohide'  => 1,
										'loop'  		=> $loop,
										'autoplay'  => $autoplay,
										'mute'  		=> $autoplay,
									);
									$new_src = add_query_arg($params, $src);
									$iframe = str_replace($src, $new_src, $iframe);
									$attributes = 'frameborder="0"';
									$iframe = str_replace('></iframe>', ' ' . $attributes . '></iframe>', $iframe);
									echo $iframe;
									?>
								<?php endif; ?>
								<button aria-label="close pop-up container" class="close-button" data-close aria-label="Close modal" type="button">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
						<?php endif; ?>
					<?php endwhile; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endwhile; ?>
	<?php endif; ?>
	<?/* 
RIGHT POPUP CONTENT
Duplicate of Left popup content, change 'left' to 'right'
*/ ?>
	<?php if (have_rows('right_content')) : ?>
		<?php while (have_rows('right_content')) : the_row(); ?>
			<?php $rownum = (get_row_index()); ?>
			<?php if (get_sub_field('content_type') == 'text') : ?>
				<?php if (have_rows('text_block')) : ?>
					<?php while (have_rows('text_block')) : the_row(); ?>
						<?php if (get_sub_field('link_type_type') == 'popup') : ?>
							<?php if (get_sub_field('popup_label') && get_sub_field('popup_content')) : ?>
								<div class="reveal medium" id="right_popup_<?php echo $modnum; ?>_<?php echo $rownum; ?>" data-reveal data-reset-on-close="true">
									<?php echo get_sub_field('popup_content'); ?>
									<button aria-label="close pop-up container" class="close-button" data-close aria-label="Close modal" type="button">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					<?php endwhile; ?>
				<?php endif; ?>
			<?php endif; ?>
			<?php if (get_sub_field('content_type') == 'video') : ?>
				<?php if (have_rows('video_block')) : ?>
					<?php while (have_rows('video_block')) : the_row(); ?>
						<?php if (get_sub_field('video_display')) :
							//Video is a popup 
						?>
							<div class="reveal large" id="right_popup_<?php echo $modnum; ?>_<?php echo $rownum; ?>" data-reveal data-reset-on-close="true">
								<?php if (get_sub_field('video_type') == 'id') : ?>
									<img style="width: 100%; margin: auto; display: block;" class="vidyard-player-embed" alt="vidyard video player" src="https://play.vidyard.com/<?php echo get_sub_field('vidyard_id'); ?>.jpg" data-uuid="<?php echo get_sub_field('vidyard_id'); ?>" data-v="4" data-type="inline" data-autoplay="<?= "$autoplay" ?>" data-loop="<?= "$loop" ?>" data-controls="<?= "$controls" ?>" />
								<?php else : ?>
									<?php
									$iframe = get_sub_field('video_link');
									preg_match('/src="(.+?)"/', $iframe, $matches);
									$src = $matches[1];
									// Add extra parameters to src and replace HTML.
									$params = array(
										'controls'  => $controls,
										'hd'        => 1,
										'autohide'  => 1,
										'loop'  		=> $loop,
										'autoplay'  => $autoplay,
										'mute'  		=> $autoplay,
									);
									$new_src = add_query_arg($params, $src);
									$iframe = str_replace($src, $new_src, $iframe);
									$attributes = 'frameborder="0"';
									$iframe = str_replace('></iframe>', ' ' . $attributes . '></iframe>', $iframe);
									echo $iframe;
									?>
								<?php endif; ?>
								<button aria-label="close pop-up container" class="close-button" data-close aria-label="Close modal" type="button">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
						<?php endif; ?>
					<?php endwhile; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endwhile; ?>
	<?php endif; ?>
<?php endif; ?>