<?php if (get_sub_field('show_for_this_language') != 'hide') : ?>
	<?php $modnum = get_row_index(); ?>
	<?php if (get_sub_field('anchor_link')) : ?>
		<?php $anchor = ('id="' . get_sub_field('anchor_link') . '"'); ?>
	<?php else : ?>
		<?php $anchor = ('id="stats-section-' . $modnum . '"'); ?>
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

	<?php if (get_sub_field('stats_per_row') == 'two') : ?>
		<?php $statsperrow = 'small-12 medium-6 large-6'; ?>
	<?php elseif (get_sub_field('stats_per_row') == 'three') : ?>
		<?php $statsperrow = 'small-12 medium-4 large-4'; ?>
	<?php elseif (get_sub_field('stats_per_row') == 'four') : ?>
		<?php $statsperrow = 'small-12 medium-6 large-3'; ?>
	<?php else : ?>
		<?php $statsperrow = 'small-12 medium-6 large-3'; ?>
	<?php endif; ?>

	<?php if (get_sub_field('stats_alignment') == 'left') : ?>
		<?php $statsalignment = ''; ?>
	<?php elseif (get_sub_field('statsalignment') == 'center') : ?>
		<?php $statsalignment = 'align-center'; ?>
	<?php else : ?>
		<?php $statsalignment = ''; ?>
	<?php endif; ?>


	<section class="statistics-section <?= "$toppadding $bottompadding $bg_color" ?>" <?= "$anchor" ?>>
		<div class="grid-container">

			<?php if (get_sub_field('content_before')) : ?>
				<div class="grid-x grid-padding-x <?php if (get_sub_field('content_before_position') == 'center') : ?>align-center<?php endif; ?>">
					<div class="cell small-12 medium-11 large-10 content-before">
						<div class="wysiwyg-content"><?php echo get_sub_field('content_before'); ?></div>
					</div>
				</div>
			<?php endif; ?>



			<?php if (have_rows('stats')) : ?>
				<div class="grid-x grid-padding-x <?= "$statsalignment" ?>">
					<?php while (have_rows('stats')) : the_row(); ?>

						<div class="cell <?= "$statsperrow" ?> stat-item">






							<?php if (have_rows('large_stat')) : ?>
								<?php while (have_rows('large_stat')) : the_row(); ?>
									<?php if (get_sub_field('stat')) : ?>
										<?php if (get_sub_field('stat_prefix')) : ?>
											<?php if (get_sub_field('prefix_type') == 'sup') : ?>
												<?php $stat_prefix = '<sup>' . get_sub_field('stat_prefix') . '</sup>'; ?>
											<?php elseif (get_sub_field('prefix_type') == 'sub') : ?>
												<?php $stat_prefix = '<sub>' . get_sub_field('stat_prefix') . '</sub>'; ?>
											<?php else : ?>
												<?php $stat_prefix = '<span>' . get_sub_field('stat_prefix') . '</span>'; ?>
											<?php endif; ?>
										<?php else : ?>
											<?php $stat_prefix = ''; ?>
										<?php endif; ?>
										<?php if (get_sub_field('stat_suffix')) : ?>
											<?php if (get_sub_field('suffix_type') == 'sup') : ?>
												<?php $stat_suffix = '<sup>' . get_sub_field('stat_suffix') . '</sup>'; ?>
											<?php elseif (get_sub_field('suffix_type') == 'sub') : ?>
												<?php $stat_suffix = '<sub>' . get_sub_field('stat_suffix') . '</sub>'; ?>
											<?php else : ?>
												<?php $stat_suffix = '<span>' . get_sub_field('stat_suffix') . '</span>'; ?>
											<?php endif; ?>
										<?php else : ?>
											<?php $stat_suffix = ''; ?>
										<?php endif; ?>

										<h3 class="stat-num"><?php echo $stat_prefix; ?><?php echo get_sub_field('stat'); ?><?php echo $stat_suffix; ?></h3>
									<?php endif; ?>
								<?php endwhile; ?>
							<?php endif; ?>


							<?php if (get_sub_field('stat_description')) : ?>
								<p class="stat-desc"><?php echo get_sub_field('stat_description'); ?></p>
							<?php else : ?>
							<?php endif; ?>
						</div>

					<?php endwhile; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>