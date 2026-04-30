<?php
get_header(); ?>
<?php $default_post_archive_url = get_permalink(get_option('page_for_posts')); ?>

<section class="partners-hero-banner mediumtoppadding mediumbottompadding bg-dblue">
  <div class="grid-container">
    <div class="grid-x grid-padding-x">
      <div class="cell small-12 medium-8 large-6 hero-content">
        <?php if (get_field('academic_users_archive_headline', 'option')) : ?>
          <h1 class="hero-headline"><?php echo get_field('academic_users_archive_headline', 'option'); ?></h1>
        <?php else : ?>
          <h1 class="hero-headline"><?php esc_html_e('Academic Users', 'aras'); ?></h1>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<main class="acedemic-users-archive mediumtoppadding largebottompadding bg-white" role="main">
  <div class="grid-container">
    <section class="grid-x align-center align-middle partner-post-loop">
      <?php if (get_field('academic_users_archive_introduction', 'option')) : ?>
        <div class="cell small-12 academic-user-intro-content mediumbottompadding">
          <?php echo get_field('academic_users_archive_introduction', 'option'); ?>
        </div>
      <?php endif; ?>
      <?php
      $args = array(
        'post_type' => 'sf-academic-users',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
      );
      $academic_users_query = new WP_Query($args);
      if ($academic_users_query->have_posts()) : $postCount = 0;
        while ($academic_users_query->have_posts()) : $academic_users_query->the_post();
          $postCount++; ?>

          <div class="cell academic-users-item">
            <?php if (get_field('academic_logo__c')) :
              $thumbnail_url = get_field('academic_logo__c');
              $thumbnail_url_test = str_replace(
                'https://www.aras.com/wp-content/',
                'https://aras1.wpenginepowered.com/wp-content/',
                $thumbnail_url
              );
              $url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            ?>

              <?php if (get_field('academic_website_link__c')) : ?>
                <a aria-label="<?php the_title() ?>" class="academic-users-logo" href="<?php echo get_field('academic_website_link__c'); ?>" target="_blank">
                  <?php if (strpos($url, "aras1.wpenginepowered.com") !== false) { ?>
                    <img src="<?php echo $thumbnail_url_test; ?>" alt="<?php the_title(''); ?>" title="<?php the_title(''); ?>" />
                  <?php } else { ?>
                    <img src="<?php echo $thumbnail_url; ?>" alt="<?php the_title(''); ?>" title="<?php the_title(''); ?>" />
                  <?php } ?>
                </a>
              <?php else : ?>
                <div class="academic-users-logo">
                  <img src="<?php echo $thumbnail_url; ?>" alt="<?php the_title(''); ?>" title="<?php the_title(''); ?>" />
                </div>
              <?php endif; ?>
            <?php else : ?>
              <div class="academic-users-logo"></div>
            <?php endif; ?>

            <div class="academic-users-content">
              <?php if (get_field('academic_website_link__c')) : ?>
                <h4>
                  <a aria-label="<?php the_title() ?>" href="<?php echo get_field('academic_website_link__c'); ?>" target="_blank">
                    <?php the_title(''); ?>
                  </a>
                </h4>
              <?php else : ?>
                <h4><?php the_title(''); ?></h4>
              <?php endif; ?>
              <?php if (get_field('academic_description_for_website__c')) : ?>
                <?php echo get_field('academic_description_for_website__c'); ?>
              <?php endif; ?>
            </div>
          </div>

        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
      <?php else : ?>
      <?php endif; ?>
    </section>
  </div>
</main>

<?php get_template_part('parts/_template_parts/footer_cta_academic_users'); ?>
<?php get_footer(); ?>