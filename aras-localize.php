<?php
/**
 * Plugin Name: Aras - Localize
 * Description: Custom LocalizeJS language switcher styled to match Aras branding.
 * Version: 1.0.3
 * Author: Aras
 */

if (!defined('ABSPATH')) {
    exit;
}

const ARAS_LOCALIZE_VERSION = '1.0.4';

require_once __DIR__ . '/lib/API/LocalizeAPI.php';
require_once __DIR__ . '/lib/Util/Common.php';
require_once __DIR__ . '/lib/Module/LanguageSwitcher.php';
require_once __DIR__ . '/lib/Module/LinkList.php';
require_once __DIR__ . '/lib/Module/Hreflang.php';
require_once __DIR__ . '/lib/Module/Feed.php';
require_once __DIR__ . '/lib/Module/Sitemap.php';
require_once __DIR__ . '/lib/Module/Prerender.php';
require_once __DIR__ . '/lib/ACF.php';

add_action('plugins_loaded', function() {
    $modules = [
        new \Aras\Localize\Module\LanguageSwitcher(),
        new \Aras\Localize\Module\LinkList(),
        new \Aras\Localize\Module\Hreflang(),
        new \Aras\Localize\Module\Feed(),
        new \Aras\Localize\Module\Sitemap(),
        new \Aras\Localize\Module\Prerender(),
    ];

    $acf = new \Aras\Localize\ACF($modules);
    $acf->register();

    foreach ($modules as $module) {
        $module->register();
    }
});
