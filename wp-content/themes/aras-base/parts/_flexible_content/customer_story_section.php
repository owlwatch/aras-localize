<?php if (get_sub_field('show_for_this_language') != 'hide') : ?>
	<?php $modnum = get_row_index(); ?>
	<?php if (get_sub_field('anchor_link')) : ?>
		<?php $anchor = ('id="' . get_sub_field('anchor_link') . '"'); ?>
	<?php else : ?>
		<?php $anchor = ('id="customerstorysection-' . $modnum . '"'); ?>
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

	<?php if (get_sub_field('background_image_side') == 'left') : ?>
		<?php $imageside = 'background-left-img'; ?>
		<?php $contentside = 'align-right'; ?>
	<?php else : ?>
		<?php $imageside = 'background-right-img'; ?>
		<?php $contentside = 'align-left'; ?>
	<?php endif; ?>
	<?php $text_color = get_sub_field('text_color') ?: 'text-dark' ?>
	<section class="customer-story-section <?= "$toppadding $bottompadding $bg_color $text_color" ?>" <?= "$anchor" ?>>
		<?php get_template_part('parts/_template_parts/background_visual'); ?>
		<?php if (get_sub_field('background_image')) : ?>
			<?php $image = get_sub_field('background_image'); ?>
			<div class="customer-bg-overlay <?php echo $imageside; ?>"></div>
			<div class="customer-bg-img <?php echo $imageside; ?>" title="<?php echo esc_attr($image['alt']); ?>" style="background-image: url(<?php echo esc_url($image['url']); ?>);"></div>
		<?php endif; ?>
		<div class="grid-container">
			<div class="grid-x grid-padding-x <?php echo $contentside; ?>">
				<div class="cell small-12 medium-9 large-7">
					<?php $image = get_sub_field('customer_logo');
					if (!empty($image)) : ?>
						<img class="customer-logo" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
					<?php endif; ?>
					<?php if (get_sub_field('customer_subhead')) : ?>
						<h6><?php echo get_sub_field('customer_subhead'); ?></h6>
					<?php endif; ?>
					<?php if (get_sub_field('customer_headline')) : ?>
						<h3><?php echo get_sub_field('customer_headline'); ?></h3>
					<?php endif; ?>
					<?php if (get_sub_field('customer_content')) : ?>
						<div class="wysiwyg-content">
							<?php echo get_sub_field('customer_content'); ?>
						</div>
					<?php endif; ?>
					<?php $link = get_sub_field('customer_link');
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
	</section>
<?php endif; ?>