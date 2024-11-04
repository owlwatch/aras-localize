<?php

/**
 * Template part for displaying a single post
 */
?>

<?php
$site_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$default_post_archive_url = get_permalink(get_option('page_for_posts'));
$author_id = get_the_author_meta('ID');
$current_post_id = get_the_ID();
$categories = get_the_terms($current_post_id, 'category');
$tags = get_the_tags($current_post_id);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(''); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
	<?php get_template_part('parts/_template_parts/hero_banner_blog'); ?>
	<section class="post-content mediumtoppadding mediumbottompadding">
		<div class="grid-container">
			<div class="grid-x grid-padding-x">
				<div id="post-content" class="cell small-12 medium-auto">
					<?php the_content(); ?>
					<?php get_template_part('parts/_flexible_post_content/_flexible_post_content'); ?>
					<?php if ($tags) : ?>
						<div class="tag-container">
							<h6>Related:</h6>
							<?php foreach ($tags as $tag) :
								$tag_link = get_category_link($tag->term_id);
								$tag_name = $tag->name;

								if (str_contains($site_url, '/ja-jp/')) {
									$tag_link = str_replace('/en/', '/ja-jp/', $tag_link);
									$tag_name = get_field('cat_label_japanese', $tag) ?: $tag_name;
								} elseif (str_contains($site_url, '/fr-fr/')) {
									$tag_link = str_replace('/en/', '/fr-fr/', $tag_link);
									$tag_name = get_field('cat_label_french', $tag) ?: $tag_name;
								} elseif (str_contains($site_url, '/de-de/')) {
									$tag_link = str_replace('/en/', '/de-de/', $tag_link);
									$tag_name = get_field('cat_label_german', $tag) ?: $tag_name;
								}

							?>
								<a aria-label="<?php echo esc_html($tag_name) ?>" class="blog-tag-button" href="<?php echo esc_url($tag_link) ?>"><?php echo esc_html($tag_name) ?></a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<div style="margin-top: 1rem">
						<a class="card-link" href="<?php echo get_post_type_archive_link( 'post' ) ?>">← Back to Blog</a>
					</div>
				</div>
				<div class="cell small-12 medium-shrink postsidebar blogpostsidebar">
					<?php
					$has_other_category = false;
					if ($categories) {
						foreach ($categories as $category) {
							if ($category->slug !== 'uncategorized' && $category->slug !== 'uncategorized-fr-fr' && $category->slug !== 'uncategorized-de-de' && $category->slug !== 'uncategorized-ja-jp') {
								$has_other_category = true;
								break;
							}
						}
					}
					if ($has_other_category) :
					?>
						<div class="blog-sidebar-topics">
							<?php
							$cat_intro = 'Category:';
							$cat_intro = get_field('category_page_heading', 'option') ?: $cat_intro;
							?>
							<h6><?php echo $cat_intro; ?></h6>
							<?php foreach ($categories as $category) :
								if ($category->name !== 'Uncategorized') {
									$cat_link = get_category_link($category->term_id);
									$cat_name = $category->name;
									if (str_contains($site_url, '/ja-jp/')) {
										$cat_link = str_replace('/en/', '/ja-jp/', $cat_link);
										$cat_name = get_field('cat_label_japanese', $category) ?: $cat_name;
									} elseif (str_contains($site_url, '/fr-fr/')) {
										$cat_link = str_replace('/en/', '/fr-fr/', $cat_link);
										$cat_name = get_field('cat_label_french', $category) ?: $cat_name;
									} elseif (str_contains($site_url, '/de-de/')) {
										$cat_link = str_replace('/en/', '/de-de/', $cat_link);
										$cat_name = get_field('cat_label_german', $category) ?: $cat_name;
									}
							?>
									<a aria-label="<?php echo esc_html($cat_name) ?>" class="sidebar-cat" href="<?php echo esc_url($cat_link) ?>"><?php echo esc_html($cat_name) ?></a>
							<?php
								}
							endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if (get_field('include_blog_sidebar_cta', 'option')) : ?>
						<?php if (have_rows('blog_sidebar_cta', 'option')) : ?>
							<?php while (have_rows('blog_sidebar_cta', 'option')) : the_row(); ?>
								<?php if (get_sub_field('cta_form', 'option')) : ?>
									<div class="sidebar-cta">
										<?php if (get_sub_field('cta_headline', 'option')) : ?>
											<h5><?php echo get_sub_field('cta_headline', 'option'); ?></h5>
										<?php else : ?>
											<h5>STAY UP TO DATE</h5>
										<?php endif; ?>
										<button class="aras-button" data-open="blog-sidebar-form">
											<?php if (get_sub_field('cta_form_label', 'option')) {
												echo get_sub_field('cta_form_label', 'option');
											} else {
												echo 'Subscribe';
											} ?>
										</button>
									</div>
								<?php endif; ?>
							<?php endwhile; ?>
						<?php endif; ?>
					<?php endif; ?>

				</div>
			</div>
		</div>
	</section>
	<?php if (have_rows('author_information', 'user_' . $author_id)) : ?>
		<?php while (have_rows('author_information', 'user_' . $author_id)) : the_row(); ?>
			<?php if (get_sub_field('author_bio', 'user_' . $author_id)) : ?>
				<section class="author-section">
					<div class="grid-container">
						<div class="grid-x grid-margin-x">
							<div class="cell small-12 mediumtoppadding mediumbottompadding">
								<?php $image = get_sub_field('author_headshot');
								if (!empty($image)) : ?>
									<img class="author-headshot" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
								<?php endif; ?>
								<div class="author-bio">
									<h3>About <?php the_author(); ?></h3>
									<?php echo get_sub_field('author_bio'); ?>
								</div>
							</div>
						</div>
					</div>
				</section>
			<?php endif; ?>
		<?php endwhile; ?>
	<?php endif; ?>

	<?php comments_template();
	?>

	<?php get_template_part('parts/posttypes/blog_recommended_blogs'); ?>
</article> <!-- end article -->


<?php if (get_field('include_blog_sidebar_cta', 'option')) : ?>
	<?php if (have_rows('blog_sidebar_cta', 'option')) : ?>
		<?php while (have_rows('blog_sidebar_cta', 'option')) : the_row(); ?>
			<?php if (get_sub_field('cta_form', 'option')) : ?>
				<div class="reveal medium" id="blog-sidebar-form" data-reveal>
					<div class="heroform">
						<?php if (get_sub_field('cta_form_headline', 'option')) {
							echo '<h3 class="text-center" style="margin-bottom: 1rem; font-weight: 400;">' . get_sub_field('cta_form_headline', 'option') . '<h3>';
						} ?>
						<?php $gravity_form_id = get_sub_field('cta_form', 'option');
						echo do_shortcode('[gravityform id="' . $gravity_form_id . '" title="false" description="false"]'); ?>
					</div>
					<?php get_template_part('parts/_template_parts/gform_variables'); ?>
					<button class="close-button" data-close aria-label="Close modal" type="button">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			<?php endif; ?>
		<?php endwhile; ?>
	<?php endif; ?>
<?php endif; ?>