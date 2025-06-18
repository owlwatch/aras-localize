<?php

/**
 * The template for displaying search results pages
 *
 * For more info: https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 */
get_header(); ?>
<?php $search_refer = $_GET['post_type']; ?>

<main class="search" role="main">
	<section id="search-hero" class="short-hero hero-banner bg-dblue">
		<div class="grid-container">
			<div class="grid-x grid-padding-x align-top">
				<div class="cell small-12 medium-10 hero-content">
					<div class="hero-content-inner">
						<h1 class="hero-headline">Search Results for: <?php echo esc_attr(get_search_query()); ?></h1>
					</div>
					<div class="cell small-12 medium-10">
						<form role="search" method="get" class="blog-search-form" action="<?php echo home_url('/'); ?>">
							<label>
								<span class="screen-reader-text"><?php echo _x('Search for:', 'label', 'jointswp') ?></span>

								<?php if ($search_refer == 'resource') : ?>
									<input type="hidden" name="post_type" value="resource" />
								<?php elseif ($search_refer == 'post') : ?>
									<input type="hidden" name="post_type" value="post" />
								<?php else : ?>
									<input type="hidden" name="post_type" value="*" />
								<?php endif; ?>
								<input aria-label="search" type="search" class="search-field" placeholder="" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x('Search for:', 'jointswp') ?>" />


							</label>
							<input class="search-arrow-icon" type="submit" value=" " alt="Search" style="background-image:url(<?php echo get_template_directory_uri(); ?>/assets/images/icons/searchicon.svg)" />
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php if ($search_refer == 'resource') : ?>
		<?php $resource_archive_url = get_post_type_archive_link('resource'); ?>
		<section class="backlink smalltoppadding nobottompadding">
			<div class="grid-container">
				<div class="grid-x grid-padding-x align-middle">
					<div class="cell small-12">
						<a aria-label="All Resources" class="backlink-link" href="<?php echo $resource_archive_url; ?>">
							<h6>All Resources&nbsp;→</h6>
						</a>
					</div>
				</div>
			</div>
		</section>
	<?php elseif ($search_refer == 'post') : ?>
		<?php $blog_archive_url = get_post_type_archive_link('post'); ?>
		<section class="backlink smalltoppadding nobottompadding">
			<div class="grid-container">
				<div class="grid-x grid-padding-x align-middle">
					<div class="cell small-12">
						<a aria-label="All Blogs" class="backlink-link" href="<?php echo $blog_archive_url; ?>">
							<h6>All Blogs&nbsp;→</h6>
						</a>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<main class="blog-archive mediumtoppadding largebottompadding" role="main">
		<div class="grid-container">
			<section class="grid-x grid-margin-x blog-post-loop">
				<?php if (have_posts()) : $postCount = 0;
					while (have_posts()) : the_post();
						$postCount++; ?>
						<?php $posttype = get_post_type(); ?>
						<article class="cell cell-card blog-item-cell bordercard small-12 medium-6 large-4" id="post-<?php the_ID(); ?>" role="article">
							<?php get_template_part('parts/loop', 'auto_card_archive'); ?>
						</article>
					<?php endwhile; ?>
					<div class="cell small-12"><?php joints_page_navi(); ?></div>
					<?php wp_reset_postdata(); ?>
				<?php else : ?>
					<?php get_template_part('parts/content', 'missing'); ?>
				<?php endif; ?>
			</section>
		</div>
	</main>


	<?php get_footer(); ?>