<?php
// we need to find the user's corresponding profile
// account for co_authors
$authors = [get_the_author_meta('ID')];
$co_authors = get_field('co_authors');
if( !empty($co_authors) && is_array($co_authors) ) {
	// merge co-authors with the main author
	$authors = array_merge( $authors, $co_authors );
}
foreach( $authors as $author_id ){
	$profile = get_posts(array(
		'post_type' => 'user-profile',
		'meta_key' => 'user',
		'meta_value' => $author_id,
	));

	if ($profile) {
		$profile_id = $profile[0]->ID;
	} else {
		return;
	}

	// get the translated profile ID if available
	$profile_id = apply_filters('wpml_object_id', $profile_id, 'user-profile', true );
	?>
	<?php if (have_rows('author_information', $profile_id)) : ?>
		<?php while (have_rows('author_information', $profile_id)) : the_row(); ?>
			<?php if (get_sub_field('author_bio', $profile_id)) : ?>
				<section class="author-section">
					<div class="grid-container">
						<div class="grid-x grid-margin-x">
							<div class="cell small-12 mediumtoppadding mediumbottompadding">
								<?php $image = get_sub_field('author_headshot');
								if (!empty($image)) : ?>
									<img class="author-headshot" src="<?php echo esc_url($image['url']); ?>" alt="<?php if (esc_attr($image['alt'])) : ?> <?php echo esc_attr($image['alt']); ?> <?php else : ?> <?php the_title(); ?> <?php endif; ?>" />
								<?php endif; ?>
								<div class="author-bio">
									<h3>About <?php echo get_the_title( $profile_id ); ?></h3>
									<?php echo get_sub_field('author_bio'); ?>
								</div>
							</div>
						</div>
					</div>
				</section>
			<?php endif; ?>
		<?php endwhile; ?>
	<?php endif; ?>
<?php } ?>