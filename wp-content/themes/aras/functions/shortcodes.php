<?php

function aras_anchor_shortcode($atts=[], $content='', $tag='')
{
	static $i = 0;

	$atts = shortcode_atts([
		'htmlTag'  => $content ? 'span' : 'div',
		'class' => '',
		'id' => 'anchor'.(++$i),
	], $atts, $tag );

	
	$htmlTag = $atts['htmlTag'];
	unset( $atts['htmlTag'] );
	
	$attributes = implode(' ', array_map( function($key, $value){
		$escaped = esc_attr($value);
		return "$key=\"$escaped\"";
	}, array_keys($atts), array_values($atts) ) );

	$html = "<$htmlTag $attributes>$content</$htmlTag>";
	return $html;

}

add_shortcode('anchor', 'aras_anchor_shortcode');