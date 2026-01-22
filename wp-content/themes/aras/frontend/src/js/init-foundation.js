
jQuery(document).foundation();

try {
	const enableScroll = window.Foundation.Reveal.prototype._enableScroll;
	const disableScroll = window.Foundation.Reveal.prototype._disableScroll;

	window.Foundation.Reveal.prototype._enableScroll = function() {
		// set the scroll behavior to auto to prevent jumping
		jQuery('html').css('scroll-behavior', 'auto');
		enableScroll.apply(this, arguments);
		// wait a tick and restore the scroll behavior
		setTimeout(() => {
			jQuery('html').css('scroll-behavior', '');
		}, 1);
		
	};
}catch (e) {
	console.warn('Foundation Reveal not found on window.Foundation');
}