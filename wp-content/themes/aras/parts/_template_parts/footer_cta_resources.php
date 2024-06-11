<?php if (get_field('resources_footer_use_global_content', 'option')) : ?>
	<?php get_template_part('parts/_template_parts/footer_cta_global'); ?>
<?php else : ?>
	<?php if (have_rows('resources_footer_cta', 'option')) : ?>
		<?php while (have_rows('resources_footer_cta', 'option')) : the_row(); ?>
			<section class="simple-footer-cta mediumtoppadding smallbottompadding bg-grey">
				<div class="grid-container">
					<div class="grid-x grid-padding-x align-center text-center">
						<div class="cell small-12 medium-shrink">
							<div class="footer-cta-content">
								<?php if (get_sub_field('cta_headline', 'option')) : ?>
									<h2 class="footer-cta-headline"><?php echo get_sub_field('cta_headline', 'option'); ?></h2>
								<?php endif; ?>
								<?php if (get_sub_field('cta_content', 'option')) : ?>
									<?php echo get_sub_field('cta_content', 'option'); ?>
								<?php endif; ?>
							</div>
							<?php $link = get_sub_field('cta_button', 'option');
							if ($link) : $link_url = $link['url'];
								$link_title = $link['title'];
								$link_target = $link['target'] ? $link['target'] : '_self';
							?>
								<a aria-label="<?php echo esc_attr($link_title); ?>" class="aras-button" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
									<?php echo esc_html($link_title); ?>
								</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</section>
		<?php endwhile; ?>
	<?php endif; ?>
<?php endif; ?>