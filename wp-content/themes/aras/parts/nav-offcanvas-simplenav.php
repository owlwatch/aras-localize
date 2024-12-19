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
					$link_url = $link['url'] ?? home_url();
					$link_target = $link['target'] ?? '_self';
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


			<?php if (have_rows('simplified_navigation')) : ?>
				<?php while (have_rows('simplified_navigation')) : the_row(); ?>
					<nav class="cell auto desktop-nav-sizing navigation">

						<?php if (have_rows('navigation_items')) : ?>
							<ul class="dropdown menu simplenav <?php echo $lightnav; ?>" data-dropdown-menu>
								<?php while (have_rows('navigation_items')) : the_row(); ?>
									<li>

										<?php $link = get_sub_field('navigation_link');
										if ($link) : $link_url = $link['url'];
											$link_title = $link['title'];
											$link_target = $link['target'] ? $link['target'] : '_self';
										?>

											<?php if (get_sub_field('navigation_link_type') == 'button') : ?>
												<a class="aras-button simplenav-button <?php echo $lightnav; ?>" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
													<?php echo esc_html($link_title); ?>
												</a>
											<?php else : ?>
												<a class="simplenav-toplevel <?php echo $lightnav; ?>" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
													<?php echo esc_html($link_title); ?>
												</a>
											<?php endif; ?>

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