<?php $lightnav = ''; ?>
<?php if (get_field('header_placement') == 'overlap') : ?>
	<?php if (have_rows('hero_background')) : ?>
		<?php while (have_rows('hero_background')) : the_row(); ?>
			<?php if (get_sub_field('background_style') != 'solid') : ?>
				<?php if (get_sub_field('background_options') == 'darkoverlay') : ?>
					<?php $lightnav = 'lightnav'; ?>
				<?php elseif (get_sub_field('background_options') == 'nooverlaywhite') : ?>
					<?php $lightnav = 'lightnav'; ?>
				<?php else : ?>
					<?php $lightnav = ''; ?>
				<?php endif; ?>
			<?php else : ?>
				<?php
				$background_color = get_sub_field('background_color');
				$lightnav = '';
				switch ($background_color) {
					case 'dblue':
						$lightnav = 'lightnav';
						break;
					default:
						$lightnav = '';
				}
				?>
			<?php endif; ?>
		<?php endwhile; ?>
	<?php endif; ?>
<?php endif; ?>



<div class="nav-bar nav-bar-logo">
	<div class="grid-container">
		<div class="hidden-nav">
			<a aria-label="Skip to Intro" id="skip-link" href="#page-intro">Skip to Intro</a>
		</div>
		<div class="grid-x grid-margin-x align-middle">
			<div class="cell shrink nav-logo-container logo-only-container">
				<?php
				$logo_image_type = get_field('logo_image_type');
				$logo_link_type = get_field('logo_link_type');
				$image = $logo_image_type == 'custom' ? get_field('logo_image_override') : get_field('website_logo', 'option');

				if ($logo_image_type != 'none' && !empty($image)) :
					$link = get_field('logo_link_override');
					$link_url = $link['url'] ?: home_url();
					$link_target = $link['target'] ?: '_self';
					$link_aria_label = $link_target != '_self' ? esc_attr($link_target) : 'Homepage';
					$image_alt = !empty($image['alt']) ? esc_attr($image['alt']) : get_the_title();
				?>
					<?php if ($logo_link_type == 'custom' && $link) : ?>
						<a aria-label="<?php echo $link_aria_label; ?>" class="nav-logo-link" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
							<img class="nav-logo" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo $image_alt; ?>" />
						</a>
					<?php elseif ($logo_link_type == 'none') : ?>
						<span class="nav-logo-link">
							<img class="nav-logo" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo $image_alt; ?>" />
						</span>
					<?php else : ?>
						<a aria-label="Homepage" class="nav-logo-link" href="<?php echo home_url(); ?>">
							<img class="nav-logo" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo $image_alt; ?>" />
						</a>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			<?php
			$nav_post_id = $current_post_id = get_queried_object_id();
			// check if 'use_parent_page_navigation' is set to true
			$use_parent_nav = get_field('use_parent_page_navigation');

			if ($use_parent_nav) {
				$parent_id = $current_post_id;
				while( $use_parent_nav ){
					// get the parent page ID
					$parent_id = wp_get_post_parent_id($parent_id);
					// if the parent ID is 0, then we are on the parent page
					// so we can use the parent page ID
					if ($parent_id == 0) {
						$use_parent_nav = false;
					} else {
						$use_parent_nav = get_field('use_parent_page_navigation', $parent_id);
						if( !$use_parent_nav ){

						// lets use setup_postdata to set the parent page as the current
						// page so we can get the navigation from the parent page
							$nav_post_id = $parent_id;
							$current_post_id = $parent_id;
						}
					}
				}
			}
			?>
			<?php if (have_rows('simplified_navigation', $nav_post_id)) : ?>
				<?php while (have_rows('simplified_navigation', $nav_post_id)) : the_row(); ?>
					<nav class="cell auto desktop-nav-sizing navigation">

						<?php if (have_rows('navigation_items', $nav_post_id)) : ?>
							<ul class="dropdown menu simplenav <?php echo $lightnav; ?>" data-dropdown-menu>
								<?php while (have_rows('navigation_items', $nav_post_id)) : the_row(); ?>
									<li>

										<?php $link = get_sub_field('navigation_link');
										if ($link) : $link_url = $link['url'];
											$link_title = $link['title'];
											$link_target = $link['target'] ? $link['target'] : '_self';
										?>

											<?php if (get_sub_field('navigation_link_type') == 'button') : ?>
												<a class="aras-button simplenav-button <?php echo $lightnav; ?>" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target) ?: '_self'; ?>">
													<?php echo esc_html($link_title); ?>
												</a>
											<?php else : ?>
												<a class="simplenav-toplevel <?php echo $lightnav; ?>" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target) ?: '_self'; ?>">
													<?php echo esc_html($link_title); ?>
												</a>
											<?php endif; ?>

										<?php endif; ?>

										<?php if (get_sub_field('has_dropdown') && have_rows('dropdown_items')) : ?>
											<ul class="menu vertical nested simplenav-dropdown">
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
								<?php if (get_sub_field('language_switcher') == 'show') : ?>
									<?php echo do_shortcode('[custom_language_dropdown]'); ?>
								<?php endif; ?>
							</ul>
						<?php endif; /* Endif meganav */ ?>

					</nav>

					<div class="cell auto mobile-nav-sizing">
						<span class="mobile-menu float-right <?php echo $lightnav; ?>">
							<button aria-label="open mobile navigation" class="menu-icon <?php echo $lightnav; ?>" type="button" data-toggle="off-canvas"></button>
							<?php if (get_sub_field('language_switcher') == 'show') : ?>
								<?php echo do_shortcode('[custom_language_dropdown]'); ?>
							<?php endif; ?>
						</span>
					</div>

				<?php endwhile; ?>
			<?php endif; ?>
		</div>
	</div>
</div>