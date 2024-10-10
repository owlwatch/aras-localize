<?php $bgcolor = ''; ?>
<?php if (get_field('header_placement') == 'overlap') : ?>
  <?php $navformat = 'toppadded'; ?>
<?php else : ?>
  <?php $navformat = ''; ?>
<?php endif; ?>

<?php if (have_rows('hero_background')) : ?>
  <?php while (have_rows('hero_background')) : the_row(); ?>
    <?php if (get_sub_field('full_screen_background_options') == 'darkoverlay') : ?>
      <?php $bgcolor = 'bg-dblue'; ?>
    <?php elseif (get_sub_field('full_screen_background_options') == 'nooverlaywhite') : ?>
      <?php $bgcolor = 'bg-dblue'; ?>
    <?php else : ?>
      <?php $bgcolor = 'bg-norm'; ?>
    <?php endif; ?>
  <?php endwhile; ?>
<?php endif; ?>

<?php if (get_field('form_hero_style') == 'block') : ?>
  <main class="gated-top">
    <section id="gated-hero" class="gated-hero hero-banner <?php echo $bgcolor; ?> <? echo $navformat; ?>">
      <?php if (have_rows('hero_background')) : ?>
        <?php while (have_rows('hero_background')) : the_row(); ?>

          <?php if (get_sub_field('background_style') == 'customside') : ?>
            <?php $image = get_sub_field('background_visual'); ?>
            <div class="background-pattern-half" title="<?php echo esc_attr($image['alt']); ?>" style="background-image: url(<?php echo esc_url($image['url']); ?>);"></div>
          <?php elseif (get_sub_field('background_style') == 'customfull') : ?>
            <?php $image = get_sub_field('background_visual'); ?>
            <div class="background-pattern-full" title="<?php echo esc_attr($image['alt']); ?>" style="background-image: url(<?php echo esc_url($image['url']); ?>);"></div>
          <?php elseif (get_sub_field('background_style') == 'customvideo') : ?>
            <?php if (get_sub_field('background_vidyard_video_id')) : ?>
              <iframe class="vidyard-player-background" src="//play.vidyard.com/<?php echo get_sub_field('background_vidyard_video_id'); ?>/type/background" scrolling="no" frameborder="0" allowtransparency="true" allowfullscreen="true"></iframe>
            <?php endif; ?>
          <?php else : ?>
            <div class="background-pattern-half"></div>
          <?php endif; ?>

          <?php if (get_sub_field('full_screen_background_options') == 'darkoverlay') : ?>
            <div class="background-overlay-dark"></div>
          <?php elseif (get_sub_field('full_screen_background_options') == 'lightoverlay') : ?>
            <div class="background-overlay-light"></div>
          <?php endif; ?>

        <?php endwhile; ?>
      <?php endif; ?>
      <div class="grid-container">
        <div class="grid-x grid-padding-x align-middle">
          <div class="cell small-12 small-12 medium-6 large-7 hero-content">
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
              <?php if (get_field('hero_content')) : ?>
                <?php echo get_field('hero_content'); ?>
              <?php endif; ?>
              <?php if (get_field('event_date')) : ?><h6><?php if (!get_field('hide_date_on_listing')) : ?><?php echo get_field('event_date'); ?><?php endif; ?><?php if (get_field('event_time')) : ?><?php if (!get_field('hide_date_on_listing')) : ?>,<?php endif; ?> <?php echo get_field('event_time'); ?><?php endif; ?></h6><?php endif; ?>
            </div>
            <div class="intro-content content-section">
              <?php get_template_part('parts/_template_parts/intro_content_section'); ?>
            </div>
          </div>
          <div class="cell small-12 medium-6 large-5 ">
            <div id="hero-form-container" class="hero-form-container">
              <?php if (get_field('form_shortcode')) : ?>
                <div class="hero-form bg-white">
                  <?php if (get_field('form_headline')) : ?>
                    <h4 class="hero-form-headline"><?php echo get_field('form_headline')  ?></h4>
                  <?php endif; ?>
                  <?php $gravity_form_id = get_field('form_shortcode');
                  echo do_shortcode('[gravityform id="' . $gravity_form_id . '" title="false" description="false"]'); ?>
                </div>
                <?php get_template_part('parts/_template_parts/gform_variables'); ?>
              <?php endif; ?>
              <?php if (get_field('content_below_form')) : ?>
                <div class="hero-form-end">
                  <?php echo get_field('content_below_form'); ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
<?php else : ?>
  <main class="gated-top">

    <section id="gated-hero" class="gated-hero hero-banner <?php echo $bgcolor; ?> <? echo $navformat; ?>">
      <?php if (have_rows('hero_background')) : ?>
        <?php while (have_rows('hero_background')) : the_row(); ?>

          <?php if (get_sub_field('background_style') == 'customside') : ?>
            <?php $image = get_sub_field('background_visual'); ?>
            <div class="background-pattern-half" title="<?php echo esc_attr($image['alt']); ?>" style="background-image: url(<?php echo esc_url($image['url']); ?>);"></div>
          <?php elseif (get_sub_field('background_style') == 'customfull') : ?>
            <?php $image = get_sub_field('background_visual'); ?>
            <div class="background-pattern-full" title="<?php echo esc_attr($image['alt']); ?>" style="background-image: url(<?php echo esc_url($image['url']); ?>);"></div>
          <?php elseif (get_sub_field('background_style') == 'customvideo') : ?>
            <?php if (get_sub_field('background_vidyard_video_id')) : ?>
              <iframe class="vidyard-player-background" src="//play.vidyard.com/<?php echo get_sub_field('background_vidyard_video_id'); ?>/type/background" scrolling="no" frameborder="0" allowtransparency="true" allowfullscreen="true"></iframe>
            <?php endif; ?>
          <?php else : ?>
            <div class="background-pattern-half"></div>
          <?php endif; ?>

          <?php if (get_sub_field('full_screen_background_options') == 'darkoverlay') : ?>
            <div class="background-overlay-dark"></div>
          <?php elseif (get_sub_field('full_screen_background_options') == 'lightoverlay') : ?>
            <div class="background-overlay-light"></div>
          <?php endif; ?>

        <?php endwhile; ?>
      <?php endif; ?>
      <div class="grid-container">
        <div class="grid-x grid-padding-x align-top">
          <div class="cell small-12 small-12 medium-6 large-7 hero-content">
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
              <?php if (get_field('hero_content')) : ?>
                <?php echo get_field('hero_content'); ?>
              <?php endif; ?>
            </div>
          </div>
          <div class="cell small-12 medium-6 large-5 hero-form-side">
            <div id="hero-form-container" class="hero-form-container">
              <?php if (get_field('form_shortcode')) : ?>
                <div class="hero-form bg-white">
                  <?php if (get_field('form_headline')) : ?>
                    <h4 class="hero-form-headline"><?php echo get_field('form_headline')  ?></h4>
                  <?php endif; ?>
                  <?php $gravity_form_id = get_field('form_shortcode');
                  echo do_shortcode('[gravityform id="' . $gravity_form_id . '" title="false" description="false"]'); ?>
                </div>
                <?php get_template_part('parts/_template_parts/gform_variables'); ?>
              <?php endif; ?>
              <?php if (get_field('content_below_form')) : ?>
                <div class="hero-form-end bg-transparent">
                  <?php echo get_field('content_below_form'); ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section id="gated-intro" class="gated-intro mediumtoppadding mediumbottompadding bg-white">
      <div class="grid-container">
        <div class="grid-x grid-padding-x">
          <div class="cell small-12 medium-6 large-7 intro-content content-section">
            <?php get_template_part('parts/_template_parts/intro_content_section'); ?>
          </div>
        </div>
      </div>
    </section>
  </main>




  <script>
    jQuery(document).ready(function() {
      function adjustMinHeight() {
        var heroHeight = jQuery('#gated-hero').outerHeight();
        var heroFormHeight = jQuery('#hero-form-container').outerHeight();
        var heroPaddingTop = parseInt(jQuery('#gated-hero').css('padding-top'));

        var minHeight = heroFormHeight - heroHeight + heroPaddingTop;

        if (jQuery(window).width() <= 639) {
          jQuery('#gated-intro').css('min-height', 'auto');
        } else {
          jQuery('#gated-intro').css('min-height', minHeight + 'px');
        }
      }

      // Function to observe changes in #hero-form-container height
      function observeHeroFormHeightChanges() {
        var heroFormContainer = document.getElementById('hero-form-container');

        var observer = new MutationObserver(function(mutationsList, observer) {
          // When height changes observed, adjustMinHeight
          adjustMinHeight();
        });

        // Options for the observer (which mutations to observe)
        var config = {
          attributes: true,
          childList: true,
          subtree: true
        };

        // Start observing the target node for configured mutations
        observer.observe(heroFormContainer, config);
      }

      // Call the function to start observing height changes
      observeHeroFormHeightChanges();

      // Call adjustMinHeight initially
      adjustMinHeight();

      // Re-adjust minimum height when window is resized
      jQuery(window).resize(function() {
        adjustMinHeight();
      });
    });
  </script>
<?php endif; ?>