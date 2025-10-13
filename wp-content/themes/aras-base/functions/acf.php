<?php
// Add ACF field group location condition for current theme (aras or xplm)
add_filter('acf/location/rule_types', function($choices) {
	$choices['Theme']['theme'] = 'Theme is';
	return $choices;
});

add_filter('acf/location/rule_values/theme', function($choices) {
	$choices['aras'] = 'aras';
	$choices['xplm'] = 'xplm';
	return $choices;
});

add_filter('acf/location/rule_match/theme', function($match, $rule, $options) {

	$current_theme = wp_get_theme()->get('Name');
	$selected_theme = $rule['value'];
	if ($rule['operator'] == '==') {
		$match = strtolower($current_theme) === strtolower($selected_theme);
	} elseif ($rule['operator'] == '!=') {
		$match = strtolower($current_theme) !== strtolower($selected_theme);
	}
	return $match;
}, 10, 3);

// filter 'flexible_content' field to disable layouts that start with xplm or aras based on current theme
add_filter('acf/load_field/type=flexible_content', function($field) {

	// bail if we are editing a field group
	if (is_admin() && isset($_GET['post']) && get_post_type($_GET['post']) === 'acf-field-group') {
		return $field;
	}
	$layouts = $field['layouts'];
	$current_theme = wp_get_theme()->get('Name');
	foreach ($layouts as $key => $layout) {
		if (preg_match('/^(xplm|aras)\:/i', $layout['label'])) {
			// get the prefix of the layout (xplm: or aras: )
			$prefix = strtolower(substr($layout['label'], 0, strpos($layout['label'], ':')));
			if (!preg_match('/' . preg_quote(strtolower($current_theme), '/') . '/i', strtolower($prefix))) {
				unset($layouts[$key]);
			}
			else {
				// strip the prefix from the label
				$layouts[$key]['label'] = trim(substr($layout['label'], strpos($layout['label'], ':') + 1));
			}
		}
	}

	// sort the layouts alphabetically by label
	usort($layouts, function($a, $b) {
		return strcmp($a['label'], $b['label']);
	});
	$field['layouts'] = $layouts;
	return $field;
}, 10, 2);

// allow for field group locations to have aribtrary values
add_filter('acf/location/rule_values/taxonomy', function($choices) {
	$choices['xplm-vendor'] = 'XPLM Vendor';
	$choices['xplm-benefit'] = 'XPLM Benefit';
	// feature
	$choices['xplm-feature'] = 'XPLM Feature';
	// category
	$choices['xplm-category'] = 'XPLM Category';
	return $choices;
});


// allow for field group locations to have aribtrary values
add_filter('acf/location/rule_values/post_type', function($choices) {
	$choices['xplm-solution'] = 'XPLM Solution';
	$choices['xplm-combination'] = 'XPLM Combination';
	$choices['xplm-customer-story'] = 'XPLM Customer Story';
	$choices['xplm-sales-contact'] = 'XPLM Sales Contact';
	return $choices;
});

// lets fix the hide on screen so that it merges all the hidden items
// from all the field groups
function aras_acf_hide_on_screen_fix( $style, $field_group )
{
	// first we want to remove this filter so we don't get into an infinite loop
	remove_filter('acf/get_field_group_style', 'aras_acf_hide_on_screen_fix', 10, 2);

	// doing ajax?
	$args = defined('DOING_AJAX') && DOING_AJAX ? wp_parse_args( wp_unslash( $_REQUEST ), [
		'screen'  => '',
		'post_id' => 0,
		'ajax'    => true,
		'exists'  => array(),
	]) : [
		'post_id'   => get_the_ID(),
		'post_type' => get_post_type( get_the_ID() ),
	];


	// we need the field groups for the current screen (or ajax request)
	$field_groups = acf_get_field_groups( $args );
	
	$style = '';
	if ( $field_groups ) {
		foreach ( $field_groups as $i => $field_group ) {
			$field_group_style = acf_get_field_group_style( $field_group );
			$style .= $field_group_style;
		}
	}
	// first we want to remove this filter so we don't get into an infinite loop
	add_filter('acf/get_field_group_style', 'aras_acf_hide_on_screen_fix', 10, 2);
	return $style;
}
add_filter('acf/get_field_group_style', 'aras_acf_hide_on_screen_fix', 10, 2);