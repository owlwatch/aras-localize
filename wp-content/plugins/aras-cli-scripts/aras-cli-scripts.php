<?php
/**
 * Plugin Name: Aras CLI Scripts
 * Description: WP-CLI-only commands for Aras maintenance scripts.
 * Version: 1.0.0
 * Author: Aras
 */

if (! defined('ABSPATH')) {
	exit;
}

// Load command classes only in WP-CLI to avoid browser execution paths.
if (defined('WP_CLI') && WP_CLI) {
	require_once __DIR__ . '/includes/class-aras-cli-scripts-command.php';
	WP_CLI::add_command('aras scripts', 'Aras_CLI_Scripts_Command');
}
