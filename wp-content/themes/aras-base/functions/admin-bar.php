<?php

if (! defined('ABSPATH')) {
	exit;
}

function aras_remove_wp_logo_from_admin_bar($wp_admin_bar)
{
	if (! is_admin_bar_showing() || ! ($wp_admin_bar instanceof WP_Admin_Bar)) {
		return;
	}

	$wp_admin_bar->remove_node('wp-logo');

	$site_name_node = $wp_admin_bar->get_node('site-name');

	if (! $site_name_node) {
		return;
	}

	$favicon_url = esc_url(get_template_directory_uri() . '/favicon.png');
	$site_name_node->title = sprintf(
		'<span class="aras-admin-bar-site-icon" aria-hidden="true"><img src="%1$s" alt="" /></span><span class="aras-admin-bar-site-title">%2$s</span>',
		$favicon_url,
		$site_name_node->title
	);

	$wp_admin_bar->add_node($site_name_node);
}
add_action('admin_bar_menu', 'aras_remove_wp_logo_from_admin_bar', 999);

function aras_admin_bar_favicon_css()
{
	if (! is_admin_bar_showing()) {
		return;
	}

	$favicon_url = esc_url(get_template_directory_uri() . '/favicon.png');
	?>
	<style id="aras-admin-bar-favicon">
		#wpadminbar #wp-admin-bar-site-name > .ab-item {
			display: inline-flex;
			align-items: center;
			gap: 6px;
		}

		#wpadminbar #wp-admin-bar-site-name > .ab-item:before {
			display: none;
		}

		#wpadminbar .aras-admin-bar-site-icon {
			display: inline-flex;
			width: 16px;
			height: 16px;
			flex: 0 0 16px;
		}

		#wpadminbar .aras-admin-bar-site-icon img {
			display: block;
			width: 16px;
			height: 16px;
			object-fit: contain;
		}
	</style>
	<?php
}
add_action('admin_head', 'aras_admin_bar_favicon_css');
add_action('wp_head', 'aras_admin_bar_favicon_css');
