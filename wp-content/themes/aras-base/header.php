<?php
// The template for displaying the header
// This is the template that displays all of the <head> section
$blog_gtm_id = get_field('blog_tag_manager_id', 'option');
$main_site_gtm_id = get_field('main_site_tag_manager_id', 'option');

$gtm_id = (is_singular('post') || is_home() || is_category() || is_author() || is_tag())  ? $blog_gtm_id : $main_site_gtm_id;
?>
<!doctype html>
<html class="no-js" <?php language_attributes(); ?>>

<head>
	<?php if ($gtm_id ): ?>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','<?php echo $gtm_id ?>');</script>
<!-- End Google Tag Manager -->
	<?php endif; ?>
	<meta charset="utf-8">
	<!-- Force IE to use the latest rendering engine available -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!-- Mobile Meta -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta class="foundation-mq">

	<?php get_template_part('parts/head'); ?>

	<?php if (!function_exists('has_site_icon') || !has_site_icon()) { ?>
		<!-- If Site Icon isn't set in customizer -->
		<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
	<?php } ?>
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
	<?php
	
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/assets/slick/slick.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/assets/slick/slick-theme.css" />
	<!-- The script tag should live in the head of your page if at all possible -->
	<script type="text/javascript" async src=https://play.vidyard.com/embed/v4.js></script>

	<?php wp_head(); ?>
	
</head>

<body <?php body_class(); ?>>
	<?php if ($gtm_id) : ?>
		<!-- Google Tag Manager (noscript) -->
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $gtm_id; ?>"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<!-- End Google Tag Manager (noscript) -->
	<?php endif; ?>
	<script>
		async function trackView(theHref, message) {
			var thisHref = theHref.href;
			piTracker(message + ':  ' + thisHref);
		}
	</script>

	<div class="off-canvas-wrapper">
		<!-- Load off-canvas container -->
		<?php get_template_part('parts/nav', 'offcanvas-content-mobile'); ?>
		<div class="off-canvas-content" data-off-canvas-content>


			<?php if (get_field('header_type') == 'logo') : ?>
				<?php if (get_field('header_placement') == 'overlap') : ?>
					<?php $navformat = 'overlapnav'; ?>
				<?php elseif (get_field('header_placement') == 'top') : ?>
					<?php $navformat = 'toponlynav'; ?>
				<?php else : ?>
					<?php $navformat = ''; ?>
				<?php endif; ?>
				<?php if (get_field('logo_image_type') != 'none') : ?>
					<header class="header header-simplenav <?php echo $navformat; ?>" role="banner">
						<?php get_template_part('parts/nav', 'offcanvas-logonav'); ?>
					</header>
				<?php else : ?>
					<header class="header header-none" role="banner">
					</header>
				<?php endif; ?>
			<?php elseif (get_field('header_type') == 'simplenav') : ?>
				<?php if (get_field('header_placement') == 'overlap') : ?>
					<?php $navformat = 'overlapnav'; ?>
				<?php elseif (get_field('header_placement') == 'top') : ?>
					<?php $navformat = 'toponlynav'; ?>
				<?php else : ?>
					<?php $navformat = ''; ?>
				<?php endif; ?>
				<header class="header header-simplenav <?php echo $navformat; ?>" role="banner">
					<?php get_template_part('parts/nav', 'offcanvas-simplenav'); ?>
				</header>
			<?php elseif (get_field('header_type') == 'none') : ?>
				<header class="header header-none" role="banner">
				</header>
			<?php else : ?>
				<header class="header header-base" role="banner">
					<?php get_template_part('parts/nav', 'offcanvas-global'); ?>
				</header>
			<?php endif; ?>