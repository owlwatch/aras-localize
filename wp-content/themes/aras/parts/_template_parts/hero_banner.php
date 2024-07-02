<?php if (get_field('hero_image')) {
  $heropadding = 'img-hero-banner';
  $heroheight = 'hero-height-standard';
  $herocontent = 'small-12 medium-9 large-7';
} else {
  $heropadding = '';
  $heroheight = 'hero-height-shortened';
  $herocontent = 'small-12 medium-11';
}
?>
<section class="hero-banner <?= "$heropadding" ?>" id="page-intro">
  <div class="grid-container">
    <div class="<?= "$heroheight" ?> grid-x grid-padding-x align-middle">
      <div class="cell <?= "$herocontent" ?> hero-content">
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
            <div role="heading" role="heading" aria-level="2">
              <?php echo get_field('hero_content'); ?>
            </div>
          <?php endif; ?>
          <?php $link = get_field('hero_button');
          if ($link) : $link_url = $link['url'];
            $link_title = $link['title'];
            $link_target = $link['target'] ? $link['target'] : '_self';
          ?>
            <a aria-label="<?php echo esc_attr($link_title); ?>" class="aras-button" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
              <?php echo esc_html($link_title); ?>
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <?/*
  <?php if (get_field('hero_image')) : ?>
    <?php $image = get_field('hero_image'); ?>
    <?php if (!empty($image)) : ?>
      <div class="hero-background-overlay"></div>
      <div class="hero-background-image" style="background-image:url(<?php echo esc_url($image['url']); ?>);" title="<?php echo esc_attr($image['alt']); ?>"></div>
    <?php endif; ?>
  <?php endif; ?>
  */ ?>
</section>