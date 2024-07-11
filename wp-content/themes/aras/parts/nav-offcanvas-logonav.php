<div class="nav-bar nav-bar-logo">
	<div class="grid-container">
		<div class="hidden-nav">
			<a aria-label="Skip to Intro" id="skip-link" href="#page-intro">Skip to Intro</a>
		</div>
		<div class="grid-x grid-margin-x align-bottom">
			<div class="cell shrink nav-logo-container logo-only-container">
				<?php
				$logo_image_type = get_field('logo_image_type');
				$logo_link_type = get_field('logo_link_type');
				$image = $logo_image_type == 'custom' ? get_field('logo_image_override') : get_field('website_logo', 'option');
				if (!empty($image)) :
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
		</div>
	</div>
</div>