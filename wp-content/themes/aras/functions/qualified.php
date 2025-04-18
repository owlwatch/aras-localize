<?php
namespace Aras;
/**
 * This file adds gravity forms integrations for Qualified
 * 
 * We can assume that the qualified script is already included
 * on the page through Google Tag Manager
 */


class QualifiedIntegration
{
	private static $instance;

	public static function getInstance()
	{
		if (! isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct()
	{
		add_filter('gform_confirmation', [$this, 'gform_confirmation'], 20, 4);
		add_action('wp_footer', [$this, 'wp_footer'], 10, 4);

	}

	private function is_qualified_enabled_for_form( $form )
	{
		// stored in the cssClass as 'use-qualified'
		$useQualified = false;
		if (isset($form['cssClass'])) {
			$cssClasses = $form['cssClass'];
			// we are going to match by class
			if( preg_match('/(^|\s)use-qualified-([^\s]+?)(\s|$)/', $cssClasses, $matches) ){
				$useQualified = $matches[2];
			}
		}
		return $useQualified;
	}

	public function gform_confirmation($confirmation, $form, $entry, $ajax)
	{
		$redirect = false;

		$enabled = $this->is_qualified_enabled_for_form( $form );
		
		// if the confirmation is not a redirect, return
		if (is_array($confirmation) && !empty($confirmation['redirect'])) {
	
			// get the redirect url
			$redirect = $confirmation['redirect'];
		
			// add flag to trigger qualified experience
			if( $enabled ){
				$redirect = add_query_arg('show_qualified_experience', $enabled, $redirect);
			}
			$redirectingText = __('Redirecting...', 'aras');
			$confirmation = '<div class="aras-redirecting">'.$redirectingText.'</div>';
		}

		else {
			// get the qualified script
			$script = get_field('qualified_show_experience_script', 'option');

			// replace the experience id with the form specific one
			if ($script) {
				$script = preg_replace('/experience-([\d]+)/', 'experience-'.$enabled, $script);
			}
			if ($script) {
				$confirmation.=$script;
			}
		}

		// we can't use the normal redirect because we need to trigger the qualified
		// saveFormData event using the gform_confirmation_loaded filter

		// go through our form / entry and create a payload
		$payload = [
			'email' => '',
			'company' => '',
			'phone' => '',
			'firstName' => '',
			'lastName' => ''
		];

		// get the form fields
		$fields = $form['fields'];
		
		foreach ($fields as $field) {
			$fieldId = $field->id;
			$fieldLabel = strtolower($field->label);
			$isRequired = $field->isRequired;

			// check if the field is in the entry
			if (empty($entry[$fieldId])) {
				continue;
			}

			// get the field value
			$fieldValue = $entry[$fieldId];

			// check if the field is an email
			if (strpos($fieldLabel, 'email') !== false && $isRequired) {
				$payload['email'] = $fieldValue;
			}

			// check if the field is a company
			if (strpos($fieldLabel, 'company') !== false && $isRequired && strpos($fieldLabel, 'facebook') === false) {
				$payload['company'] = $fieldValue;
			}

			// check if the field is a phone
			if (strpos($fieldLabel, 'phone') !== false && $isRequired) {
				$payload['phone'] = $fieldValue;
			}

			// check if the field is a first name
			if (strpos($fieldLabel, 'first name') !== false && $isRequired) {
				$payload['firstName'] = $fieldValue;
			}

			// check if the field is a last name
			if (strpos($fieldLabel, 'last name') !== false && $isRequired) {
				$payload['lastName'] = $fieldValue;
			}
		}

		// we actually want to convert the confirmation to a string
		// with a script that calls our "fireQualifiedEvent" function
		
		$confirmation .= "<script>window.parent.arasFireQualifiedEvent(" . json_encode($payload) . ", " . json_encode($redirect) . ", ". json_encode($redirectingText).", ". json_encode( $enabled ) .");</script>";
		// also add the gf_{form_id} div to the confirmation
		$form_id = $form['id'];
		$confirmation .= "<div id='gf_$form_id'></div>";

		return $confirmation;
	}

	function wp_footer()
	{
		// we don't know where gravity forms will be output, so we'll include a small
		// script that hooks into the gravity forms ajax event on all pages
		?>
		<script>
			document.addEventListener( 'gform/post_init', function(){

				// lets add our function to fire the qualified event
				function arasFireQualifiedEvent( payload, redirect, redirectText, enabled ){

					if( window.qualified && enabled ){	
						qualified("saveFormData", payload);
						qualified("emitFormFill", "custom");
					}

					if( redirect ){
						// allow for gtm processing
						let fallback = setTimeout(() => {
							window.location = redirect;
						}, 2500);
						// wait for google analytics events
						window.addEventListener('googleanalytics/event_sent', () => {
							clearTimeout( fallback );
							window.location = redirect;
						});
					}
				}

				// export the "fireQualifiedEvent" function
				window.arasFireQualifiedEvent = arasFireQualifiedEvent;

				// this would be ideal if Google Analytics played nice...
				gform.utils.addAsyncFilter('gform/ajax/post_ajax_submission', async (data) => {

					// get the form data from data.form
					function findLabel( elementId ){
						const label = document.querySelector(`label[for="${elementId}"]`);
						return label ? label.innerText : elementId;
					}

					const payload = {
						email:'',
						company:'',
						phone:'',
						firstName:'',
						lastName:''
					};

					data.form.querySelectorAll('label').forEach( label => {
						const field = data.form.querySelector(`#${label.getAttribute('for')}`);
						const labelText = label.innerText;
						
						if( labelText.match(/email/i) && labelText.match(/required/i)){
							payload.email = field.value;
						}
						else if( labelText.match(/company/i) && !labelText.match(/facebook/i) ){
							payload.company = field.value;
						}
						else if( labelText.match(/phone/i) ){
							payload.phone = field.value;
						}
						else if( labelText.match(/first\sname/i) && labelText.match(/required/i) ){
							payload.firstName = field.value;
						}
						else if( labelText.match(/last\sname/i) && labelText.match(/required/i) ){
							payload.lastName += field.value;
						}
					});

					if( !window.qualified ){
						return data;
					}
					
					arasFireQualifiedEvent(payload);
					return data;
				});
			});
		</script>
		<?php

		// check if the query parameter is set
		if (! isset($_GET['show_qualified_experience'])) {
			return;
		}

		// get the qualified script
		$script = get_field('qualified_show_experience_script', 'option');
		if (! $script) {
			return;
		}

		$id = $_GET['show_qualified_experience'];
		// replace the experience id with the form specific one
		$script = preg_replace('/experience-([\d]+)/', 'experience-'.$id, $script);
		
		// output the script
		echo $script;
	}
}

QualifiedIntegration::getInstance();