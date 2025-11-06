<?php
$categories = get_terms( array(
	'taxonomy' => 'mp-solution-category',
	'hide_empty' => true,
));

$types = get_terms( array(
	'taxonomy' => 'mp-solution-type',
	'hide_empty' => true,
));

$essid = get_field('marketplace_essid', 'option');

// show a select box for both taxonomies,
// and check for the current term to set the selected
?>
<div class="mp-solution-filters">
	<form class="mp-solution-filters__form" action="<?php echo get_post_type_archive_link('mp-solution'); ?>" method="get">

		<?php if( !is_wp_error( $categories ) && !empty($categories ) ){ ?>
		<fieldset>
			<label for="mp-solution-category-filter"><?php _e('Filter by Category', 'aras-marketplace'); ?></label>
			<select id="mp-solution-category-filter" name="mp-solution-category" onchange="document.querySelector('.mp-solution-filters__form').submit()">
				<option value=""><?php _e('All Categories', 'aras-marketplace'); ?></option>
				<?php
				$current_category = get_query_var('mp-solution-category');
				foreach( $categories as $category ){
					?>
					<?php
					$selected = '';
					// get the link
					$value = $category->slug;
					if( $current_category == $category->slug ){
						$selected = 'selected';
					}
					?>
					<option <?php echo $selected ?> value="<?php echo esc_attr($value); ?>">
						<?php echo $category->name; ?>
					</option>
				<?php } ?>
			</select>
		</fieldset>

		<?php } ?>

		<?php if( !is_wp_error( $types ) && !empty($types) ){ ?>

		<fieldset>
			<label for="mp-solution-type-filter"><?php _e('Filter by Type', 'aras-marketplace'); ?></label>
			<select id="mp-solution-type-filter" name="mp-solution-type" onchange="document.querySelector('.mp-solution-filters__form').submit()">
				<option value=""><?php _e('All Types', 'aras-marketplace'); ?></option>
				<?php 
				$current_type = get_query_var('mp-solution-type');
				foreach( $types as $type ){ 
					$selected = '';
					// get the link
					$value = $type->slug;
					if( $current_type == $type->slug ){
						$selected = 'selected';
					}
					?>
					<option <?php echo $selected ?> value="<?php echo esc_attr($value); ?>">
						<?php echo $type->name; ?>
					</option>
				<?php } ?>
			</select>
		</fieldset>

		<?php } ?>

		<fieldset class="mp-solution-type-search">
			<label class="mp-solution-type-filter" for="mp-search"><?php _e('Search', 'aras-marketplace'); ?></label>
			<input 
				class="mp-banner__search-input"
				type="search"
				name="mp-search"
				placeholder="<?php _e('Search Marketplace', 'aras-marketplace'); ?>"
				value="<?php echo get_search_query(); ?>"
			/>
		</fieldset>
		<?php if( $essid ): ?>
			<input type='hidden' value='<?php echo esc_attr($essid); ?>' name='wpessid' />
		<?php endif; ?>
		<button type="submit" class="mp-solution-filters__submit aras-button">
			<?php _e('Search', 'aras-marketplace'); ?>
		</button>
	</form>
</div>