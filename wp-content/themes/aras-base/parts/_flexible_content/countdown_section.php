<?php
// add a function to know if we've been in this file before
if( !function_exists('aras_has_displayed_countdown_section') ){
	function aras_has_displayed_countdown_section()
	{
		static $been_here = false;
		if (! $been_here) {
			$been_here = true;
			return false;
		}
		return true;
	}
}
// Set up our variables
$content_before = get_sub_field('content_before');
$date_time = get_sub_field('date_time');
$timezone = get_sub_field('timezone');
$content_after = get_sub_field('content_after');
$hide_when_expired = get_sub_field('hide_when_expired');

$call_to_action_button = get_sub_field('call_to_action_button');

// create the actual DateTime
$datetime_string = $date_time . ' ' . $timezone;
$datetime = new DateTime($date_time, new DateTimeZone($timezone));
$now = new DateTime('now', new DateTimeZone($timezone));
$expired = $now > $datetime;
// If we are to hide when expired and it is expired, then return
if ($hide_when_expired && $expired) {
	return;
}

$side_content_position = get_sub_field('side_content_position');
$size_content = false;
if ($side_content_position != 'none') {
	$side_content = get_sub_field('side_content_html');
}


?>
<?php if (get_sub_field('show_for_this_language') == 'hide') : ?>
	<?php return; ?>
<?php endif; ?>
<?php $modnum = get_row_index(); ?>
<?php if (get_sub_field('anchor_link')) : ?>
	<?php $anchor = ('id="' . get_sub_field('anchor_link') . '"'); ?>
<?php else : ?>
	<?php $anchor = ('id="contentsection-' . $modnum . '"'); ?>
<?php endif; ?>
<?php
$background_color = get_sub_field('background_color');
$top_padding = get_sub_field('top_padding_amount');
$bottom_padding = get_sub_field('bottom_padding_amount');
$bg_color = '';
$toppadding = '';
$bottompadding = '';
switch ($background_color) {
	case 'transparent':
		$bg_color = 'bg-transparent';
		break;
	case 'white':
		$bg_color = 'bg-white';
		break;
	case 'grey':
		$bg_color = 'bg-grey';
		break;
	case 'dblue':
		$bg_color = 'bg-dblue';
		break;
	case 'whitetogrey':
		$bg_color = 'bg-whitetogrey';
		break;
	case 'greytowarm':
		$bg_color = 'bg-greytowarm';
		break;
	default:
		$bg_color = 'bg-white';
}
switch ($top_padding) {
	case 'large':
		$toppadding = 'largetoppadding';
		break;
	case 'medium':
		$toppadding = 'mediumtoppadding';
		break;
	case 'small':
		$toppadding = 'smalltoppadding';
		break;
	case 'none':
		$toppadding = 'notoppadding';
		break;
	default:
		$toppadding = 'mediumtoppadding';
}
switch ($bottom_padding) {
	case 'large':
		$bottompadding = 'largebottompadding';
		break;
	case 'medium':
		$bottompadding = 'mediumbottompadding';
		break;
	case 'small':
		$bottompadding = 'smallbottompadding';
		break;
	case 'none':
		$bottompadding = 'nobottompadding';
		break;
	default:
		$bottompadding = 'mediumbottompadding';
}
?>
<?php $horizontal_alignment = get_sub_field('horizontal_alignment');
// this is named backwards. horizontal_alignment is actually 'Vertical Alignment'
switch ($horizontal_alignment) {
	case 'top':
		$horiz = 'align-top';
		break;
	case 'middle':
		$horiz = 'align-middle';
		break;
	case 'bottom':
		$horiz = 'align-bottom';
		break;
	default:
		$horiz = 'align-middle';
}
?>
<?php if (get_sub_field('background_image')) : ?>
	<?php $background_image = get_sub_field('background_image'); ?>

	<?php $bg_placement = get_sub_field('background_image_position');
	switch ($bg_placement) {
		case 'topleft':
			$bgp = 'background-position: top left';
			break;
		case 'topcenter':
			$bgp = 'background-position: top center';
			break;
		case 'topright':
			$bgp = 'background-position: top right';
			break;
		case 'middleleft':
			$bgp = 'background-position: center left';
			break;
		case 'middlecenter':
			$bgp = 'background-position: center center';
			break;
		case 'middleright':
			$bgp = 'background-position: center right';
			break;
		case 'bottomleft':
			$bgp = 'background-position: bottom left';
			break;
		case 'bottomcenter':
			$bgp = 'background-position: bottom center';
			break;
		case 'bottomright':
			$bgp = 'background-position: bottom right';
			break;
		default:
			$bgp = 'background-position: top left';
	}
	?>
<?php endif; ?>

<?php $text_color = get_sub_field('text_color') ?: 'text-dark' ?>

<section class="content-section countdown-section <?= "$toppadding $bottompadding $bg_color $text_color" ?> <?php if (get_sub_field('background_image') != '') : ?>has-bg-img<?php endif; ?>" <?= "$anchor" ?> <?php if (get_sub_field('background_image')) : ?>title="<?php echo esc_attr($background_image['alt']); ?>" style="background-image: url(<?php echo esc_url($background_image['url']); ?>);min-height: calc((<?php echo ($background_image['height']); ?> / <?php echo ($background_image['width']); ?>) * 100vw);<?php echo $bgp; ?>" <?php endif; ?>>
	<?php get_template_part('parts/_template_parts/background_visual'); ?>
	<div class="grid-container">
		<div class="countdown-section__container">
			<div class="grid-x grid-padding-x <?php echo $horiz; ?> <?php if ($side_content_position != 'none') : ?>align-justify<?php else : ?>align-center<?php endif; ?> <?php if ($side_content_position != 'none') : ?>medium-flex-dir-row<?php else : ?>medium-flex-dir-column<?php endif; ?>">
				<?php if ($side_content_position == 'left' && $side_content) : ?>
					<div class="cell small-12 medium-5 large-4 countdown-section__side-content <?php if ($size_content) : ?>sized-content<?php endif; ?>">
						<div class="wysiwyg-content">
							<?php echo $side_content; ?>
						</div>
					</div>
				<?php endif; ?>
				<div class="cell small-12 <?php if ($side_content) : ?>medium-6 large-8<?php endif; ?> countdown-section__countdown">

					<?php if( $content_before ): ?>
						<div class="countdown-section__content-before wysiwyg-content">
							<?php echo $content_before; ?>
						</div>
					<?php endif; ?>
					<div class="countdown-timer" data-datetime="<?php echo esc_attr($datetime->format('Y-m-d H:i:s')); ?>" data-timezone="<?php echo esc_attr($timezone); ?>" data-expired-message="<?php echo esc_attr(get_sub_field('expired_message') ?: 'This event has passed.'); ?>">
						<div class="countdown-segment">
							<span class="countdown-number" data-countdown="days">00</span>
							<span class="countdown-label">Days</span>
						</div>
						<div class="countdown-segment">
							<span class="countdown-number" data-countdown="hours">00</span>
							<span class="countdown-label">Hours</span>
						</div>
						<div class="countdown-segment">
							<span class="countdown-number" data-countdown="minutes">00</span>
							<span class="countdown-label">Minutes</span>
						</div>
						<div class="countdown-segment">
							<span class="countdown-number" data-countdown="seconds">00</span>
							<span class="countdown-label">Seconds</span>
						</div>
					</div>

					<?php if( $content_after ): ?>
						<div class="countdown-section__content-after wysiwyg-content">
							<?php echo $content_after; ?>
						</div>
					<?php endif; ?>

					<?php if( $call_to_action_button ){ ?>
						<div class="countdown-section__cta-button">
							<a
								href="<?php echo esc_url($call_to_action_button['url']); ?>"
								class="aras-button" 
								<?php if( $call_to_action_button['target'] ){ ?>
									target="<?php echo esc_attr($call_to_action_button['target']); ?>"
								<?php } ?>
							>
								<?php echo esc_html($call_to_action_button['title'] ?: 'Learn More'); ?>
							</a>
						</div>
					<?php } ?>
				</div>
				<?php if ($side_content_position == 'right' && $side_content) : ?>
					<div class="cell small-12 medium-5 large-4 countdown-section__side-content <?php if ($size_content) : ?>sized-content<?php endif; ?>">
						<div class="wysiwyg-content">
							<?php echo $side_content; ?>
						</div>
					</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
</section>

<?php
if (aras_has_displayed_countdown_section()) {
	// only load script/style once
	return;
}
?>

<style>
	.countdown-section__container {
		max-width: 1200px;
		margin: 0 auto;
	}

	.countdown-section__container > .grid-x {
		row-gap: 3rem;
	}
	.countdown-section__side-content {
		margin-bottom: 1.5rem;
	}

	.countdown-section__countdown {
		text-align: center;
		display: grid;
		gap: 2rem;
	}

	.countdown-section__cta-button .aras-button {
		margin-top: 0rem;

	}


	:root {
		--aras-countdown-size: 1.5rem;
	}
	.countdown-timer {
		display: flex;
		justify-content: center;
		gap: 1rem;
		flex-wrap: no-wrap;
		font-size: var(--aras-countdown-size);
		color: var(--aras-countdown-color, inherit);
	}

	.countdown-segment {
		flex: 1 1 3em;
		max-width: 3.5em;
	}

	.countdown-number {
		display: block;
		font-size: 2em;
		font-weight: bold;
		line-height: 1;
	}

	.countdown-label {
		display: block;
		font-size: 0.5em;
		text-transform: uppercase;
	}

	@media (min-width: 640px) {
		:root{
			--aras-countdown-size: 2rem;
		}
	}

	@media (min-width: 1024px) {
		:root{
			--aras-countdown-size: 2.5rem;
		}
	}

	@media (min-width: 1200px) {
		:root{
			--aras-countdown-size: 3rem;
		}
	}
</style>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const countdownElements = document.querySelectorAll('.countdown-timer');
		countdownElements.forEach(function(countdown) {
			const targetDateStr = countdown.getAttribute('data-datetime');
			const timezone = countdown.getAttribute('data-timezone');
			const expiredMessage = countdown.getAttribute('data-expired-message') || 'This event has passed.';
			const targetDate = new Date(new Date(targetDateStr + ' UTC').toLocaleString("en-US", {
				timeZone: timezone
			}));

			function updateCountdown() {
				const now = new Date(new Date().toLocaleString("en-US", {
					timeZone: timezone
				}));
				const diff = targetDate - now;

				if (diff <= 0) {
					countdown.innerHTML = `<div class="countdown-expired">${expiredMessage}</div>`;
					return;
				}

				const days = Math.floor(diff / (1000 * 60 * 60 * 24));
				const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
				const minutes = Math.floor((diff / (1000 * 60)) % 60);
				const seconds = Math.floor((diff / 1000) % 60);

				countdown.querySelector('[data-countdown="days"]').textContent = String(days).padStart(2, '0');
				countdown.querySelector('[data-countdown="hours"]').textContent = String(hours).padStart(2, '0');
				countdown.querySelector('[data-countdown="minutes"]').textContent = String(minutes).padStart(2, '0');
				countdown.querySelector('[data-countdown="seconds"]').textContent = String(seconds).padStart(2, '0');
			}

			updateCountdown();
			setInterval(updateCountdown, 1000);
		});
	});
</script>