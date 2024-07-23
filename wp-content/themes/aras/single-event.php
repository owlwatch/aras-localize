<?php
/*
Template Post Type: event
*/
get_header();
if (get_field('external_url')) {
  global $post; // < -- globalize, just in case
  wp_redirect(esc_url(get_field('external_url')), 301);
}
$current_post_id = get_the_ID();
?>

<?php if (get_field('post_submission_action') == 'update') : ?>
  <?php if (isset($_GET['id']) && intval($_GET['id']) === $current_post_id) : /* Checks if submission URL has ID in it */ ?>
    <?php get_template_part('parts/_template_parts/hero_banner_gated_ungated'); ?>
  <?php else : //not submitted
  ?>
    <?php get_template_part('parts/_template_parts/hero_banner_gated'); ?>

    <?php if (have_posts()) :
      while (have_posts()) : the_post(); ?>
        <?php get_template_part('parts/_flexible_content/_flexible_content'); ?>
    <?php endwhile;
    endif; ?>
  <?php endif; ?>

<?php elseif (get_field('post_submission_action') == 'redirect') : ?>
  <?php if (isset($_GET['id']) && intval($_GET['id']) === $current_post_id) : /* Checks if submission URL has ID in it */ ?>
    <?php if (get_field('post-submission_redirect_url')) : ?>
      <?php $redirect_url = get_field('post-submission_redirect_url'); ?>
      <?php header("Location: $redirect_url"); ?>
    <?php endif; ?>
  <?php endif; ?>
<?php else : //post_submission_action != update
?>
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