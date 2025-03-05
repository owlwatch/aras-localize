<?php
namespace Aras\Marketplace\Service;

/**
 * @todo finish the autogeneration
 */
class Permalinks
{

	public function __construct()
	{
		add_filter('wp_unique_post_slug', array($this, 'generate_permalink'), 10, 6);
	}

	// generate the default permalink as a concatenation of the acf fields
	// <Contributor> - <Solution Title>
	public function generate_default_permalink($post_id)
	{
		$contributor = get_first_term('mp-contributor', $post_id);
		$contributor_name = $contributor ? $contributor->name : '';
		$solution_title = get_the_title($post_id);
		$permalink = sanitize_title($contributor_name . ' - ' . $solution_title);
		return $permalink;
	}

}