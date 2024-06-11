<?php if (get_field('enable_global_footer_cta', 'option')) : ?>
	<?php if (get_field('global_footer_cta_style', 'option') == 'pattern') : ?>
		<section class="footer-cta largetoppadding">
			<div class="grid-container">
				<div class="grid-x grid-padding-x">
					<div class="cell small-12 medium-9 large-6">
					<?php else : ?>
						<section class="simple-footer-cta mediumtoppadding smallbottompadding bg-grey">
							<div class="grid-container">
								<div class="grid-x grid-padding-x align-center text-center">
									<div class="cell small-12 medium-shrink">
									<?php endif; ?>

									<div class="footer-cta-content">
										<?php if (get_field('global_footer_cta_headline', 'option')) : ?>
											<h2 class="footer-cta-headline"><?php echo get_field('global_footer_cta_headline', 'option'); ?></h2>
										<?php endif; ?>
										<?php if (get_field('global_footer_cta_content', 'option')) : ?>
											<?php echo get_field('global_footer_cta_content', 'option'); ?>
										<?php endif; ?>
									</div>
									<?php $link = get_field('global_footer_cta_button', 'option');
									if ($link) : $link_url = $link['url'];
										$link_title = $link['title'];
										$link_target = $link['target'] ? $link['target'] : '_self';
									?>
										<a aria-label="<?php echo esc_attr($link_title); ?>" class="aras-button" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
											<?php echo esc_html($link_title); ?>
										</a>
									<?php endif; ?>

									<?php if (get_sub_field('global_footer_cta_style', 'otpion') == 'pattern') : ?>
									</div>
								</div>
							</div>
						</section>
					<?php else : ?>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>
<?php endif; ?>