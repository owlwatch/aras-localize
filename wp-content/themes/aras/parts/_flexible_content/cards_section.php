<?php if (get_sub_field('show_for_this_language') != 'hide') : ?>
	<?php $modnum = get_row_index(); ?>
	<?php if (get_sub_field('anchor_link')) : ?>
		<?php $anchor = ('id="' . get_sub_field('anchor_link') . '"'); ?>
	<?php else : ?>
		<?php $anchor = ('id="cardsection-' . $modnum . '"'); ?>
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
			$bg_color = 'bg-transparent';
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






	<?php if (get_sub_field('cards_per_row') == 'two') : ?>
		<?php $perrow = ('small-12 medium-6 large-6'); ?>
	<?php elseif (get_sub_field('cards_per_row') == 'three') : ?>
		<?php $perrow = ('small-12 medium-4 large-4'); ?>
	<?php elseif (get_sub_field('cards_per_row') == 'four') : ?>
		<?php $perrow = ('small-12 medium-6 large-3'); ?>
	<?php elseif (get_sub_field('cards_per_row') == 'slider') : ?>
		<?php $perrow = ('card-slick'); ?>
	<?php else : ?>
		<?php $perrow = ('small-12 medium-4'); ?>
	<?php endif; ?>

	<?php if (get_sub_field('visual_style') == 'icon') : ?>
		<?php $imagecontainer = ('card-icon-container'); ?>
		<?php $imagestyle = ('card-icon'); ?>
		<?php if (get_sub_field('cards_per_row') == 'two') : ?>
			<?php $flexicon = ('card-flex'); ?>
		<?php else : ?>
			<?php $flexicon = (''); ?>
		<?php endif; ?>
	<?php else : ?>
		<?php $imagecontainer = ('card-image-container'); ?>
		<?php $imagestyle = ('card-image'); ?>
		<?php $flexicon = (''); ?>
	<?php endif; ?>

	<?php if (get_sub_field('horizontal_alignment') == 'left') : ?>
		<?php $horiz = ('align-left'); ?>
	<?php elseif (get_sub_field('horizontal_alignment') == 'center') : ?>
		<?php $horiz = ('align-center'); ?>
	<?php else : ?>
		<?php $horiz = ('align-left'); ?>
	<?php endif; ?>

	<?php if (get_sub_field('card_style') == 'free') : ?>
		<?php $cardstyle = ('freecard'); ?>
		<?php $is_highlight = '' ?>
		<?php $connector = '' ?>
	<?php else : ?>
		<?php $cardstyle = ('bordercard'); ?>
		<?php $card_styles = get_sub_field('card_style_options'); ?>
		<?php if (get_sub_field('highlight_style') == 'gradient') : ?>
			<?php $is_highlight = 'has_gradient_highlight' ?>
		<?php else : ?>
			<?php $is_highlight = 'has_solid_highlight' ?>
		<?php endif; ?>

		<?php if ($card_styles && in_array('connector', $card_styles)) : ?>
			<?php $connector = 'card-connector' ?>
		<?php else : ?>
			<?php $connector = '' ?>
		<?php endif; ?>
	<?php endif; ?>

	<section class="cards-section <?= "$toppadding $bottompadding $bg_color" ?>" <?= "$anchor" ?>>
		<?php get_template_part('parts/_template_parts/background_visual'); ?>
		<div class="grid-container">

			<?php if (get_sub_field('content_before')) : ?>
				<div class="grid-x grid-padding-x <?php if (get_sub_field('content_before_position') == 'center') : ?>align-center<?php endif; ?>">
					<div class="cell small-12 medium-11 large-10 content-before">
						<div class="wysiwyg-content"><?php echo get_sub_field('content_before'); ?></div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ($perrow == 'card-slick') :
				/* SLICK SLIDER */ ?>
				<?php if (have_rows('cards')) : ?>
					<div class="card-slider-slick">
						<?php while (have_rows('cards')) : the_row(); ?>
							<?php $cardnum = get_row_index(); ?>

							<?php if (get_sub_field('card_link')) : ?>
								<?php if ($is_highlight == 'has_gradient_highlight') : ?>
									<?php $highlight = 'card-highlight-gradient' ?>
									<?php $highlightcont = 'card-highlight-gradient-cont' ?>
								<?php elseif ($is_highlight == 'has_solid_highlight') : ?>
									<?php $highlight = 'card-highlight' ?>
									<?php $highlightcont = 'card-highlight-cont' ?>
								<?php else : ?>
									<?php $highlight = '' ?>
									<?php $highlightcont = '' ?>
								<?php endif; ?>
							<?php else : ?>
								<?php $highlight = '' ?>
								<?php $highlightcont = '' ?>
							<?php endif; ?>
							<div class="<?= "$perrow $highlightcont $cardstyle" ?> cell-card <?php if ($cardstyle == 'bordercard') : ?>bg-white<?php endif; ?>">
								<div class="card-container <?= "$highlight $connector $flexicon" ?>">
									<?php $image = get_sub_field('card_image');
									if (!empty($image)) : ?>
										<?php $link = get_sub_field('card_link');
										if ($link) : $link_url = $link['url'];
											$link_title = $link['title'];
											$link_target = $link['target'] ? $link['target'] : '_self';
										?>
											<div class="<?php echo $imagecontainer; ?>">

												<?php if (get_sub_field('card_link_type') == 'popup') : ?>
													<button aria-label="<?php echo get_sub_field('card_popup_label'); ?>" data-open="popup_<?php echo $modnum; ?>_<?php echo $cardnum; ?>">
														<img class="<?php echo $imagestyle; ?>" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
													</button>
												<?php else : ?>
													<a aria-label="<?php echo esc_html($link_title); ?>" class="" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
														<img class="<?php echo $imagestyle; ?>" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
													</a>
												<?php endif; ?>


											</div>
										<?php else : ?>
											<div class="<?php echo $imagecontainer; ?>">
												<img class="<?php echo $imagestyle; ?>" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
											</div>
										<?php endif; ?>
									<?php endif; ?>
									<div class="card-content-container">
										<?php if (get_sub_field('card_subhead')) : ?>
											<h6 class="card-subhead"><?php echo get_sub_field('card_subhead'); ?></h6>
										<?php endif; ?>
										<?php if (get_sub_field('card_headline')) : ?>
											<h3 class="card-headline"><?php echo get_sub_field('card_headline'); ?></h3>
										<?php endif; ?>
										<?php if (get_sub_field('card_content')) : ?>
											<?php echo get_sub_field('card_content'); ?>
										<?php endif; ?>

										<?php if (get_sub_field('card_link_type') == 'popup') : ?>
											<?php if (get_sub_field('card_popup_label')) : ?>
												<span class="card-link card-link--buffer">
													<?php echo get_sub_field('card_popup_label'); ?>&nbsp;→
												</span>
												<div class="card-bottom">
													<button aria-label="<?php echo get_sub_field('card_popup_label'); ?>" class="card-link" data-open="popup_<?php echo $modnum; ?>_<?php echo $cardnum; ?>">
														<?php echo get_sub_field('card_popup_label'); ?>&nbsp;→
													</button>
												</div>
											<?php endif; ?>
										<?php else : ?>
											<?php $link = get_sub_field('card_link');
											if ($link) : $link_url = $link['url'];
												$link_title = $link['title'];
												$link_target = $link['target'] ? $link['target'] : '_self';
											?>
												<span class="card-link card-link--buffer">
													<?php echo esc_html($link_title); ?>&nbsp;→
												</span>
												<div class="card-bottom">
													<a aria-label="<?php echo esc_html($link_title); ?>" class="card-link" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
														<?php echo esc_html($link_title); ?>&nbsp;→
													</a>
												</div>
											<?php endif; ?>
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endwhile; ?>
					</div>
					<?php if (get_sub_field('slider_arrows') == 'show') : ?>
						<div class="card-arrows"></div>
					<?php endif; ?>
				<?php endif; ?>
			<?php else : ?>
				<?php if (have_rows('cards')) : ?>
					<div class="grid-x grid-margin-x <?php echo $horiz; ?>">
						<?php while (have_rows('cards')) : the_row(); ?>
							<?php $cardnum = get_row_index(); ?>
							<?php if (get_sub_field('card_link')) : ?>
								<?php if ($is_highlight == 'has_gradient_highlight') : ?>
									<?php $highlight = 'card-highlight-gradient' ?>
									<?php $highlightcont = 'card-highlight-gradient-cont' ?>
								<?php elseif ($is_highlight == 'has_solid_highlight') : ?>
									<?php $highlight = 'card-highlight' ?>
									<?php $highlightcont = 'card-highlight-cont' ?>
								<?php else : ?>
									<?php $highlight = '' ?>
									<?php $highlightcont = '' ?>
								<?php endif; ?>
							<?php else : ?>
								<?php $highlight = '' ?>
								<?php $highlightcont = '' ?>
							<?php endif; ?>

							<div class="cell <?= "$perrow $highlightcont $cardstyle" ?> cell-card <?php if ($cardstyle == 'bordercard') : ?>bg-white<?php endif; ?>">
								<div class="card-container <?= "$highlight $connector $flexicon" ?>">
									<?php $image = get_sub_field('card_image');
									if (!empty($image)) : ?>
										<?php $link = get_sub_field('card_link');
										if ($link) : $link_url = $link['url'];
											$link_title = $link['title'];
											$link_target = $link['target'] ? $link['target'] : '_self';
										?>
											<div class="<?php echo $imagecontainer; ?>">
												<?php if (get_sub_field('card_link_type') == 'popup') : ?>
													<button aria-label="<?php echo get_sub_field('card_popup_label'); ?>" data-open="popup_<?php echo $modnum; ?>_<?php echo $cardnum; ?>">
														<img class="<?php echo $imagestyle; ?>" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
													</button>
												<?php else : ?>
													<a aria-label="<?php echo esc_html($link_title); ?>" class="" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
														<img class="<?php echo $imagestyle; ?>" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
													</a>
												<?php endif; ?>

											</div>
										<?php else : ?>
											<div class="<?php echo $imagecontainer; ?>">
												<img class="<?php echo $imagestyle; ?>" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
											</div>
										<?php endif; ?>
									<?php endif; ?>
									<div class="card-content-container">
										<?php if (get_sub_field('card_subhead')) : ?>
											<h6 class="card-subhead"><?php echo get_sub_field('card_subhead'); ?></h6>
										<?php endif; ?>
										<?php if (get_sub_field('card_headline')) : ?>
											<h3 class="card-headline"><?php echo get_sub_field('card_headline'); ?></h3>
										<?php endif; ?>
										<?php if (get_sub_field('card_content')) : ?>
											<?php echo get_sub_field('card_content'); ?>
										<?php endif; ?>

										<?php if (get_sub_field('card_link_type') == 'popup') : ?>
											<?php if (get_sub_field('card_popup_label')) : ?>
												<span class="card-link card-link--buffer">
													<?php echo get_sub_field('card_popup_label'); ?>&nbsp;→
												</span>
												<div class="card-bottom">
													<button aria-label="<?php echo get_sub_field('card_popup_label'); ?>" class="card-link" data-open="popup_<?php echo $modnum; ?>_<?php echo $cardnum; ?>">
														<?php echo get_sub_field('card_popup_label'); ?>&nbsp;→
													</button>
												</div>
											<?php endif; ?>
										<?php else : ?>
											<?php $link = get_sub_field('card_link');
											if ($link) : $link_url = $link['url'];
												$link_title = $link['title'];
												$link_target = $link['target'] ? $link['target'] : '_self';
											?>
												<span class="card-link card-link--buffer">
													<?php echo esc_html($link_title); ?>&nbsp;→
												</span>
												<div class="card-bottom">
													<a aria-label="<?php echo esc_html($link_title); ?>" class="card-link" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
														<?php echo esc_html($link_title); ?>&nbsp;→
													</a>
												</div>
											<?php endif; ?>
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endwhile; ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>


			<?php if (get_sub_field('buttons_after_cards')) : ?>
				<div class="card-buttons-section <?php if (get_sub_field('content_before_position') == 'center') : ?>grid-x grid-padding-x align-center<?php endif; ?>">
					<?php if (have_rows('buttons_after_cards')) : ?>
						<?php while (have_rows('buttons_after_cards')) : the_row(); ?>
							<?php $link = get_sub_field('button_link');
							if ($link) : $link_url = $link['url'];
								$link_title = $link['title'];
								$link_target = $link['target'] ? $link['target'] : '_self';
							?>
								<?php if (get_sub_field('button_style') == 'solid') : ?>
									<?php $buttontype = 'aras-button'; ?>
								<?php else : ?>
									<?php $buttontype = 'aras-button--outline'; ?>
								<?php endif; ?>
								<a aria-label="<?php echo esc_html($link_title); ?>" class="<?php echo $buttontype; ?>" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
									<?php echo esc_html($link_title); ?>
								</a>
							<?php endif; ?>
						<?php endwhile; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	</section>


	<?php if (have_rows('cards')) : ?>
		<?php while (have_rows('cards')) : the_row(); ?>
			<?php $cardpopnum = get_row_index(); ?>
			<?php if (get_sub_field('card_link_type') == 'popup') : ?>
				<div class="reveal large" id="popup_<?php echo $modnum; ?>_<?php echo $cardpopnum; ?>" data-reveal data-reset-on-close="true">
					<?php if (get_sub_field('card_popup_type') == 'content') : ?>
						<?php if (get_sub_field('card_popup_content')) : ?>
							<?php echo get_sub_field('card_popup_content'); ?>
						<?php endif; ?>
					<?php else : ?>
						<?php if (get_sub_field('card_popup_vidyard_id')) : ?>
							<img style="width: 100%; margin: auto; display: block;" class="vidyard-player-embed" alt="vidyard video player" src="https://play.vidyard.com/<?php echo get_sub_field('card_popup_vidyard_id'); ?>.jpg" data-uuid="<?php echo get_sub_field('card_popup_vidyard_id'); ?>" data-v="4" data-type="inline" />
						<?php endif; ?>
					<?php endif; ?>
					<button aria-label="close pop-up container" class="close-button" data-close aria-label="Close modal" type="button">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			<?php endif; ?>
		<?php endwhile; ?>
	<?php endif; ?>
<?php endif; ?>