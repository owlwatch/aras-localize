<?php
$current_post_id = get_the_ID(); ?>

<?php if ((isset($_GET['id']) && intval($_GET['id']) === $current_post_id)) : ?>
  <?php if (have_rows('flexible_content')) :
    while (have_rows('flexible_content')) : the_row(); ?>
      <?php $ungated_content = false; ?>
      <?php
      // split_content_section
      if (get_row_layout() == 'split_content_section') {
        if (have_rows('right_content', $current_post_id)) :
          while (have_rows('right_content', $current_post_id)) : the_row();
            if (have_rows('form_block', $current_post_id)) :
              while (have_rows('form_block', $current_post_id)) : the_row();
                if (get_sub_field('post_submission_action') == 'update' && !$ungated_content) {
                  get_template_part('parts/_flexible_content/form_ungated_content');
                  break;
                  $ungated_content = true;
                }
              endwhile;
            endif;
          endwhile;
        endif;
        if (have_rows('left_content', $current_post_id)) :
          while (have_rows('left_content', $current_post_id)) : the_row();
            if (have_rows('form_block', $current_post_id)) :
              while (have_rows('form_block', $current_post_id)) : the_row();
                if (get_sub_field('post_submission_action') == 'update' && !$ungated_content) {
                  get_template_part('parts/_flexible_content/form_ungated_content');
                  break;
                  $ungated_content = true;
                }
              endwhile;
            endif;
          endwhile;
        endif;
      }
      if (get_row_layout() == 'full_width_form_section') {
        if (get_sub_field('post_submission_action') == 'update'  && !$ungated_content) {
          get_template_part('parts/_flexible_content/form_ungated_content');
          break;
          $ungated_content = true;
        }
      }
      ?>
    <?php endwhile; ?>
  <?php endif; ?>
<?php else : ?>

  <?php if (have_rows('flexible_content')) :
    while (have_rows('flexible_content')) : the_row(); ?>
      <?php
      // hero_banner
      if (get_row_layout() == 'hero_banner')
        get_template_part('parts/_flexible_content/hero_banner');

      // cards_section
      if (get_row_layout() == 'cards_section')
        get_template_part('parts/_flexible_content/cards_section');
      // automatic_cards_section
      if (get_row_layout() == 'automatic_cards_section')
        get_template_part('parts/_flexible_content/automatic_cards_section');

      // content_section
      if (get_row_layout() == 'content_section')
        get_template_part('parts/_flexible_content/content_section');

      // customer_story_section
      if (get_row_layout() == 'customer_story_section')
        get_template_part('parts/_flexible_content/customer_story_section');

      // floating_cta_section
      if (get_row_layout() == 'floating_cta_section')
        get_template_part('parts/_flexible_content/floating_cta_section');

      // leadership_tabs_section
      if (get_row_layout() == 'leadership_tabs_section')
        get_template_part('parts/_flexible_content/leadership_tabs_section');

      // logo_section
      if (get_row_layout() == 'logo_section')
        get_template_part('parts/_flexible_content/logo_section');

      // roadmap_section
      if (get_row_layout() == 'roadmap_section')
        get_template_part('parts/_flexible_content/roadmap_section');

      // scrolling_content_section
      if (get_row_layout() == 'scrolling_content_section')
        get_template_part('parts/_flexible_content/scrolling_content_section');

      // side_tab_locations_section
      if (get_row_layout() == 'side_tab_locations_section')
        get_template_part('parts/_flexible_content/side_tab_locations_section');

      // side_tabs_section
      if (get_row_layout() == 'side_tabs_section')
        get_template_part('parts/_flexible_content/side_tabs_section');

      // statistics_section
      if (get_row_layout() == 'statistics_section')
        get_template_part('parts/_flexible_content/statistics_section');

      // quote_section
      if (get_row_layout() == 'quote_section')
        get_template_part('parts/_flexible_content/quote_section');

      // split_content_section
      if (get_row_layout() == 'split_content_section')
        get_template_part('parts/_flexible_content/split_content_section');

      // comparison_table
      if (get_row_layout() == 'comparison_table')
        get_template_part('parts/_flexible_content/comparison_table');

      // featured_speakers_section
      if (get_row_layout() == 'featured_speakers_section')
        get_template_part('parts/_flexible_content/featured_speakers_section');

      // full_width_form_section
      if (get_row_layout() == 'full_width_form_section')
        get_template_part('parts/_flexible_content/full_width_form_section');
      ?>

    <?php endwhile; ?>
  <?php endif; ?>
<?php endif; ?>