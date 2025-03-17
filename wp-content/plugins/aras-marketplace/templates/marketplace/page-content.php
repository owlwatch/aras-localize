<?php
$field_name = $args['field_name'];
if( have_rows($field_name, 'option') ):
	while( have_rows($field_name, 'option') ):
		the_row();
		if (have_rows('flexible_post_content')) :
			while (have_rows('flexible_post_content')) : the_row();

				// post_content_section
				if (get_row_layout() == 'post_content_section')
				get_template_part('parts/_flexible_post_content/post_content_section');

				// post_split_content_section
				if (get_row_layout() == 'post_split_content_section')
				get_template_part('parts/_flexible_post_content/post_split_content_section');

				// post_floating_cta_section
				if (get_row_layout() == 'post_floating_cta_section')
				get_template_part('parts/_flexible_post_content/post_floating_cta_section');

			//// automatic_cards_section
			//if (get_row_layout() == 'automatic_cards_section')
			//  get_template_part('parts/_flexible_content/automatic_cards_section');

			endwhile;
		endif;
	endwhile;
endif;