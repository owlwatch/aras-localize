<?php

/**
 * Template Name: Gated Page
 */
get_header();
$current_post_id = get_the_ID(); ?>

<?php if (get_field('post_submission_action') == 'update') : ?>

  <?php if (isset($_GET['id']) && intval($_GET['id']) === $current_post_id) : /* Checks if submission URL has ID in it */ ?>
    <section class="gated-hero hero-banner">
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
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="post-submission-content mediumtoppadding largebottompadding box-bg-white">
      <div class="grid-container">
        <div class="grid-x grid-padding-x">
          <div class="cell small-12">
            <?php if (get_field('post_submission_content')) : ?>
              <?php echo get_field('post_submission_content'); ?>
            <?php endif; ?>
            <?php $link = get_field('post_submission_button');
            if ($link) : $link_url = $link['url'];
              $link_title = $link['title'];
              $link_target = $link['target'] ? $link['target'] : '_self';
            ?>
              <a aria-label="<?php echo esc_html($link_title); ?>" class="aras-button" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
                <?php echo esc_html($link_title); ?>
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
  <?php else : /* if not submitted */  ?>
    <?php get_template_part('parts/_template_parts/hero_banner_gated'); ?>
    <?php if (have_posts()) :
      while (have_posts()) : the_post(); ?>
        <?php get_template_part('parts/_flexible_content/_flexible_content'); ?>
    <?php endwhile;
    endif; ?>
  <?php endif; ?>

<?php elseif (get_field('post_submission_action') == 'redirect') : ?>
  <?php if ($form_submitted || isset($_GET['pi_list_email'])) : ?>
    <?php if (get_field('post-submission_redirect_url')) : ?>
      <?php $redirect_url = get_field('post-submission_redirect_url'); ?>
      <?php header("Location: $redirect_url"); ?>
    <?php endif; ?>
  <?php else : /* if not submitted */ ?>
    <?php get_template_part('parts/_template_parts/hero_banner_gated'); ?>
    <?php if (have_posts()) :
      while (have_posts()) : the_post(); ?>
        <?php get_template_part('parts/_flexible_content/_flexible_content'); ?>
    <?php endwhile;
    endif; ?>

  <?php endif; ?>

<?php else : /* post_submission_action is default */ ?>
  <?php get_template_part('parts/_template_parts/hero_banner_gated'); ?>
  <?php if (have_posts()) :
    while (have_posts()) : the_post(); ?>
      <?php get_template_part('parts/_flexible_content/_flexible_content'); ?>
  <?php endwhile;
  endif; ?>
<?php endif; ?>

<?php get_template_part('parts/_template_parts/footer_cta'); ?>


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
<?php get_footer(); ?>