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

	// lets get our quotes
	$quotes = [];
	$source = get_sub_field('quote_source') ?: 'manual'; // default to manual if not set
	if ('manual' === $source) {
		// manual quotes
		$quote = [
			'content' => get_sub_field('quote_content'),
			'font_size' => get_sub_field('quote_font_size') ?: 'default',
			'attribution' => get_sub_field('quote_attribution'),
			'attribution_url' => get_sub_field('quote_attribution_url'),
			'attribution_logo' => get_sub_field('quote_attribution_logo'),
			'headshot' => get_sub_field('quote_headshot')
		];
		$quotes[] = $quote;
	} else {
		// we are going to pull them from the library
		$quote_posts = get_sub_field('quotes');
		foreach ($quote_posts as $quote_post) {
			$quote = [
				'content' => get_field('quote_content', $quote_post->ID),
				'font_size' => get_field('quote_font_size', $quote_post->ID) ?: 'default',
				'attribution' => get_field('quote_attribution', $quote_post->ID),
				'attribution_url' => get_field('quote_attribution_url', $quote_post->ID),
				'attribution_logo' => get_field('quote_attribution_logo', $quote_post->ID),
				'headshot' => get_field('quote_headshot', $quote_post->ID)
			];
			$quotes[] = $quote;
		}
	}
	?>
	<?php if (get_sub_field('background_visual') == 'custom') : ?>
		<?php $image = get_sub_field('custom_background_visual'); ?>
		<style>
			.quote-section .grid-container::after {
				background-image: url(<?php echo esc_url($image['url']); ?>);
			}
		</style>
	<?php endif; ?>

	<?php $text_color = get_sub_field('text_color') ?: 'text-dark' ?>
	<section class="quote-section <?= "$bg_color" ?>" <?= "$anchor" ?>>
		<div class="grid-container <?= "$toppadding $bottompadding" ?>">
			<?php if (count($quotes) > 1) {
				$autoplay = get_sub_field('carousel_autoplay');
				?>
				<div
					class="quote-carousel swiper"
					data-autoplay="<?php echo esc_attr(json_encode($autoplay)); ?>"
					data-duration="<?php echo esc_attr(get_sub_field('autoplay_duration') ?: 5000); ?>"
				>
					<div class="swiper-wrapper">
					<?php } ?>
					<?php
					foreach ($quotes as $quote) {
					?>
						<?php if (count($quotes) > 1) { ?>
							<div class="swiper-slide">
							<?php } ?>
							<div class="grid-x grid-margin-x">

								<div class="cell small-12 medium-11 large-10">
									<div class="quote-container">
										<?php if ($quote['content']) : ?>
											<div class="quote-content <?php echo 'quote-content--' . $quote['font_size']; ?>">
												<?php echo $quote['content']; ?>
											</div>
										<?php endif; ?>
										<?php if ($quote['attribution_url']) : ?>
											<?php if ($quote['attribution']) : ?>
												<a class="quote-attribution h6" aria-label="<?php echo $quote['attribution']; ?>" class="attr-link" href="<?php echo $quote['attribution_url']; ?>" target="_blank">
													<?php
													if( $quote['headshot']){
														?>
														<img class="quote-headshot" src="<?php echo esc_url($quote['headshot']['medium']); ?>" alt="<?php if (esc_attr($quote['headshot']['alt'])) : ?> <?php echo esc_attr($quote['headshot']['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
														<?php
													}
													?>
													<span><?php echo $quote['attribution']; ?></span>
												</a>
											<?php endif; ?>
											<?php $image = $quote['attribution_logo'];
											if (!empty($image)) : ?>
												<a aria-label="<?php echo $quote['attribution']; ?>" class="attr-img-link" href="<?php echo $quote['attribution_url']; ?>" target="_blank">
													<img class="quote-image" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
												</a>
											<?php endif; ?>
										<?php else : ?>
											<?php if ($quote['attribution']) : ?>
												<div class="quote-attribution h6">
													<?php
													if( $quote['headshot']){
														?>
														<img class="quote-headshot" src="<?php echo esc_url($quote['headshot']['sizes']['medium']); ?>" alt="<?php if (esc_attr($quote['headshot']['alt'])) : ?> <?php echo esc_attr($quote['headshot']['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
														<?php
													}
													?>
													<span><?php echo $quote['attribution']; ?></span>
												</div>
											<?php endif; ?>
											<?php $image = $quote['attribution_logo'];
											if (!empty($image)) : ?>
												<img class="quote-image" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
											<?php endif; ?>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<?php if (count($quotes) > 1) { ?>
							</div>
						<?php } ?>
					<?php
					}
					?>
					<?php if (count($quotes) > 1) { ?>
					</div>
					<div class="swiper-pagination"></div>

					<div class="swiper-button-prev"></div>
					<div class="swiper-button-next"></div>
				</div>
			<?php } ?>
		</div>
	</section>
<?php endif; ?>