<?php
/*
 The template part for displaying offcanvas content
 */
?>

<?php if (get_field('header_type') == 'simplenav') : ?>

	<?php
	$nav_post_id = $current_post_id = get_queried_object_id();
	// check if 'use_parent_page_navigation' is set to true
	$use_parent_nav = get_field('use_parent_page_navigation');

	if ($use_parent_nav) {
		// get the parent page ID
		$parent_id = wp_get_post_parent_id($current_post_id);

		// if the parent ID is 0, then we are on the parent page
		// so we can use the parent page ID
		if ($parent_id == 0) {
		} else {
			// lets use setup_postdata to set the parent page as the current
			// page so we can get the navigation from the parent page
			$nav_post_id = $parent_id;
		}
	}
	?>

	<nav class="off-canvas position-right" id="off-canvas" data-off-canvas>
		<div class="upper-nav"></div>



		<?php if (have_rows('simplified_navigation', $nav_post_id)) : ?>
			<?php while (have_rows('simplified_navigation', $nav_post_id)) : the_row(); ?>

				<?php if (have_rows('navigation_items', $nav_post_id)) : ?>
					<ul class="vertical menu simplenav" style="margin-top: 3rem">
						<?php while (have_rows('navigation_items', $nav_post_id)) : the_row(); ?>
							<?php
							?>
							<li>

								<?php
								$link = get_sub_field('navigation_link');
								if ($link) : $link_url = $link['url'];
									$link_title = $link['title'];
									$link_target = $link['target'] ? $link['target'] : '_self';
								?>

									<?php if (get_sub_field('navigation_link_type') == 'button') : ?>
										<a class="aras-button simplenav-button" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
											<?php echo esc_html($link_title); ?>
										</a>
									<?php else : ?>
										<a class="simplenav-toplevel" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
											<?php echo esc_html($link_title); ?>
										</a>
									<?php endif; ?>

								<?php endif; ?>

								<?php if (get_sub_field('has_dropdown')) : ?>
									<ul class="menu vertical nested bg-dblue submenu is-accordion-submenu">
										<?php while (have_rows('dropdown_items')) : the_row(); ?>
											<li>
												<?php $sub_link = get_sub_field('navigation_link');
												if ($sub_link) : $sub_link_url = $sub_link['url'];
													$sub_link_title = $sub_link['title'];
													$sub_link_target = $sub_link['target'] ? $sub_link['target'] : '_self';
												?>
													<a class="simplenav-sub <?php echo $lightnav; ?>" href="<?php echo esc_url($sub_link_url); ?>" target="<?php echo esc_attr($sub_link_target) ?: '_self'; ?>">
														<?php echo esc_html($sub_link_title); ?>
													</a>
												<?php endif; ?>
											</li>
										<?php endwhile; ?>
									</ul>
								<?php endif; ?>

							</li>
						<?php endwhile; /* Endwhile meganav */ ?>
					</ul>
				<?php endif; /* Endif meganav */ ?>
			<?php endwhile; ?>
		<?php endif; ?>
	</nav>
<?php else : ?>
	<nav class="off-canvas position-right" id="off-canvas" data-off-canvas>
		<div class="upper-nav">
			<?php if (get_field('nav_site_search', 'option') == 'enable') : ?>
				<?php get_template_part('parts/search/nav-searchform'); ?>
			<?php endif; ?>
			<?php if (have_rows('upper_nav_links', 'option')) : ?>
				<?php while (have_rows('upper_nav_links', 'option')) : the_row(); ?>
					<?php $link = get_sub_field('upper_nav_link', 'option');
					if ($link) : $link_url = $link['url'];
						$link_title = $link['title'];
						$link_target = $link['target'] ? $link['target'] : '_self';
					?>
						<a aria-label="<?php echo esc_html($link_title); ?>" class="upper-nav-item" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
							<?php echo esc_html($link_title); ?>
						</a>
					<?php endif; ?>
				<?php endwhile; ?>
			<?php endif; ?>
		</div>

		<?php if (have_rows('meganav', 'option')) : ?>
			<ul class="accordion-menu vertical menu meganav" data-accordion-menu>
				<?php while (have_rows('meganav', 'option')) : the_row(); ?>

					<li <?php if (get_sub_field('dropdown_type', 'option') == 'megamenu') : ?>class="nav-toplevel-mega" <?php endif; ?>>
						<?php if (get_sub_field('top_level_type', 'option') == 'link') : ?>
							<?php $link = get_sub_field('top_level_link', 'option');
							if ($link) : $link_url = $link['url'];
								$link_title = $link['title'];
								$link_target = $link['target'] ? $link['target'] : '_self';
							?>
								<a aria-label="<?php echo esc_html($link_title); ?>" class="nav-toplevel" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
									<?php echo esc_html($link_title); ?>
								</a>
							<?php endif; ?>
						<?php elseif (get_sub_field('top_level_type', 'option') == 'button') : ?>
							<?php $link = get_sub_field('top_level_link', 'option');
							if ($link) : $link_url = $link['url'];
								$link_title = $link['title'];
								$link_target = $link['target'] ? $link['target'] : '_self';
							?>
								<a aria-label="<?php echo esc_html($link_title); ?>" class="aras-button meganav-top-level-button" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
									<?php echo esc_html($link_title); ?>
								</a>
							<?php endif; ?>
						<?php else : /*top_level_type == label*/ ?>
							<?php if (get_sub_field('top_level_label', 'option')) : ?>
								<a aria-label="<?php echo get_sub_field('top_level_label', 'option'); ?>" class="nav-toplevel"><?php echo get_sub_field('top_level_label', 'option'); ?></a>
							<?php endif; ?>
						<?php endif; ?>
						<?php if (get_sub_field('dropdown_type', 'option') == 'basic') : ?>
							<?php if (have_rows('second_level_dropdown', 'option')) : ?>
								<ul class="menu vertical nested">
									<?php while (have_rows('second_level_dropdown', 'option')) : the_row(); ?>
										<?php $link = get_sub_field('second_level_link', 'option');
										if ($link) : $link_url = $link['url'];
											$link_title = $link['title'];
											$link_target = $link['target'] ? $link['target'] : '_self';
										?>
											<li>
												<a aria-label="<?php echo esc_html($link_title); ?>" class="second-level-link" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
													<?php echo esc_html($link_title); ?>
												</a>
												<?php if (get_sub_field('include_third_level', 'option')) : ?>
													<?php if (have_rows('third_level_dropdown', 'option')) : ?>
														<ul class="menu vertical nested third-dropdown">
															<?php while (have_rows('third_level_dropdown', 'option')) : the_row(); ?>
																<?php $link = get_sub_field('third_level_link', 'option');
																if ($link) : $link_url = $link['url'];
																	$link_title = $link['title'];
																	$link_target = $link['target'] ? $link['target'] : '_self';
																?>
																	<li>
																		<a aria-label="<?php echo esc_html($link_title); ?>" class="third-level-link" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
																			<?php echo esc_html($link_title); ?>
																		</a>
																	</li>
																<?php endif; ?>
															<?php endwhile; /* Endwhile third_level_dropdown */ ?>
														</ul>
													<?php endif; /* Endif third_level_dropdown */ ?>
												<?php endif; /* Endif include_third_level */ ?>
											</li>
										<?php endif; /* End second_level_link */ ?>
									<?php endwhile;  /* Endwhile second_level_dropdown */ ?>
								</ul>
							<?php endif; /* Endif second_level_dropdown */ ?>
						<?php elseif (get_sub_field('dropdown_type', 'option') == 'megamenu') : ?>
							<?php if (have_rows('mega_menu_columns', 'option')) : ?>
								<ul class="menu vertical nested bg-dblue">
									<?php while (have_rows('mega_menu_columns', 'option')) : the_row(); ?>
										<?php if (get_sub_field('mega_menu_column_type', 'option') == 'links') : ?>
											<div class="mega-col">
												<?php if (have_rows('mega_menu_items', 'option')) : ?>
													<?php while (have_rows('mega_menu_items', 'option')) : the_row(); ?>

														<?php if (get_sub_field('indent_as_third_level', 'option')) {
															$indent = 'indent-item';
														} else {
															$indent = '';
														} ?>

														<?php if (get_sub_field('mega_menu_item_type', 'option') == 'link') : ?>
															<?php $link = get_sub_field('mega_menu_link');
															if ($link) : $link_url = $link['url'];
																$link_title = $link['title'];
																$link_target = $link['target'] ? $link['target'] : '_self';
															?>
																<a aria-label="<?php echo esc_html($link_title); ?>" class="<?php echo $indent; ?> mega-menu-link-item" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
																	<?php echo esc_html($link_title); ?>
																</a>
															<?php endif; ?>


														<?php else : /* If mega_menu_item_type == label */ ?>

															<?php if (get_sub_field('heading_type', 'option') == 'link') : ?>
																<?php $link = get_sub_field('heading_link', 'option');
																if ($link) : $link_url = $link['url'];
																	$link_title = $link['title'];
																	$link_target = $link['target'] ? $link['target'] : '_self';
																?>
																	<a aria-label="<?php echo esc_html($link_title); ?>" class="<?php echo $indent; ?> mega-menu-label" href=" <?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
																		<?php echo esc_html($link_title); ?>
																	</a>
																<?php endif; ?>
															<?php else : ?>
																<?php if (get_sub_field('mega_menu_label', 'option')) : ?>
																	<div class="<?php echo $indent; ?> mega-menu-label h6"><?php echo get_sub_field('mega_menu_label', 'option'); ?></div>
																<?php else : ?>
																	<div class="<?php echo $indent; ?> mega-menu-label h6"></div>
																<?php endif; ?>
															<?php endif; ?>
														<?php endif; ?>

													<?php endwhile; ?>
											</div>
										<?php endif; ?>
									<?php else : /* If mega_menu_column_type == cta */ ?>
										<?php if (have_rows('mega_menu_cta', 'option')) : ?>
											<div class="mega-col mega-menu-cta-item">
												<?php while (have_rows('mega_menu_cta', 'option')) : the_row(); ?>
													<?php $image = get_sub_field('mega_menu_cta_image');
													if (!empty($image)) : ?>
														<?php $link = get_sub_field('mega_menu_cta_link');
														if ($link) : $link_url = $link['url'];
															$link_title = $link['title'];
															$link_target = $link['target'] ? $link['target'] : '_self';
														?>
															<a aria-label="<?php echo esc_html($link_title); ?>" class="mega-menu-cta-img-link" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
																<img class="mega-menu-cta-image" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
															</a>
														<?php else : ?>
															<img class="mega-menu-cta-image" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
														<?php endif; ?>
													<?php endif; ?>
													<?php if (get_sub_field('mega_menu_cta_subhead', 'option')) : ?>
														<div class="mega-menu-cta-subhead h6"><?php echo get_sub_field('mega_menu_cta_subhead', 'option'); ?></div>
													<?php endif; ?>
													<?php if (get_sub_field('mega_menu_cta_headline', 'option')) : ?>
														<div class="mega-menu-cta-headline h4"><?php echo get_sub_field('mega_menu_cta_headline', 'option'); ?></div>
													<?php endif; ?>
													<?php if (get_sub_field('mega_menu_cta_description', 'option')) : ?>
														<div class="mega-menu-cta-desc"><?php echo get_sub_field('mega_menu_cta_description', 'option'); ?></div>
													<?php endif; ?>
													<?php $link = get_sub_field('mega_menu_cta_link');
													if ($link) : $link_url = $link['url'];
														$link_title = $link['title'];
														$link_target = $link['target'] ? $link['target'] : '_self';
													?>
														<a aria-label="<?php echo esc_html($link_title); ?>" class="aras-button--outline mega-menu-cta-button" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
															<?php echo esc_html($link_title); ?>
														</a>
													<?php endif; ?>
												<?php endwhile; ?>
											</div>
										<?php endif; ?>
									<?php endif; ?>
								<?php endwhile; ?>
								</ul>
							<?php endif; ?>
						<?php else : /*No Menu*/ ?>
						<?php endif; ?>
					</li>
				<?php endwhile; /* Endwhile meganav */ ?>
			</ul>
		<?php endif; /* Endif meganav */ ?>
		<?/*
	<?php joints_off_canvas_nav(); ?>
	<?php if ( is_active_sidebar( 'offcanvas' ) ) : ?>
		<?php dynamic_sidebar( 'offcanvas' ); ?>
	<?php endif; ?>
*/ ?>
	</nav>
<?php endif; ?>