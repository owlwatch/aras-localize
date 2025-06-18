<?php

/**
 * Displays current comments and comment form. Works with includes/comments.php.
 * For more info: https://developer.wordpress.org/themes/template-files-section/partial-and-miscellaneous-template-files/comments/
 */
if (post_password_required()) {
	return;
}
?>

<?php if (!comments_open() && '0' != get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
<?php else : ?>
	<?php if (comments_open() || get_comments_number()) : ?>

		<?php if (have_comments()) {
			$commentleft = 'small-12 medium-5 large-6';
			$commentright = 'small-12 medium-7 large-6';
		} else {
			$commentleft = 'small-12 medium-5 large-6';
			$commentright = 'small-12 medium-7 large-6';
		} ?>

		<section class="bg-grey">
			<section id="comments" class="comments-area grid-container smalltoppadding smallbottompadding">
				<div class="grid-x grid-margin-x align-left">
					<div class="cell <?php echo $commentleft; ?>">
						<?php
						function custom_comment_fields($fields)
						{

							// Unset the URL field
							unset($fields['url']);
							// Save the comment field and unset it to re-add it later
							$comment_field = $fields['comment'];
							unset($fields['comment']);

							$fields['author'] = '<input type="hidden" class="commentfield" id="author" name="author" required>';
							// Create new fields for first name and last name
							$first_name_field = '<input class="commentfield" id="first_name" name="first_name" placeholder="First Name*" required>';
							$last_name_field = '<input class="commentfield" id="last_name" name="last_name" placeholder="Last Name*" required>';
							$fields['email'] = '<input class="commentfield" id="email" name="email" placeholder="Email*" required>';
							$comment_field = '<textarea id="comment" name="comment" placeholder="Comment"></textarea>';

							// Add custom fields in the desired order
							$new_fields['first_name'] = $first_name_field;
							$new_fields['last_name'] = $last_name_field;
							$new_fields['author'] = $fields['author'];
							$new_fields['email'] = $fields['email'];
							$new_fields['comment'] = $comment_field;  // Re-add the comment field

							return $new_fields;
						}
						add_filter('comment_form_fields', 'custom_comment_fields');

						comment_form(array(
							'comment_notes_before' => '',
							'submit_button' => '<input name="%1$s" type="submit" id="%2$s" class="%3$s aras-button" value="%4$s">',
							'title_reply' => 'Leave a Comment',
						));
						?>
					</div>
					<div class="cell <?php echo $commentright; ?>">

						<?php if (have_comments()) : ?>
							<h4 class="comments-title">
								<?php
								printf(
									esc_html(_nx('Comments: %1$s', 'Comments: %1$s', get_comments_number(), 'comments title', 'jointswp')),
									number_format_i18n(get_comments_number()),
									'<span>' . get_the_title() . '</span>'
								);
								?>
							</h4>
							<ol class="commentlist">
								<?php wp_list_comments('type=comment&callback=joints_comments'); ?>
							</ol>
							<?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : // Are there comments to navigate through? 
							?>
								<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
									<h2 class="screen-reader-text"><?php esc_html_e('Comment navigation', 'jointswp'); ?></h2>
									<div class="nav-links">
										<div class="nav-previous"><?php previous_comments_link(esc_html__('Older Comments', 'jointswp')); ?></div>
										<div class="nav-next"><?php next_comments_link(esc_html__('Newer Comments', 'jointswp')); ?></div>
									</div>
								</nav>
							<?php endif; ?>
						<?php else : ?>
						<?php endif; ?>
					</div>
				</div>
			</section>
		</section>




		<script>
			document.addEventListener('DOMContentLoaded', function() {
				// Get the input fields
				const firstNameInput = document.getElementById('first_name');
				const lastNameInput = document.getElementById('last_name');
				const authorInput = document.getElementById('author');

				// Function to update the author field
				function updateAuthorField() {
					const firstName = firstNameInput.value.trim();
					const lastName = lastNameInput.value.trim();
					authorInput.value = firstName + ' ' + lastName;
				}

				// Attach event listeners to both input fields
				firstNameInput.addEventListener('input', updateAuthorField);
				lastNameInput.addEventListener('input', updateAuthorField);
			});
		</script>

<?php endif;
endif; ?>