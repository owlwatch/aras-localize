<?php if (get_sub_field('show_for_this_language') != 'hide') : ?>
	<?php $modnum = get_row_index(); ?>
	<?php if (get_sub_field('anchor_link')) : ?>
		<?php $anchor = ('id="' . get_sub_field('anchor_link') . '"'); ?>
	<?php else : ?>
		<?php $anchor = ('id="quote-section-' . $modnum . '"'); ?>
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

	<?php if (get_sub_field('speakers_per_row') == 'four') : ?>
		<?php $cellone = 'small-10 medium-10 large-8'; ?>
		<?php $celltwo = 'small-up-1 medium-up-2 large-up-4'; ?>
	<?php elseif (get_sub_field('speakers_per_row') == 'five') : ?>
		<?php $cellone = 'small-10 medium-10 large-10'; ?>
		<?php $celltwo = 'small-up-1 medium-up-3 large-up-5'; ?>
	<?php else : ?>
		<?php $cellone = 'small-10 medium-12 large-12'; ?>
		<?php $celltwo = 'small-up-1 medium-up-3 large-up-6'; ?>
	<?php endif; ?>

	<section class="featured-speakers-section <?= "$bg_color $toppadding $bottompadding" ?>" <?= "$anchor" ?>>
		<?php get_template_part('parts/_template_parts/background_visual'); ?>
		<?php if (get_sub_field('content_before')) : ?>
			<div class="grid-x grid-padding-x <?php if (get_sub_field('content_before_position') == 'center') : ?>align-center<?php endif; ?>">
				<div class="cell small-12 medium-11 large-10 content-before">
					<div class="wysiwyg-content"><?php echo get_sub_field('content_before'); ?></div>
				</div>
			</div>
		<?php endif; ?>



		<?php if (have_rows('speakers')) : ?>
			<div class="grid-container">
				<div class="grid-x grid-margin-x align-center">
					<div class="cell <?php echo $cellone; ?>">
						<div class="grid-x grid-margin-x align-center <?php echo $celltwo; ?>">
							<?php while (have_rows('speakers')) : the_row(); ?>

								<div class="cell speaker-item">

									<?php
									$featured_post = get_sub_field('speaker');
									if ($featured_post) : ?>

										<?php $image = get_field('speaker_headshot', $featured_post);
										if (!empty($image)) : ?>
											<img class="speaker-headshot" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
										<?php else : ?>
											<span class="speaker-headshot-placeholder"></span>
										<?php endif; ?>

										<h4><?php echo get_field('speaker_name', $featured_post); ?></h4>
										<h5><?php echo get_field('speaker_position', $featured_post); ?></h5>


										<?php if (have_rows('speaker_links', $featured_post)) : ?>
											<?php while (have_rows('speaker_links', $featured_post)) : the_row(); ?>

												<?php $link = get_sub_field('speaker_link', $featured_post);
												if ($link) : $link_url = $link['url'];
													$link_title = $link['title'];
													$link_target = $link['target'] ? $link['target'] : '_self';
												?>
													<a aria-label="<?php echo esc_html($link_title); ?>" class="logolink" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
														<?php echo $link_title; ?>
													</a>
												<?php endif; ?>

											<?php endwhile; ?>
										<?php endif; ?>

									<?php endif; ?>


								</div>


							<?php endwhile; ?>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>




	</section>
<?php endif; ?>