<?php

$path = get_template_directory() . '/functions/wpml-resources/resource_updates_189.json';
$jsonstring = file_get_contents($path);
$jsondata = json_decode($jsonstring, true);


add_action('wp_footer', 'update_resource');

function update_resource()
{
    $current_user = wp_get_current_user();
    if ($current_user->user_email == 'jake@receptor.design') {
        global $jsondata;
        foreach ($jsondata as $data) {
            $en = null;
            $de = null;
            $jp = null;
            $fr = null;
            foreach ($data['posts'] as $lang) {
                if ($lang['WPML Language Code'] == 'en') {
                    $en = $lang;
                };
                if ($lang['WPML Language Code'] == 'de-de') {
                    $de = $lang;
                };
                if ($lang['WPML Language Code'] == 'ja-jp') {
                    $jp = $lang;
                };
                if ($lang['WPML Language Code'] == 'fr-fr') {
                    $fr = $lang;
                };
            }
            $langs = array();
            if ($en != null) {
                array_push($langs, $en);
            };
            if ($de != null) {
                array_push($langs, $de);
            };
            if ($jp != null) {
                array_push($langs, $jp);
            };
            if ($fr != null) {
                array_push($langs, $fr);
            };
            $wpml_element_type = 'post_resource';
            $get_language_args = array('element_id' => $langs[0]['POST_ID'], 'element_type' => 'post_resource');
            $original_post_language_info = apply_filters('wpml_element_language_details', null, $get_language_args);
            foreach ($langs as $key => $lang) {
                if ($lang != null && $key != array_key_first($langs)) {
                    $get_translation_args = array('element_id' => $lang['POST_ID'], 'element_type' => 'post_resource');
                    $translation_post_language_info = apply_filters('wpml_element_language_details', null, $get_translation_args);
                    $set_language_args = array(
                        'element_id'    => $translation_post_language_info->element_id,
                        'element_type'  => $wpml_element_type,
                        'trid'   => $original_post_language_info->trid,
                        'language_code'   => $translation_post_language_info->language_code,
                        'source_language_code' => $original_post_language_info->language_code
                    );
                    do_action('wpml_set_element_language_details', $set_language_args);
                }
            }
        }
    }
}
