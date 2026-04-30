<?php if (get_sub_field('show_for_this_language') != 'hide') : ?>
	<?php $modnum = get_row_index(); ?>
	<?php if (get_sub_field('anchor_link')) : ?>
		<?php $anchor = ('id="' . get_sub_field('anchor_link') . '"'); ?>
	<?php else : ?>
		<?php $anchor = ('id="leadership-section-' . $modnum . '"'); ?>
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
	<?php $text_color = get_sub_field('text_color') ?: 'text-dark' ?>
	<section class="leadership-tabs-section <?= "$toppadding $bottompadding $bg_color $text_color" ?>" <?= "$anchor" ?>>
		<?php get_template_part('parts/_template_parts/background_visual'); ?>
		<?php if (get_sub_field('content_before')) : ?>
			<div class="grid-container">
				<div class="grid-x grid-padding-x <?php if (get_sub_field('content_before_position') == 'center') : ?>align-center<?php endif; ?>">
					<div class="cell small-12 medium-11 large-10 content-before">
						<div class="wysiwyg-content"><?php echo get_sub_field('content_before'); ?></div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php if (have_rows('tab_items')) : ?>
			<div class="grid-container">
				<div class="grid-x grid-padding-x">
					<div class="cell small-12 leadertabscontainer">
						<ul class="tabs leader-tabs" data-tabs id="leader-tabs-<?php echo $modnum; ?>">
							<?php while (have_rows('tab_items')) : the_row(); ?>
								<?php $titlenum = get_row_index(); ?>
								<?php if (get_sub_field('tab_label')) : ?>
									<?php $tab_label = get_sub_field('tab_label'); ?>
								<?php else : ?>
									<?php $tab_label = ''; ?>
								<?php endif; ?>
								<?php if ($titlenum == '1') : ?>
									<li class="tabs-title is-active">
										<a aria-label="<?php echo $tab_label; ?>" data-tabs-target="leaders-<?php echo $titlenum; ?>-<?php echo $modnum; ?>" href="#leaders<?php echo $titlenum; ?>" aria-selected="true"><?php echo $tab_label; ?></a>
									</li>
								<?php else : ?>
									<li class="tabs-title">
										<a aria-label="<?php echo $tab_label; ?>" data-tabs-target="leaders-<?php echo $titlenum; ?>-<?php echo $modnum; ?>" href="#leaders<?php echo $titlenum; ?>"><?php echo $tab_label; ?></a>
									</li>
								<?php endif; ?>
							<?php endwhile; ?>
						</ul>
					</div>
				</div>
			</div>
			<div class="tabs-content" data-tabs-content="leader-tabs-<?php echo $modnum; ?>">
				<?php while (have_rows('tab_items')) : the_row(); ?>
					<?php $contentnum = get_row_index(); ?>

					<?php if ($contentnum == '1') : ?>
						<div class="tabs-panel is-active" id="leaders-<?php echo $contentnum; ?>-<?php echo $modnum; ?>">
						<?php else : ?>
							<div class="tabs-panel" id="leaders-<?php echo $contentnum; ?>-<?php echo $modnum; ?>">
							<?php endif; ?>


							<?php if (have_rows('leaders')) : ?>
								<div class="grid-container">
									<div class="grid-x grid-padding-x align-left">
										<?php while (have_rows('leaders')) : the_row(); ?>
											<div class="cell small-12 medium-6 leader-container">
												<?php $leadernum = get_row_index(); ?>

												<?php
												$bio = get_sub_field('bio');
												$excerpt = wp_trim_words( $bio, 20, '...' );
												$use_excerpt = $bio != $excerpt;
												?>

												<?php $image = get_sub_field('headshot');
												if (!empty($image)) : ?>
													<img class="leader-headshot" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
												<?php endif; ?>
												<div class="leader-content">
													<?php if (get_sub_field('name')) : ?>
														<h4><?php echo get_sub_field('name'); ?></h4>
													<?php endif; ?>
													<?php if (get_sub_field('position')) : ?>
														<h6><?php echo get_sub_field('position'); ?></h6>
													<?php endif; ?>
													<?php if (get_sub_field('bio')) :
														$bio = get_sub_field('bio');
														echo '<p>';
														echo $excerpt;
														echo '</p>';
														if( $use_excerpt ){
															?>
															<span class="bio-readmore" data-open="leader-<?php echo $contentnum; ?>-<?php echo $leadernum; ?>-<?php echo $modnum; ?>">
																<?php esc_html_e('READ BIO', 'aras'); ?>
															</span>
															<?php
														}
														?>
													<?php endif; ?>
												</div>
											</div>

											<?php if (get_sub_field('bio')) : ?>
												<?php if ($use_excerpt) : ?>
													<div class="reveal leader-container" id="leader-<?php echo $contentnum; ?>-<?php echo $leadernum; ?>-<?php echo $modnum; ?>" data-reveal data-reset-on-close="true">
														<?php $image = get_sub_field('headshot');
														if (!empty($image)) : ?>
															<img class="leader-headshot" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
														<?php endif; ?>
														<div class="leader-content">
															<?php if (get_sub_field('name')) : ?>
																<h4><?php echo get_sub_field('name'); ?></h4>
															<?php endif; ?>
															<?php if (get_sub_field('position')) : ?>
																<h6><?php echo get_sub_field('position'); ?></h6>
															<?php endif; ?>
															<?php if (get_sub_field('bio')) :
																echo $bio; ?>
															<?php endif; ?>
														</div>
														<button aria-label="close pop-up container" class="close-button" data-close aria-label="Close modal" type="button">
															<span aria-hidden="true">&times;</span>
														</button>
													</div>
												<?php endif; ?>
											<?php endif; ?>

										<?php endwhile; ?>
									</div>
								</div>
							<?php endif; ?> <?php if ($contentnum == '1') : ?>
							</div>
						<?php else : ?>
						</div>
					<?php endif; ?>

				<?php endwhile; ?>
			</div>

		<?php endif; ?>
	</section>
<?php endif; ?>