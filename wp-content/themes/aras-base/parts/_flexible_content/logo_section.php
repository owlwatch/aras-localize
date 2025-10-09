<?php if (get_sub_field('show_for_this_language') != 'hide') : ?>
	<?php $modnum = get_row_index(); ?>
	<?php if (get_sub_field('anchor_link')) : ?>
		<?php $anchor = ('id="' . get_sub_field('anchor_link') . '"'); ?>
	<?php else : ?>
		<?php $anchor = ('id="logo-section-' . $modnum . '"'); ?>
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

	<?php if (get_sub_field('logo_style') == 'greyscale') : ?>
		<?php $logostyle = 'greyscale-logos'; ?>
	<?php else : ?>
		<?php $logostyle = ''; ?>
	<?php endif; ?>

	<?php $text_color = get_sub_field('text_color') ?: 'text-dark' ?>
	<section class="logo-section <?= "$toppadding $bottompadding $bg_color $text_color" ?>" <?= "$anchor" ?>>
		<?php get_template_part('parts/_template_parts/background_visual'); ?>
		<div class="grid-container">

			<?php if (get_sub_field('content_before')) : ?>
				<div class="grid-x grid-padding-x <?php if (get_sub_field('content_before_position') == 'center') : ?>align-center<?php endif; ?>">
					<div class="cell small-12 medium-11 large-10 content-before">
						<div class="wysiwyg-content"><?php echo get_sub_field('content_before'); ?></div>
					</div>
				</div>
			<?php endif; ?>

			<?php
			$logos_to_show = get_sub_field('logos_to_show');
			?>

			<?php if (have_rows('logos')) : ?>
				<div class="grid-x grid-padding-x logo-slider-slick <?php echo $logostyle; ?>" data-logos-to-show="<?php echo $logos_to_show ?>" >
					<?php while (have_rows('logos')) : the_row(); ?>

						<?php $image = get_sub_field('logo_image');
						if (!empty($image)) : ?>

							<?php $link = get_sub_field('logo_link');
							if ($link) : $link_url = $link['url'];
								$link_title = $link['title'];
								$link_target = $link['target'] ? $link['target'] : '_self';
							?>
								<a aria-label="<?php echo esc_html($link_title); ?>" class="logolink" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
									<img class="logoimg" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
								</a>
							<?php else : ?>
								<div class="">
									<img class="logoimg" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
								</div>
							<?php endif; ?>

						<?php endif; ?>

					<?php endwhile; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<?php
if( empty( $_REQUEST['logo_slider_shown']) ) {
	$_REQUEST['logo_slider_shown'] = true;
}
else {
	return;
}
add_action('wp_footer', function() {

	?>
	<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/slick/slick.min.js"></script>
	<script>
			jQuery('.logo-slider-slick').each( function(){
				const logos_to_show = jQuery(this).data('logos-to-show');
				let count, countmed, countsmall, countxsmall;
				switch( logos_to_show ) {
					case 'three':
						count = 3;
						countmed = 3;
						countsmall = 2;
						countxsmall = 1;
						break;
					case 'four':
						count = 4;
						countmed = 3;
						countsmall = 2;
						countxsmall = 1;
						break;
					case 'five':
						count = 5;
						countmed = 4;
						countsmall = 3;
						countxsmall = 2;
						break;
					
					default:
						count = 6;
						countmed = 4;
						countsmall = 3;
						countxsmall = 2;
				}

				jQuery(this).slick({
					infinite: true,
					slidesToShow: count,
					slidesToScroll: 1,
					dots: false,
					arrows: false,
					autoplay: true,
					autoplaySpeed: 3000,
					pauseOnHover: false,
					speed: 500,
					cssEase: 'ease-in-out',
					draggable: true,
					pauseOnHover: false,
					swipeToSlide: true,
					responsive: [{
							breakpoint: 1023,
							settings: {
								slidesToShow: countmed
							},
						},
						{
							breakpoint: 639,
							settings: {
								slidesToShow: countsmall
							},
						},
						{
							breakpoint: 450,
							settings: {
								slidesToShow: countxsmall
							}
						}
					]
				});
			});
	</script>
	<?php $logo_slider_shown = true;  ?>
	<?php
});
?>