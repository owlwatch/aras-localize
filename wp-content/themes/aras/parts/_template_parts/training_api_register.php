<?php
$site_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$data = load_data_from_file('Web_Get_Class_Data.json');
if ($data != null) {
	$timezones = array(
		'GMT+1' => 'Central Europe (GMT+1)',
		'GMT+9' => 'Japan (GMT+9)',
		'GMT-5' => 'Eastern US (GMT-5)',
	);
	$currentTimestamp = time();
	//FILTER ARRAYS
?>
	<?php foreach ($data['Item'] as $record) : ?>
		<?php if (isset($record['Relationships']['Item'])) : ?>
			<?php $relationships_item = $record['Relationships']['Item']; ?>
			<?php if (is_array($relationships_item)) : ?>
				<?php foreach ($relationships_item as $relationship) : ?>
					<?php if (isset($relationship['state']) && $relationship['state'] == 'Planned') : ?>
						<?php
						$tempArray = [
							'aras_type' => isset($relationship['@aras.type']) ? str_replace('_', ' ', $relationship['@aras.type']) : '',
							'_class_name' => isset($relationship['_class_name']['#text']) ? str_replace('_', ' ', $relationship['_class_name']['#text']) : '',
							'id' => isset($relationship['id']['#text']) ? $relationship['id']['#text'] : '',
							'_class_language' => isset($relationship['_class_language']) ? $relationship['_class_language'] : '',
							'_delivery_method' => isset($relationship['_delivery_method']) ? ($relationship['_delivery_method'] === 'Virtual' ? 'Virtual: Instructor-led' : $relationship['_delivery_method']) : '',
							'_student_role' => isset($relationship['_student_role']) ? $relationship['_student_role'] : '',
							'_class_duration' => isset($relationship['_class_duration']) ? $relationship['_class_duration'] : '',
							'startdatelong' => isset($relationship['startdatelong']) ? $relationship['startdatelong'] : '',
							'state' => isset($relationship['state']) ? $relationship['state'] : '',
							'coursecost' => isset($relationship['coursecost']) ? $relationship['coursecost'] : '',
							'_class_description' => isset($relationship['_class_description']['#text']) ? $relationship['_class_description']['#text'] : '',
							'location' => isset($relationship['location']) ? $relationship['location'] : '',
							'location_long' => isset($relationship['location']) ? ($relationship['location'] === 'Remote' ? 'Remote, Anywhere' : $relationship['location']) : '',
							'_timezone' => isset($relationship['_timezone']) ? $relationship['_timezone'] : '',
							'currency' => isset($relationship['currency']) ? $relationship['currency'] : '',
							'pdf_id' => isset($relationship['_class_pdf']['Item']['id']['#text']) ? $relationship['_class_pdf']['Item']['id']['#text'] : '',
						];
						$trainingItems[] = $tempArray;

						//if (str_contains($site_url, '/ja-jp/')) {
						//	$tempArray['_delivery_method'] = isset($relationship['_delivery_method']) ? ($relationship['_delivery_method'] === 'Virtual' ? 'Virtual: Instructor-led Japan' : $relationship['_delivery_method']) : '';
						//	$tempArray['location_long'] = isset($relationship['location']) ? ($relationship['location'] === 'Remote' ? 'Remote, Anywhere Japan' : $relationship['location']) : '';
						//} else {
						//	$tempArray['_delivery_method'] = isset($relationship['_delivery_method']) ? ($relationship['_delivery_method'] === 'Virtual' ? 'Virtual: Instructor-led' : $relationship['_delivery_method']) : '';
						//	$tempArray['location_long'] = isset($relationship['location']) ? ($relationship['location'] === 'Remote' ? 'Remote, Anywhere' : $relationship['location']) : '';
						//}

						?>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		<?php endif; ?>
	<?php endforeach; ?>
	<?php foreach ($data['Item'] as $record) : ?>
		<?php
		if (array_key_exists('Relationships', $record)) {
			if (isset($record['Relationships']['Item']['state']) == 'Planned') {
				$tempArray = [
					'aras_type' => isset($record['Relationships']['Item']['@aras.type']) ? str_replace('_', ' ', $record['Relationships']['Item']['@aras.type']) : '',
					'_class_name' => isset($record['Relationships']['Item']['_class_name']['#text']) ? str_replace('_', ' ', $record['Relationships']['Item']['_class_name']['#text']) : '',
					'id' => isset($record['Relationships']['Item']['id']['#text']) ? $record['Relationships']['Item']['id']['#text'] : '',
					'_class_language' => isset($record['Relationships']['Item']['_class_language']) ? $record['Relationships']['Item']['_class_language'] : '',
					'_delivery_method' => isset($record['Relationships']['Item']['_delivery_method']) ? ($record['Relationships']['Item']['_delivery_method'] === 'Virtual' ? 'Virtual: Instructor-led' : $record['Relationships']['Item']['_delivery_method']) : '',
					'_student_role' => isset($record['Relationships']['Item']['_student_role']) ? $record['Relationships']['Item']['_student_role'] : '',
					'_class_duration' => isset($record['Relationships']['Item']['_class_duration']) ? $record['Relationships']['Item']['_class_duration'] : '',
					'startdatelong' => isset($record['Relationships']['Item']['startdatelong']) ? $record['Relationships']['Item']['startdatelong'] : '',
					'state' => isset($record['Relationships']['Item']['state']) ? $record['Relationships']['Item']['state'] : '',
					'coursecost' => isset($record['Relationships']['Item']['coursecost']) ? $record['Relationships']['Item']['coursecost'] : '',
					'_class_description' => isset($record['Relationships']['Item']['_class_description']['#text']) ? $record['Relationships']['Item']['_class_description']['#text'] : '',
					'location' => isset($record['Relationships']['Item']['location']) ? $record['Relationships']['Item']['location'] : '',
					'location_long' => isset($record['Relationships']['Item']['location']) ? ($record['Relationships']['Item']['location'] === 'Remote' ? 'Remote, Anywhere' : $record['Relationships']['Item']['location']) : '',
					'_timezone' => isset($record['Relationships']['Item']['_timezone']) ? $record['Relationships']['Item']['_timezone'] : '',
					'currency' => isset($record['Relationships']['Item']['currency']) ? $record['Relationships']['Item']['currency'] : '',
					'pdf_id' => isset($record['Relationships']['Item']['_class_pdf']['Item']['id']['#text']) ? $record['Relationships']['Item']['_class_pdf']['Item']['id']['#text'] : '',
				];
				$trainingItems[] = $tempArray;

				//if (str_contains($site_url, '/ja-jp/')) {
				//	$tempArray['_delivery_method'] = isset($record['Relationships']['Item']['_delivery_method']) ? ($record['Relationships']['Item']['_delivery_method'] === 'Virtual' ? 'Virtual: Instructor-led Japanese' : $record['Relationships']['Item']['_delivery_method']) : '';
				//	$tempArray['location_long'] = isset($record['Relationships']['Item']['location']) ? ($record['Relationships']['Item']['location'] === 'Remote' ? 'Remote, Anywhere Japanese' : $record['Relationships']['Item']['location']) : '';
				//} else {
				//	$tempArray['_delivery_method'] = isset($record['Relationships']['Item']['_delivery_method']) ? ($record['Relationships']['Item']['_delivery_method'] === 'Virtual' ? 'Virtual: Instructor-led' : $record['Relationships']['Item']['_delivery_method']) : '';
				//	$tempArray['location_long'] = isset($record['Relationships']['Item']['location']) ? ($record['Relationships']['Item']['location'] === 'Remote' ? 'Remote, Anywhere' : $record['Relationships']['Item']['location']) : '';
				//}
			}
		}
		?>
	<?php endforeach; ?>


	<?php if (isset($_GET['occurrenceid'])) {
		$occurrenceid = $_GET['occurrenceid'];
		$occurrenceFound = false;
		foreach ($trainingItems as $item) :

			if ($item['id'] == $occurrenceid) : ?>
				<?php
				if (isset($item['_class_duration']) && $item['_class_duration'] != '') {
					$_class_duration = $item['_class_duration'];
					$startdatelong = $item['startdatelong'];
					if ($_class_duration == 1) {
						$classdates = $startdatelong;
					} elseif ($_class_duration >= 2) {
						$endDate = date('M d, Y', strtotime($startdatelong . ' +' . ($_class_duration - 1) . ' days'));
						$classdates = $startdatelong . ' - ' . $endDate;
					}
					if (str_contains($site_url, '/ja-jp/')) {
						if ($_class_duration == 1) {
							$class_duration_long = '1 日';
						} else {
							$class_duration_long = $_class_duration . ' 日';
						}
					} else {
						if ($_class_duration == 1) {
							$class_duration_long = '1 Day';
						} else {
							$class_duration_long = $_class_duration . ' Days';
						}
					}
				}
				if (isset($item['_timezone']) && $item['_timezone'] != '') {
					$_timezone = $item['_timezone'];
					if (isset($timezones[$_timezone])) {
						$timezone_locale = $timezones[$_timezone];
					} else {
						$timezone_locale = $_timezone;
					}
				}
				?>
				<main class="training-single">
					<section id="gated-hero" class="gated-hero hero-banner">
						<div class="grid-container">
							<div class="grid-x grid-padding-x align-top">
								<div class="cell small-12 small-12 medium-6 large-7 hero-content">
									<div class="hero-content-inner">
										<?php if (get_field('training_course_headline')) : ?>
											<h1 class="hero-headline"><?php echo get_field('training_course_headline'); ?></h1>
										<?php else : ?>
											<h1 class="hero-headline">Register for Training</h1>
										<?php endif; ?>
									</div>
								</div>
								<div class="cell small-12 medium-6 large-5 hero-form-side">
									<div id="hero-form-container" class="hero-form-container">
										<?php if (get_field('form_shortcode')) : ?>
											<?php if (isset($_GET['sign_up_on_site']) && $_GET['sign_up_on_site'] === 'true') : ?>
												<div class="hero-form">
													<?php if (get_field('form_headline')) : ?>
														<h4 class="hero-form-headline"><?php echo get_field('form_headline')  ?></h4>
													<?php endif; ?>
													<?php $gravity_form_id = get_field('form_shortcode');
													echo do_shortcode('[gravityform id="' . $gravity_form_id . '" title="false" description="false"]'); ?>
												</div>
												<?php get_template_part('parts/_template_parts/gform_variables'); ?>
											<?php else : ?>
												<div class="hero-form">
													<?php if (get_field('subscribers_button')) { ?>
														<a aria-label="<?php echo get_field('subscribers_button'); ?>" class="training-signup-button aras-button" target="_blank" href="https://www.aras.com/community/subscriber-portal/p/login?returnURL=%2Fcommunity%2Fsubscriber-portal%2Fp%2Ftraining-calendar%3Fevent%3D<?php echo $item['id']; ?>">
															<?php echo get_field('subscribers_button'); ?>
														</a>
													<?php } ?>
													<?php if (get_field('partners_button')) { ?>
														<a aria-label="<?php echo get_field('partners_button'); ?>" class="training-signup-button aras-button" target="_blank" href="https://www.aras.com/community/partner-portal/p/login?returnURL=%2Fcommunity%2Fpartner-portal%2Fp%2Ftraining-calendar%3Fevent%3D<?php echo $item['id']; ?>">
															<?php echo get_field('partners_button'); ?>
														</a>
													<?php } ?>
													<?php if (get_field('form_load_button_text')) { ?>
														<button aria-label="<?php echo get_field('form_load_button_text'); ?>" class="training-signup-button-nolink" onclick="reloadWithSignUpOnSite()">
															<?php echo get_field('form_load_button_text'); ?>
														</button>
													<?php } else { ?>
														<button aria-label="Not a partner or subscriber, click here" class="training-signup-button-nolink" onclick="reloadWithSignUpOnSite()">
															Not a partner or subscriber, click here
														</button>
													<?php };	?>
												</div>
											<?php endif; ?>
										<?php else : ?>
											<div class="hero-form">
												<?php if (get_field('subscribers_button')) { ?>
													<a aria-label="<?php echo get_field('subscribers_button'); ?>" class="training-signup-button aras-button" target="_blank" href="https://www.aras.com/community/subscriber-portal/p/login?returnURL=%2Fcommunity%2Fsubscriber-portal%2Fp%2Ftraining-calendar%3Fevent%3D<?php echo $item['id']; ?>">
														<?php echo get_field('subscribers_button'); ?>
													</a>
												<?php } ?>
												<?php if (get_field('partners_button')) { ?>
													<a aria-label="<?php echo get_field('partners_button'); ?>" class="training-signup-button aras-button" target="_blank" href="https://www.aras.com/community/partner-portal/p/login?returnURL=%2Fcommunity%2Fpartner-portal%2Fp%2Ftraining-calendar%3Fevent%3D<?php echo $item['id']; ?>">
														<?php echo get_field('partners_button'); ?>
													</a>
												<?php } ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</div>
					</section>
					<div class="grid-container smalltoppadding largebottompadding">
						<div class="grid-x grid-padding-x">
							<div class="cell small-12 medium-6 large-7 content-section">
								<?php $link = get_field('archive_page');
								if ($link) : $link_url = $link['url'];
									$link_title = $link['title'];
									$link_target = $link['target'] ? $link['target'] : '_self';
								?>
									<a aria-label="<?php echo esc_attr($link_title); ?>" class="backlink-link" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
										<h6><?php echo esc_html($link_title); ?>&nbsp;→</h6>
									</a>
								<?php else : ?>
									<button aria-label="Back to Training Classes" class="backlink-link" onclick="reloadWithoutParams()">
										<h6>Back to Training Classes&nbsp;→</h6>
									</button>
								<?php endif; ?>

								<?php if (str_contains($site_url, '/ja-jp/')) { ?>
									<p class="training-data">セッション: <strong><?php echo $item['_class_name']; ?></strong></p>
									<p class="training-data">開催日: <strong><?php echo $classdates; ?></strong></p>
									<div class="training-single-twocol">
										<p class="training-data">Time Zone: <strong><?php echo $timezone_locale; ?></strong></p>
										<p class="training-data">日: <strong><?php echo isset($item['_class_duration']) ? $item['_class_duration'] : ''; ?></strong></p>
										<?php if ($item['location'] == 'Tokyo') {
											$location = '東京';
										} else {
											$location = isset($item['location']) ? $item['location_long'] : '';;
										}; ?>
										<p class="training-data">場所: <strong><?php echo $location; ?></strong></p>
										<p class="training-data">費用: <strong><?php echo isset($item['currency']) ? $item['currency'] : ''; ?><?php echo isset($item['coursecost']) ? $item['coursecost'] : ''; ?></strong></p>
										<?php if ($item['_delivery_method'] == 'Virtual: Instructor-led') {
											$_delivery_method = '講師によるオンライントレーニング';
										} else {
											$_delivery_method = isset($item['_delivery_method']) ? $item['_delivery_method'] : '';
										}; ?>
										<p class="training-data">形式: <strong><?php echo $_delivery_method; ?></strong></p>
										<?php if ($item['_student_role'] == 'System Administrator') {
											$_student_role = 'システム管理者';
										} elseif ($item['_student_role'] == 'Computer Programmer') {
											$_student_role = 'プログラマー';
										} else {
											$_student_role = isset($item['_student_role']) ? $item['_student_role'] : '';
										}; ?>
										<p class="training-data">対象者: <strong><?php echo $_student_role; ?></strong></p>
									</div>
									<?php $upload_dir = wp_upload_dir(); ?>
									<a aria-label="コースのPDFをダウンロード" class="aras-button" target="_blank" href="<?php echo $upload_dir['baseurl']; ?>/training/<?php echo $item['pdf_id']; ?>.pdf">コースのPDFをダウンロード</a>
									<div class="training-single-description wysiwyg-content">
										<h5><strong>詳細</strong></h5>
										<?php echo isset($item['_class_description']) ? $item['_class_description'] : ''; ?>
										<?php if (get_field('post_description_content')) {
											echo get_field('post_description_content');
										} ?>
										<?/*
										<p><strong>Aras のサブスクリプションを契約いただくと、Aras Innovator ソフトウェアのトレーニングを無料で受講いただけます。</strong></p>
										<p><strong><em>トレーニングを受講希望の方は、登録フォームに必要事項をご記入ください。</em></strong></p>
										*/ ?>
									</div>
								<?php } else { ?>
									<p class="training-data">Session: <strong><?php echo $item['_class_name']; ?></strong></p>
									<p class="training-data">Dates: <strong><?php echo $classdates; ?></strong></p>
									<div class="training-single-twocol">
										<p class="training-data">Time Zone: <strong><?php echo $timezone_locale; ?></strong></p>
										<p class="training-data">Days: <strong><?php echo isset($item['_class_duration']) ? $item['_class_duration'] : ''; ?></strong></p>
										<p class="training-data">Location: <strong><?php echo isset($item['location']) ? $item['location'] : ''; ?></strong></p>
										<p class="training-data">Cost: <strong><?php echo isset($item['currency']) ? $item['currency'] : ''; ?><?php echo isset($item['coursecost']) ? $item['coursecost'] : ''; ?></strong></p>
										<p class="training-data">Delivery Method: <strong><?php echo isset($item['_delivery_method']) ? $item['_delivery_method'] : ''; ?></strong></p>
										<p class="training-data">Student Role: <strong><?php echo isset($item['_student_role']) ? $item['_student_role'] : ''; ?></strong></p>
									</div>
									<?php $upload_dir = wp_upload_dir(); ?>
									<a aria-label="Download the Course PDF" class="aras-button" target="_blank" href="<?php echo $upload_dir['baseurl']; ?>/training/<?php echo $item['pdf_id']; ?>.pdf">Download the Course PDF</a>
									<div class="training-single-description wysiwyg-content">
										<h6><strong>Description</strong></h6>
										<?php echo isset($item['_class_description']) ? $item['_class_description'] : ''; ?>
										<?php if (get_field('post_description_content')) {
											echo get_field('post_description_content');
										} ?>
										<?/*
										<p><strong>Aras Innovator Software Training is Free for Aras Subscribers</strong></p>
										<p><strong><em>Please complete the form to request registration in the upcoming session.</em></strong></p>
										*/ ?>
									</div>
								<?php }; ?>


							</div>
						</div>
					</div>
					</section>
				</main>
				<script>
					function reloadWithSignUpOnSite() {
						var url = window.location.href;
						url = url.replace('sign_up_on_site=false', 'sign_up_on_site=true');
						window.location.href = url;
					}

					function reloadWithoutParams() {
						var url = window.location.href;
						url = url.replace(/[?&](occurrenceid|sign_up_on_site)=[^&]+/g, '');
						url = url.replace(/[?&]$/, '');
						window.location.href = url;
					}
				</script>
			<?php
				$occurrenceFound = true;
				break;
			endif; ?>
	<?php endforeach;
		if (!$occurrenceFound) {
			$occurrenceFound = false;
		}
	} else {
		$occurrenceFound = false;
	}
	?>
<? } ?>