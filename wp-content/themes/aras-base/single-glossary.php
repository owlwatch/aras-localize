<?php

/**
 * The template for displaying all single posts and attachments
 */

get_header(); ?>
<main class="single-content">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<?php get_template_part('parts/posttypes/glossary_loop_single'); ?>
		<?php endwhile; ?>
	<?php else : ?>
		<?php get_template_part('parts/content', 'missing'); ?>
	<?php endif; ?>
</main>
<?php get_footer(); ?>