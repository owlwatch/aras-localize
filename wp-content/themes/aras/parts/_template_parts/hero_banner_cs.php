<?php $post_id = get_the_ID(); ?>

<section id="cs-hero" class="cs-hero hero-banner">
  <div class="grid-container">
    <div class="grid-x grid-padding-x align-top">
      <div class="cell small-12 medium-6 large-7 hero-content">
        <div class="hero-content-inner">
          <?php if (get_field('headline_color') == 'red') : ?>
            <?php $h1color = 'color-red'; ?>
          <?php else : ?>
            <?php $h1color = ''; ?>
          <?php endif; ?>

          <?php if (get_field('hero_subhead')) : ?>
            <h6><?php echo get_field('hero_subhead'); ?></h6>
          <?php else : ?>
          <?php endif; ?>


          <?php if (get_field('hero_headline')) : ?>
            <h1 class="hero-headline <?php echo $h1color; ?>"><?php echo get_field('hero_headline'); ?></h1>
          <?php else : ?>
            <h1 class="hero-headline <?php echo $h1color; ?>"><?php the_title(''); ?></h1>
          <?php endif; ?>
          <?php if (get_field('hero_content')) : ?>
            <?php echo get_field('hero_content'); ?>
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
      <div class="cell small-12 medium-6 large-5 hero-side">
        <div id="hero-side-container" class="hero-side-container">
          <?php $image = get_field('company_logo');
          if (!empty($image)) : ?>
            <div class="hero-side-img">
              <img class="" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>">
            </div>
          <?php endif; ?>
          <?php if (have_rows('customer_story_details')) : ?>
            <?php while (have_rows('customer_story_details')) : the_row(); ?>
              <div class="side-cat">
                <?php if (get_sub_field('detail_headline')) : ?>
                  <h6><?php echo get_sub_field('detail_headline'); ?></h6>
                <?php endif; ?>
                <?php if (get_sub_field('detail_content')) : ?>
                  <?php echo get_sub_field('detail_content'); ?>
              </div>
            <?php endif; ?>
          <?php endwhile; ?>
        <?php endif; ?>
        <?/*
        <?php $terms = get_the_terms($post_id, 'industry');
        if ($terms && !is_wp_error($terms)) : ?>
          <div class="side-cat">
            <h6>Industries</h6>
            <p>
              <?php
              $term_names = array();
              foreach ($terms as $term) {
                $term_names[] = $term->name;
              }
              echo implode(', ', $term_names);
              ?>
            </p>
          </div>
        <?php endif; ?>
        <?php $terms = get_the_terms($post_id, 'location');
        if ($terms && !is_wp_error($terms)) : ?>
          <div class="side-cat">
            <h6>Locations</h6>
            <p>
              <?php
              $term_names = array();
              foreach ($terms as $term) {
                $term_names[] = $term->name;
              }
              echo implode(', ', $term_names);
              ?>
            </p>
          </div>
        <?php endif; ?>
        */ ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php if (have_rows('overview_section')) : ?>
  <?php $modnum = get_row_index(); ?>
  <?php if (get_sub_field('anchor_link')) : ?>
    <?php $anchor = ('id="' . get_sub_field('anchor_link') . '"'); ?>
  <?php else : ?>
    <?php $anchor = ('id="stats-section-' . $modnum . '"'); ?>
  <?php endif; ?>
  <?php if (get_sub_field('items_per_row') == 'two') : ?>
    <?php $itemsperrow = 'small-12 medium-6 large-6'; ?>
  <?php elseif (get_sub_field('items_per_row') == 'three') : ?>
    <?php $itemsperrow = 'small-12 medium-4 large-4'; ?>
  <?php elseif (get_sub_field('items_per_row') == 'four') : ?>
    <?php $itemsperrow = 'small-12 medium-6 large-3'; ?>
  <?php else : ?>
    <?php $itemsperrow = 'small-12 medium-4 large-4'; ?>
  <?php endif; ?>
  <?php if (get_sub_field('item_alignment') == 'left') : ?>
    <?php $itemalignment = ''; ?>
  <?php elseif (get_sub_field('item_alignment') == 'center') : ?>
    <?php $itemalignment = 'align-center'; ?>
  <?php else : ?>
    <?php $itemalignment = ''; ?>
  <?php endif; ?>
  <?php while (have_rows('overview_section')) : the_row(); ?>
    <?php if (have_rows('content_items')) : ?>
      <section class="cs-overview-section bg-dblue mediumtoppadding mediumbottompadding" <?= "$anchor" ?>>
        <div class="grid-container" style="padding-top: 1rem;">
          <div class="grid-x grid-padding-x <?= "$itemalignment" ?>">
            <?php while (have_rows('content_items')) : the_row(); ?>
              <div class="cell <?= "$itemsperrow" ?> cs-overview-content wysiwyg-content">
                <?php if (get_sub_field('item_headline')) : ?>
                  <h4 class="item-headline"><?php echo get_sub_field('item_headline'); ?></h4>
                <?php else : ?>
                <?php endif; ?>
                <?php if (get_sub_field('item_content')) : ?>
                  <?php echo get_sub_field('item_content'); ?>
                <?php else : ?>
                <?php endif; ?>
              </div>
            <?php endwhile; ?>
          </div>
      </section>
    <?php endif; ?>
  <?php endwhile; ?>
<?php endif; ?>