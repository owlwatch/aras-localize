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
$background_color = get_sub_field('background_color')?:'white';
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
		
		<div class="grid-x grid-padding-x <?php if (get_sub_field('content_before_position') == 'center') : ?>align-center<?php endif; ?>">
			<div class="cell small-12 content-before">
				<?php

				// we need to get the competitors
				$competitors = get_sub_field('competitors');

				// and the capabilities
				$capabilities = get_sub_field('capabilities');
				?>
				<table>
					<thead>
						<tr>
							<th></th>
							<?php
							foreach( $competitors as $competitor ){
								?>
								<th><?php echo $competitor->post_title ?></th>
								<?php
							}
							?>
						</tr>
					</thead>
					<tbody>
						<?php foreach( $capabilities as $capability ){
							?>
						<tr>
							<th><?php echo esc_html( $capability->name ); ?></th>
							<?php
							foreach( $competitors as $competitor ){
								// get the rating for this capability and competitor
								$rating = get_post_meta( $competitor->ID, 'aras-compare-' . $capability->slug . '-rating', true );
								$description = get_post_meta( $competitor->ID, 'aras-compare-' . $capability->slug . '-description', true );
								?>
								<td>
									<div class="aras-compare-rating">
										<?php
										for ( $i = 0; $i <= 4; $i++ ){
											if( $rating == $i ){
												echo '<span class="aras-compare-rating-star filled">★</span>';
											}
											else {
												echo '<span class="aras-compare-rating-star">☆</span>';
											}
										}
										?>
									</div>
									<div class="aras-compare-description"><?php echo esc_html( $description ); ?></div>
								</td>
								<?php
							}
							?>
						</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>