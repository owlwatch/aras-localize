<?php $post_id = get_the_ID(); ?>
<?php
$industries = get_field('industries_partner__c');
if ($industries) {
	$industries_array = explode(';', $industries);
	$industries_slugs = array_map('sanitize_and_convert_to_slug', $industries_array);
	$industries_slugs_list = implode(' ', $industries_slugs);
} else {
	$industries_slugs_list = '';
}
$partner_solutions = get_field('partner_solutions__c');
if ($partner_solutions) {
	$solutions_array = explode(';', $partner_solutions);
	$solutions_slugs = array_map('sanitize_and_convert_to_slug', $solutions_array);
	$solutions_slugs_list = implode(' ', $solutions_slugs);
} else {
	$solutions_slugs_list = '';
}
$type_partner = get_field('type_partner__c');
if ($type_partner) {
	$type_array = explode(';', $type_partner);
	$type_slugs = array_map('sanitize_and_convert_to_slug', $type_array);
	$type_slugs_list = implode(' ', $type_slugs);
} else {
	$type_slugs_list = '';
}
$regions_partner = get_field('regions_partner__c');
if ($regions_partner) {
	$regions_array = explode(';', $regions_partner);
	$regions_slugs = array_map('sanitize_and_convert_to_slug', $regions_array);
	$regions_slugs_list = implode(' ', $regions_slugs);
} else {
	$regions_slugs_list = '';
}
$partner_integrations = get_field('partner_integrations__c');
if ($partner_integrations) {
	$integrations_array = explode(';', $partner_integrations);
	$integrations_slugs = array_map('sanitize_and_convert_to_slug', $integrations_array);
	$integrations_slugs_list = implode(' ', $integrations_slugs);
} else {
	$integrations_slugs_list = '';
}
?>

<?php if (get_field('partner_icon_for_website__c')) {
	$thumbnail_url = get_field('partner_icon_for_website__c');
	$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$site_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	if (str_contains($site_url, '/ja-jp/')) {
		if (get_field('partner_name_for_website_japan__c')) {
			$partner_title = get_field('partner_name_for_website_japan__c');
		} else {
			$partner_title = get_the_title('');
		}
	} else {
		$partner_title = get_the_title('');
	} ?>
	<div class="partner-item mix <?= "$industries_slugs_list $solutions_slugs_list $type_slugs_list $regions_slugs_list $integrations_slugs_list" ?>">
		<a aria-label="<?php echo $partner_title; ?>" title="<?php echo $partner_title; ?>" href="<?php the_permalink() ?>">
			<img src="<?php echo $thumbnail_url; ?>" alt="<?php echo $partner_title; ?>" title="<?php echo $partner_title; ?>" />
		</a>
	</div>
<?php } ?>