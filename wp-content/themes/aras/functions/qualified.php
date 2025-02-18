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
		add_filter('gform_confirmation', [$this, 'gform_confirmation'], 11, 4);
		add_action('wp_footer', [$this, 'wp_footer'], 10, 4);

	}

	public function gform_confirmation($confirmation, $form, $entry, $ajax)
	{
		// if the confirmation is not a redirect, return
		if (! is_array($confirmation) || empty($confirmation['redirect'])) {
			return $confirmation;
		}
	
		// get the redirect url
		$redirect = $confirmation['redirect'];
	
		// add flag to trigger qualified experience
		$redirect = add_query_arg('show_qualified_experience', 'true', $redirect);
		$confirmation['redirect'] = $redirect;
		return $confirmation;
	}

	function wp_footer()
	{
		// we don't know where gravity forms will be output, so we'll include a small
		// script that hooks into the gravity forms ajax event on all pages
		?>
		<script>
			document.addEventListener( 'gform/post_init', function(){
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
						else if( labelText.match(/company/i) ){
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

					console.log( {payload} );

					if( !window.qualified ){
						return data;
					}
					
					qualified("saveFormData", payload);
					qualified("emitFormFill", "custom");
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

		// output the script
		echo $script;
	}
}

QualifiedIntegration::getInstance();