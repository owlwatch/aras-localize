<?php

// if the post is password protected and they have not entered the password, return early
if (post_password_required()) {
?>
  <section class="content-section mediumtoppadding smallbottompadding bg-white " id="contentsection-1">


    <div class="grid-container">
      <div class="grid-x grid-margin-x align-top" data-equalizer="" data-equalizer-by-row="true" data-resize="uwu7ga-eq" data-mutate="b0xc1d-eq" data-t="woswv5-t" data-events="resize">



        <div class="cell text-block wysiwyg-content small-12 fullwidthblock">
          <?php
          $the_content = get_the_content();
          // update the input[type="submit"] button to add a class .button
          $the_content = preg_replace('/<input type="submit"/', '<input type="submit" class="aras-button"', $the_content);
          echo apply_filters('the_content', $the_content);
          ?>
        </div>

        <style>
          form.post-password-form {
            max-width: 400px;
            margin: 0 auto;

            input[type="password"] {
              width: 100%;
            }
          }
        </style>

      </div>
    </div>
  </section>
<?php
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