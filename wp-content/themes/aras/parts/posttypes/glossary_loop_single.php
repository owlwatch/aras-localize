<?php
$default_glossary_archive_url = get_post_type_archive_link('glossary');
$author_id = get_the_author_meta('ID');
$current_post_id = get_the_ID();
$categories = get_the_terms($current_post_id, 'category');
$tags = get_the_tags($current_post_id);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(''); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
	<?php get_template_part('parts/_template_parts/glossary_hero_banner'); ?>
	<section class="backlink smalltoppadding nobottompadding">
		<div class="grid-container">
			<div class="grid-x grid-padding-x align-middle">
				<div class="cell small-12">
					<a aria-label="Back to Glossary" class="backlink-link" href="<?php echo $default_glossary_archive_url; ?>">
						<h6>Glossary&nbsp;→</h6>
					</a>
				</div>
			</div>
		</div>
	</section>
	<section class="post-content smalltoppadding mediumbottompadding">
		<div class="grid-container">
			<div class="grid-x grid-padding-x">

				<div class="cell small-12 medium-shrink postsidebar">
					<?php if (get_field('include_table_of_contents') == 'show') : ?>
						<div id="toc-container" class="gloss-tableofcontents">
							<h6>In this article:</h6>
							<ul id="toc"></ul>
						</div>
					<?php endif; ?>


					<?php
					$featured_posts = get_field('related_terms');
					if ($featured_posts) : ?>
						<div class="gloss-related desktopsize">
							<h6>Related Terms</h6>
							<ul>
								<?php foreach ($featured_posts as $post) :
									setup_postdata($post); ?>
									<li>
										<a aria-label="<?php the_title(''); ?>" href="<?php the_permalink(); ?>">
											<?php if (get_field('glossary_term_headline')) : ?>
												<?php echo get_field('glossary_term_headline'); ?>
											<?php else : ?>
												<?php the_title(''); ?>
											<?php endif; ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
						<?php wp_reset_postdata(); ?>
					<?php endif; ?>



					<?php if (have_rows('glossary_sidebar_cta', 'option')) : ?>
						<?php while (have_rows('glossary_sidebar_cta', 'option')) : the_row(); ?>

							<div class="sidebar-gloss-cta desktopsize">
								<?php $image = get_sub_field('cta_image', 'option');
								if (!empty($image)) : ?>
									<img class="sidebar-cta-image" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
								<?php endif; ?>
								<div class="sidebar-gloss-cta-content">
									<?php if (get_sub_field('cta_headline', 'option')) : ?>
										<h4><?php the_sub_field('cta_headline', 'option'); ?></h4>
									<?php endif; ?>
									<?php if (get_sub_field('cta_description', 'option')) : ?>
										<p><?php the_sub_field('cta_description', 'option'); ?></p>
									<?php endif; ?>
									<?php $link = get_sub_field('cta_button', 'option');
									if ($link) : $link_url = $link['url'];
										$link_title = $link['title'];
										$link_target = $link['target'] ? $link['target'] : '_self';
									?>
										<a aria-label="<?php echo esc_html($link_title); ?>" class="aras-button" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
											<?php echo esc_html($link_title); ?>
										</a>
									<?php endif; ?>
								</div>
							</div>
						<?php endwhile; ?>
					<?php endif; ?>

				</div>



				<div id="post-content" class="cell small-12 medium-auto">
					<?php the_content(); ?>
					<?php get_template_part('parts/_flexible_post_content/_flexible_post_content'); ?>
				</div>

				<div class="cell small-12 medium-shrink postsidebar mobilesize">
					<?php
					$featured_posts = get_field('related_terms');
					if ($featured_posts) : ?>
						<div class="gloss-related">
							<h6>Related Terms</h6>
							<ul>
								<?php foreach ($featured_posts as $post) :
									setup_postdata($post); ?>
									<li>
										<a aria-label="<?php echo the_title(''); ?>" href="<?php the_permalink(); ?>">
											<?php if (get_field('glossary_term_headline')) : ?>
												<?php echo get_field('glossary_term_headline'); ?>
											<?php else : ?>
												<?php the_title(''); ?>
											<?php endif; ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
						<?php wp_reset_postdata(); ?>
					<?php endif; ?>

					<?php if (have_rows('glossary_sidebar_cta', 'option')) : ?>
						<?php while (have_rows('glossary_sidebar_cta', 'option')) : the_row(); ?>
							<div class="sidebar-gloss-cta">
								<?php $image = get_sub_field('cta_image', 'option');
								if (!empty($image)) : ?>
									<img class="sidebar-cta-image" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
								<?php endif; ?>
								<div class="sidebar-gloss-cta-content">
									<?php if (get_sub_field('cta_headline', 'option')) : ?>
										<h4><?php the_sub_field('cta_headline', 'option'); ?></h4>
									<?php endif; ?>
									<?php if (get_sub_field('cta_description', 'option')) : ?>
										<p><?php the_sub_field('cta_description', 'option'); ?></p>
									<?php endif; ?>
									<?php $link = get_sub_field('cta_button', 'option');
									if ($link) : $link_url = $link['url'];
										$link_title = $link['title'];
										$link_target = $link['target'] ? $link['target'] : '_self';
									?>
										<a aria-label="<?php echo esc_html($link_title); ?>" class="aras-button" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
											<?php echo esc_html($link_title); ?>
										</a>
									<?php endif; ?>
								</div>
							</div>
						<?php endwhile; ?>
					<?php endif; ?>

				</div>
			</div>
		</div>
	</section>
</article> <!-- end article -->

<?php get_template_part('parts/_template_parts/footer_cta_glossary'); ?>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const contentContainer = document.getElementById('post-content');
		const tocContainer = document.getElementById('toc');
		const headings = contentContainer.querySelectorAll('h2');

		function sanitizeForId(string) {
			return string.toLowerCase().replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-');
		}

		headings.forEach(function(heading) {
			let isExcluded = false;
			let parent = heading.parentElement;
			while (parent !== contentContainer) {
				if (parent.classList.contains('floating-cta') /*|| parent.classList.contains('other-example')*/ ) {
					isExcluded = true;
					break;
				}
				parent = parent.parentElement;
			}
			if (!isExcluded) {
				const listItem = document.createElement('li');
				const anchor = document.createElement('a');
				anchor.setAttribute('href', '#' + sanitizeForId(heading.textContent));
				anchor.textContent = heading.textContent;
				listItem.appendChild(anchor);
				tocContainer.appendChild(listItem);
				heading.setAttribute('id', sanitizeForId(heading.textContent));
			}
		});

		function updateActiveItem() {
			const scrollPosition = window.scrollY;
			const windowHeight = window.innerHeight;

			headings.forEach(function(heading) {
				const bounding = heading.getBoundingClientRect();
				if (bounding.top <= windowHeight / 2 && bounding.bottom >= windowHeight / 2) {
					const activeAnchor = document.querySelector('a[href="#' + sanitizeForId(heading.textContent) + '"]');
					const activeItem = activeAnchor.parentElement;
					const activeItems = document.querySelectorAll('.toc-active');
					activeItems.forEach(function(item) {
						item.classList.remove('toc-active');
					});
					activeItem.classList.add('toc-active');
				}
			});
		}
		window.addEventListener('scroll', updateActiveItem);
		if (headings.length > 0) {
			const firstAnchor = document.querySelector('a[href="#' + sanitizeForId(headings[0].textContent) + '"]');
			const firstItem = firstAnchor.parentElement;
			firstItem.classList.add('toc-active');
		}
		tocContainer.addEventListener('click', function(event) {
			if (event.target.tagName === 'A') {
				const clickedItem = event.target.parentElement;
				const activeItems = document.querySelectorAll('.toc-active');
				activeItems.forEach(function(item) {
					item.classList.remove('toc-active');
				});
				clickedItem.classList.add('toc-active');
			}
		});
	});
</script>