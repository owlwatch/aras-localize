<?php
/*
Template Post Type: lp
//This is the default tempalate for Landing Pages.
//The template label has been replaced from 'Default' to 'Gated Resource'
*
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