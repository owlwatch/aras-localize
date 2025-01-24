<?php
namespace Aras\Marketplace\Service;

use AC\ListScreenRepository\Storage\ListScreenRepositoryFactory;
use AC\ListScreenRepository\Rules;
use AC\ListScreenRepository\Rule;

/**
 * Class to set the save paths for Admin Columns Pro
 */
class ACP {

	/**
	 * The constructor
	 */
	public function __construct()
	{
		add_filter( 'acp/storage/repositories', array( $this, 'filterStorage' ), 10, 2 );
	}

	public function filterStorage( array $repositories, ListScreenRepositoryFactory $factory )
	{
		$writable = strpos( home_url(), '.local' ) !== false;

		$rules = new Rules( Rules::MATCH_ANY );
		$rules->add_rule( new Rule\EqualType( 'mp-solution') );
		$rules->add_rule( new Rule\EqualType( 'wp-taxonomy_mp-contributor') );
		$rules->add_rule( new Rule\EqualType( 'wp-taxonomy_mp-solution-category') );
		$rules->add_rule( new Rule\EqualType( 'wp-taxonomy_mp-solution-type') );
		$rules->add_rule( new Rule\EqualType( 'wp-taxonomy_mp-aras-version') );
		$rules->add_rule( new Rule\EqualType( 'wp-taxonomy_mp-language') );

		$repositories['my_plugin_slug'] = $factory->create(
			ARAS_MARKETPLACE_PATH . '/config/acp',
			$writable,
			$rules
		);

		return $repositories;
	}

}