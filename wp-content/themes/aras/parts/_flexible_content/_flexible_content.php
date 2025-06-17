<?php

// if this is password protected, just show the_content
if (post_password_required()) {
  the_content();
  return;
}

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

      // try to get the associated template part
      get_template_part('parts/_flexible_content/' . get_row_layout());
      
      ?>

    <?php endwhile; ?>
  <?php endif; ?>
<?php endif; ?>