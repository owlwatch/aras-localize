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
			<?php foreach( $categories as $category ){ ?>
				<?php
				$selected = '';
				if( is_tax('mp-solution-category', $category->term_id) ){
					$selected = 'selected';
				}
				?>
				<option <?php echo $selected ?> value="<?php echo get_term_link($category); ?>">
					<?php echo $category->name; ?>
				</option>
			<?php } ?>
		</select>
	</fieldset>

	<fieldset>
		<label for="mp-solution-type-filter"><?php _e('Filter by Type', 'aras-marketplace'); ?></label>
		<select id="mp-solution-type-filter" onchange="window.location=this.value">
			<option value="<?php echo get_post_type_archive_link( 'mp-solution'); ?>"><?php _e('All Types', 'aras-marketplace'); ?></option>
			<?php foreach( $types as $type ){ ?>
				<?php
				$selected = '';
				if( is_tax('mp-solution-type', $type->term_id) ){
					$selected = 'selected';
				}
				?>
				<option <?php echo $selected ?> value="<?php echo get_term_link($type); ?>">
					<?php echo $type->name; ?>
				</option>
			<?php } ?>
		</select>
	</fieldset>
</div>