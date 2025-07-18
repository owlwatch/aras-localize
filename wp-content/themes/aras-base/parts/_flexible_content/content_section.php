<?php if (get_sub_field('show_for_this_language') != 'hide') : ?>
	<?php $modnum = get_row_index(); ?>
	<?php if (get_sub_field('anchor_link')) : ?>
		<?php $anchor = ('id="' . get_sub_field('anchor_link') . '"'); ?>
	<?php else : ?>
		<?php $anchor = ('id="contentsection-' . $modnum . '"'); ?>
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
	<?php $horizontal_alignment = get_sub_field('horizontal_alignment');
	// this is named backwards. horizontal_alignment is actually 'Vertical Alignment'
	switch ($horizontal_alignment) {
		case 'top':
			$horiz = 'align-top';
			break;
		case 'middle':
			$horiz = 'align-middle';
			break;
		case 'bottom':
			$horiz = 'align-bottom';
			break;
		default:
			$horiz = 'align-top';
	}
	?>
	<?php if (get_sub_field('background_image')) : ?>
		<?php $background_image = get_sub_field('background_image'); ?>

		<?php $bg_placement = get_sub_field('background_image_position');
		switch ($bg_placement) {
			case 'topleft':
				$bgp = 'background-position: top left';
				break;
			case 'topcenter':
				$bgp = 'background-position: top center';
				break;
			case 'topright':
				$bgp = 'background-position: top right';
				break;
			case 'middleleft':
				$bgp = 'background-position: center left';
				break;
			case 'middlecenter':
				$bgp = 'background-position: center center';
				break;
			case 'middleright':
				$bgp = 'background-position: center right';
				break;
			case 'bottomleft':
				$bgp = 'background-position: bottom left';
				break;
			case 'bottomcenter':
				$bgp = 'background-position: bottom center';
				break;
			case 'bottomright':
				$bgp = 'background-position: bottom right';
				break;
			default:
				$bgp = 'background-position: top left';
		}
		?>
	<?php endif; ?>
	<?php $text_color = get_sub_field('text_color') ?: 'text-dark' ?>
	<section class="content-section <?= "$toppadding $bottompadding $bg_color $text_color" ?> <?php if (get_sub_field('background_image') != '') : ?>has-bg-img<?php endif; ?>" <?= "$anchor" ?> <?php if (get_sub_field('background_image')) : ?>title="<?php echo esc_attr($background_image['alt']); ?>" style="background-image: url(<?php echo esc_url($background_image['url']); ?>);min-height: calc((<?php echo ($background_image['height']); ?> / <?php echo ($background_image['width']); ?>) * 100vw);<?php echo $bgp; ?>" <?php endif; ?>>
		<?php get_template_part('parts/_template_parts/background_visual'); ?>
		<?php if (have_rows('content')) : ?>

			<?php if (get_sub_field('background_image')) : ?>
				<div class="mobile-bg-blocker <?= "$bg_color" ?>"></div>
			<?php endif; ?>
			<div class="grid-container">
				<div class="grid-x grid-margin-x <?= "$horiz" ?>" data-equalizer data-equalizer-by-row="true">
					<?php while (have_rows('content')) : the_row(); ?>
						<?php $rownum = (get_row_index()); ?>

						<?php if (get_sub_field('content_width') == 'twothird') : ?>
							<?php $blockwidth = 'small-12 medium-8 fullwidthblock'; ?>
						<?php else : ?>
							<?php $blockwidth = 'small-12 fullwidthblock'; ?>
						<?php endif; ?>

						<?php if (get_sub_field('content_type') == 'text') : ?>
							<?php if (have_rows('text_block')) : ?>
								<?php while (have_rows('text_block')) : the_row(); ?>
									<?php if (get_sub_field('link_alignment') == 'left') : ?>
										<?php $lalign = 'text-left'; ?>
									<?php elseif (get_sub_field('link_alignment') == 'center') : ?>
										<?php $lalign = 'text-center'; ?>
									<?php else : ?>
										<?php $lalign = 'text-left'; ?>
									<?php endif; ?>

									<div class="cell text-block wysiwyg-content <?= "$blockwidth" ?>">
										<?php if (get_sub_field('text_content')) : ?>
											<?php echo get_sub_field('text_content'); ?>
										<?php endif; ?>

										<?php if (get_sub_field('link_type_type') == 'popup') : ?>
											<?php if (get_sub_field('popup_label') && get_sub_field('popup_content')) : ?>
												<div class="<?php echo $lalign; ?>">
													<button aria-label="<?php echo get_sub_field('popup_label'); ?>" class="aras-button" data-open="popup_<?php echo $modnum; ?>_<?php echo $rownum; ?>">
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
													<a aria-label="<?php echo esc_html($link_title); ?>" class="aras-button" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
														<?php echo esc_html($link_title); ?>
													</a>
												</div>
											<?php endif; ?>
										<?php endif; ?>

									</div>
								<?php endwhile; ?>
							<?php endif; ?>

						<?php elseif (get_sub_field('content_type') == 'image') : ?>
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

									<?php if (get_sub_field('image_width') == 'full') : ?>
										<?php $iwidth = 'fullwidth'; ?>
									<?php else : ?>
										<?php $iwidth = ''; ?>
									<?php endif; ?>

									<?php $image = get_sub_field('image');
									if (!empty($image)) : ?>

										<div class="cell image-block <?= "$blockwidth" ?> text-center">
											<div class="image-container image-container-fullwidth <?= "$greyscale $overlay $shadow" ?>" <?php if (get_sub_field('image_width') == 'exact') : ?>style="width:<?php echo get_sub_field('pixel_width'); ?>px;" <?php endif; ?>>
												<?php if ($overlay == 'overlay') : ?>
													<img class="image-overlay" src="<?php echo get_template_directory_uri(); ?>/assets/images/orange_overlay.svg" alt="orange overlay layer" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>" />
												<?php endif; ?>
												<img class="split-image" class="<?php echo $iwidth; ?>" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>">
											</div>
										</div>
									<?php endif; ?>
								<?php endwhile; ?>
							<?php endif; ?>

						<?php elseif (get_sub_field('content_type') == 'links') : ?>
							<?php if (have_rows('link_block')) : ?>
								<?php while (have_rows('link_block')) : the_row(); ?>
									<?php if (get_sub_field('link_alignment') == 'left') : ?>
										<?php $lalign = 'text-left'; ?>
									<?php elseif (get_sub_field('link_alignment') == 'center') : ?>
										<?php $lalign = 'text-center'; ?>
									<?php else : ?>
										<?php $lalign = 'text-left'; ?>
									<?php endif; ?>
									<?php $ltype = 'aras-button'; ?>

									<?php if (have_rows('content_links')) : ?>
										<div class="cell button-block <?= "$blockwidth $lalign" ?>">
											<?php while (have_rows('content_links')) : the_row(); ?>
												<?php $link = get_sub_field('link');
												if ($link) : $link_url = $link['url'];
													$link_title = $link['title'];
													$link_target = $link['target'] ? $link['target'] : '_self';
												?>
													<a aria-label="<?php echo esc_html($link_title); ?>" class="<?php echo $ltype; ?>" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
														<?php echo esc_html($link_title); ?>
													</a>
												<?php endif; ?>
											<?php endwhile; ?>
										</div>
									<?php endif; ?>
								<?php endwhile; ?>
							<?php endif; ?>

						<?php elseif (get_sub_field('content_type') == 'video') : ?>

							<?php if (have_rows('video_block')) : ?>
								<?php while (have_rows('video_block')) : the_row(); ?>
									<?php $is_storylane = get_sub_field('video_type') == 'storylane' ?>
									<?php $block_settings = get_sub_field('block_settings'); ?>
									<?php if ($block_settings && !$is_storylane && in_array('shadow', $block_settings)) : ?>
										<?php $shadow = 'shadow' ?>
									<?php else : ?>
										<?php $shadow = '' ?>
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
									<div class="cell video-block fullwidthblock small-12">

										<div class="video-container video-container-fullwidth <?= "$shadow" ?>">
											<?php if (get_sub_field('video_type') == 'id') : ?>
												<img style="width: 100%; margin: auto; display: block;" class="vidyard-player-embed" alt="vidyard video player" src="https://play.vidyard.com/<?php echo get_sub_field('vidyard_id'); ?>.jpg" data-uuid="<?php echo get_sub_field('vidyard_id'); ?>" data-v="4" data-type="inline" data-autoplay="<?= "$autoplay" ?>" data-loop="<?= "$loop" ?>" data-controls="<?= "$controls" ?>" />

											<?php elseif (get_sub_field('video_type') == 'storylane') : ?>
												<?php
												$heading = get_sub_field('video_heading');
												$embed_url = add_query_arg('embed', 'inline', preg_replace('/\/share\//', '/demo/', get_sub_field('storylane_share_link')));
												?>
												<?php if( $heading): ?>
												<h3 style="text-align: center; max-width: 30em; margin: 0 auto 2em;"><?php echo $heading; ?></h3>
												<?php endif; ?>
												<div>
													<script async src="https://js.storylane.io/js/v2/storylane.js"></script>
													<div class="sl-embed" style="position:relative;padding-bottom:56.25%;width:100%;height:0;transform:scale(1)">
														<iframe loading="lazy" class="sl-demo" src="<?php echo $embed_url ?>" name="sl-embed" allow="fullscreen" allowfullscreen style="position:absolute;top:0;left:0;width:100%!important;height:100%!important;border:1px solid rgba(63,95,172,0.35);box-shadow: 0px 0px 18px rgba(26, 19, 72, 0.15);border-radius:10px;box-sizing:border-box;"></iframe>
													</div>
												</div>
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


									</div>
								<?php endwhile; ?>
							<?php endif; ?>

						<?php endif; ?>

					<?php endwhile; ?>
				</div>
			</div>
		<?php endif; ?>
	</section>



	<?php if (have_rows('content')) : ?>
		<?php while (have_rows('content')) : the_row(); ?>
			<?php $rownum = (get_row_index()); ?>

			<?php if (get_sub_field('content_type') == 'text') : ?>
				<?php if (have_rows('text_block')) : ?>
					<?php while (have_rows('text_block')) : the_row(); ?>

						<?php if (get_sub_field('link_type_type') == 'popup') : ?>
							<?php if (get_sub_field('popup_label') && get_sub_field('popup_content')) : ?>
								<div class="reveal large" id="popup_<?php echo $modnum; ?>_<?php echo $rownum; ?>" data-reveal data-reset-on-close="true">
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

		<?php endwhile; ?>
	<?php endif; ?>
<?php endif; ?>