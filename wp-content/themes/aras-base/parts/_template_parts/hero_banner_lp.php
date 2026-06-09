<?php if (get_field('header_type') == 'global') : ?>
  <?php $positionbuffer = 'globalbuffer'; ?>
<?php else : ?>
  <?php if (get_field('header_placement') == 'overlap') : ?>
    <?php $positionbuffer = ''; ?>
  <?php else : ?>
    <?php $positionbuffer = 'shortbuffer'; ?>
  <?php endif; ?>
<?php endif; ?>

<?php if (get_field('header_placement') == 'overlap') : ?>
  <?php $navformat = 'toppadded'; ?>
<?php else : ?>
  <?php $navformat = ''; ?>
<?php endif; ?>

<?php if (get_field('hero_height') == 'partial') : ?>
  <?php $minheight = 'hb-partial-fullscreen'; ?>
<?php elseif (get_field('hero_height') == 'full') : ?>
  <?php $minheight = 'hb-fullscreen'; ?>
<?php else : ?>
  <?php $minheight = ''; ?>
<?php endif; ?>

<?php $home_font_styling = get_field('enable_home_page_font_styling') ? ' home-page-font-styling' : ''; ?>


    <?php if (get_field('hero_background_background_style') != 'solid') : ?>
      <?php if (get_field('hero_background_background_options') == 'darkoverlay') : ?>
        <?php $bg_color = 'bg-dblue'; ?>
      <?php elseif (get_field('hero_background_background_options') == 'nooverlaywhite') : ?>
        <?php $bg_color = 'bg-dblue'; ?>
      <?php else : ?>
        <?php $bg_color = 'bg-norm'; ?>
      <?php endif; ?>
    <?php else : ?>
      <?php
      $background_color = get_field('hero_background_background_color');
      $bg_color = '';
      switch ($background_color) {
        case 'transparent':
          $bg_color = 'bg-transparent';
          break;
        case 'white':
          $bg_color = 'bg-white';
          break;
        case 'grey':
          $bg_color = 'bg-grey';
          break;
        case 'dblue':
          $bg_color = 'bg-dblue';
          break;
        case 'whitetogrey':
          $bg_color = 'bg-whitetogrey';
          break;
        case 'greytowarm':
          $bg_color = 'bg-greytowarm';
          break;
        default:
          $bg_color = 'bg-white';
      }
      ?>
    <?php endif; ?>
<section class="hero-banner lp-hero <? echo $minheight; ?> <? echo $positionbuffer; ?> <? echo $bg_color; ?> <? echo $navformat; ?><?php echo $home_font_styling; ?>" id="page-intro">

      <?php if (get_field('hero_background_background_style') == 'image') : ?>


          
            <?php $bg_placement = get_field('hero_background_background_image_background_image_position');
            switch ($bg_placement) {
              case 'topleft':
                $bgp = 'background-position: top left';
                break;
              case 'topcenter':
                $bgp = 'background-position: top center';
                break;
              case 'topright':
                $bgp = 'background-position: top right';
                break;
              case 'middleleft':
                $bgp = 'background-position: center left';
                break;
              case 'middlecenter':
                $bgp = 'background-position: center center';
                break;
              case 'middleright':
                $bgp = 'background-position: center right';
                break;
              case 'bottomleft':
                $bgp = 'background-position: bottom left';
                break;
              case 'bottomcenter':
                $bgp = 'background-position: bottom center';
                break;
              case 'bottomright':
                $bgp = 'background-position: bottom right';
                break;
              default:
                $bgp = 'background-position: top left';
            }
            ?>
            <?php $image = get_field('hero_background_background_image_image'); ?>
            <div class="background-pattern-full" title="<?php echo esc_attr($image['alt']); ?>" style="background-image: url(<?php echo esc_url($image['url']); ?>); <?php echo $bgp; ?>">
            </div>
      <?php elseif (get_field('hero_background_background_style') == 'video') : ?>
        <?php if (get_field('hero_background_background_vidyard_video_id')) : ?>
          <iframe class="vidyard-player-background" src="//play.vidyard.com/<?php echo get_field('hero_background_background_vidyard_video_id'); ?>/type/background" scrolling="no" frameborder="0" allowtransparency="true" allowfullscreen="true"></iframe>
        <?php endif; ?>
      <?php endif; ?>
      <?php if (get_field('hero_background_background_style') != 'solid') : ?>
        <?php if (get_field('hero_background_background_options') == 'darkoverlay') : ?>
          <div class="background-overlay-dark"></div>
        <?php elseif (get_field('hero_background_background_options') == 'lightoverlay') : ?>
          <div class="background-overlay-light"></div>
        <?php endif; ?>
      <?php endif; ?>




  <div class="grid-container">
    <div class="grid-x grid-padding-x align-middle">

      <?php if (get_field('visual_style') != 'none') :
        //VISUAL: IMAGE OR VIDEO
      ?>

        <?php $visual_column_split = get_field('visual_column_split') ?: 'half_half'; ?>
        <?php if ($visual_column_split == 'two_thirds_one_third') : ?>
          <?php $content_style = 'small-12 medium-8 large-8 hero-content'; ?>
          <?php $image_visual_style = 'small-12 medium-4 large-4 image-block'; ?>
          <?php $video_visual_style = 'small-12 medium-4 large-4 video-block'; ?>
        <?php else : ?>
          <?php $content_style = 'small-12 medium-6 large-6 hero-content'; ?>
          <?php $image_visual_style = 'small-12 medium-6 large-6 image-block'; ?>
          <?php $video_visual_style = 'small-12 medium-6 large-6 video-block'; ?>
        <?php endif; ?>
        <?php if (get_field('visual_style') == 'image') : ?>
          <?php $visual_style = $image_visual_style; ?>
        <?php elseif (get_field('visual_style') == 'video') : ?>
          <?php $visual_style = $video_visual_style; ?>
        <?php else : ?>
          <?php $visual_style = 'hero-visual-none'; ?>
        <?php endif; ?>
        <?php if (get_field('visual_side') == 'left') : ?>
          <?php $visual_side = 'small-order-1 medium-order-1'; ?>
          <?php $content_side = 'small-order-2 medium-order-2'; ?>
        <?php else : ?>
          <?php $visual_side = 'small-order-1 medium-order-2'; ?>
          <?php $content_side = 'small-order-2 medium-order-1'; ?>
        <?php endif; ?>



        <?php if (get_field('visual_style') == 'video') : ?>
          <?php if (have_rows('hero_video')) : ?>
            <?php while (have_rows('hero_video')) : the_row(); ?>
              <?php $block_settings = get_sub_field('block_settings'); ?>
              <?php if ($block_settings && in_array('shadow', $block_settings)) : ?>
                <?php $shadow = 'shadow' ?>
              <?php else : ?>
                <?php $shadow = '' ?>
              <?php endif; ?>
              <?php if ((get_sub_field('video_display') != '') && (get_sub_field('poster_image') != '') && (get_sub_field('video_type') != 'uploaded')) : ?>
                <?php $pop_image_options = get_sub_field('popup_image_settings'); ?>
                <?php if ($pop_image_options && in_array('greyscale', $pop_image_options)) : ?>
                  <?php $greyscale = 'greyscale' ?>
                <?php else : ?>
                  <?php $greyscale = '' ?>
                <?php endif; ?>
                <?php if ($pop_image_options && in_array('overlay', $pop_image_options)) : ?>
                  <?php $overlay = 'overlay' ?>
                <?php else : ?>
                  <?php $overlay = '' ?>
                <?php endif; ?>
                <?php if ($pop_image_options && in_array('icon', $pop_image_options)) : ?>
                  <?php $icon = 'icon' ?>
                <?php else : ?>
                  <?php $icon = '' ?>
                <?php endif; ?>
              <?php else : ?>
                <?php $greyscale = '' ?>
                <?php $overlay = '' ?>
                <?php $icon = '' ?>
              <?php endif; ?>

              <?php $video_features = get_sub_field('video_settings'); ?>
              <?php if ($video_features && in_array('autoplay', $video_features)) : ?>
                <?php $autoplay = '1' ?>
              <?php else : ?>
                <?php $autoplay = '0' ?>
              <?php endif; ?>
              <?php if ($video_features && in_array('loop', $video_features)) : ?>
                <?php $loop = '1' ?>
              <?php else : ?>
                <?php $loop = '0' ?>
              <?php endif; ?>
              <?php if ($video_features && in_array('controls', $video_features)) : ?>
                <?php $controls = '1' ?>
              <?php else : ?>
                <?php $controls = '0' ?>
              <?php endif; ?>

              <?php $image = get_sub_field('poster_image');
              if (!empty($image)) : ?>
                <?php $poster = ($image['url']); ?>
              <?php else : ?>
                <?php $poster = (''); ?>
              <?php endif; ?>
              <div class="cell  <?= "$visual_style $visual_side" ?>">
                <?php if (get_sub_field('video_display') && get_sub_field('video_type') != 'uploaded') :  ?>
                  <div class="video-container <?= "$shadow $greyscale $overlay $icon" ?>">

                    <?php $image = get_sub_field('poster_image');
                    if (!empty($image)) : ?>
                      <button aria-label="<?php echo esc_attr__('open video in pop-up container', 'aras'); ?>" class="" data-open="left_popup_<?php echo $modnum; ?>_<?php echo $rownum; ?>">
                        <?php if ($overlay == 'overlay') : ?>
                          <img class="video-overlay" src="<?php echo get_template_directory_uri(); ?>/assets/images/orange_overlay.svg" alt="orange overlay layer" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>" />
                        <?php endif; ?>
                        <?php if ($icon == 'icon') : ?>
                          <img class="video-icon" src="<?php echo get_template_directory_uri(); ?>/assets/images/video-icon.svg" alt="video play icon" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>" />
                        <?php endif; ?>
                        <img class="video-poster" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>" />
                      </button>
                    <?php endif; ?>
                  </div>
                <?php else :
                  //Video is inline
                ?>
                  <div class="video-container <?= "$shadow" ?>">
                    <?php if (get_sub_field('video_type') == 'id') : ?>
                      <img style="width: 100%; margin: auto; display: block;" class="vidyard-player-embed" src="https://play.vidyard.com/<?php echo get_sub_field('vidyard_id'); ?>.jpg" data-uuid="<?php echo get_sub_field('vidyard_id'); ?>" data-v="4" data-type="inline" data-autoplay="<?= "$autoplay" ?>" data-loop="<?= "$loop" ?>" data-controls="<?= "$controls" ?>" />
                    <?php elseif (get_sub_field('video_type') == 'uploaded') : ?>
                      <?php $uploaded_video = get_sub_field('uploaded_video'); ?>
                      <?php if (!empty($uploaded_video['url'])) : ?>
                        <video playsinline <?php echo $autoplay == '1' ? 'autoplay muted ' : ''; ?><?php echo $loop == '1' ? 'loop ' : ''; ?><?php echo $controls == '1' ? 'controls ' : ''; ?>style="width: 100%; margin: auto; display: block;" src="<?php echo esc_url($uploaded_video['url']); ?>"></video>
                      <?php endif; ?>
                    <?php else : ?>
                      <?php
                      $iframe = get_sub_field('video_link');
                      preg_match('/src="(.+?)"/', $iframe, $matches);
                      $src = $matches[1];
                      $params = array(
                        'controls'  => $controls,
                        'hd'        => 1,
                        'autohide'  => 1,
                        'loop'      => $loop,
                        'autoplay'  => $autoplay,
                        'mute'      => $autoplay,
                      );
                      $new_src = add_query_arg($params, $src);
                      $iframe = str_replace($src, $new_src, $iframe);
                      $attributes = 'frameborder="0"';
                      $iframe = str_replace('></iframe>', ' ' . $attributes . '></iframe>', $iframe);
                      echo $iframe;
                      ?>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </div>
            <?php endwhile; ?>
          <?php endif; ?>

        <?php elseif (get_field('visual_style') == 'image') : ?>

          <?php if (have_rows('hero_image')) : ?>
            <?php while (have_rows('hero_image')) : the_row(); ?>
              <?php $image_options = get_sub_field('image_options'); ?>
              <?php if ($image_options && in_array('greyscale', $image_options)) : ?>
                <?php $greyscale = 'greyscale' ?>
              <?php else : ?>
                <?php $greyscale = '' ?>
              <?php endif; ?>
              <?php if ($image_options && in_array('overlay', $image_options)) : ?>
                <?php $overlay = 'overlay' ?>
              <?php else : ?>
                <?php $overlay = '' ?>
              <?php endif; ?>
              <?php if ($image_options && in_array('shadow', $image_options)) : ?>
                <?php $shadow = 'shadow' ?>
              <?php else : ?>
                <?php $shadow = '' ?>
              <?php endif; ?>
              <?php $enable_image_zoom = get_sub_field('enable_image_zoom'); ?>
              <?php $image = get_sub_field('image');
              if (!empty($image)) : ?>
                <div class="cell  <?= "$visual_style $visual_side" ?>">
                  <div class="image-container <?= "$greyscale $overlay $shadow" ?>">
                    <?php if ($overlay == 'overlay') : ?>
                      <img class="image-overlay" src="<?php echo get_template_directory_uri(); ?>/assets/images/orange_overlay.svg" alt="orange overlay layer" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>" />
                    <?php endif; ?>
                    <?php if ($enable_image_zoom) : ?>
                      <a href="<?php echo esc_url($image['url']); ?>" data-pswp-width="<?php echo esc_attr($image['width']); ?>" data-pswp-height="<?php echo esc_attr($image['height']); ?>" class="zoomable-image">
                    <?php endif; ?>
                    <img class="split-image" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" width="<?php echo ($image['width']); ?>" height="<?php echo ($image['height']); ?>">
                    <?php if ($enable_image_zoom) : ?>
                      </a>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endif; ?>
            <?php endwhile; ?>
          <?php endif; ?>
        <?php endif; ?>








      <?php else :
        //VISUAL: NONE
      ?>

        <?php $content_style = 'small-12 medium-9 large-7 hero-content'; ?>
        <?php $visual_style = ''; ?>
        <?php $visual_side = ''; ?>
        <?php $content_side = ''; ?>
      <?php endif; ?>



      <div class="cell <?= "$content_style $content_side" ?>">
        <div class="hero-content-inner">

          <?php if (get_field('headline_color') == 'red') : ?>
            <?php $h1color = 'color-red'; ?>
          <?php else : ?>
            <?php $h1color = ''; ?>
          <?php endif; ?>

          <?php if (get_field('hero_headline')) : ?>
            <h1 class="hero-headline <?php echo $h1color; ?><?php echo get_field('enable_home_page_font_styling') ? ' home-hero-headline' : ''; ?>"><?php echo get_field('hero_headline'); ?></h1>
          <?php else : ?>
            <h1 class="hero-headline <?php echo $h1color; ?><?php echo get_field('enable_home_page_font_styling') ? ' home-hero-headline' : ''; ?>"><?php the_title(''); ?></h1>
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

</section>
