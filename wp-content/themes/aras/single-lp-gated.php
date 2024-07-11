<?php
/*
Template Name: Gated LP
Template Post Type: lp
*
*/
$current_post_id = get_the_ID();
get_header(); ?>

<?php if (get_field('post_submission_action') == 'update') : ?>
  <?php if (isset($_GET['id']) && intval($_GET['id']) === $current_post_id) : /* Checks if submission URL has ID in it */ ?>
    <?php get_template_part('parts/_template_parts/hero_banner_gated_ungated'); ?>
  <?php else : /* if not submitted */  ?>
    <?php get_template_part('parts/_template_parts/hero_banner_gated'); ?>
    <?php if (have_posts()) :
      while (have_posts()) : the_post(); ?>
        <?php get_template_part('parts/_flexible_content/_flexible_content'); ?>
    <?php endwhile;
    endif; ?>
  <?php endif; ?>

<?php elseif (get_field('post_submission_action') == 'redirect') : ?>
  <?php if ($form_submitted || isset($_GET['pi_list_email'])) : ?>
    <?php if (get_field('post-submission_redirect_url')) : ?>
      <?php $redirect_url = get_field('post-submission_redirect_url'); ?>
      <?php header("Location: $redirect_url"); ?>
    <?php endif; ?>
  <?php else : /* if not submitted */ ?>
    <?php get_template_part('parts/_template_parts/hero_banner_gated'); ?>
    <?php if (have_posts()) :
      while (have_posts()) : the_post(); ?>
        <?php get_template_part('parts/_flexible_content/_flexible_content'); ?>
    <?php endwhile;
    endif; ?>

  <?php endif; ?>

<?php else : /* post_submission_action is default */ ?>
  <?php get_template_part('parts/_template_parts/hero_banner_gated'); ?>
  <?php if (have_posts()) :
    while (have_posts()) : the_post(); ?>
      <?php get_template_part('parts/_flexible_content/_flexible_content'); ?>
  <?php endwhile;
  endif; ?>
<?php endif; ?>

<?php get_template_part('parts/_template_parts/footer_cta'); ?>


<?php get_footer(); ?>