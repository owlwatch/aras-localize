<?php
/**
 * Plugin Name: Aras - Email Validation
 * Description: Restrict Gravity Forms email fields to company domains only.
 * Version: 1.0.0
 * Author: Aras
 * Text Domain: aras-email-validation
 */

if (!defined('ABSPATH')) {
    exit;
}

function aras_email_validation_get_personal_domains() {
    return [
        'gmail.com',
        'googlemail.com',
        'yahoo.com',
        'ymail.com',
        'outlook.com',
        'hotmail.com',
        'live.com',
        'msn.com',
        'aol.com',
        'icloud.com',
        'me.com',
        'mac.com',
        'proton.me',
        'protonmail.com',
        'pm.me',
        'hey.com',
        'zoho.com',
        'gmx.com',
        'gmx.net',
        'mail.com',
        'yandex.com',
        'yandex.ru',
    ];
}

function aras_email_validation_is_personal_domain($email) {
    if (!is_string($email) || $email === '') {
        return false;
    }

    $email = trim(strtolower($email));
    $at_pos = strrpos($email, '@');
    if ($at_pos === false) {
        return false;
    }

    $domain = substr($email, $at_pos + 1);
    if ($domain === '') {
        return false;
    }

    return in_array($domain, aras_email_validation_get_personal_domains(), true);
}

add_filter('gform_field_validation', function($result, $value, $form, $field) {
    if (!$result['is_valid']) {
        return $result;
    }

    if (!isset($field->type) || $field->type !== 'email') {
        return $result;
    }

    $field_enabled = !empty($field->aras_company_email_only);

    if (!$field_enabled) {
        return $result;
    }

    if (aras_email_validation_is_personal_domain($value)) {
        $result['is_valid'] = false;
        $result['message'] = __('Please use your company email address.', 'aras-email-validation');
    }

    return $result;
}, 10, 4);

// Form-level setting commented out for now.
// add_filter('gform_form_settings_fields', function($fields, $form) {
//     $fields['aras_email_validation'] = [
//         'title' => esc_html__('Email Validation', 'aras-email-validation'),
//         'fields' => [
//             [
//                 'label' => esc_html__('Company Email Only', 'aras-email-validation'),
//                 'type' => 'checkbox',
//                 'name' => 'aras_company_email_only',
//                 'choices' => [
//                     [
//                         'label' => esc_html__('Require company email addresses for all email fields in this form.', 'aras-email-validation'),
//                         'name' => 'aras_company_email_only',
//                     ],
//                 ],
//             ],
//         ],
//     ];
//     return $fields;
// }, 10, 2);

// add_filter('gform_form_settings_save', function($form, $settings) {
//     $form['aras_company_email_only'] = !empty($settings['aras_company_email_only']) ? 1 : 0;
//     return $form;
// }, 10, 2);

add_filter('gform_field_standard_settings', function($position, $form_id) {
    if ($position !== 1600) {
        return;
    }
    ?>
    <li class="aras-company-email-setting field_setting">
        <label for="aras_company_email_only" class="section_label">
            <?php echo esc_html__('Company Email Only', 'aras-email-validation'); ?>
            <?php gform_tooltip('aras_company_email_only'); ?>
        </label>
        <input type="checkbox" id="aras_company_email_only" onclick="SetFieldProperty('aras_company_email_only', this.checked ? 1 : 0);" />
        <label for="aras_company_email_only" class="inline">
            <?php echo esc_html__('Require a company email for this field.', 'aras-email-validation'); ?>
        </label>
    </li>
    <?php
}, 10, 2);

add_action('gform_editor_js', function() {
    ?>
    <script type="text/javascript">
        jQuery(document).on('gform_load_field_settings', function(event, field) {
            jQuery('#aras_company_email_only').prop('checked', !!field.aras_company_email_only);
        });

        if (window.fieldSettings && fieldSettings.email) {
            if (fieldSettings.email.indexOf('.aras-company-email-setting') === -1) {
                fieldSettings.email += ', .aras-company-email-setting';
            }
        }
    </script>
    <?php
});

add_filter('gform_tooltips', function($tooltips) {
    $tooltips['aras_company_email_only'] = '<h6>' . esc_html__('Company Email Only', 'aras-email-validation') . '</h6>' .
        esc_html__('Only allow non-personal (company) email domains.', 'aras-email-validation');
    return $tooltips;
});
