<?php
$default_partners_archive_url = get_post_type_archive_link('partners');
$post_id = get_the_ID();
$site_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$partner_title = '';
$partner_info = '';
if (str_contains($site_url, '/ja-jp/')) {
	if (get_field('partner_name_for_website_japan__c')) {
		$partner_title = get_field('partner_name_for_website_japan__c');
	} else {
		$partner_title = get_the_title('');
	}
	if (get_field('partner_info_japan__c')) {
		$partner_info = get_field('partner_info_japan__c');
	} else {
		$partner_info = get_field('partner_info__c');
	}
} else {
	$partner_title = get_the_title('');
	$partner_info = get_field('partner_info__c');
} ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(''); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
	<section class="partners-hero-banner mediumtoppadding mediumbottompadding bg-dblue">
		<div class="grid-container">
			<div class="grid-x grid-padding-x">
				<div class="cell small-12 hero-content">
					<h1 class="hero-headline"><?php echo $partner_title; ?></h1>
				</div>
			</div>
		</div>
	</section>
	<section class="backlink smalltoppadding nobottompadding">
		<div class="grid-container">
			<div class="grid-x grid-padding-x align-middle">
				<div class="cell small-12">
					<a aria-label="See All Partners" class="archive-link" href="<?php echo $default_partners_archive_url; ?>">
						<?php if (get_field('partner_single_breadcrumb_label', 'option')) : ?>
							<h6><?php echo get_field('partner_single_breadcrumb_label', 'option'); ?>&nbsp;→</h6>
						<?php else : ?>
							<h6>All Partners&nbsp;→</h6>
						<?php endif; ?>
					</a>
				</div>
			</div>
		</div>
	</section>


	<section class="partners-post-content post-content smalltoppadding mediumbottompadding">
		<div class="grid-container">
			<div class="grid-x grid-padding-x">
				<div class="cell small-12">
					<?php if (get_field('partner_icon_for_website__c')) {
						$thumbnail_url = get_field('partner_icon_for_website__c'); ?>
						<img class="partner-image" src="<?php echo $thumbnail_url; ?>" alt="<?php echo $partner_title; ?>" title="<?php echo $partner_title; ?>" />
					<?php } ?>
				</div>
				<div id="post-content" class="cell small-12 medium-auto">

					<?php if ($partner_info != '') {
						echo $partner_info;
					} ?>

					<?php if (get_field('partners_url_link__c')) : ?>
						<a aria-label="Visit Website" class="aras-button" href="<?php echo get_field('partners_url_link__c'); ?>" target="_blank">
							<?php if (get_field('partner_single_button_label', 'option')) : ?>
								<?php echo get_field('partner_single_button_label', 'option'); ?>
							<?php else : ?>
								Visit Website
							<?php endif; ?>
						</a>
					<?php endif; ?>
				</div>
				<div class="cell small-12 medium-shrink postsidebar">
					<?php if (get_field('type_partner__c')) : ?>
						<?php $partner_type = get_field('type_partner__c');
						$partner_type_formatted = str_replace(';', ', ', $partner_type); ?>
						<div class="filters-item">
							<?php if (get_field('partner_single_types_label', 'option')) : ?>
								<h6><?php echo get_field('partner_single_types_label', 'option'); ?></h6>
							<?php else : ?>
								<h6>Types</h6>
							<?php endif; ?>
							<p><?= "$partner_type_formatted" ?></p>
						</div>
					<?php endif; ?>
					<?php if (get_field('regions_partner__c')) : ?>
						<?php $partner_regions = get_field('regions_partner__c');
						$partner_regions_formatted = str_replace(';', ', ', $partner_regions); ?>
						<div class="filters-item">
							<?php if (get_field('partner_single_regions_label', 'option')) : ?>
								<h6><?php echo get_field('partner_single_regions_label', 'option'); ?></h6>
							<?php else : ?>
								<h6>Regions</h6>
							<?php endif; ?>
							<p><?= "$partner_regions_formatted" ?></p>
						</div>
					<?php endif; ?>
					<?php if (get_field('industries_partner__c')) : ?>
						<?php $partner_industries = get_field('industries_partner__c');
						$partner_industries_formatted = str_replace(';', ', ', $partner_industries); ?>
						<div class="filters-item">
							<?php if (get_field('partner_single_industries_label', 'option')) : ?>
								<h6><?php echo get_field('partner_single_industries_label', 'option'); ?></h6>
							<?php else : ?>
								<h6>Industries</h6>
							<?php endif; ?>
							<p><?= "$partner_industries_formatted" ?></p>
						</div>
					<?php endif; ?>
					<?php if (get_field('partner_integrations__c')) : ?>
						<?php $partner_integrations = get_field('partner_integrations__c');
						$partner_integrations_formatted = str_replace(';', ', ', $partner_integrations); ?>
						<div class="filters-item">
							<?php if (get_field('partner_single_integrations_label', 'option')) : ?>
								<h6><?php echo get_field('partner_single_integrations_label', 'option'); ?></h6>
							<?php else : ?>
								<h6>Integrations</h6>
							<?php endif; ?>
							<p><?= "$partner_integrations_formatted" ?></p>
						</div>
					<?php endif; ?>
					<?php if (get_field('partner_solutions__c')) : ?>
						<?php $partner_solutions = get_field('partner_solutions__c');
						$partner_solutions_formatted = str_replace(';', ', ', $partner_solutions); ?>
						<div class="filters-item">
							<?php if (get_field('partner_single_solutions_label', 'option')) : ?>
								<h6><?php echo get_field('partner_single_solutions_label', 'option'); ?></h6>
							<?php else : ?>
								<h6>Solutions</h6>
							<?php endif; ?>
							<p><?= "$partner_solutions_formatted" ?></p>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<?php get_template_part('parts/_template_parts/partners_footer_cta'); ?>
</article>