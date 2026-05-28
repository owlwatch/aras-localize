<?php

/**
 * Template Name: Sitemap
 */
get_header(); ?>

<?php get_template_part('parts/_template_parts/hero_banner'); ?>

<div class="grid-container mediumtoppadding mediumbottompadding">
    <div class="grid-x grid-margin-x grid-padding-x align-center">
        <div class="large-12 medium-12 small-12 cell">

            <h2><?php esc_html_e('Pages', 'aras'); ?></h2>
            <ul>
                <?php
                $args = array(
                    'title_li' => '',
                    'sort_column' => 'menu_order',
                    'walker' => new Aras\Sitemap_Walker()
                );
                wp_list_pages($args);
                $resources_link = home_url('/resources/');
                ?>
                <li>
                    <a href="<?php echo $resources_link ?>">
                        Resources - Reports, Demos, & More
                    </a>
                </li>
                <li>
                    <a href="<?php echo add_query_arg( 'format', 'demo-series', $resources_link) ?>">
                        Demo Series Resources
                    </a>
                </li>
                <li>
                    <a href="<?php echo add_query_arg( 'format', 'customer-story', $resources_link) ?>">
                        Customer Stories &amp; Case Studies
                    </a>
                </li>
                <li>
                    <a href="<?php echo get_post_type_archive_link('event') ?>">
                        Events &amp; Webinars
                    </a>
                </li>

                <li>
                    <a href="<?php echo get_post_type_archive_link('news') ?>">
                        News
                    </a>
                </li>

                <li>
                    <a href="<?php echo get_post_type_archive_link('glossary') ?>">
                        Glossary
                    </a>
                </li>

                <li>
                    <a href="<?php echo get_post_type_archive_link('partners') ?>">
                        Find a Partner
                    </a>
                </li>
            </ul>


            <h2><?php esc_html_e('Resources', 'aras'); ?></h2>
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

            <h2><?php esc_html_e('Blog', 'aras'); ?></h2>
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

            <h2><?php esc_html_e('News', 'aras'); ?></h2>
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

            <h2><?php esc_html_e('Documentation', 'aras'); ?></h2>
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