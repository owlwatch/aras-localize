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

foreach( $fields as $field ){
	$v = get_field( $field['field_name'], $field['post_id'] );
	if( $v && !empty( $v['flexible_post_content'] )){
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
		break;
	}
}
?>