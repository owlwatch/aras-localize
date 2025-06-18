<?php

/**
 * The template for displaying all pages
 * This is the template that displays all pages by default.
 */
get_header(); ?>

<?php get_template_part('parts/_template_parts/hero_banner'); ?>

<?php if (have_posts()) :
  while (have_posts()) : the_post(); ?>
    <?php get_template_part('parts/_flexible_content/_flexible_content'); ?>
<?php endwhile;
endif; ?>

<?php get_template_part('parts/_template_parts/footer_cta'); ?>

<?php get_footer(); ?>