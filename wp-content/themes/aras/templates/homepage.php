<?php

/**
 * Template Name: Homepage
 */
get_header(); ?>
<?php get_template_part('parts/_template_parts/hero_banner_home'); ?>

<?php if (have_posts()) :
  while (have_posts()) : the_post(); ?>
    <?php get_template_part('parts/_flexible_content/_flexible_content'); ?>
<?php endwhile;
endif; ?>

<?php get_template_part('parts/_template_parts/footer_cta'); ?>

<?php get_footer(); ?>