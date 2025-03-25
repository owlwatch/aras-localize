<?php
namespace Aras\Marketplace;
use Aras\Marketplace\Service\Template;
use function Aras\Marketplace\app;
app()->templateService->enqueue_style();
get_header();

Template::get_template_part('marketplace/banner');

$address = get_field('address', get_queried_object());
$email = get_field('email', get_queried_object());
$phone = get_field('phone_number', get_queried_object());
$website = get_field('website', get_queried_object());

$social_links = get_field('social_links', get_queried_object());

$has_social_links = false;
if( $social_links ){
	foreach( $social_links as $key => $link ){
		if( $link ){
			$has_social_links = true;
			break;
		}
	}
}

?>
<section class="mp-contributor-banner bg-dgrey">
	<div class="grid-container">
		<?php
		$logo = get_field('logo', get_queried_object());
		if( $logo ){
			?>
			<div class="mp-contributor-banner__logo-wrapper">
			<?php
			echo wp_get_attachment_image( $logo['id'], 'large', false, [
				'class' => 'mp-contributor-banner__logo'
			]);
			?>
			</div>
			<?php
		}
		else {
			?>
			<h1 class="mp-contributor-banner__title">
				<?php
				single_term_title();
				?>
			</h1>
			<?php
		}
		?>
	</div>
</section>
<section class="mediumtoppadding mediumbottompadding">
	<div class="grid-container">
		<div class="mp-contributor-page">

			<?php
			// output the term description
			$description = term_description();
			if( $description ){
				?>
				<div class="mp-contributor-page__description">
					<?php echo $description; ?>
				</div>
				<?php
			}
			?>
			<div class="mp-contributor-page__columns">
				<div class="mp-contributor-page__contact-column">
					<h2><?php _e('Contact', 'aras-marketplace'); ?></h2>
					<div class="mp-contributor-page__contact-blocks">
						<?php
						if( $address ){
							?>
						<div class="mp-contributor-page__contact-block mp-contributor-page__contact-block--address">
							
							<div class="mp-contributor-page__contact-icon">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/></svg>
							</div>
							<div class="mp-contributor-page__contact-value">
								<?php 
								// $address is a multiline address, output
								// as a microformatted address
								echo nl2br( $address );
								?>
							</div>
						</div>
							<?php
						}
						if( $email ){
							?>
						<div class="mp-contributor-page__contact-block">
							
							<div class="mp-contributor-page__contact-icon">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M64 112c-8.8 0-16 7.2-16 16l0 22.1L220.5 291.7c20.7 17 50.4 17 71.1 0L464 150.1l0-22.1c0-8.8-7.2-16-16-16L64 112zM48 212.2L48 384c0 8.8 7.2 16 16 16l384 0c8.8 0 16-7.2 16-16l0-171.8L322 328.8c-38.4 31.5-93.7 31.5-132 0L48 212.2zM0 128C0 92.7 28.7 64 64 64l384 0c35.3 0 64 28.7 64 64l0 256c0 35.3-28.7 64-64 64L64 448c-35.3 0-64-28.7-64-64L0 128z"/></svg>
							</div>
							<div class="mp-contributor-page__contact-value">
								<a href="mailto:<?php echo $email; ?>">
								<?php echo $email; ?>
								</a>
							</div>
						</div>
							<?php
						}

						if( $phone ){
							?>
						<div class="mp-contributor-page__contact-block">
							
							<div class="mp-contributor-page__contact-icon">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M16 64C16 28.7 44.7 0 80 0L304 0c35.3 0 64 28.7 64 64l0 384c0 35.3-28.7 64-64 64L80 512c-35.3 0-64-28.7-64-64L16 64zM224 448a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM304 64L80 64l0 320 224 0 0-320z"/></svg>
							</div>
							<div class="mp-contributor-page__contact-value">
								<a href="tel:<?php echo preg_replace('/[^\d]/', '', $phone); ?>">
								<?php echo $phone; ?>
								</a>
							</div>
						</div>
							<?php
						}

						if( $website ){
							?>
						<div class="mp-contributor-page__contact-block">
							
							<a href="<?php echo $website; ?>" target="_blank" class="mp-contributor-page__website">
								<?php _e( 'Visit Website', 'aras-marketplace' ); ?>
							</a>
						</div>
							<?php
						}

						if( $has_social_links ){
							?>
						<div class="mp-contributor-page__contact-block mp-contributor-page__social-links">
							<?php
							foreach( $social_links as $key => $link ){
								if( $link ){
									?>
								<a href="<?php echo $link; ?>" target="_blank" class="mp-contributor-page__social-link no-external-icon">
									<?php
									switch( $key ){
										case 'linkedin':
											?>
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M100.3 448H7.4V148.9h92.9zM53.8 108.1C24.1 108.1 0 83.5 0 53.8a53.8 53.8 0 0 1 107.6 0c0 29.7-24.1 54.3-53.8 54.3zM447.9 448h-92.7V302.4c0-34.7-.7-79.2-48.3-79.2-48.3 0-55.7 37.7-55.7 76.7V448h-92.8V148.9h89.1v40.8h1.3c12.4-23.5 42.7-48.3 87.9-48.3 94 0 111.3 61.9 111.3 142.3V448z"/></svg>
											<?php
											break;
										case 'facebook':
											?>
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z"/></svg>
											<?php
											break;
										case 'x':
											?>
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>
											<?php
											break;
										case 'instagram':
											?>
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
											<?php
											break;
										case 'youtube':
											?>
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M549.7 124.1c-6.3-23.7-24.8-42.3-48.3-48.6C458.8 64 288 64 288 64S117.2 64 74.6 75.5c-23.5 6.3-42 24.9-48.3 48.6-11.4 42.9-11.4 132.3-11.4 132.3s0 89.4 11.4 132.3c6.3 23.7 24.8 41.5 48.3 47.8C117.2 448 288 448 288 448s170.8 0 213.4-11.5c23.5-6.3 42-24.2 48.3-47.8 11.4-42.9 11.4-132.3 11.4-132.3s0-89.4-11.4-132.3zm-317.5 213.5V175.2l142.7 81.2-142.7 81.2z"/></svg>
											<?php
											break;
									}
									?>
								</a>
									<?php
								}
							}
							?>
						</div>
							<?php
						}
						?>
					</div>

				</div>

				<div class="mp-contributor-page__content-column">
					<h2>
						<?php
						// wp translation for "Solutions by %s" where %s is the contributor name
						printf(
							__('Solutions by %s', 'aras-marketplace'),
							single_term_title('', false)
						);
						?>
					</h2>

					<div class="mp-solution-grid">
						<?php
						if (have_posts()) {
							while (have_posts()) {
								the_post();
								Template::get_template_part('marketplace/card', 'solution');
							}
						} else {
							?>
							<div class="cell">
								<p><?php _e('No solutions found.', 'aras-marketplace'); ?></p>
							</div>
							<?php
						}
						?>
					</div>

					<?php
					// output pagination
					Template::get_template_part('marketplace/pagination');
					?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php
get_footer();