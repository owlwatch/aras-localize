<?php if (get_sub_field('show_for_this_language') != 'hide') : ?>
	<?php $modnum = get_row_index(); ?>
	<?php if (get_sub_field('anchor_link')) : ?>
		<?php $anchor = ('id="' . get_sub_field('anchor_link') . '"'); ?>
	<?php else : ?>
		<?php $anchor = ('id="floatingctasection-' . $modnum . '"'); ?>
	<?php endif; ?>
	<?php
	$background_color = get_sub_field('background_color');
	$cta_box_color = get_sub_field('cta_box_color');
	$top_padding = get_sub_field('top_padding_amount');
	$bottom_padding = get_sub_field('bottom_padding_amount');
	$bg_color = '';
	$cta_boxcolor = '';
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
	switch ($cta_box_color) {
		case 'white':
			$cta_boxcolor = 'box-bg-white';
			break;
		case 'grey':
			$cta_boxcolor = 'bg-grey';
			break;
		case 'dblue':
			$cta_boxcolor = 'bg-dblue';
			break;
		case 'whitetogrey':
			$cta_boxcolor = 'bg-whitetogrey';
			break;
		case 'greytowarm':
			$cta_boxcolor = 'bg-greytowarm';
			break;
		default:
			$cta_boxcolor = 'box-bg-white';
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


	<?php if (get_sub_field('cta_image_placement') == 'breaktop') : ?>
		<?php $imgplace = 'breaktop'; ?>
	<?php else : ?>
		<?php $imgplace = ''; ?>
	<?php endif; ?>
	<?php if (get_sub_field('cta_image_orientation') == 'landscape') : ?>
		<?php $imgorient = 'landscapeimg'; ?>
	<?php else : ?>
		<?php $imgorient = ''; ?>
	<?php endif; ?>
	<section class="floating-cta-section <?= "$bg_color $toppadding $bottompadding" ?>" <?= "$anchor" ?>>
		<div class="grid-container">
			<div class="grid-x grid-margin-x">
				<div class="cell small-12 floating-cta <?= "$cta_boxcolor $imgplace" ?>">
					<div class="cta-image <?= "$imgorient" ?>">
						<?php $image = get_sub_field('cta_image');
						if (!empty($image)) : ?>
							<img src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
						<?php endif; ?>
					</div>
					<div class="cta-content">
						<?php if (get_sub_field('cta_subhead')) : ?>
							<h6><?php echo get_sub_field('cta_subhead'); ?></h6>
						<?php endif; ?>
						<?php if (get_sub_field('cta_headline')) : ?>
							<h3><?php echo get_sub_field('cta_headline'); ?></h3>
						<?php endif; ?>
						<?php if (get_sub_field('cta_content')) : ?>
							<?php echo get_sub_field('cta_content'); ?>
						<?php endif; ?>
						<?php $link = get_sub_field('cta_button');
						if ($link) : $link_url = $link['url'];
							$link_title = $link['title'];
							$link_target = $link['target'] ? $link['target'] : '_self';
						?>
							<a aria-label="<?php echo esc_html($link_title); ?>" class="aras-button--outline" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
								<?php echo esc_html($link_title); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>