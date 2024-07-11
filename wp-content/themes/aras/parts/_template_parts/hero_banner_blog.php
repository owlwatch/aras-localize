<section id="blog-hero" class="blog-hero hero-banner">
  <div class="grid-container">
    <div class="grid-x grid-padding-x align-top">
      <div class="cell small-12 medium-6 large-7 hero-content">
        <div class="hero-content-inner">
          <?php if (get_field('headline_color') == 'red') : ?>
            <?php $h1color = 'color-red'; ?>
          <?php else : ?>
            <?php $h1color = ''; ?>
          <?php endif; ?>
          <?php if (get_field('hero_headline')) : ?>
            <h1 class="hero-headline <?php echo $h1color; ?>"><?php echo get_field('hero_headline'); ?></h1>
          <?php else : ?>
            <h1 class="hero-headline <?php echo $h1color; ?>"><?php the_title(''); ?></h1>
          <?php endif; ?>
          <h6 class="byline">
            <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo get_the_time('F j, Y'); ?>
          </h6>
        </div>
      </div>
      <div class="cell small-12 medium-6 large-5 hero-image-side">
        <?php //the_post_thumbnail('full');
        $image_id = get_post_thumbnail_id();
        $image_url = wp_get_attachment_image_src($image_id, 'full'); // 'full' size image
        if (get_post_meta($image_id, '_wp_attachment_image_alt', true) != '') {
          $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        } else {
          $image_alt = get_the_title('');
        }
        $image_data = wp_get_attachment_metadata($image_id);
        $image_width = $image_data['width'];
        $image_height = $image_data['height'];
        $image_loading = 'lazy';
        $image_decoding = 'async';
        $image_srcset = wp_get_attachment_image_srcset($image_id, 'full'); // 'full' size image srcset
        ?>
        <img src="<?php echo $image_url[0]; ?>" srcset="<?php echo esc_attr($image_srcset); ?>" alt="<?php echo $image_alt; ?>" width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" loading="<?php echo $image_loading; ?>" decoding="<?php echo $image_decoding; ?>" />
      </div>
    </div>
  </div>
</section>
<section class="blog-share smalltoppadding smallbottompadding">
  <div class="grid-container">
    <div class="grid-x grid-padding-x align-middle">
      <div class="cell small-12 medium-6 large-7 share-links">
        <h6>Share this post:</h6>
        <a aria-label="Share on LinkedIn" class="share-link" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo get_permalink(); ?>" target="_blank">
          <img class="share-icon" src="<?php echo get_template_directory_uri(); ?>/assets/images/social/social-linkedin.svg" alt="Share on LinkedIn" />
        </a>
        <a aria-label="Share on Twitter" class="share-link" href="http://www.twitter.com/share?url=<?php echo get_permalink(); ?>" target="_blank">
          <img class="share-icon" src="<?php echo get_template_directory_uri(); ?>/assets/images/social/social-x.svg" alt="Share on Twitter" />
        </a>
        <a aria-label="Share on Facebook" class="share-link" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo get_permalink(); ?>" target="_blank">
          <img class="share-icon" src="<?php echo get_template_directory_uri(); ?>/assets/images/social/social-facebook.svg" alt="Share on FaceBook" />
        </a>
      </div>
    </div>
  </div>
</section>