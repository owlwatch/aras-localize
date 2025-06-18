<?php if (have_rows('content')) : ?>
	<section class="content-section post-content">
		<?php while (have_rows('content')) : the_row(); ?>
			<?php $rownum = (get_row_index()); ?>

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

						<div class="text-block fullwidthblock wysiwyg-content ">
							<?php if (get_sub_field('text_content')) : ?>
								<?php echo get_sub_field('text_content'); ?>
							<?php endif; ?>
							<?php if (get_sub_field('link_type_type') == 'popup') : ?>
								<?php if (get_sub_field('popup_label') && get_sub_field('popup_content')) : ?>
									<div class="<?php echo $lalign; ?>">
										<button aria-label="<?php echo get_sub_field('popup_label'); ?>" class="aras-button" data-open="popup_intro_<?php echo $rownum; ?>">
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
										<a aria-label="<?php echo esc_attr($link_title); ?>" class="aras-button" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
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
							<div class="image-block fullwidthblock text-center">
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

						<?php if (have_rows('content_links')) : ?>
							<div class="cell button-block <?= "$blockwidth $lalign" ?>">
								<?php while (have_rows('content_links')) : the_row(); ?>
									<?php $link = get_sub_field('link');
									if ($link) : $link_url = $link['url'];
										$link_title = $link['title'];
										$link_target = $link['target'] ? $link['target'] : '_self';
									?>
										<a aria-label="<?php echo esc_attr($link_title); ?>" class="aras-button" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
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
						<?php $block_settings = get_sub_field('block_settings'); ?>
						<?php if ($block_settings && in_array('shadow', $block_settings)) : ?>
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
						<div class="video-block">

							<div class="video-container fullwidthblock video-container-fullwidth <?= "$shadow" ?>">
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


						</div>
					<?php endwhile; ?>
				<?php endif; ?>

			<?php endif; ?>

		<?php endwhile; ?>
	</section>
<?php endif; ?>







<?php if (have_rows('intro_content_sections')) : ?>
	<?php while (have_rows('intro_content_sections')) : the_row(); ?>
		<?php $rownum = (get_row_index()); ?>

		<?php if (get_sub_field('content_type') == 'text') : ?>
			<?php if (have_rows('text_block')) : ?>
				<?php while (have_rows('text_block')) : the_row(); ?>

					<?php if (get_sub_field('link_type_type') == 'popup') : ?>
						<?php if (get_sub_field('popup_label') && get_sub_field('popup_content')) : ?>
							<div class="reveal large" id="popup_intro_<?php echo $rownum; ?>" data-reveal data-reset-on-close="true">
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