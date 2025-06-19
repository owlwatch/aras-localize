<section id="page-intro" class="home-hero-banner">
  <div class="grid-container">
    <div class="grid-x grid-padding-x align-middle">
      <?php if (have_rows('home_hero_content_groups')) : ?>
        <div class="cell small-12 medium-8 large-6 hero-content">
          <?php $row_count = count(get_field('home_hero_content_groups')); ?>
          <?php if ($row_count != 1) : ?>
            <?php if (get_field('include_navigation_squares')) : ?>
              <div class="scroll-dots-homenav"></div>
            <?php endif; ?>
          <?php endif; ?>
          <div class="hero-content-inner">
            <?php while (have_rows('home_hero_content_groups')) : the_row(); ?>
              <?php $rownum = get_row_index(); ?>
              <div>
                <?php if (get_sub_field('hero_headline')) : ?>
                  <?php if ($rownum == '1') : ?>
                    <h1 class="home-hero-headline"><?php echo get_sub_field('hero_headline'); ?></h1>
                  <?php else : ?>
                    <h2 class="home-hero-headline"><?php echo get_sub_field('hero_headline'); ?></h2>
                  <?php endif; ?>
                <?php else : ?>
                  <?php if ($rownum == '1') : ?>
                    <h1 class="home-hero-headline"><?php the_title(''); ?></h1>
                  <?php else : ?>
                    <h2 class="home-hero-headline"><?php the_title(''); ?></h2>
                  <?php endif; ?>
                <?php endif; ?>
                <?php if (get_sub_field('hero_content')) : ?>
                  <div role="heading" role="heading" aria-level="2">
                    <?php echo get_sub_field('hero_content'); ?>
                  </div>
                <?php endif; ?>
                <?php $link = get_sub_field('hero_button');
                if ($link) : $link_url = $link['url'];
                  $link_title = $link['title'];
                  $link_target = $link['target'] ? $link['target'] : '_self';
                ?>
                  <a aria-label="<?php echo esc_attr($link_title); ?>" class="aras-button" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
                    <?php echo esc_html($link_title); ?>
                  </a>
                <?php endif; ?>
              </div>
            <?php endwhile; ?>
          </div>
        </div>
      <?php endif; ?>

      <div class="cell small-12 medium-4 large-6 hero-visual">
        <?php if (get_field('visual_type') == 'video') : ?>
          <?php
          $video = get_field('hero_video');

          if (empty($video) || !$video) {
            $url = get_template_directory_uri() . '/assets/video/aras-home-animation.mp4';
          }
          else {
            $url = $video['url'];
          }
          error_log('Hero video URL: ' . $url);
          
          ?>
          <!-- hero_video: <?php print_r( $video ); ?> -->
          <video playsinline muted autoplay loop src="<?php echo $url; ?>"></video>
        <?php else : ?>
          <?php $image = get_field('hero_image');
          if (!empty($image)) : ?>
            <img class="hero-visual-image" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php if ($row_count != 1) : ?>
  <?php if (get_field('autoplay_speed')) {
    $autoplay_speed =  get_sub_field('autoplay_speed');
    if ($autoplay_speed == '0') {
      $speed = 0;
      $autoplay = 'false';
    } else {
      $speed = $autoplay_speed * 1000;
      $autoplay = 'true';
    }
  } else {
    $speed = '5000';
    $autoplay = 'true';
  } ?>

  <script>
    jQuery(document).ready(function() {
      jQuery('.hero-content-inner').slick({
        <?php if (get_field('include_navigation_squares')) : ?>
          appendDots: jQuery('.scroll-dots-homenav'),
          dots: true,
        <?php else : ?>
          dots: false,
        <?php endif; ?>
        autoplay: <?php echo $autoplay; ?>,
        autoplaySpeed: <?php echo $speed; ?>,
        arrows: false,
        cssEase: 'linear',
        draggable: true,
        fade: true,
        infinite: true,
        pauseOnHover: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        speed: 500,
        swipeToSlide: true,
      });
    });
  </script>
<?php endif; ?>