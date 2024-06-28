<?php if (get_sub_field('show_for_this_language') != 'hide') : ?>
	<?php $modnum = get_row_index(); ?>
	<?php if (get_sub_field('anchor_link')) : ?>
		<?php $anchor = ('id="' . get_sub_field('anchor_link') . '"'); ?>
	<?php else : ?>
		<?php $anchor = ('id="side-locations-section-' . $modnum . '"'); ?>
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



	<section class="side-tabs-section <?= "$toppadding $bottompadding $bg_color" ?>" <?= "$anchor" ?>>
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
		<?php if (get_sub_field('tab_layout') == 'twocol') : ?>
			<?php $tabscol = 'two-col'; ?>
			<?php $tabscell = 'medium-6'; ?>
			<?php $contcell = 'medium-6'; ?>
		<?php else : ?>
			<?php $tabscol = ''; ?>
			<?php $tabscell = 'medium-4'; ?>
			<?php $contcell = 'medium-8'; ?>
		<?php endif; ?>

		<?php if (get_sub_field('tab_label_style') == 'upper') : ?>
			<?php $labelstyle = 'label-upper'; ?>
		<?php else : ?>
			<?php $labelstyle = ''; ?>
		<?php endif; ?>

		<?php if (have_rows('tab_items')) : ?>
			<div class="grid-container">
				<div class="grid-x grid-padding-x">
					<div class="cell small-12 <?php echo $tabscell; ?>">
						<ul class="tabs side-tabs <?php echo $tabscol; ?>" data-tabs id="side-tabs-<?php echo $modnum; ?>">
							<?php while (have_rows('tab_items')) : the_row(); ?>
								<?php $titlenum = get_row_index(); ?>
								<?php if (get_sub_field('tab_label')) : ?>
									<?php $tab_label = get_sub_field('tab_label'); ?>
								<?php else : ?>
									<?php $tab_label = ''; ?>
								<?php endif; ?>
								<?php if ($titlenum == '1') : ?>
									<li class="tabs-title is-active <?php echo $labelstyle; ?>">
										<a aria-label="<?php echo $tablabel; ?>" data-tabs-target="sidetab-<?php echo $titlenum; ?>-<?php echo $modnum; ?>" aria-selected="true"><?php echo $tab_label; ?></a>
									</li>
								<?php else : ?>
									<li class="tabs-title <?php echo $labelstyle; ?>">
										<a aria-label="<?php echo $tablabel; ?>" data-tabs-target="sidetab-<?php echo $titlenum; ?>-<?php echo $modnum; ?>" href="#sidetab<?php echo $titlenum; ?>"><?php echo $tab_label; ?></a>
									</li>
								<?php endif; ?>
							<?php endwhile; ?>
						</ul>
					</div>
					<div class="cell small-12  <?php echo $contcell; ?>">
						<div class="tabs-content side-tabs-content" data-tabs-content="side-tabs-<?php echo $modnum; ?>">

							<?php while (have_rows('tab_items')) : the_row(); ?>
								<?php $contentnum = get_row_index(); ?>
								<?php $image = get_sub_field('tab_location_background'); ?>
								<?php if ($contentnum == '1') : ?>
									<div class="tabs-panel tab-location-panel is-active" id="sidetab-<?php echo $contentnum; ?>-<?php echo $modnum; ?>" title="<?php echo esc_attr($image['alt']); ?>" style="background-image: url(<?php echo esc_url($image['url']); ?>);">
									<?php else : ?>
										<div class="tabs-panel tab-location-panel " id="sidetab-<?php echo $contentnum; ?>-<?php echo $modnum; ?>" title="<?php echo esc_attr($image['alt']); ?>" style="background-image: url(<?php echo esc_url($image['url']); ?>);">
										<?php endif; ?>

										<div class="tab-location-window">
											<?php if (get_sub_field('tab_location_name')) : ?>
												<h4><?php echo get_sub_field('tab_location_name'); ?></h4>
											<?php endif; ?>
											<?php if (get_sub_field('tab_location')) : ?>
												<?php echo get_sub_field('tab_location'); ?>
											<?php endif; ?>
										</div>

										<?php if ($contentnum == '1') : ?>
										</div>
									<?php else : ?>
									</div>
								<?php endif; ?>
							<?php endwhile; ?>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</section>
<?php endif; ?>