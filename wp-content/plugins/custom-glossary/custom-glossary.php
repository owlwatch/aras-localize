<?php

/**
 * Custom Glossary
 *
 * This plugin adds a Glossary Post Type
 *
 * @since             1.0.0
 * @package           Custom_Glossary
 *
 * @wordpress-plugin
 * Plugin Name:       Custom Glossary
 * Description:       Provides a glossary that displays an index of terms and optionally filters content to add internal links.
 * Version:           1.0.0
 * Author:            Mark Fabrizio
 * Author URI:        https://owlwatch.com
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       custom-glossary
 * Domain Path:       /languages
 * Network:			  true
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Custom\Glossary;

// extend the other glossary
add_action('plugins_loaded', function () {
	if (!defined('GT_VERSION')) {
		// bail
		return;
	}

	// lets add our hooks
	require_once(__DIR__ . '/inc/vite.php');
	require_once(__DIR__ . '/inc/extras.php');

	// 
});
