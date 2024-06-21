<?php
function var_error_log($object = null)
{
    ob_start();                    // start buffer capture
    var_dump($object);           // dump the values
    $contents = ob_get_contents(); // put the buffer into a variable
    ob_end_clean();                // end capture
    error_log($contents);        // log contents of the result of var_dump( $object )
}
$path = get_template_directory() . '/functions/wpml-resources/resource_updates_232.json';
$jsonstring = file_get_contents($path);
$jsondata = json_decode($jsonstring, true);

$count = true;

add_action('wp_footer', 'update_resource');

function update_resource()
{
    $start = microtime(true);
    global $jsondata;
    global $count;
    foreach ($jsondata as $data) {
        if ($count) {
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
                    $current_user = wp_get_current_user();
                    if ($current_user->user_email == 'jake@receptor.design') {
                        do_action('wpml_set_element_language_details', $set_language_args);
                    }
                }
            }
            $count += 1;
        } else {
            break;
        }
    }
    $time_elapsed_secs = microtime(true) - $start;
    error_log($time_elapsed_secs);
}
