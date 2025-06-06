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

<section class="<?= "$toppadding $bottompadding $bg_color" ?> <?php if (get_sub_field('background_image') != '') : ?>has-bg-img<?php endif; ?>" <?= "$anchor" ?> <?php if (get_sub_field('background_image')) : ?>title="<?php echo esc_attr($background_image['alt']); ?>" style="background-image: url(<?php echo esc_url($background_image['url']); ?>);min-height: calc((<?php echo ($background_image['height']); ?> / <?php echo ($background_image['width']); ?>) * 100vw);<?php echo $bgp; ?>" <?php endif; ?>>
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

				// show harvey ball
				$show_harvey_ball = get_sub_field('show_harvey_ball');

				// use tooltip
				$use_tooltip = get_sub_field('use_tooltip');
				?>
				<table class="competitor-table <?php if( $show_harvey_ball ){ ?>competitor-table--harvey-balls<?php } ?> <?php if( $use_tooltip ){ ?>competitor-table--tooltip<?php } ?>">
					<thead>
						<tr>
							<th>
								<?php
								echo get_sub_field('table_title');
								?>
							</th>
							<?php
							foreach( $competitors as $competitor ){
								?>
								<th><?php 
								// if the competitor has a logo (featured image), show it
								if( has_post_thumbnail( $competitor->ID ) ){
									$logo = get_the_post_thumbnail( $competitor->ID, 'medium' );
									echo $logo;
								} else {
									echo esc_html( $competitor->post_title );
								}
								?></th>
								<?php
							}
							?>
						</tr>
					</thead>
					<tbody>
						<?php foreach( $capabilities as $capability ){
							?>
						<tr>
							<th>
								<div class="competitor-table__capability-name">
									<?php echo esc_html( $capability->name ); ?>
								</div>
								<?php if( $capability->description ){ ?>
									<div class="competitor-table__capability-description">
										<?php echo esc_html( $capability->description ); ?>
									</div>
								<?php } ?>
							</th>
							<?php
							foreach( $competitors as $competitor ){
								// get the rating for this capability and competitor
								$rating = get_post_meta( $competitor->ID, 'aras-compare-' . $capability->slug . '-rating', true );
								$description = get_post_meta( $competitor->ID, 'aras-compare-' . $capability->slug . '-description', true );
								?>
								<td>
									<div class="competitor-table__difference">
										<?php if( $show_harvey_ball ){ ?>
											<div class="right harvey-ball harvey-ball--<?php echo esc_attr( $rating * 25 ); ?>" <?php if( $use_tooltip && $description ){ ?>data-tooltip title="<?php echo esc_attr( $description ); ?>"<?php } ?>></div>
										</td>
										<?php } ?>
										<?php if( $show_harvey_ball && !$use_tooltip || !$show_harvey_ball) { ?>
											<div class="description">
												<?php echo esc_html( $description ); ?>
											</div>

										<?php } ?>
									</div>
								<?php
							}
							?>
						</tr>
							<?php
						}
						?>
					</tbody>
				</table>
				<?php if( $show_harvey_ball ){ ?>
				<div class="competitor-table__legend">
					<?php
					for( $i=0; $i<6; $i++ ){
						$harvey_ball_class = 'harvey-ball--' . ( $i * 25 );
						$harvey_ball_key = get_field( 'harvey_ball_legend_key_' . ( $i * 25 ), 'option' );
						if( !$harvey_ball_key ){
							continue;
						}
						?>
						<div class="competitor-table__legend-item">
							<div class="harvey-ball <?php echo esc_attr( $harvey_ball_class ); ?>"></div>
							<span><?php echo $harvey_ball_key; ?></span>
						</div>
						<?php
					}
					?>
				<?php } ?>
			</div>
		</div>
	</div>
</section>
<?php
if( !function_exists('_aras_should_print_competitor_script') ){
	function _aras_should_print_competitor_script(){
		static $printed = false;
		if( $printed ){
			return false;
		}
		$printed = true;
		return true;
	}
}
if( _aras_should_print_competitor_script() ){
	?>
<script>
	// when the 'competitor-table' width is less than 700px', we want to
	// copy the logo (or text) in the thead th into its corresponding tbody td
	// and then remove it when the width is greater than 700px. the logo should
	// be wrapped in a div with the class 'competitor-table__difference-logo'
	jQuery(document).ready(function($) {
		function adjustCompetitorTable() {
			
			$('.competitor-table').each(function() {
				const table = this;
				if ($(table).width() < 700) {
					if( $(table).hasClass('competitor-table--mobile') ){
						return;
					}
					$(table).addClass('competitor-table--mobile');

					$(table).find('thead th').each(function(index) {
						if( index == 0 ){
							return;
						}
						const html = $(this).html();
						const company = $('<div class="competitor-table__difference-logo"></div>').html(html);
						$(table).find('tbody tr td:nth-child(' + (index + 1) + ')').prepend(company);
					});
				} else {
					if( !$(table).hasClass('competitor-table--mobile') ){
						return;
					}
					$(table).removeClass('competitor-table--mobile');
					$(table).find('tbody tr td .competitor-table__difference-logo').remove();
				}
			});
		}

		// Initial adjustment
		adjustCompetitorTable();

		// Adjust on window resize
		$(window).resize(function() {
			adjustCompetitorTable();
		});
	});
</script>
	<?php
}
?>