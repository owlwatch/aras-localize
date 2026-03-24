<?php
namespace Aras\Localize;

use Aras\Localize\Module\LanguageSwitcher;

class ACF {
    public function register() {
        add_action('acf/init', [$this, 'register_options_page']);
        add_action('acf/init', [$this, 'register_fields']);
    }

    public function register_options_page() {
        if (!function_exists('acf_add_options_page')) {
            return;
        }

        acf_add_options_page([
            'page_title' => 'Aras Localize',
            'menu_title' => 'Aras Localize',
            'menu_slug' => LanguageSwitcher::OPTIONS_PAGE,
            'capability' => 'manage_options',
            'redirect' => false,
        ]);
    }

    public function register_fields() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_aras_localize_settings',
            'title' => 'Aras Localize Settings',
            'fields' => [
                [
                    'key' => 'field_aras_localize_api_key',
                    'label' => 'API key',
                    'name' => 'localize_api_key',
                    'type' => 'password',
                ],
                [
                    'key' => 'field_aras_localize_enable_switcher',
                    'label' => 'Enable switcher',
                    'name' => LanguageSwitcher::FIELD_ENABLE_SWITCHER,
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => LanguageSwitcher::OPTIONS_PAGE,
                    ],
                ],
            ],
        ]);
    }
}
