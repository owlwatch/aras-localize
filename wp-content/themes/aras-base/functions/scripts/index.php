<?php

if (! defined('ABSPATH')) {
	exit;
}

function aras_run_requested_script()
{
	if (! is_super_admin()) {
		return;
	}

	if (! isset($_REQUEST['aras_script'])) {
		return;
	}

	$script_key = sanitize_key(wp_unslash($_REQUEST['aras_script']));
	if (! preg_match('/^aras_[a-z0-9_-]+$/', $script_key)) {
		wp_die('Invalid script key.');
	}

	$script_slug = substr($script_key, strlen('aras_'));
	if (! preg_match('/^[a-z0-9_-]+$/', $script_slug)) {
		wp_die('Invalid script key.');
	}

	$scripts_dir = realpath(__DIR__ . '/handlers');
	if (! $scripts_dir) {
		wp_die('Scripts directory not found.');
	}

	$target = $scripts_dir . DIRECTORY_SEPARATOR . $script_slug . '.php';
	$target_realpath = realpath($target);
	if (! $target_realpath || strpos($target_realpath, $scripts_dir . DIRECTORY_SEPARATOR) !== 0) {
		wp_die('Invalid script path.');
	}

	if (! is_file($target_realpath)) {
		wp_die('Unknown script: ' . esc_html($script_key));
	}

	require_once $target_realpath;

	if (! is_callable($script_key)) {
		wp_die('Script callback is not callable: ' . esc_html($script_key));
	}

	call_user_func($script_key);
	exit;
}

add_action('init', 'aras_run_requested_script');
