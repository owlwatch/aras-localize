<?php
namespace Aras\WPML;

function seo_partner_content( $content )
{
	if( is_singular('partners') ){
		// get the field partner_info__c
		$info = get_field( 'partner_info__c');
		$desc = wp_html_excerpt( $info, 162, '...' );
		return $desc;
	}
	return $content;
}

add_filter('wpseo_metadesc', 'Aras\\WPML\\seo_partner_content');