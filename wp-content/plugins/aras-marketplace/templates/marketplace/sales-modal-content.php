<?php
namespace Aras\Marketplace;
use Aras\Marketplace\Service\Template;
$fields = [
	[
		'field_name' => 'marketplace_sales_modal_content',
		'post_id' => get_the_ID()
	]
];

// get the top level term
$contributor = get_first_term('mp-contributor', get_the_ID() );
if( $contributor ){
	$fields[] = [
		'field_name' => 'marketplace_sales_modal_content',
		'post_id' => $contributor
	];
}

$fields[] = [
	'field_name' => 'marketplace_sales_modal_content',
	'post_id' => 'option'
];

function sales_modal_replacements($content){
	
	$contributor = get_first_term('mp-contributor', get_the_ID() );
	$content = preg_replace_callback('/{{\s*?(.+)\.(.+)\s*?}}/', function($matches) use ($contributor){
		$object = $matches[1];
		$var = $matches[2];
		$value = '';
		switch( $object ){
			case 'contributor':
				switch($var){
					case 'name':
						$value = $contributor->name;
						break;
					case 'email':
						$value = get_field('email', $contributor);
						break;

				}
				break;
			case 'solution':
				switch($var){
					case 'name':
						$value = get_the_title();
						break;
				}
				break;
		}
		return $value;
	}, $content);
	
	return $content;
}

foreach( $fields as $field ){
	$v = get_field( $field['field_name'], $field['post_id'] );
	if( $v && !empty( $v['flexible_post_content'] )){
		add_filter('acf_the_content', 'Aras\Marketplace\sales_modal_replacements', 10, 1);
		?>
		<div class="reveal medium" id="aras-solution-modal" data-reveal>
			<?php
			Template::get_template_part('marketplace/page-content', null, [
				'field_name' => $field['field_name'],
				'post_id' => $field['post_id']
			]);
			?>
			<button class="close-button" data-close aria-label="Close modal" type="button">
				<span aria-hidden="true">&times;</span>
			</button>
			</div>
			<button type="button" class="aras-button mp-solution-page__download-button" data-open="aras-solution-modal">
			<?php _e('Get it', 'aras-marketplace'); ?>
			</button>
		<?php
		remove_filter('acf_the_content', 'Aras\Marketplace\sales_modal_replacements', 10);
		break;
	}
}
?>