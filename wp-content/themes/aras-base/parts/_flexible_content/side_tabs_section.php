<?php if (get_sub_field('show_for_this_language') != 'hide') : ?>
	<?php $modnum = get_row_index(); ?>
	<?php if (get_sub_field('anchor_link')) : ?>
		<?php $anchor = ('id="' . get_sub_field('anchor_link') . '"'); ?>
	<?php else : ?>
		<?php $anchor = ('id="side-tabs-section-' . $modnum . '"'); ?>
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
			<?php $tabscell = 'large-6 medium-4'; ?>
			<?php $contcell = 'large-6 medium-8'; ?>
		<?php elseif (get_sub_field('tab_layout') == 'stacked') : ?>
			<?php $tabscol = 'tabs-stacked'; ?>
			<?php $tabscell = 'medium-12'; ?>
			<?php $contcell = 'medium-12'; ?>
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
									<li id="sidelabel-<?php echo $titlenum; ?>-<?php echo $modnum; ?>" class="tabs-title is-active <?php echo $labelstyle; ?>">
										<a aria-label="<?php echo $tablabel; ?>" data-tabs-target="sidetab-<?php echo $titlenum; ?>-<?php echo $modnum; ?>" href="#sidetab<?php echo $titlenum; ?>" aria-selected="true"><?php echo $tab_label; ?></a>
									</li>
								<?php else : ?>
									<li id="sidelabel-<?php echo $titlenum; ?>-<?php echo $modnum; ?>" class="tabs-title <?php echo $labelstyle; ?>">
										<a aria-label="<?php echo $tablabel; ?>" data-tabs-target="sidetab-<?php echo $titlenum; ?>-<?php echo $modnum; ?>" href="#sidetab<?php echo $titlenum; ?>"><?php echo $tab_label; ?></a>
									</li>
								<?php endif; ?>
							<?php endwhile; ?>
						</ul>
					</div>

					<div class="cell small-12  <?php echo $contcell; ?>">
						<div class="tabs-content side-tabs-content" data-tabs-content="side-tabs-<?php echo $modnum; ?>" data-reset-on-close="true">
							<?php while (have_rows('tab_items')) : the_row(); ?>
								<?php $contentnum = get_row_index(); ?>

								<?php if ($contentnum == '1') : ?>
									<div class="tabs-panel is-active" id="sidetab-<?php echo $contentnum; ?>-<?php echo $modnum; ?>">
									<?php else : ?>
										<div class="tabs-panel" id="sidetab-<?php echo $contentnum; ?>-<?php echo $modnum; ?>">
										<?php endif; ?>

										<?php if (get_sub_field('tab_subhead')) : ?>
											<h6><?php echo get_sub_field('tab_subhead'); ?></h6>
										<?php endif; ?>
										<?php if (get_sub_field('tab_headline')) : ?>
											<h3><?php echo get_sub_field('tab_headline'); ?></h3>
										<?php endif; ?>
										<?php if (get_sub_field('tab_content')) : ?>
											<div class="tab-content-content wysiwyg-content"><?php echo get_sub_field('tab_content'); ?></div>
										<?php endif; ?>

										<?php $image = get_sub_field('tab_visual');
										if (!empty($image)) : ?>
											<?php $link = get_sub_field('tab_visual_link');
											if ($link) : $link_url = $link['url'];
												$link_title = $link['title'];
												$link_target = $link['target'] ? $link['target'] : '_self';
											?>
												<a aria-label="<?php echo esc_attr($link_title); ?>" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
													<img class="tab-visual" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
												</a>
											<?php else : ?>
												<img class="tab-visual" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
											<?php endif; ?>
										<?php endif; ?>

										<?php $link = get_sub_field('tab_button');
										if ($link) : $link_url = $link['url'];
											$link_title = $link['title'];
											$link_target = $link['target'] ? $link['target'] : '_self';
										?>
											<a aria-label="<?php echo esc_attr($link_title); ?>" class="aras-button" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
												<?php echo esc_html($link_title); ?>
											</a>
										<?php endif; ?>

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

	<?php if (have_rows('tab_items')) : ?>
		<script>
			jQuery(document).ready(function($) {
				jQuery('.tabs-title').on('click', function() {
					var $tabsContent = jQuery('.tabs-content');
					var $activePanel = jQuery(jQuery(this).children('a').attr('href'));
					setTimeout(function() {
						var newHeight = $activePanel.outerHeight();
						$tabsContent.stop().animate({
							height: newHeight
						}, 5);
					});
				});
			});
		</script>
	<?php endif; ?>
	<?php if (have_rows('tab_items')) : ?>
		<script>
			jQuery('.tabs-title').on('click', function() {
				var $tabsContent = jQuery('.tabs-content');
				var $vidyardContainers = $tabsContent.find('.vidyard-player-container');
				$vidyardContainers.each(function() {
					jQuery(this).html(jQuery(this).html());
				});
			});
		</script>
	<?php endif; ?>
<?php endif; ?>