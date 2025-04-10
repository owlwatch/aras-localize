<?php if (get_sub_field('show_for_this_language') == 'hide') : ?>
	<?php return; ?>
<?php endif; ?>
<?php $modnum = get_row_index(); ?>
<?php if (get_sub_field('anchor_link')) : ?>
	<?php $anchor = ('id="' . get_sub_field('anchor_link') . '"'); ?>
<?php else : ?>
	<?php $anchor = ('id="contentsection-' . $modnum . '"'); ?>
<?php endif; ?>
<?php
$background_color = get_sub_field('background_color');
$top_padding = get_sub_field('top_padding_amount');
$bottom_padding = get_sub_field('bottom_padding_amount');
$bg_color = '';
$toppadding = '';
$bottompadding = '';
$isFaq = get_sub_field('is_faq');
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
<?php $horizontal_alignment = get_sub_field('horizontal_alignment');
// this is named backwards. horizontal_alignment is actually 'Vertical Alignment'
switch ($horizontal_alignment) {
	case 'top':
		$horiz = 'align-top';
		break;
	case 'middle':
		$horiz = 'align-middle';
		break;
	case 'bottom':
		$horiz = 'align-bottom';
		break;
	default:
		$horiz = 'align-top';
}
?>
<style>
.accordion .accordion-title {
	font-family: "azo-sans-web", sans-serif;
	font-size: 1.25rem;
	font-weight: 300;
	line-height: 1;
	margin-bottom: 1.5rem;
	color: #4f4f4f;
	border: none;
	background: white;
	border-bottom: 1px solid #d6d6d6;
	margin-bottom: 0;
}

.accordion .accordion-title:before {
	margin-top: 0;
	transform: translateY(-50%);
}

.accordion .accordion-item.is-active .accordion-title {
	border-bottom: 0;
}

.accordion .accordion-content {
	border-top: 0;
	border-right: 0;
	border-bottom: 1px solid #d6d6d6;
	border-left: 0;
}

.accordion .accordion-content p {
	font-size: .9375rem;
	margin: 0 0 .5rem 0;
	color: #4f4f4f;
}

.accordion .accordion-content a {
	font-weight: 500;
}
</style>

<?php if (get_sub_field('background_image')) : ?>
	<?php $background_image = get_sub_field('background_image'); ?>

	<?php $bg_placement = get_sub_field('background_image_position');
	switch ($bg_placement) {
		case 'topleft':
			$bgp = 'background-position: top left';
			break;
		case 'topcenter':
			$bgp = 'background-position: top center';
			break;
		case 'topright':
			$bgp = 'background-position: top right';
			break;
		case 'middleleft':
			$bgp = 'background-position: center left';
			break;
		case 'middlecenter':
			$bgp = 'background-position: center center';
			break;
		case 'middleright':
			$bgp = 'background-position: center right';
			break;
		case 'bottomleft':
			$bgp = 'background-position: bottom left';
			break;
		case 'bottomcenter':
			$bgp = 'background-position: bottom center';
			break;
		case 'bottomright':
			$bgp = 'background-position: bottom right';
			break;
		default:
			$bgp = 'background-position: top left';
	}
	?>
<?php endif; ?>

<section class="content-section <?= "$toppadding $bottompadding $bg_color" ?> <?php if (get_sub_field('background_image') != '') : ?>has-bg-img<?php endif; ?>" <?= "$anchor" ?> <?php if (get_sub_field('background_image')) : ?>title="<?php echo esc_attr($background_image['alt']); ?>" style="background-image: url(<?php echo esc_url($background_image['url']); ?>);min-height: calc((<?php echo ($background_image['height']); ?> / <?php echo ($background_image['width']); ?>) * 100vw);<?php echo $bgp; ?>" <?php endif; ?>>
	<?php get_template_part('parts/_template_parts/background_visual'); ?>
	<div class="grid-container">
		<?php if (get_sub_field('content_before')) : ?>
			<div class="grid-x grid-padding-x <?php if (get_sub_field('content_before_position') == 'center') : ?>align-center<?php endif; ?>">
				<div class="cell small-12 content-before">
					<div class="wysiwyg-content"><?php echo get_sub_field('content_before'); ?></div>
				</div>
			</div>
		<?php endif; ?>
		<ul 
			class="accordion"
			data-accordion
			data-allow-all-closed="true"
			data-multi-expand="true"
			<?php if( $isFaq ): ?>
			itemscope itemtype="https://schema.org/FAQPage"
			<?php endif; ?>
		>
			<?php while( have_rows('items')) : the_row(); ?>
				<?php $accordion_heading = get_sub_field('heading'); ?>
				<?php $accordion_content = get_sub_field('content'); ?>
				<li
					class="accordion-item"
					data-accordion-item
					<?php if( $isFaq ): ?>
					itemscope itemprop="mainEntity" itemtype="https://schema.org/Question"
					<?php endif; ?>
				>
					<a 
						href="#"
						class="accordion-title"
						<?php if( $isFaq ): ?>
						itemprop="name"
						<?php endif; ?>
					>
						<?php echo esc_html($accordion_heading); ?>
					</a>
					<div
						class="accordion-content wysiwyg-content"
						data-tab-content
						<?php if( $isFaq ): ?>
						itemprop="acceptedAnswer"
						itemscope
						itemprop="text"
						itemtype="https://schema.org/Answer"
						<?php endif; ?>
					>
						<?php echo wp_kses_post($accordion_content); ?>
					</div>
				</li>
			<?php endwhile; ?>
		</ul>
	</div>
</section>