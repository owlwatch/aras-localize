<?php
$eyebrow_enabled = get_field('eyebrow_enabled', 'option');
$eyebrow_content = get_field('eyebrow_content', 'option');

// lets allow eyebrow testing
if( isset( $_REQUEST['test-announcement']) ){
	$eyebrow_enabled = true;
}
$has_eyebrow = $eyebrow_enabled && $eyebrow_content;
?>
<div class="nav-bar<?php if( $has_eyebrow){ ?> has-eyebrow<?php } ?>">
	<div class="grid-container">
		<div class="hidden-nav">
			<a aria-label="<?php echo esc_attr__('Skip to Intro', 'aras'); ?>" id="skip-link" href="#page-intro"><?php esc_html_e('Skip to Intro', 'aras'); ?></a>
		</div>
		<div class="grid-x grid-margin-x align-bottom">
			<div class="cell shrink nav-logo-container">
				<?php $image = get_field('website_logo', 'option');
				if (!empty($image)) : ?>
					<a aria-label="<?php echo esc_attr__('Homepage', 'aras'); ?>" class="nav-logo-link" href="<?php echo home_url(); ?>">
						<img class="nav-logo" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
					</a>
				<?php endif; ?>
			</div>
			<nav class="cell auto desktop-nav-sizing navigation">
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
								<a aria-label="<?php echo esc_attr($link_title); ?>" class="<?php if( get_sub_field('is_button') ) { ?>aras-button<?php }else { ?>upper-nav-item<?php } ?>" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
									<?php echo esc_html($link_title); ?>
								</a>
							<?php endif; ?>
						<?php endwhile; ?>
					<?php endif; ?>
					<?php echo do_shortcode('[custom_language_dropdown]'); ?>
				</div>
				<?php if (have_rows('meganav', 'option')) : ?>
					<ul class="dropdown menu meganav" data-dropdown-menu>
						<?php while (have_rows('meganav', 'option')) : the_row(); ?>
							<li <?php if (get_sub_field('dropdown_type', 'option') == 'megamenu') : ?>class="nav-toplevel-mega" <?php endif; ?>>
								<?php if (get_sub_field('top_level_type', 'option') == 'link') : ?>
									<?php $link = get_sub_field('top_level_link', 'option');
									if ($link) : $link_url = $link['url'];
										$link_title = $link['title'];
										$link_target = $link['target'] ? $link['target'] : '_self';
									?>
										<a aria-label="<?php echo esc_html($link_title); ?>" aria-haspopup="true" class="nav-toplevel" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
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
								<?php elseif (get_sub_field('top_level_type', 'option') == 'custom') : ?>
									<?php echo get_sub_field('custom_html', 'option') ?>
								<?php else : /*top_level_type == label*/ ?>
									<?php if (get_sub_field('top_level_label', 'option')) : ?>
										<button aria-label="<?php echo get_sub_field('top_level_label', 'option'); ?>" aria-haspopup="true" class="nav-toplevel"><?php echo get_sub_field('top_level_label', 'option'); ?></button>
									<?php endif; ?>
								<?php endif; ?>
								<?php if (get_sub_field('dropdown_type', 'option') == 'basic') : ?>
									<?php if (have_rows('second_level_dropdown', 'option')) : ?>
										<ul class="menu basic-dropdown bg-dblue">
											<?php while (have_rows('second_level_dropdown', 'option')) : the_row(); ?>
												<?php $link = get_sub_field('second_level_link', 'option');
												if ($link) : $link_url = $link['url'];
													$link_title = $link['title'];
													$link_target = $link['target'] ? $link['target'] : '_self';
												?>
													<li>
														<a aria-label="<?php echo esc_attr($link_title); ?>" aria-haspopup="true" class="second-level-link" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
															<?php echo esc_html($link_title); ?>
														</a>
														<?php if (get_sub_field('include_third_level', 'option')) : ?>
															<?php if (have_rows('third_level_dropdown', 'option')) : ?>
																<ul class="menu third-dropdown">
																	<?php while (have_rows('third_level_dropdown', 'option')) : the_row(); ?>
																		<?php $link = get_sub_field('third_level_link', 'option');
																		if ($link) : $link_url = $link['url'];
																			$link_title = $link['title'];
																			$link_target = $link['target'] ? $link['target'] : '_self';
																		?>
																			<li>
																				<a aria-label="<?php echo esc_attr($link_title); ?>" class="third-level-link" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
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
										<ul class="menu mega-dropdown bg-dblue">
											<div class="grid-container">
												<div class="grid-x grid-margin-x mega-columns">
													<?php while (have_rows('mega_menu_columns', 'option')) : the_row(); ?>

														<?php if (get_sub_field('mega_menu_column_type', 'option') == 'links') : ?>
															<div class="cell auto mega-col">
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
																				<a aria-label="<?php echo esc_attr($link_title); ?>" class="<?php echo $indent; ?> mega-menu-link-item" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
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
																					<a aria-label="<?php echo esc_attr($link_title); ?>" class="<?php echo $indent; ?> mega-menu-label" href=" <?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
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
																<?php endif; ?>
															</div>
														<?php else : /* If mega_menu_column_type == cta */ ?>
															<div class="cell shrink mega-col-cta">
																<?php if (have_rows('mega_menu_cta', 'option')) : ?>
																	<?php while (have_rows('mega_menu_cta', 'option')) : the_row(); ?>
																		<?php $image = get_sub_field('mega_menu_cta_image');
																		if (!empty($image)) : ?>
																			<?php $link = get_sub_field('mega_menu_cta_link');
																			if ($link) : $link_url = $link['url'];
																				$link_title = $link['title'];
																				$link_target = $link['target'] ? $link['target'] : '_self';
																			?>
																				<a aria-label="<?php echo esc_attr($link_title); ?>" class="mega-menu-cta-img-link" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
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
																			<a aria-label="<?php echo esc_attr($link_title); ?>" class="aras-button--outline mega-menu-cta-button" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
																				<?php echo esc_html($link_title); ?>
																			</a>
																		<?php endif; ?>
																	<?php endwhile; ?>
																<?php endif; ?>
															</div>
														<?php endif; ?>
													<?php endwhile; ?>
												</div>
											</div>
										</ul>
									<?php endif; ?>
								<?php else : /*No Menu*/ ?>
								<?php endif; ?>
							</li>
						<?php endwhile; /* Endwhile meganav */ ?>
					</ul>
				<?php endif; /* Endif meganav */ ?>
			</nav>
			<div class="cell auto mobile-nav-sizing">
				<span class="mobile-menu float-right">
					<button aria-label="open mobile navigation" class="menu-icon" type="button" data-toggle="off-canvas"></button>

					<?php echo do_shortcode('[custom_language_dropdown]'); ?>

				</span>
			</div>
		</div>
	</div>
	<?php
	if( $has_eyebrow ){ 
		get_template_part('parts/nav', 'offcanvas-eyebrow');
	}
	?>
</div>