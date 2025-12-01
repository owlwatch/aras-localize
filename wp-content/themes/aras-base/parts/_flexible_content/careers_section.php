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
$background_color = get_sub_field('background_color')?:'white';
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
		$horiz = 'align-top';
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
<section class="<?= "$toppadding $bottompadding $bg_color $text_color" ?> <?php if (get_sub_field('background_image') != '') : ?>has-bg-img<?php endif; ?>" <?= "$anchor" ?> <?php if (get_sub_field('background_image')) : ?>title="<?php echo esc_attr($background_image['alt']); ?>" style="background-image: url(<?php echo esc_url($background_image['url']); ?>);min-height: calc((<?php echo ($background_image['height']); ?> / <?php echo ($background_image['width']); ?>) * 100vw);<?php echo $bgp; ?>" <?php endif; ?>>
	<?php get_template_part('parts/_template_parts/background_visual'); ?>
	<div class="grid-container">
		<?php if (get_sub_field('content_before')) : ?>
			<div class="grid-x grid-padding-x <?php if (get_sub_field('content_before_position') == 'center') : ?>align-center<?php endif; ?>">
				<div class="cell small-12 content-before">
					<div class="wysiwyg-content"><?php echo get_sub_field('content_before'); ?></div>
				</div>
			</div>
		<?php endif; ?>
		
		<div class="grid-x grid-padding-x <?php if (get_sub_field('content_before_position') == 'center') : ?>align-center<?php endif; ?>">
			<div class="cell small-12">
				<?php

				$cache = wp_cache_get( 'bamboo_listings', 'bamboo');
				if( !$cache || !isset($cache['jobs']) || empty( $cache['jobs']) ) {
					$cache = [];
				
					// get our bamboo api key from acf options
					$api_key = get_field('bamboo_api_key', 'option');

					$params = http_build_query( array(
						'statusGroups' => 'Open',
					));

					// get our bamboo jobs from api
					$response = wp_remote_get( 
						'https://aras.bamboohr.com/api/v1/applicant_tracking/jobs?'.$params,
						array(
							'headers' => array(
								'Accept'        => 'application/json',
								'Authorization' => 'Basic ' . base64_encode( $api_key . ':x' ),
							),
						) 
					);

					// example response data
					// [{
					// 	"id": 197,
					// 	"title": {
					// 		"id": null,
					// 		"label": "Software Developer ECAD (m/f/d)"
					// 	},
					// 	"postedDate": "2025-07-11T10:53:43+00:00",
					// 	"location": {
					// 		"id": 19307,
					// 		"label": "Dresden, Saxony",
					// 		"address": {
					// 			"name": "XPLM Dresden, Germany",
					// 			"description": null,
					// 			"addressLine1": "Altmarkt-Galerie Dresden",
					// 			"addressLine2": "Altmarkt 25",
					// 			"city": "Dresden",
					// 			"state": "Saxony",
					// 			"zipcode": "01067",
					// 			"country": "Germany",
					// 			"phoneNumber": null
					// 		}
					// 	},
					// 	"department": {
					// 		"id": 18583,
					// 		"label": "300 - Development"
					// 	},
					// 	"status": {
					// 		"id": 1,
					// 		"label": "Open"
					// 	},
					// 	"hiringLead": {
					// 		"employeeId": 834,
					// 		"firstName": "Thomas",
					// 		"lastName": "Müller",
					// 		"avatar": "https://images7.bamboohr.com/399522/photos/834-6-4.jpg?Policy=eyJTdGF0ZW1lbnQiOlt7IlJlc291cmNlIjoiaHR0cHM6Ly9pbWFnZXM3LmJhbWJvb2hyLmNvbS8zOTk1MjIvKiIsIkNvbmRpdGlvbiI6eyJEYXRlR3JlYXRlclRoYW4iOnsiQVdTOkVwb2NoVGltZSI6MTc2Mzk0NjQ1OH0sIkRhdGVMZXNzVGhhbiI6eyJBV1M6RXBvY2hUaW1lIjoxNzY2NTM4NDY4fX19XX0_&Signature=dRk~NjdVWC5bcyEWGqD9t~NjMExyCHjDaW6E3zX8KgSzQs1S02xMjzWHxATkA~DAyp01DrwhIu9-udGGPy2hALWAzjxGBFWtu7-JmfYoh2gjiaJwG1BZg-tc1AuqH1TstlD~hPHKRmJkLTNbmhwNZcVtFpjqq89ucR3P~B22FpKgtejc~mSdCTkux2rNCU7bg~5aCgFDe-WBHCww~~NSmMsYv3crlkZsArWHdE6MXUvn4sHl7sHvJjYxI3ZnYYMCmcnToJb7zIa75lhLb2~aS6AIj5ynHdpGKSHHrVPwMqC1ELwZ4fkgHBZyqvCo6jSWcxW5wNtL~lOquxNpqXpe9w__&Key-Pair-Id=APKAIZ7QQNDH4DJY7K4Q",
					// 		"jobTitle": {
					// 			"id": null,
					// 			"label": null
					// 		}
					// 	},
					// 	"newApplicantsCount": 57,
					// 	"activeApplicantsCount": 58,
					// 	"totalApplicantsCount": 94,
					// 	"postingUrl": "https://aras.bamboohr.com/jobs/view.php?id=197"
					// 	}]
					

					$jobs = [];
					$departments = [];

					if ( !is_wp_error( $response ) ) {
						
						$body = wp_remote_retrieve_body( $response );
						$data = json_decode( $body, true );

						

						// lets sort these out
						foreach( $data as $job ) {

							// we need to filter out any jobs that
							// have ['location']['address']['name'] contains xplm
							if ( strpos( strtolower( $job['location']['address']['name'] ), 'xplm' ) !== false ) {
								continue;
							}

							$jobs[] = $job;
							$department = $job['department']['label'];
							if( $departments[$department] === null ) {
								$departments[$department] = [];
							}
							$departments[$department][] = $job;
						}
					}

					ksort( $departments );
					$cache = ['jobs' => $jobs, 'departments' => $departments];
					// cache for 1 hour
					if( !empty($jobs) ){
						wp_cache_set( 'bamboo_listings', $cache, 'bamboo', 3600 ); 
					}
				}
				else {
					$jobs = $cache['jobs'];
					$departments = $cache['departments'];
				}
				?>
				<div class="job-listings">
				<?php
				foreach ($departments as $department_name => $department_jobs) : ?>
					<h2 class="job-listings__department department-name"><?php echo esc_html( $department_name ); ?></h2>
					<ul class="job-listings__list">
						<?php foreach ( $department_jobs as $job ) : ?>
							<?php
							$url = $job['postingUrl'];
							// this is in the format https://aras.bamboohr.com/jobs/view.php?id=197
							// but we want to convert it to https://aras.bamboohr.com/careers/197
							$parsed_url = parse_url( $url );
							parse_str( $parsed_url['query'], $query_params );
							$job_id = $query_params['id'];
							$new_url = 'https://aras.bamboohr.com/careers/' . $job_id;
							?>
							<li class="job-listing">
								<a class="job-listing__link" href="<?php echo esc_url( $new_url ); ?>" target="_blank" rel="noopener noreferrer">
									<h3 class="job-listing__title"><?php echo esc_html( $job['title']['label'] ); ?></h3>
									<span class="job-listing__location">
										<?php
										$location = $job['location']['label'];
										if( $location ){
											$location = sprintf( __('%s (Hybrid)', 'aras'), $location );
										}
										else {
											$location = __('Remote', 'aras');
										}
										echo esc_html( $location ); 
										?>
									</span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>
