<?php
namespace Aras;

// create a class that looks for the parameter 'aras_script'
// and then will try to find the corresponding class in a file
// where the file is snake cased and the class is camel cased
add_action('init', function () {
	// if (!is_super_admin()) {
	// 	return;
	// }
	if (!isset($_REQUEST['aras-script'])) {
		return;
	}
	$script = $_REQUEST['aras-script'];
	$file = get_stylesheet_directory() . '/src/scripts/' . $script . '.php';
	if (!file_exists($file)) {
		/* translators: 1: script slug, 2: full file path */
		wp_die(sprintf(__('No file found for script: %1$s file: %2$s', 'aras'), $script, $file));
		return;
	}
	require_once($file);
	$className = 'Aras\\Script\\' . str_replace(' ', '', ucwords(str_replace('-', ' ', $script)));
	if (!class_exists($className)) {
		return;
	}
	$instance = new $className();
	if (method_exists($instance, 'run')) {
		$instance->run();
	}
});