<section class="recommended-posts bg-dblue mediumtoppadding mediumbottompadding">
  <div class="grid-container">
    <div class="grid-x grid-margin-x">
      <div class="cell small-12 ">
        <?php if (get_field('recommended_posts_headline', 'option')) : ?>
          <h2 class="recommended-title"><?php the_field('recommended_posts_headline', 'option'); ?></h2>
        <?php else : ?>
          <h2 class="recommended-title">More Blog Posts</h2>
        <?php endif; ?>
      </div>
      <?php
      $current_post_id = get_the_ID();
      $recentposts = new WP_Query(array(
        'posts_per_page' => 3,
        'post__not_in'   => array($current_post_id),
      ));
      ?>
      <?php if ($recentposts->have_posts()) : ?>
        <?php while ($recentposts->have_posts()) : $recentposts->the_post(); ?>

          <?php get_template_part('parts/loop-archive'); ?>

        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
      <?php endif; ?>
    </div>
  </div>
</section>