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
</body>
</html>