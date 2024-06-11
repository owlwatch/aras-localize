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
                    'sort_column' => 'menu_order'
                );
                wp_list_pages($args);
                ?>
            </ul>


            <h2>Resources</h2>
            <ul>
                <?php $resource_posts = new WP_Query(
                    array(
                        'post_type' => 'resource',
                        'posts_per_page' => -1
                    )
                ); ?>
                <?php while ($resource_posts->have_posts()) : $resource_posts->the_post(); ?>
                    <li>
                        <?php the_title('<a aria-label="' . the_title_attribute('echo=0') . '"  href="' . get_permalink() . '" title="' . the_title_attribute('echo=0') . '" rel="bookmark">', '</a>'); ?>
                    </li>
                <?php endwhile; ?>
            </ul>

            <h2>Blog</h2>
            <ul>
                <?php $blog_posts = new WP_Query(
                    array(
                        'posts_per_page' => -1
                    )
                ); ?>
                <?php while ($blog_posts->have_posts()) : $blog_posts->the_post(); ?>
                    <li>
                        <?php the_title('<a aria-label="' . the_title_attribute('echo=0') . '" href="' . get_permalink() . '" title="' . the_title_attribute('echo=0') . '" rel="bookmark">', '</a>'); ?>
                    </li>
                <?php endwhile; ?>
            </ul>

            <h2>News</h2>
            <ul>
                <?php $news_posts = new WP_Query(
                    array(
                        'post_type' => 'news_type',
                        'posts_per_page' => -1
                    )
                ); ?>
                <?php while ($news_posts->have_posts()) : $news_posts->the_post(); ?>
                    <li>
                        <?php the_title('<a aria-label="' . the_title_attribute('echo=0') . '" href="' . get_permalink() . '" title="' . the_title_attribute('echo=0') . '" rel="bookmark">', '</a>'); ?>
                    </li>
                <?php endwhile; ?>
            </ul>

            <h2>Events</h2>
            <!--<ul>
            <?php $news_posts = new WP_Query(
                array(
                    'post_type' => 'news_type',
                    'posts_per_page' => -1
                )
            ); ?>
                <?php while ($news_posts->have_posts()) : $news_posts->the_post(); ?>
                    <li>
                        <?php the_title('<a aria-label="' . the_title_attribute('echo=0') . '" href="' . get_permalink() . '" title="' . the_title_attribute('echo=0') . '" rel="bookmark">', '</a>'); ?>
                    </li>
                <?php endwhile; ?>
            </ul>-->

            <h2>Partners</h2>
            <!--<ul>
            <?php $news_posts = new WP_Query(
                array(
                    'post_type' => 'news_type',
                    'posts_per_page' => -1
                )
            ); ?>
                <?php while ($news_posts->have_posts()) : $news_posts->the_post(); ?>
                    <li>
                        <?php the_title('<a aria-label="' . the_title_attribute('echo=0') . '" href="' . get_permalink() . '" title="' . the_title_attribute('echo=0') . '" rel="bookmark">', '</a>'); ?>
                    </li>
                <?php endwhile; ?>
            </ul>-->

            <h2>Documentation</h2>
            <ul>
                <?php $documentation_posts = new WP_Query(
                    array(
                        'post_type' => 'documentation',
                        'posts_per_page' => -1
                    )
                ); ?>
                <?php while ($documentation_posts->have_posts()) : $documentation_posts->the_post(); ?>
                    <li>
                        <?php the_title('<a aria-label="' . the_title_attribute('echo=0') . '" href="' . get_permalink() . '" title="' . the_title_attribute('echo=0') . '" rel="bookmark">', '</a>'); ?>
                    </li>
                <?php endwhile; ?>
            </ul>

        </div>
    </div>
</div>

<?php get_footer(); ?>