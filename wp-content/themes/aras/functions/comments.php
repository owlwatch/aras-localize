<?php
// Comment Layout
function joints_comments($comment, $args, $depth)
{
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class('panel'); ?>>
		<article id="comment-<?php comment_ID(); ?>">
			<header class="comment-author">
				<?php
				$first_name = get_comment_meta($comment->comment_ID, 'first_name', true);
				if (empty($first_name)) {
					$first_name = get_comment_author_link();
				}
				?>
				<?php echo esc_html($first_name); ?> on
				<time datetime="<?php echo comment_time('Y-m-j'); ?>"><a href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)) ?>"><?php comment_time(__(' F jS, Y', 'jointswp')); ?> </a></time>
				<?php edit_comment_link(__('(Edit)', 'jointswp'), '  ', '') ?>
			</header>
			<?php if ($comment->comment_approved == '0') : ?>
				<div class="alert alert-info">
					<p>Your comment is awaiting moderation.</p>
				</div>
			<?php endif; ?>
			<section class="comment_content clearfix">
				<?php comment_text() ?>
			</section>
			<?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
		</article>
		<!-- </li> is added by WordPress automatically -->
	<?php
}
