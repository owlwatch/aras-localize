<?php

function footer_scripts()
{
?>
	<script>
		//// Cookiebot scripting
		window.addEventListener('load', function() {
			//Update the tracked page title and send a page view to Pardot.  This is used for sending the download button clicks link as a page view since you can't necessarily navigate to a page holding the download.  We used this on all of our resource download buttons.
			async function trackView(theHref, message) {
				var thisHref = theHref.href;
				piTracker(message + ':  ' + thisHref);
			}
			//CookieBot Reload after button press //
			var cookieButton = document.querySelectorAll("#CybotCookiebotDialogBodyButtonsWrapper button");
			for (var i = 0; i < cookieButton.length; i++) {
				cookieButton[i].addEventListener("click", function(event) {
					setTimeout(function() {
						document.location.reload()
					}, 3000);
				});
			}
		})
	</script>

<?php
}
add_action('wp_footer', 'footer_scripts');
