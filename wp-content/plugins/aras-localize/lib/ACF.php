<?php
namespace Aras\Localize;

class ACF {
    const OPTIONS_PAGE = 'aras-localize';
    const FIELD_API_KEY = 'localize_api_key';

    /**
     * @var array<int, object>
     */
    private $modules;

    /**
     * @param array<int, object> $modules
     */
    public function __construct(array $modules = []) {
        $this->modules = $modules;
    }

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
            'menu_slug' => self::OPTIONS_PAGE,
            'capability' => 'manage_options',
            'redirect' => false,
        ]);
    }

    public function register_fields() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        $fields = [
            [
                'key' => 'field_aras_localize_api_tab',
                'label' => 'API',
                'type' => 'tab',
                'placement' => 'top',
            ],
            [
                'key' => 'field_aras_localize_api_key',
                'label' => 'API key',
                'name' => self::FIELD_API_KEY,
                'type' => 'password',
            ],
        ];

        foreach ($this->modules as $module) {
            if (!method_exists($module, 'get_acf_fields')) {
                continue;
            }

            $module_fields = $module->get_acf_fields();
            if (!is_array($module_fields) || empty($module_fields)) {
                continue;
            }

            $fields = array_merge($fields, $module_fields);
        }

        acf_add_local_field_group([
            'key' => 'group_aras_localize_settings',
            'title' => 'Aras Localize Settings',
            'fields' => $fields,
            'location' => [
                [
                    [
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => self::OPTIONS_PAGE,
                    ],
                ],
            ],
        ]);
    }
}
