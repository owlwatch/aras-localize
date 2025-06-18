<?php
if( !function_exists('aras_map_section_id') ){
	function aras_map_section_count() {
		static $count = 0;
		return $count++;
	}
}
$index = aras_map_section_count();
$map_section_id = 'aras-map-section-' . $index;

$location = get_sub_field('address');
?>
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

<section class="content-section map-section <?= "$toppadding $bottompadding $bg_color" ?> <?php if (get_sub_field('background_image') != '') : ?>has-bg-img<?php endif; ?>" <?= "$anchor" ?> <?php if (get_sub_field('background_image')) : ?>title="<?php echo esc_attr($background_image['alt']); ?>" style="background-image: url(<?php echo esc_url($background_image['url']); ?>);min-height: calc((<?php echo ($background_image['height']); ?> / <?php echo ($background_image['width']); ?>) * 100vw);<?php echo $bgp; ?>" <?php endif; ?>>
	<?php get_template_part('parts/_template_parts/background_visual'); ?>
	<div class="grid-container">
		<?php if (get_sub_field('content_before')) : ?>
			<div class="grid-x grid-padding-x <?php if (get_sub_field('content_before_position') == 'center') : ?>align-center<?php endif; ?>">
				<div class="cell small-12 content-before">
					<div class="wysiwyg-content"><?php echo get_sub_field('content_before'); ?></div>
				</div>
			</div>
		<?php endif; ?>
		<div class="acf-map__container">
			<iframe
				width="100%"
				height="450"
				style="border:0"
				loading="lazy"
				allowfullscreen
				referrerpolicy="no-referrer-when-downgrade"
				class="acf-map"
				src="https://www.google.com/maps/embed/v1/place?key=<?php 
				echo get_field('google_maps_api_key', 'options'); ?>&q=place_id:<?php echo $location['place_id']; ?>">
			</iframe>
			<?php 
			$side_content_position = get_sub_field('side_content');
			if( $side_content_position !== 'none' ){
				?>
			<div class="acf-map__side-content acf-map__side-content--<?php echo $side_content_position; ?>">
				<?php echo get_sub_field('side_content_html'); ?>
			</div>
				<?php
			}
			?>
		</div>
	</div>
</section>