<?php if (get_sub_field('background_visual_style') != 'none') : ?>
	<?php if (get_sub_field('greyscale_visual')) : ?>
		<?php $greyscale = 'greyscale-img'; ?>
	<?php else : ?>
		<?php $greyscale = ''; ?>
	<?php endif; ?>
<?php endif; ?>

<?php if (get_sub_field('background_visual_style') == 'left') : ?>
	<?php $image = get_sub_field('background_image'); ?>
	<div class="background-pattern-half-left <?php echo $greyscale; ?>" title="<?php echo esc_attr($image['alt']); ?>" style="background-image: url(<?php echo esc_url($image['url']); ?>);"></div>
<?php elseif (get_sub_field('background_visual_style') == 'right') : ?>
	<?php $image = get_sub_field('background_image'); ?>
	<div class="background-pattern-half <?php echo $greyscale; ?>" title="<?php echo esc_attr($image['alt']); ?>" style="background-image: url(<?php echo esc_url($image['url']); ?>);"></div>
<?php elseif (get_sub_field('background_visual_style') == 'full') : ?>
	<?php $image = get_sub_field('background_image'); ?>
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
	<div class="background-pattern-full <?php echo $greyscale; ?>" title="<?php echo esc_attr($image['alt']); ?>" style="background-image: url(<?php echo esc_url($image['url']); ?>); <?php echo $bgp; ?>"></div>
<?php elseif (get_sub_field('background_visual_style') == 'video') : ?>
	<?php if (get_sub_field('background_vidyard_video_id')) : ?>
		<iframe class="vidyard-player-background <?php echo $greyscale; ?>" src="//play.vidyard.com/<?php echo get_sub_field('background_vidyard_video_id'); ?>/type/background" scrolling="no" frameborder="0" allowtransparency="true" allowfullscreen="true"></iframe>
	<?php endif; ?>
<?php else : ?>
<?php endif; ?>


<?php if (get_sub_field('background_visual_style') != 'none') : ?>
	<?php if (get_sub_field('background_overlay') == 'darkoverlay') : ?>
		<div class="background-overlay-dark"></div>
	<?php elseif (get_sub_field('background_overlay') == 'lightoverlay') : ?>
		<div class="background-overlay-light"></div>
	<?php endif; ?>
<?php endif; ?>