<?php
namespace Aras\Competitors\Service;

use function Aras\Competitors\app;

class Admin {
	
	public function __construct() {

		// we want to add a metabox for aras-compare objects
		add_action( 'add_meta_boxes', array( $this, 'addMetaBox' ) );

		// we want to save the meta box
		add_action( 'save_post', array( $this, 'saveMetaBox' ) );
	}

	/**
	 * Add the meta box
	 *
	 * @return void
	 */
	public function addMetaBox() {
		// get the swoogo events

		add_meta_box(
			'aras_compare',
			'Capabilities',
			array( $this, 'renderMetaBox' ),
			'aras-competitor',
			'normal',
			'high'
		);
	}

	/**
	 * Save the meta box
	 *
	 * @param int $post_id
	 * @return void
	 */
	public function saveMetaBox( $post_id ) {
		// check if this is a autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// check if this is a valid post type
		if ( !isset( $_POST['post_type'] ) || $_POST['post_type'] != 'aras-competitor' ) {
			return;
		}

		// check for aras-competitor-<slug>-rating
		// and aras-competitor-<slug>-description
		$terms = get_terms( array(
			'taxonomy' => 'aras-competitor-capability',
			'hide_empty' => false,
			'orderby' => 'name',
			'order' => 'ASC',
		) );
		if( !count( $terms ) ){
			return;
		}
		foreach( $terms as $term ) {
			// check for the rating
			if( isset( $_POST['aras-compare-' . $term->slug . '-rating'] ) ){
				update_post_meta( $post_id, 'aras-compare-' . $term->slug . '-rating', $_POST['aras-compare-' . $term->slug . '-rating'] );
			}

			// check for the description
			if( isset( $_POST['aras-compare-' . $term->slug . '-description'] ) ){
				update_post_meta( $post_id, 'aras-compare-' . $term->slug . '-description', $_POST['aras-compare-' . $term->slug . '-description'] );
			}
		}
	}

	/**
	 * Render the meta box
	 *
	 * @param object $post
	 * @return void
	 */
	public function renderMetaBox( $post ) {

		// we want to go through each 'aras-compare-attr' term
		// and output a rating and description for each
		$terms = get_terms( array(
			'taxonomy' => 'aras-competitor-capability',
			'hide_empty' => false,
			'orderby' => 'name',
			'order' => 'ASC',
		) );

		if( !count( $terms ) ){
			echo '<p>No capabilities found</p>';
			return;
		}

		?>
		<table class="form-table striped-table">
		<?php
		
		// lets go through each term and output
		// a wordpress field table
		foreach( $terms as $term ) {
			// output the a one row table
			// with the term name as the first column (label)
			// second column is a 'range' field for rating
			// and the third column is the text for description
			
			$rating = get_post_meta( $post->ID, 'aras-compare-' . $term->slug . '-rating', true );
			$description = get_post_meta( $post->ID, 'aras-compare-' . $term->slug . '-description', true );
			?>
				<tr style="vertical-align: top">
					<th scope="row" style="width: 33%"><?php echo esc_html( $term->name ); ?></th>
					<td style="width: 150px;">
						<div style="display: flex; gap: 0.6em;">
						<?php for ( $i = 0; $i <= 4; $i++ ): ?>
						<div style="display:flex; flex-direction:column; align-items: center;">
							<input style="margin: 0 !important" type="radio" id="aras-compare-<?php echo esc_attr( $term->slug ); ?>-rating-<?php echo $i ?>" name="aras-compare-<?php echo esc_attr( $term->slug ); ?>-rating" value="<?php echo $i; ?>" <?php checked( $rating, $i ); ?> style="margin-right: 0.5rem;" />
							<label style="margin: 0.5rem 0;" for="aras-compare-<?php echo esc_attr( $term->slug ); ?>-rating-<?php echo $i ?>"><?php echo $i * 25; ?>%</label>
						</div>
						<?php endfor; ?>
						</div>
					</td>
					<td>
						<textarea style="display: block; height: 4rem; width: 100%;" type="text" name="aras-compare-<?php echo esc_attr( $term->slug ); ?>-description" value=""><?php
							echo esc_html($description);
						?></textarea>
					</td>
				</tr>
			<?php
		}
		?>
		</table>
		<style>
			.striped-table {
				border: 1px solid #ddd;
				border-radius: 0.5rem;
				margin-bottom: 1rem;
			}
			.striped-table tr:nth-child(odd) {
				background-color: #f9f9f9;
			}
			.striped-table tr:nth-child(even) {
				background-color: #fff;
			}
			.striped-table th {
				padding: 15px 10px;
			}
		</style>
		<?php
	}
}