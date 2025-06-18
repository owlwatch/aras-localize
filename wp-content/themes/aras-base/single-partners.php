<?php
/*
Template Post Type: partners
*/
get_header(); ?>
<?php if (have_posts()) :
  while (have_posts()) : the_post(); ?>
    <?php get_template_part('parts/posttypes/partners_loop_single'); ?>
<?php endwhile;
endif; ?>
<?php get_footer(); ?>