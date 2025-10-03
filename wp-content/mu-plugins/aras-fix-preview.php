<?php
/**
 * Plugin Name: Disable Types Access on Preview
 * Description: Disables the Types Access plugin if we are in preview mode to avoid conflicts.
 * Version: 1.0
 * Author: Owl Watch Consulting
 */

add_filter('option_active_plugins', function ($plugins) {
    // Only run in frontend or admin requests (not CLI or cron)
    if (isset($_GET['preview']) || isset($_GET['_ppp']) ) {
        $plugin_to_disable = 'types-access/types-access.php'; 
        $key = array_search($plugin_to_disable, $plugins);

        if ($key !== false) {
            unset($plugins[$key]);
        }
    }

    return $plugins;
});