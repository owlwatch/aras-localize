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
			//utm persistent url decorator//
			const thisUrl = window.location.href;
			const domainToCheck = "aras.com";
			// Function for getting all hrefs on the page then filter out ones that contain a # symbol
			function getAllHrefs() {
				let elements = document.querySelectorAll('a');
				let filtered = Array.from(elements).filter(elem => elem.href.includes("#") == false)
				return filtered;
			}
			// Function for removing a trailing & symbol
			function removeTrailingAmpersand(str) {
				return str.replace(/\&+$/, '');
			}
			// Function to concatenate utm url queries to all qualifying urls on the page on load
			function getQueryParams(url) {
				if (url.includes("?") == true && url.toLowerCase().includes("utm_inherit=false") == false) {
					const paramArr = url.slice(url.indexOf('?') + 1).split('&');
					let params = [];
					let counter = 0;
					// Check each query for utm parameters
					for (let i = 0; i < paramArr.length; i++) {
						if (paramArr[i].toLowerCase().includes("utm") == true) {
							let secondSplit = paramArr[i].split('=');
							params[counter] = secondSplit[0].toLowerCase() + "=" + secondSplit[1];
							counter++;
						}
					}
					let combinedUTMS = "";
					for (let i = 0; i < params.length; i++) {
						combinedUTMS = combinedUTMS + params[i] + "&";
					}
					let combinedUTMSFinal = removeTrailingAmpersand(combinedUTMS);
					// Go through each href and only concatenate qualify ones with utm parameters
					let allHrefs = getAllHrefs();
					for (let i = 0; i < allHrefs.length; i++) {
						let indexedUrl = allHrefs[i];
						if (indexedUrl != undefined && indexedUrl.href != undefined && indexedUrl.toString().includes(combinedUTMSFinal) == false && indexedUrl.toString().includes(domainToCheck) == true) {
							if (indexedUrl.href.toString().includes("?") == true) {

								allHrefs[i].href = indexedUrl.href + "&" + combinedUTMSFinal;
							} else {
								allHrefs[i].href = indexedUrl.href + "?" + combinedUTMSFinal;
							}
						}
					}
				}
			}
			getQueryParams(thisUrl);
		})
	</script>

<?php
}
add_action('wp_footer', 'footer_scripts');
