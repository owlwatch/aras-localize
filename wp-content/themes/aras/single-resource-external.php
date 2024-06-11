<?php
/*
Template Name: External Resource
Template Post Type: resource
*/
global $post; // < -- globalize, just in case
if (get_field('external_url')) {
  wp_redirect(esc_url(get_field('external_url')), 301);
}
get_header(); ?>

<?php get_footer(); ?>