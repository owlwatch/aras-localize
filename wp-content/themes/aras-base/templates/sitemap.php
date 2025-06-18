<?php

/**
 * Template Name: Sitemap
 */
get_header(); ?>

<?php get_template_part('parts/_template_parts/hero_banner'); ?>

<div class="grid-container mediumtoppadding mediumbottompadding">
    <div class="grid-x grid-margin-x grid-padding-x align-center">
        <div class="large-12 medium-12 small-12 cell">

            <h2>Pages</h2>
            <ul>
                <?php
                $args = array(
                    'title_li' => '',
                    'sort_column' => 'menu_order',
                    'walker' => new Aras\Sitemap_Walker()
                );
                wp_list_pages($args);
                ?>
            </ul>


            <h2>Resources</h2>
            <ul>
                <?php
                $args = array(
                    'title_li' => '',
                    'sort_column' => 'menu_order',
                    'post_type' => 'resource',
                    'walker' => new Aras\Sitemap_Walker()
                );
                wp_list_pages($args);
                ?>
            </ul>

            <h2>Blog</h2>
            <ul>
            <?php
                $args = array(
                    'title_li' => '',
                    'sort_column' => 'menu_order',
                    'post_type' => 'post',
                    'walker' => new Aras\Sitemap_Walker()
                );
                wp_list_pages($args);
                ?>
            </ul>

            <h2>News</h2>
            <ul>
            <?php
                $args = array(
                    'title_li' => '',
                    'sort_column' => 'menu_order',
                    'post_type' => 'news',
                    'walker' => new Aras\Sitemap_Walker()
                );
                wp_list_pages($args);
                ?>
            </ul>

            <h2>Documentation</h2>
            <ul>
            <?php
                $args = array(
                    'title_li' => '',
                    'sort_column' => 'menu_order',
                    'post_type' => 'documentation',
                    'walker' => new Aras\Sitemap_Walker()
                );
                wp_list_pages($args);
                ?>
            </ul>

        </div>
    </div>
</div>

<?php get_footer(); ?>