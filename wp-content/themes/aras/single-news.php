<?php
global $post; // < -- globalize, just in case
if (get_field('external_url')) {
	wp_redirect(esc_url(get_field('external_url')), 301);
}
get_header();
$default_news_archive_url = get_post_type_archive_link('news'); ?>

<section id="short-hero" class="short-hero hero-banner bg-dblue">
	<div class="grid-container">
		<div class="grid-x grid-padding-x align-top">
			<div class="cell small-12 medium-10 hero-content">
				<div class="hero-content-inner">
					<h1 class="hero-headline"><?php the_title(''); ?></h1>
					<?php if (get_field('news_subtitle')) : ?>
						<h4 class="hero-subhead"><?php echo get_field('news_subtitle'); ?></h4>
					<?php endif; ?>
					<h6 class="hero-byline"><?php if (get_field('press_location')) : ?><?php echo get_field('press_location'); ?> &mdash; <?php endif; ?><?php echo get_the_time('F j, Y'); ?></h6>
				</div>
			</div>
		</div>
	</div>
</section>
<main class="single-content box-bg-white">
	<article id="post-<?php the_ID(); ?>" <?php post_class(''); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
		<section class="backlink smalltoppadding nobottompadding">
			<div class="grid-container">
				<div class="grid-x grid-margin-x align-middle">
					<div class="cell small-12">
						<?php if (get_field('news_archive_backlink_label', 'option')) { ?>
							<a aria-label="<?php echo get_field('news_archive_backlink_label', 'option'); ?>" class="backlink-link" href="<?php echo $default_news_archive_url; ?>">
								<h6><?php echo get_field('news_archive_backlink_label', 'option'); ?>&nbsp;→</h6>
							</a>
						<?php } else { ?>
							<a aria-label="All News" class="backlink-link" href="<?php echo $default_news_archive_url; ?>">
								<h6>All News&nbsp;→</h6>
							</a>
						<?php } ?>
					</div>
				</div>
			</div>
		</section>
		<section class="post-content smalltoppadding largebottompadding">
			<div class="grid-container">
				<div class="grid-x grid-margin-x">
					<?php if (get_field('press_content')) : ?>
						<div class="cell small-12 wysiwyg-content">
							<?php echo get_field('press_content'); ?>
						</div>
					<?php endif; ?>
					<?php if (get_field('press_contact_information')) : ?>
						<div class="cell small-12  press-contact-info wysiwyg-content">
							<h3>Aras Press Contact</h3>
							<?php echo get_field('press_contact_information'); ?>
						</div>
					<?php endif; ?>

				</div>
			</div>
		</section>
	</article>
</main>
<?php get_template_part('parts/_template_parts/footer_cta_news'); ?>
<?php get_footer(); ?>