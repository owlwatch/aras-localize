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

	<?php $text_color = get_sub_field('text_color') ?: 'text-dark' ?>

	<section class="full-width-form-section <?= "$bg_color $toppadding $bottompadding $text_color" ?>" <?= "$anchor" ?>>
		<?php get_template_part('parts/_template_parts/background_visual'); ?>
		<div class="grid-container">
			<?php if (get_sub_field('content_before')) : ?>
				<div class="grid-x grid-padding-x <?php if (get_sub_field('content_before_position') == 'center') : ?>align-center<?php endif; ?>">
					<div class="cell small-12 medium-11 large-10 content-before">
						<div class="wysiwyg-content"><?php echo get_sub_field('content_before'); ?></div>
					</div>
				</div>
			<?php endif; ?>

			<div class="grid-x grid-margin-x align-center">
				<div class="cell small-12 medium-10 large-7">
					<div id="hero-form-container" class="full-form-container">
						<?php if (get_sub_field('form_shortcode')) : ?>
							<div class="hero-form bg-white">
								<?php if (get_sub_field('form_headline')) : ?>
									<h4 class="hero-form-headline"><?php echo get_sub_field('form_headline')  ?></h4>
								<?php endif; ?>
								<?php $gravity_form_id = get_sub_field('form_shortcode');
								echo do_shortcode('[gravityform ajax="true" id="' . $gravity_form_id . '" title="false" description="false"]'); ?>
							</div>
							<?php get_template_part('parts/_template_parts/gform_variables'); ?>
						<?php endif; ?>
						<?php if (get_sub_field('content_below_form')) : ?>
							<div class="hero-form-end">
								<?php echo get_sub_field('content_below_form'); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>


<?php endif; ?>