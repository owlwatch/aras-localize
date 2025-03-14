<?php
namespace Aras\Marketplace;
use Aras\Marketplace\Service\Template;
use function Aras\Marketplace\app;
app()->templateService->enqueue_style();
get_header();

Template::get_template_part('marketplace/banner');


while( have_posts() ): the_post();
// get the logo; if no logo, use the contributor image
$logo = get_post_thumbnail_id();

if( !$logo ){
	$contributor = get_first_term('mp-contributor');
	if( $contributor ){
		$logo = get_field('default_solution_icon', $contributor);
		if( $logo ){
			$logo = $logo['ID'];
		}
		else {
			$logo = get_field('logo', $contributor);
			if( $logo ){
				$logo = $logo['ID'];
			}
		}
	}
}

$release_date = get_field('release_date');
if( $release_date ){
	$release_date = date('F j, Y', strtotime($release_date));
}

$specs = [
	__('Release Version', 'asa-marketplace') => get_field('release_version'),
	__('Release Date', 'asa-marketplace')    => $release_date
];

$release_notes_type = get_field('release_notes_type');
switch( $release_notes_type ){
	case 'download':
		$release_notes = get_field('release_notes_file');
		if( $release_notes ){
			$release_notes = $release_notes['url'];
		}
		break;
	case 'link':
		$release_notes = get_field('release_notes_link');
		break;
	default:
		$release_notes = false;
}
if( $release_notes ){
	$specs[__('Release Notes', 'asa-marketplace')] = 
		'<a href="'.$release_notes.'" target="_blank">'.__('View Release Notes', 'asa-marketplace').'</a>';
}

$specs[__('Category', 'asa-marketplace')] = get_the_term_list( get_the_ID(), 'mp-solution-category' );

$versions = get_the_terms( get_the_ID(), 'mp-aras-version' );

if( !empty($versions) ){
	usort( $versions, function($a, $b){
		return version_compare($a->name, $b->name);
	});
	$specs[__('Supported Aras Versions', 'asa-marketplace')] = implode(', ', array_map(function($version){
		return $version->name;
	}, $versions));
}

$is_integration = get_field('is_integration');
if( $is_integration ){
	$specs[
		sprintf( 
			__('Supported Versions of %s', 'asa-marketplace'), 
			get_field('integration_tool')
		)
	] = get_field('integration_tool_supported_versions');
}

$languages = get_the_terms( get_the_ID(), 'mp-language' );
if( !empty($languages) ){
	$specs[__('Supported Languages', 'asa-marketplace')] = implode(', ', array_map(function($language){
		return $language->name;
	}, $languages));
}

$support_link = get_field('support_link');
if( $support_link ){
	$specs[__('Support', 'asa-marketplace')] = '<a href="'.$support_link.'" target="_blank">'.__('Get Support', 'asa-marketplace').'</a>';
}

$license_agreement_link = get_field('license_agreement_link');
if( $license_agreement_link ){
	$specs[__('Legal', 'asa-marketplace')] = '<a href="'.$license_agreement_link.'" target="_blank">'.__('View License Agreement', 'asa-marketplace').'</a>';
}

// get media
$media = [];
$images = get_field('images');
$videos = get_field('videos');

if( !empty($images) ){
	foreach( $images as $image ){
		$media[] = [
			'type' => 'image',
			'full' => $image['url'],
			'large' => $image['sizes']['large'],
			'image'  => $image['url'],
			'alt' => $image['alt']
		];
	}
}

if( !empty($videos) ){
	foreach( $videos as $video ){
		$media[] = [
			'type' => 'video',
			'image' => Template::getYoutubeThumbnailUrlFromVideoUrl($video['youtube_link']),
			'youtube_url'  => $video['youtube_link']
		];
	}
}	

?>
<section class="mediumtoppadding mediumbottompadding">
	<div class="grid-container">
		<div class="mp-solution-page">

		<div class="mp-solution-page__content-column">
				<h1 class="mp-solution-page__title h2">
					<?php the_title(); ?>
				</h1>
				<div class="mp-solution-page__contributor">
					<?php
					$contributor = get_first_term('mp-contributor');
					if( $contributor ){
						?>
						By 
						<a href="<?php echo get_term_link( $contributor ); ?>">
							<?php echo $contributor->name; ?>
						</a>
						<?php
					}
					?>	
				</div>

				<?php
				if( !empty( $media ) ){
					?>	
				<div class="mp-solution-page__gallery" data-mp-solution-gallery="<?php echo esc_attr( json_encode( ['media'=>$media] ) ) ?>">
					
				</div>
					<?php
				}
				?>

				<div class="mp-solution-page__content">
					<?php the_content(); ?>
				</div>
			</div>
			
			<div class="mp-solution-page__specification-column">
				<?php
				if( $logo ){
					?>
					<a href="<?php echo get_term_link( $contributor ); ?>">
						<?php
						echo wp_get_attachment_image($logo, 'medium', false, [
							'class' => 'mp-solution-page__logo'
						]);
						?>
					</a>
					<?php
				}
				// if this is downloadable, show a button
				if( get_field('is_downloadable') ){
					?>
					<a href="<?php echo get_field('download_link'); ?>" target="_blank" class="aras-button mp-solution-page__download-button">
						<?php _e('Get it', 'aras-marketplace'); ?>
					</a>
					<?php
				}
				?>

				<?php
				// show the "type"
				$type = get_first_term('mp-solution-type');
				if( $type ){
					?>
					<div class="mp-type-label">
						<?php
						$icon = get_field('icon', $type);
						if( $icon ){
							echo wp_get_attachment_image( $icon['ID'], 'medium', false, [
								'class' => 'mp-type-label__icon'
							]);
						}
						?>
						<span class="mp-type-label__name">
							<?php echo $type->name; ?>
						</span>
					</div>
					<?php
				}
				?>

				<div class="mp-solution-page__specifications">
					<?php
					foreach( $specs as $label => $value ){
						if( $value ){
							?>
							<div class="mp-solution-page__specification">
								<div class="mp-solution-page__specification-label">
									<?php echo $label; ?>
								</div>
								<div class="mp-solution-page__specification-value">
									<?php echo $value; ?>
								</div>
							</div>
							<?php
						}
					}
					?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php

endwhile;
get_footer();