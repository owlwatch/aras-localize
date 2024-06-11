<main class="gated-top">
  <section id="gated-hero" class="gated-hero hero-banner">
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
            <?php if (get_field('hero_content')) : ?>
              <?php echo get_field('hero_content'); ?>
            <?php endif; ?>
            <?php if (get_field('event_date')) : ?><h6><?php if (!get_field('hide_date_on_listing')) : ?><?php echo get_field('event_date'); ?><?php endif; ?><?php if (get_field('event_time')) : ?><?php if (!get_field('hide_date_on_listing')) : ?>,<?php endif; ?> <?php echo get_field('event_time'); ?><?php endif; ?></h6><?php endif; ?>
          </div>
        </div>
        <div class="cell small-12 medium-6 large-5 hero-form-side">
          <div id="hero-form-container" class="hero-form-container">
            <?php if (get_field('form_shortcode')) : ?>
              <div class="hero-form">
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