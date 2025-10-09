<?php
if( empty($_REQUEST['card_slider_script_included']) ) {
	$_REQUEST['card_slider_script_included'] = true;
} else {
	return;
}
add_action('wp_footer', function(){
	?>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/slick/slick.min.js"></script>
<script>
jQuery(document).ready(function($){
	jQuery('.card-slider-slick').each( function(){
		// if there is only one card, do not slick it up
		if( jQuery(this).find('>*').length < 2 ){
			return;
		}
		// get the per row data attribute, default to 3
		let perRow = $(this).data('per-row') ? $(this).data('per-row') : 3;
		let dots = $(this).data('dots');
		let autoplay = $(this).data('autoplay');
		if( autoplay == 'disabled') autoplay = false;
		
		$(this).slick({
			infinite: true,
			slidesToShow: perRow,
			slidesToScroll: 1,
			dots,
			arrows: $(this).parent().find('.card-arrows').length ? true : false,
			appendArrows: $(this).parent().find('.card-arrows'),
			autoplay,
			autoplaySpeed: autoplay,
			pauseOnHover: false,
			speed: 300,
			cssEase: 'linear',
			draggable: true,
			pauseOnHover: false,
			swipeToSlide: true,
			responsive: [{
					breakpoint: 1023,
					settings: {
						slidesToShow: perRow
					},
				},
				{
					breakpoint: 639,
					settings: {
						slidesToShow: perRow > 1 ? 2 : 1
					},
				},
				{
					breakpoint: 450,
					settings: {
						slidesToShow: 1
					}
				}
			]
		});
	});
});
</script>
	<?php
});