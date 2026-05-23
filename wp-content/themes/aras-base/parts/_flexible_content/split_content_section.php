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
	$bg_color = [
		'transparent' => 'bg-transparent',
		'white' => 'bg-white',
		'grey' => 'bg-grey',
		'dblue' => 'bg-dblue',
		'whitetogrey' => 'bg-whitetogrey',
		'greytowarm' => 'bg-greytowarm',
	][$background_color] ?? 'bg-white';
	$toppadding = [
		'large' => 'largetoppadding',
		'medium' => 'mediumtoppadding',
		'small' => 'smalltoppadding',
		'none' => 'notoppadding',
	][$top_padding] ?? 'mediumtoppadding';
	$bottompadding = [
		'large' => 'largebottompadding',
		'medium' => 'mediumbottompadding',
		'small' => 'smallbottompadding',
		'none' => 'nobottompadding',
	][$bottom_padding] ?? 'mediumbottompadding';
	?>
	<?php $vertical_alignment = get_sub_field('vertical_alignment');
	$vert = [
		'top' => 'align-top',
		'middle' => 'align-middle',
		'bottom' => 'align-bottom',
	][$vertical_alignment] ?? 'align-top';
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
	} 
	$sides = ['left', 'right'];
	?>
	<?php $text_color = get_sub_field('text_color') ?: 'text-dark' ?>
	<section class="content-section <?= "$toppadding $bottompadding $bg_color $text_color" ?>" <?= "$anchor" ?>>
		<?php get_template_part('parts/_template_parts/background_visual'); ?>
		<div class="grid-container">
			<div class="grid-x grid-margin-x <?= "$vert" ?>">
			<?php foreach( $sides as $side ) : ?>
				<?php if (have_rows($side.'_content')) : ?>
					<?php while (have_rows($side.'_content')) : the_row(); ?>

						<?php $size = $side == 'left' ? $leftsize : $rightsize; ?>
						<?php $mobile = $side == 'left' ? $leftmobile : $rightmobile; ?>
						<?php $rownum = (get_row_index()); ?>
						<?php if (get_sub_field('content_type') == 'text') :
							//TEXT BLOCK
						?>
							<?php if (have_rows('text_block')) : ?>
								<?php while (have_rows('text_block')) : the_row(); ?>
									<?php $lalign = get_sub_field('link_alignment') == 'center' ? 'text-center' : 'text-left'; ?>
									<?php $ltype = get_sub_field('link_type') == 'link' ? 'aras-link' : 'aras-button'; ?>
									<div class="cell text-block wysiwyg-content <?= "$size $mobile" ?> small-12 medium-order-1">
										<?php if (get_sub_field('text_content')) : ?>
											<?php echo get_sub_field('text_content'); ?>
										<?php endif; ?>
										<?php if (get_sub_field('link_type_type') == 'popup') : ?>
											<?php if (get_sub_field('popup_label') && get_sub_field('popup_content')) : ?>
												<div class="<?php echo $lalign; ?>">
													<button aria-label="<?php echo get_sub_field('popup_label'); ?>" class="<?php echo $ltype; ?>" data-open="<?php echo $side ?>_popup_<?php echo $modnum; ?>_<?php echo $rownum; ?>">
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
									<?php $greyscale = is_array( $image_options ) && in_array('greyscale', $image_options) ? 'greyscale' : ''; ?>
									<?php $overlay = is_array( $image_options ) && in_array('overlay', $image_options) ? 'overlay' : ''; ?>
									<?php $shadow = is_array( $image_options ) && in_array('shadow', $image_options) ? 'shadow' : ''; ?>
									<?php $image = get_sub_field('image');
									if (!empty($image)) : ?>
										<div class="cell image-block small-12 medium-order-1 <?= "$size $mobile" ?> >">
											<div class="image-container <?= "$greyscale $overlay $shadow" ?>">
												<?php if ($overlay == 'overlay') : ?>
													<img class="image-overlay" src="<?php echo get_template_directory_uri(); ?>/assets/images/orange_overlay.svg" alt="orange overlay layer" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>" />
												<?php endif; ?>
												<?php $zoomable = is_array( $image_options ) && in_array('zoomable', $image_options); ?>
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
									<?php $shadow = is_array( $block_settings ) && in_array('shadow', $block_settings) ? 'shadow' : ''; ?>
									<?php if ((get_sub_field('video_display') != '') && (get_sub_field('poster_image') != '')) : ?>
										<?php $pop_image_options = get_sub_field('popup_image_settings'); ?>
										<?php $greyscale = is_array( $pop_image_options ) && in_array('greyscale', $pop_image_options) ? 'greyscale' : ''; ?>
										<?php $overlay = is_array( $pop_image_options ) && in_array('overlay', $pop_image_options) ? 'overlay' : ''; ?>
										<?php $icon = is_array( $pop_image_options ) && in_array('icon', $pop_image_options) ? 'icon' : ''; ?>
									<?php else : ?>
										<?php $greyscale = '' ?>
										<?php $overlay = '' ?>
										<?php $icon = '' ?>
									<?php endif; ?>

									<?php $video_features = get_sub_field('video_settings'); ?>
									<?php $autoplay = is_array( $video_features ) && in_array('autoplay', $video_features) ? '1' : '0'; ?>
									<?php $loop = is_array( $video_features ) && in_array('loop', $video_features) ? '1' : '0'; ?>
									<?php $controls = is_array( $video_features ) && in_array('controls', $video_features) ? '1' : '0'; ?>

									<?php $image = get_sub_field('poster_image');
									if (!empty($image)) : ?>
										<?php $poster = ($image['url']); ?>
									<?php else : ?>
										<?php $poster = (''); ?>
									<?php endif; ?>
									<div class="cell video-block small-12 medium-order-<?php echo $side == 'left' ? '1' : '2'; ?> <?= "$size $mobile" ?>">
										<?php if (get_sub_field('video_display')) :	?>
											<div class="video-container <?= "$shadow $greyscale $overlay $icon" ?>">

												<?php $image = get_sub_field('poster_image');
												if (!empty($image)) : ?>
													<button aria-label="<?php echo esc_attr__('open video in pop-up container', 'aras'); ?>" class="" data-open="<?php echo $side ?>_popup_<?php echo $modnum; ?>_<?php echo $rownum; ?>">
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
										<?php $button_color = get_sub_field('form_button_color_override') ?: 'default'; ?>

										<div id="" class="hero-form-container form-<?php echo $side ?>">
											<?php if (get_sub_field('form_shortcode')) : ?>
												<div class="hero-form bg-white form-button-color-<?php echo esc_attr($button_color); ?>">
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
			<?php endforeach; ?>
			</div>
		</div>
	</section>



<?/* BELOW CONTENT IS FOR POP-UPS
Split into Left and Right, Text and Video
*/

?>
<?php foreach( $sides as $side ) : ?>
	<?php if (have_rows($side . '_content')) : ?>
		<?php while (have_rows($side . '_content')) : the_row(); ?>
			<?php $rownum = (get_row_index()); ?>
			<?php if (get_sub_field('content_type') == 'text') : ?>
				<?php if (have_rows('text_block')) : ?>
					<?php while (have_rows('text_block')) : the_row(); ?>
						<?php if (get_sub_field('link_type_type') == 'popup') : ?>
							<?php if (get_sub_field('popup_label') && get_sub_field('popup_content')) : ?>
								<div class="reveal medium" id="<?php echo $side ?>_popup_<?php echo $modnum; ?>_<?php echo $rownum; ?>" data-reveal data-reset-on-close="true">
									<?php echo get_sub_field('popup_content'); ?>
									<button aria-label="<?php echo esc_attr__('close pop-up container', 'aras'); ?>" class="close-button" data-close aria-label="Close modal" type="button">
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
							<div class="reveal large" id="<?php echo $side ?>_popup_<?php echo $modnum; ?>_<?php echo $rownum; ?>" data-reveal data-reset-on-close="true">
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
								<button aria-label="<?php echo esc_attr__('close pop-up container', 'aras'); ?>" class="close-button" data-close aria-label="Close modal" type="button">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
						<?php endif; ?>
					<?php endwhile; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endwhile; ?>
	<?php endif; ?>
<?php endforeach; ?>

<?php endif; ?>