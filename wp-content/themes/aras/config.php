<?php
// filter the config array to add the 'features' key
add_filter('aras_config', function ($config) {
	$config['features'] = [
		'swoogo',
		'verint',
		'salesforce',
		'resources',
		'events',
		'documentation',
		'news',
		'blog',
		'lp',
		'demo',
		'speakers',
		'user_profiles',
		'quotes',
		'qualified',
		'google_ads_enhanced_conversions'
	];
	return $config;
});