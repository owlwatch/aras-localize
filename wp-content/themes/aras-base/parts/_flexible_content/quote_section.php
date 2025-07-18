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
			<div class="grid-x grid-margin-x">

				<div class="cell small-12 medium-11 large-10 quote-container">
					<?php if (get_sub_field('quote_content')) : ?>
						<h3><?php echo get_sub_field('quote_content', false, false); ?></h3>
					<?php endif; ?>
					<?php if (get_sub_field('quote_attribution_url')) : ?>
						<?php if (get_sub_field('quote_attribution')) : ?>
							<a aria-label="<?php echo get_sub_field('quote_attribution'); ?>" class="attr-link" href="<?php echo get_sub_field('quote_attribution_url'); ?>" target="_blank">
								<h6><?php echo get_sub_field('quote_attribution'); ?></h6>
							</a>
						<?php endif; ?>
						<?php $image = get_sub_field('quote_attribution_logo');
						if (!empty($image)) : ?>
							<a aria-label="<?php echo get_sub_field('quote_attribution'); ?>" class="attr-img-link" href="<?php echo get_sub_field('quote_attribution_url'); ?>" target="_blank">
								<img class="quote-image" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
							</a>
						<?php endif; ?>
					<?php else : ?>
						<?php if (get_sub_field('quote_attribution')) : ?>
							<h6><?php echo get_sub_field('quote_attribution'); ?></h6>
						<?php endif; ?>
						<?php $image = get_sub_field('quote_attribution_logo');
						if (!empty($image)) : ?>
							<img class="quote-image" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
						<?php endif; ?>
					<?php endif; ?>
				</div>

			</div>
		</div>
	</section>
<?php endif; ?>