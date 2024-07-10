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