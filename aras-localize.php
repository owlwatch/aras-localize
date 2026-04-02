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

const ARAS_LOCALIZE_VERSION = '1.0.2';

require_once __DIR__ . '/lib/Util/Common.php';
require_once __DIR__ . '/lib/Module/LanguageSwitcher.php';
require_once __DIR__ . '/lib/Module/LinkList.php';
require_once __DIR__ . '/lib/Module/Hreflang.php';
require_once __DIR__ . '/lib/Module/Sitemap.php';
require_once __DIR__ . '/lib/Module/Prerender.php';
require_once __DIR__ . '/lib/ACF.php';

register_activation_hook(__FILE__, function() {
    $prerender = new \Aras\Localize\Module\Prerender();
    $prerender->register_rewrite_endpoint();
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});

add_action('plugins_loaded', function() {
    $acf = new \Aras\Localize\ACF();
    $acf->register();

    $module = new \Aras\Localize\Module\LanguageSwitcher();
    $module->register();

    $linkList = new \Aras\Localize\Module\LinkList();
    $linkList->register();

    $hreflang = new \Aras\Localize\Module\Hreflang();
    $hreflang->register();

    $sitemap = new \Aras\Localize\Module\Sitemap();
    $sitemap->register();

    $prerender = new \Aras\Localize\Module\Prerender();
    $prerender->register();
});
