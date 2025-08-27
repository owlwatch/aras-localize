<?php

namespace Aras\Script;

class TermUpdates
{

	public function __construct() {}

	function get_translated_term($term_id, $taxonomy, $language_code)
	{
		// get the translated term ID
		$translated_term_id = apply_filters('wpml_object_id', $term_id, $taxonomy, false, $language_code);
		if (!$translated_term_id || $translated_term_id == $term_id) {
			// print_r([
			// 	'message' => 'No translation found',
			// 	'term_id' => $term_id,
			// 	'taxonomy' => $taxonomy,
			// 	'language_code' => $language_code,
			// 	'translated_term_id' => $translated_term_id
			// ]);
			return null; // no translation found
		}

		// get the translated term
		$translated_term = get_term_by( 'term_id', $translated_term_id, $taxonomy);
		if (is_wp_error($translated_term)) {
			return null; // error getting term
		}

		return $translated_term;
	}

	function get_languages()
	{
		static $lagnagues;
		if( isset($languages) ) {
			return $languages;
		}

		// we need to get all languages
		$languages = apply_filters('wpml_active_languages', null, 'orderby=id&order=desc');
		return $languages;
	}

	function get_term_translations($term_id, $taxonomy)
	{
		
		$languages = $this->get_languages();

		foreach( $languages as $language ) {
			$translated_term = $this->get_translated_term($term_id, $taxonomy, $language['code']);
			if ($translated_term) {
				$translations[$language['code']] = $translated_term;
			}
		}

		return $translations;
	}

	public function run()
	{

		global $sitepress;
		
		header('Content-Type: application/json; charset=utf-8');
		// we want to find all categories and tags
		// and make sure that all their translations use the same slug

		$categories = get_categories(['hide_empty' => false]);
		// $tags = get_tags(['hide_empty' => false]);

		$terms = $categories;

		$terms_to_update = [];
		$updated_terms = [];
		$english_terms = [];
		$all_translations = [];

		foreach ($terms as $term) {
			// get the term ID
			$term_id = $term->term_id;

			// get the translations of the term
			$translations = $this->get_term_translations($term_id, $term->taxonomy);
			
			if (empty($translations)) {
				continue; // no translations found, skip to next term
			}

			// we need to convert the translations to an array
			$all_translations[$term->slug] = $translations;

			// get the slug of the original term
			$original_slug = $term->slug;

			// loop through each translation
			foreach ($translations as $lang => $translation) {

				// get the translated term ID
				$translated_term_id = $translation->term_id;

				// get the slug of the translated term
				$translated_slug = $translation->slug;

				// print_r([
				// 	'original_slug' => $original_slug,
				// 	'translated_slug' => $translated_slug,
				// 	'lang' => $lang,
				// 	'term_id' => $term_id,
				// 	'translated_term_id' => $translated_term_id,
				// 	'name' => $translation->name,
				// 	'taxonomy' => $translation->taxonomy
				// ]);
				// if the slugs don't match, update the translation
				if ($original_slug !== $translated_slug) {
					// wp_update_term($translated_term_id, 'category', ['slug' => $original_slug]);
					// $updated = true;
					if( !$terms_to_update[$term_id] ){
						$english_terms[$term_id] = $term;
						$terms_to_update[$term_id] = [];
					}
					$terms_to_update[$term_id][] = [
						'original_slug' => $original_slug,
						'original_term_id' => $term_id,
						'lang' => $lang,
						'term_id' => $translated_term_id,
						'slug' => $translated_slug,
						'name' => $translation->name,
						'taxonomy' => $translation->taxonomy
					];
				}

				else {

				}
			}
		}

		foreach( $terms_to_update as $term_id => &$updates ) {
			$english_term = $english_terms[$term_id];
			foreach( $updates as &$update ) {
				
				// we also want to pull the acf field from the english term
				// for the title translation
				switch( $update['lang'] ){
					case 'fr':
						$lang = 'french';
						break;
					case 'de':
						$lang = 'german';
						break;
					case 'ja':
						$lang = 'japanese';
						break;
					default:
						$lang = 'en-us';
				}
				$key = 'cat_label_' . $lang;
				$value = get_field($key, 'category_' . $english_term->term_id);

				$updated_values = ['slug' => $english_term->slug];
				if( $value ) {
					// this should be the title for the translated category
					$updated_values['name'] = $value;
				}

				// we want to switch to the language of the term we are updating
				$switch_lang = new \WPML_Temporary_Switch_Language( $sitepress, $update['lang'] );
				$update['updated'] = wp_update_term($update['term_id'], $update['taxonomy'], $updated_values);
				unset($switch_lang);
			}
		}

		echo json_encode([
			'terms_to_update' => $terms_to_update,
			'terms' => $terms
		], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		exit;
	}
}
