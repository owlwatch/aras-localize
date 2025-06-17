<?php

/**
 * The template for displaying the footer. 
 * Contains closing divs for header.php.
 * For more info: https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */
?>
<?php if (get_field('footer_type') == 'simplefoot') : ?>
	<footer class="simple-footer bg-dgrey" role="contentinfo">
		<div class="grid-container">
			<div class="grid-x grid-margin-x">
				<div class="cell small-12 medium-shrink copyrightinfo">
					<p>Copyright &copy; <?php echo date('Y'); ?>&nbsp;Aras. All rights reserved.</p>
				</div>
				<div class="cell small-12 medium-auto copyrightinfo">
					<?php if (get_field('footer_copyright_content', 'option')) : ?><?php echo get_field('footer_copyright_content', 'option'); ?><?php endif; ?>
				</div>
			</div>
		</div>
	</footer>
<?php elseif (get_field('footer_type') == 'none') : ?>
<?php else : ?>
	<footer class="footer bg-dgrey" role="contentinfo">
		<div class="grid-container">
			<div class="grid-x grid-margin-x">
				<div class="cell small-12 medium-shrink footer-intro small-order-1 medium-order-1 large-order-1">
					<?php $footerlogo = get_field('website_logo', 'option');
					if (!empty($footerlogo)) : ?>
						<a aria-label="Homepage" class="footer-logo-link" href="<?php echo home_url(); ?>">
							<img class="nav-logo" src="<?php echo esc_url($footerlogo['url']); ?>" alt="<?php if (esc_attr($footerlogo['alt'])) : ?> <?php echo esc_attr($footerlogo['alt']); ?> <?php else :	?> <?php the_title(); ?> <?php endif; ?>" />
						</a>
					<?php endif; ?>
					<?php if (get_field('footer_description', 'option')) : ?>
						<?php echo get_field('footer_description', 'option'); ?>
					<?php endif; ?>
				</div>
				<?php if (have_rows('footer_nav_column', 'option')) : ?>
					<nav class="cell small-12 medium-12 large-auto footer-links small-order-2 medium-order-3 large-order-2" role="navigation">
						<?php while (have_rows('footer_nav_column', 'option')) : the_row(); ?>
							<div class="footer-column">
								<?php if (get_sub_field('footer_column_label', 'option')) : ?>
									<div class="h6"><?php echo get_sub_field('footer_column_label', 'option'); ?></div>
								<?php endif; ?>
								<?php if (have_rows('footer_column_items', 'option')) : ?>
									<?php while (have_rows('footer_column_items', 'option')) : the_row(); ?>
										<?php $link = get_sub_field('footer_nav_item', 'option');
										if ($link) : $link_url = $link['url'];
											$link_title = $link['title'];
											$link_target = $link['target'] ? $link['target'] : '_self';
										?>
											<a aria-label="<?php echo esc_html($link_title); ?>" class="footer-linkitem" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
												<?php echo esc_html($link_title); ?>
											</a>
										<?php endif; ?>
									<?php endwhile; ?>
								<?php endif; ?>
							</div>
						<?php endwhile; ?>
					</nav>
				<?php endif; ?>
				<div class="cell small-12 medium-auto large-shrink footer-social small-order-3 medium-order-2 large-order-3">
					<?php if (get_field('social_label', 'option')) : ?>
						<div class="h6"><?php echo get_field('social_label', 'option'); ?></div>
					<?php endif; ?>
					<?php if (have_rows('social_links', 'option')) : ?>
						<div class="footer-socials">
							<?php while (have_rows('social_links', 'option')) : the_row(); ?>
								<?php $image = get_sub_field('social_icon', 'option');
								if (!empty($image)) : ?>
									<?php if (get_sub_field('social_url', 'option')) : ?>
										<a aria-label="<?php the_title() ?>" href="<?php echo get_sub_field('social_url', 'option'); ?>" target="_blank">
											<img src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
										</a>
									<?php endif; ?>
								<?php endif; ?>
							<?php endwhile; ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="cell small-12 medium-10 large-8 copyrightinfo small-order-4 medium-order-4 large-order-4">
					<p>Copyright &copy; <?php echo date('Y'); ?>&nbsp;Aras. All rights reserved.</p>
				</div>
			</div>
		</div>
	</footer>
<?php endif; ?>

</div>
</div>

<?php wp_footer(); ?>


<?php
if (have_posts()) : while (have_posts()) : the_post();
		if (have_rows('flexible_content')) :
			while (have_rows('flexible_content')) : the_row();
				$card_slider_shown = false;
				$logo_slider_shown = false;
				$content_section_empty_checker = false;
				if (get_row_layout() == 'cards_section' || get_row_layout() == 'automatic_cards_section') : ?>
					<?php if (get_sub_field('cards_per_row') == 'slider' && !$card_slider_shown) : ?>
						<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/slick/slick.min.js"></script>
						<script>
								jQuery('.card-slider-slick').slick({
									infinite: true,
									slidesToShow: 3,
									slidesToScroll: 1,
									dots: false,
									arrows: true,
									appendArrows: jQuery('.card-arrows'),
									autoplay: true,
									autoplaySpeed: 5000,
									pauseOnHover: false,
									speed: 500,
									cssEase: 'linear',
									draggable: true,
									pauseOnHover: false,
									swipeToSlide: true,
									responsive: [{
											breakpoint: 1023,
											settings: {
												slidesToShow: 3
											},
										},
										{
											breakpoint: 639,
											settings: {
												slidesToShow: 2
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
						</script>
						<?php $card_slider_shown = true;  ?>
					<?php endif; ?>
				<?php endif; ?>
				<?php if (get_row_layout() == 'logo_section') : ?>
					<?php if (!$logo_slider_shown) : ?>
						<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/slick/slick.min.js"></script>
						<script>
								jQuery('.logo-slider-slick').each( () => {
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
					<?php endif; ?>
				<?php endif; ?>
				<?php if (get_row_layout() == 'content_section') : ?>
					<script>
						// Get all elements with class 'content-section'
						// If section contains one '.text-block' element and it has no content other than text nodes
						// Find the closest '.content-section' and hide it
						document.addEventListener("DOMContentLoaded", function() {
							var textBlocks = document.querySelectorAll('.text-block');
							textBlocks.forEach(function(textBlock) {
								var hasOnlyTextNodes = Array.from(textBlock.childNodes).every(function(node) {
									return node.nodeType === Node.TEXT_NODE;
								});
								if (hasOnlyTextNodes && textBlock.textContent.trim() === '') {
									var contentSection = textBlock.closest('.content-section');
									if (contentSection) {
										contentSection.style.display = 'none';
									}
								}
							});
						});
					</script>
					<?php $content_section_empty_checker = true;  ?>
				<?php endif; ?>
			<?php endwhile; ?>
		<?php endif; ?>
	<?php endwhile; ?>
<?php endif; ?>



<?php /* Remove the Aras Cookie structure
<script type="text/javascript">
	//This is used to send data from button clicks to MyI
	document.addEventListener("DOMContentLoaded", function() {
		jQuery(function() {

			function getAllCookies() {
				var cookies = document.cookie.split(/;\s/g);
				var cookieObj = {};
				cookies.forEach(function(item) {
					var key = item.split('=')[0];
					cookieObj[key] = item.split('=')[2];
				});
				return cookieObj;
			}
			//Get cookies by key, this method depends on getAllCookies()
			function getCookieByKey(key) {
				return getAllCookies()[key];
			}

			function getData(cname) {
				var theCookie = "";
				if (getCookieByKey(cname) != null) {
					theCookie = getCookieByKey(cname);
				} else {
					theCookie = "No Cookie ID or Cookies Disabled";
				}
				return theCookie;
			}
		});

		// This part is for adding a madeup arasid cookie if not on the production site.
		var checkWebDomain = document.location.origin;
		var getRandoCookie = function(name) {
			var value = "; " + document.cookie;
			var parts = value.split("; " + name + "=");
			if (parts.length == 2) return parts.pop().split(";").shift();
		};
		var isThereCookie = getRandoCookie('arascorp');
		if (checkWebDomain != "https://www.aras.com" && isThereCookie == null) {
			function setRandoCookie(name, value, days) {
				var d = new Date;
				d.setTime(d.getTime() + 24 * 60 * 60 * 1000 * days);
				document.cookie = name + "=" + value + ";path=/;expires=" + d.toGMTString();
			}
			var randomInteger = function(pow) {
				return Math.floor(Math.random() * pow);
			};
			var rand1 = randomInteger(99999999);
			var rand2 = randomInteger(99999999);
			var newTestArasID = "arasid=TestValue" + rand1 + rand2;
			setRandoCookie('arascorp', newTestArasID);
		}
	});
</script>
*/ ?>



</body>

</html>