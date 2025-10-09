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