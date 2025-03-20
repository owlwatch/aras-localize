<?php
namespace Aras;


class GoogleAdsEnhancedConversions
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
		add_filter('gform_confirmation', [$this, 'gform_confirmation'], 21, 4);
	}

	public function gform_confirmation($confirmation, $form, $entry, $ajax)
	{
		// we are going to assume that the confirmation is a string
		if( !is_string($confirmation) ){
			return $confirmation;
		}

		// find the email from the form
		$email = '';
		foreach( $form['fields'] as $field ){
			if( $field->type === 'email' && $field->isRequired ){
				$email = rgar($entry, $field->id);
				break;
			}
		}

		if( !$email ){
			return $confirmation;
		}

		$script = <<<EOC
<script type="text/javascript">
if( window.dataLayer ) dataLayer.push({ 'upd_email' : '{$email}'});
</script>
EOC;
		$confirmation = $script . $confirmation;
		return $confirmation;

	}
}

GoogleAdsEnhancedConversions::getInstance();