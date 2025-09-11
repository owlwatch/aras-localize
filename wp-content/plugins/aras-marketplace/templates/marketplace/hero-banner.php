<?php if (get_field('marketplace_hero_image', 'option')) {
  $heropadding = 'img-hero-banner';
  $heroheight = 'hero-height-standard';
  $herocontent = 'small-12 medium-9 large-7';
} else {
  $heropadding = '';
  $heroheight = 'hero-height-shortened';
  $herocontent = 'small-12 medium-11';
}

// bail if we don't have the necessary content
if (!get_field('marketplace_hero_headline', 'option') && !get_field('marketplace_hero_content', 'option')) {
  return;
}
?>

<section class="hero-banner <?= "$heropadding" ?>" id="page-intro">
  <div class="grid-container">
    <div class="<?= "$heroheight" ?> grid-x grid-padding-x align-middle">
      <div class="cell <?= "$herocontent" ?> hero-content">
        <div class="hero-content-inner">

          <?php if (get_field('marketplace_headline_color', 'option') == 'red') : ?>
            <?php $h1color = 'color-red'; ?>
          <?php else : ?>
            <?php $h1color = ''; ?>
          <?php endif; ?>

          <?php if (get_field('marketplace_hero_headline', 'option')) : ?>
            <h1 class="hero-headline <?php echo $h1color; ?>"><?php echo get_field('marketplace_hero_headline', 'option'); ?></h1>
          <?php endif; ?>
          
          <?php if (get_field('marketplace_hero_content', 'option')) : ?>
            <div role="heading" role="heading" aria-level="2">
              <?php
              $hero_content = get_field('marketplace_hero_content', 'option');
              $trimmed_content = trim($hero_content);

              // Define the tags to check
              $tags = ['<p>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>', '<ol>', '<ul>'];
              $starts_with_tag = false;

              // Check if the content starts with any of the defined tags
              foreach ($tags as $tag) {
                if (strpos($trimmed_content, $tag) === 0) {
                  $starts_with_tag = true;
                  break;
                }
              }
              if ($starts_with_tag) {
                echo $hero_content;
              } else {
                echo '<p>' . $hero_content . '</p>';
              }
              ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <?php $image = get_field('marketplace_hero_image', 'option');  ?>
  <?php if (!empty($image)) : ?>
    <div class="hero-background-overlay"></div>
    <div class="hero-background-image" style="background-image:url(<?php echo wp_get_attachment_image_url($image, 'full'); ?>);" title="<?php echo wp_get_attachment_image($image, 'alt'); ?>"></div>
  <?php endif; ?>
</section>