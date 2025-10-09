<?php

// Add custom style dropdown to TinyMCE editor
function aras_add_editor_styles( $init ) {
	if( isset( $init['style_formats'] ) ) {
		$existing_styles = json_decode( $init['style_formats'], true );
	} else {
		$existing_styles = [];
	}
	$style_formats = array(
		array(
			'title' => 'Large Blockquote',
			'classes' => 'large-blockquote',
			'selector' => 'blockquote',
		),
	);
	$init['style_formats'] = wp_json_encode( array_merge($existing_styles, $style_formats) );
	return $init;
}
add_filter( 'tiny_mce_before_init', 'aras_add_editor_styles' );

// Callback function to insert 'styleselect' into the $buttons array
function aras_mce_buttons_2( $buttons ) {
	if( !in_array( 'styleselect', $buttons ) ) {
		array_unshift( $buttons, 'styleselect' );
	}
	return $buttons;
}
// Register our callback to the appropriate filter
add_filter( 'mce_buttons_2', 'aras_mce_buttons_2' );

// Enqueue editor stylesheet for backend
function aras_enqueue_editor_styles() {
	add_editor_style( 'editor-style.css' );
}
add_action( 'admin_init', 'aras_enqueue_editor_styles' );