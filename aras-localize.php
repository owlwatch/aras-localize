<?php
/**
 * Plugin Name: Aras - Localize
 * Description: Custom LocalizeJS language switcher styled to match Aras branding.
 * Version: 1.0.0
 * Author: Aras
 */

if (!defined('ABSPATH')) {
    exit;
}

const ARAS_LOCALIZE_VERSION = '1.0.0';

require_once __DIR__ . '/lib/Widget/LanguageSwitcher.php';
require_once __DIR__ . '/lib/Util/Hreflang.php';
require_once __DIR__ . '/lib/ACF.php';

add_action('plugins_loaded', function() {
    $acf = new \Aras\Localize\ACF();
    $acf->register();

    $widget = new \Aras\Localize\Widget\LanguageSwitcher();
    $widget->register();

    $hreflang = new \Aras\Localize\Util\Hreflang();
    $hreflang->register();
});
