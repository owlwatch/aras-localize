<?php
$categories = get_terms( array(
	'taxonomy' => 'mp-solution-category',
	'hide_empty' => true,
));

$types = get_terms( array(
	'taxonomy' => 'mp-solution-type',
	'hide_empty' => true,
));


// show a select box for both taxonomies,
// and check for the current term to set the selected
?>
<div class="mp-solution-filters">
	<fieldset>
		<label for="mp-solution-category-filter"><?php _e('Filter by Category', 'aras-marketplace'); ?></label>
		<select id="mp-solution-category-filter" onchange="window.location=this.value">
			<option value="<?php echo get_post_type_archive_link( 'mp-solution'); ?>"><?php _e('All Categories', 'aras-marketplace'); ?></option>
			<?php
			$current_category = get_query_var('mp-solution-category');
			foreach( $categories as $category ){
				?>
				<?php
				$selected = '';
				// get the link
				$link = get_term_link($category);
				if( ($type = get_query_var('mp-solution-type')) ){
					$link = add_query_arg( 'mp-solution-type', $type, $link );
				}
				if( $current_category == $category->slug ){
					$selected = 'selected';
				}
				?>
				<option <?php echo $selected ?> value="<?php echo $link; ?>">
					<?php echo $category->name; ?>
				</option>
			<?php } ?>
		</select>
	</fieldset>

	<fieldset>
		<label for="mp-solution-type-filter"><?php _e('Filter by Type', 'aras-marketplace'); ?></label>
		<select id="mp-solution-type-filter" onchange="window.location=this.value">
			<option value="<?php echo get_post_type_archive_link( 'mp-solution'); ?>"><?php _e('All Types', 'aras-marketplace'); ?></option>
			<?php 
			$current_type = get_query_var('mp-solution-type');
			foreach( $types as $type ){ 
				$selected = '';
				// get the link
				$link = get_term_link($type);
				if( ($category = get_query_var('mp-solution-category')) ){
					$link = add_query_arg( 'mp-solution-category', $category, $link );
				}
				if( $current_type == $type->slug ){
					$selected = 'selected';
				}
				?>
				<option <?php echo $selected ?> value="<?php echo $link; ?>">
					<?php echo $type->name; ?>
				</option>
			<?php } ?>
		</select>
	</fieldset>
</div>