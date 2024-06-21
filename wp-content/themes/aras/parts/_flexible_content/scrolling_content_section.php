<?php if (get_sub_field('show_for_this_language') != 'hide') : ?>
	<?php $modnum = get_row_index(); ?>
	<?php if (get_sub_field('anchor_link')) : ?>
		<?php $anchor = ('id="' . get_sub_field('anchor_link') . '"'); ?>
	<?php else : ?>
		<?php $anchor = ('id="scrollsection-' . $modnum . '"'); ?>
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

	<section class="scrolling-content-section <?= "$toppadding $bottompadding $bg_color" ?>" <?= "$anchor" ?>>
		<?php if (have_rows('scrolling_content_blocks')) : ?>
			<div class="bg-slider bg-slider-<?php echo $modnum; ?>">
				<?php while (have_rows('scrolling_content_blocks')) : the_row(); ?>
					<div class="bg-slider-item">
						<?php if (get_sub_field('background_image')) : ?>
							<?php $image = get_sub_field('background_image'); ?>
							<div class="scrolling-bg-overlay background-right-img"></div>
							<div class="scrolling-bg-img background-right-img" role="img" aria-label="<?php echo esc_attr($image['alt']); ?>" title="<?php echo esc_attr($image['alt']); ?>" style="background-image: url(<?php echo esc_url($image['url']); ?>);"></div>
						<?php endif; ?>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>

		<?php if (get_sub_field('navigation_style') == 'none') : ?>
		<?php else : ?>
			<div class="scroll-nav-dots grid-container">
				<div class="grid-x grid-padding-x">
					<div class="cell small-12">
						<?php if (get_sub_field('navigation_style') == 'dots') : ?>
							<div class="scroll-dots"></div>
						<?php endif; ?>
						<?php if (get_sub_field('navigation_style') == 'labeled') : ?>
							<div class="scroll-labeled"></div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php if (have_rows('scrolling_content_blocks')) : ?>
			<section class="scroll-content-section-slick  scroll-content-section-slick-<?php echo $modnum; ?>">



				<?php while (have_rows('scrolling_content_blocks')) : the_row(); ?>
					<?php $rowcount = get_row_index(); ?>
					<div class="scroll-content-section-item">

						<div class="grid-container">
							<div id="content-<?php echo $rowcount ?>" class="grid-x grid-padding-x align-middle align-left">
								<?php if (get_sub_field('visual_type') == 'imagebg') : ?>
									<div class="cell small-12 medium-9 large-7">
									<?php else : ?>
										<div class="cell small-12 medium-6 large-6 small-order-2 medium-order-1">
										<?php endif; ?>

										<div class="wysiwyg-content">
											<?php if (get_sub_field('subhead')) : ?>
												<h6><?php echo get_sub_field('subhead'); ?></h6>
											<?php endif; ?>
											<?php if (get_sub_field('headline')) : ?>
												<h2><?php echo get_sub_field('headline'); ?></h2>
											<?php endif; ?>
											<?php if (get_sub_field('content')) : ?>
												<?php echo get_sub_field('content'); ?>
											<?php endif; ?>
											<?php $link = get_sub_field('button');
											if ($link) : $link_url = $link['url'];
												$link_title = $link['title'];
												$link_target = $link['target'] ? $link['target'] : '_self';
											?>
												<a aria-label="<?php echo esc_html($link_title); ?>" class="aras-button" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
													<?php echo esc_html($link_title); ?>
												</a>
											<?php endif; ?>
										</div>
										</div>
										<?php if (get_sub_field('visual_type') == 'image') :
											//IMAGE BLOCK
										?>
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
											<?php $image = get_sub_field('image_beside_content');
											if (!empty($image)) : ?>
												<div class="cell image-block small-12 medium-6 large-6 small-order-1 medium-order-2">
													<div class="image-container <?= "$greyscale $overlay $shadow" ?>">
														<?php if ($overlay == 'overlay') : ?>
															<img class="image-overlay" src="<?php echo get_template_directory_uri(); ?>/assets/images/orange_overlay.svg" alt="orange overlay layer" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>" />
														<?php endif; ?>
														<img class="split-image" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>">
													</div>
												</div>
											<?php endif; ?>
										<?php endif; ?>
										<?php if (get_sub_field('visual_type') == 'video') :
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
													<div class="cell video-block small-12 medium-6 large-6 small-order-1 medium-order-2 ?>">
														<?php if (get_sub_field('video_display')) :	?>
															<div class="video-container <?= "$shadow $greyscale $overlay $icon" ?>">
																<?php $image = get_sub_field('poster_image');
																if (!empty($image)) : ?>
																	<button aria-label="Open video in pop-up container" class="" data-open="video_popup_<?php echo $modnum; ?>_<?php echo $rowcount; ?>">
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

															<div class="reveal large" id="video_popup_<?php echo $modnum; ?>_<?php echo $rowcount; ?>" data-reveal data-reset-on-close="true" data-reset-on-close="true">
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
																<button aria-label="Close pop-up container" class="close-button" data-close aria-label="Close modal" type="button">
																	<span aria-hidden="true">&times;</span>
																</button>
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
										<?php endif; ?>
									</div>
							</div>
						</div>
					<?php endwhile; ?>
			</section>
		<?php endif; ?>
	</section>
	<?php if (get_sub_field('navigation_style') == 'labeled') : ?>
		<script>
			var tabLabels = [];
			<?php if (have_rows('scrolling_content_blocks')) : ?>
				<?php while (have_rows('scrolling_content_blocks')) : the_row(); ?>
					<?php if (get_sub_field('tab_label')) : ?>
						tabLabels.push("<?php echo get_sub_field('tab_label'); ?>");
					<?php else : ?>
						tabLabels.push("");
					<?php endif; ?>
				<?php endwhile; ?>
			<?php endif; ?>
		</script>
	<?php endif; ?>

	<?php if (get_sub_field('autoplay_speed')) {
		$autoplay_speed =  get_sub_field('autoplay_speed');
		if ($autoplay_speed == '0') {
			$speed = 0;
			$autoplay = 'false';
		} else {
			$speed = $autoplay_speed * 1000;
			$autoplay = 'true';
		}
	} else {
		$speed = '5000';
		$autoplay = 'true';
	} ?>

	<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/slick/slick.min.js"></script>
	<script>
		jQuery(document).ready(function() {
			jQuery('.scroll-content-section-slick-<?php echo $modnum; ?>').slick({
				<?php if (get_sub_field('navigation_style') == 'dots') : ?>
					appendDots: jQuery('.scroll-dots'),
					dots: true,
				<?php elseif (get_sub_field('navigation_style') == 'labeled') : ?>
					appendDots: jQuery('.scroll-labeled'),
					customPaging: function(slider, i) {
						return '<button aria-label="' + tabLabels[i] + '" >' + tabLabels[i] + '</button>';
					},
					dots: true,
				<?php else : ?>
					dots: false,
				<?php endif; ?>
				autoplay: <?php echo $autoplay; ?>,
				autoplaySpeed: <?php echo $speed; ?>,
				arrows: false,
				cssEase: 'linear',
				draggable: true,
				fade: true,
				infinite: true,
				pauseOnHover: false,
				slidesToShow: 1,
				slidesToScroll: 1,
				speed: 500,
				swipeToSlide: true,
				asNavFor: '.bg-slider-<?php echo $modnum; ?>',
			});

			jQuery('.bg-slider-<?php echo $modnum; ?>').slick({
				slidesToShow: 1,
				slidesToScroll: 1,
				asNavFor: '.scroll-content-section-slick-<?php echo $modnum; ?>',
				arrows: false,
				dots: false,
				fade: true,
				infinite: true,
			});

		});
	</script>
<?php endif; ?>