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
	$classNames = [];
	$classLanguages = [];
	$deliveryMethods = [];
	$studentRoles = [];
	$locations = [];
?>

	<?php foreach ($data['Item'] as $record) {
		if (isset($record['Relationships']['Item'])) {
			$relationships_item = $record['Relationships']['Item'];
			if (is_array($relationships_item)) {
				foreach ($relationships_item as $relationship) {
					if (isset($relationship['state']) && $relationship['state'] == 'Planned') {
						// Extract values from the relationship
						$className = isset($relationship['_class_name']['#text']) ? str_replace('_', ' ', $relationship['_class_name']['#text']) : '';
						$classLanguage = isset($relationship['_class_language']) ? $relationship['_class_language'] : '';
						$deliveryMethod = isset($relationship['_delivery_method']) ? ($relationship['_delivery_method'] === 'Virtual' ? 'Virtual: Instructor-led' : $relationship['_delivery_method']) : '';
						$studentRole = isset($relationship['_student_role']) ? $relationship['_student_role'] : '';
						$location = isset($relationship['location']) ? $relationship['location'] : '';
						// Add unique values to the arrays
						if (!in_array($className, $classNames)) {
							$classNames[] = $className;
						}
						if (!in_array($classLanguage, $classLanguages)) {
							$classLanguages[] = $classLanguage;
						}
						if (!in_array($deliveryMethod, $deliveryMethods)) {
							$deliveryMethods[] = $deliveryMethod;
						}
						if (!in_array($studentRole, $studentRoles)) {
							$studentRoles[] = $studentRole;
						}
						if (!in_array($location, $locations)) {
							$locations[] = $location;
						}

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
						//			if (str_contains($site_url, '/ja-jp/')) {
						//				$tempArray['_delivery_method'] = isset($relationship['_delivery_method']) ? ($relationship['_delivery_method'] === 'Virtual' ? 'Virtual: Instructor-led Japan' : $relationship['_delivery_method']) : '';
						//				$tempArray['location_long'] = isset($relationship['location']) ? ($relationship['location'] === 'Remote' ? 'Remote, Anywhere Japan' : $relationship['location']) : '';
						//			} else {
						//				$tempArray['_delivery_method'] = isset($relationship['_delivery_method']) ? ($relationship['_delivery_method'] === 'Virtual' ? 'Virtual: Instructor-led' : $relationship['_delivery_method']) : '';
						//				$tempArray['location_long'] = isset($relationship['location']) ? ($relationship['location'] === 'Remote' ? 'Remote, Anywhere' : $relationship['location']) : '';
						//			}
						$trainingItems[] = $tempArray;
					}
				}
			}
		}
	}
	?>
	<?php foreach ($data['Item'] as $record) {
		if (array_key_exists('Relationships', $record)) {
			if (isset($record['Relationships']['Item']['state']) == 'Planned') {

				// Extract values from the relationship
				$className = isset($record['Relationships']['Item']['_class_name']['#text']) ? str_replace('_', ' ', $record['Relationships']['Item']['_class_name']['#text']) : '';
				$classLanguage = isset($record['Relationships']['Item']['_class_language']) ? $record['Relationships']['Item']['_class_language'] : '';
				$deliveryMethod = isset($record['Relationships']['Item']['_delivery_method']) ? ($record['Relationships']['Item']['_delivery_method'] === 'Virtual' ? 'Virtual: Instructor-led' : $record['Relationships']['_delivery_method']) : '';
				$studentRole = isset($record['Relationships']['Item']['_student_role']) ? $record['Relationships']['Item']['_student_role'] : '';
				$location = isset($record['Relationships']['Item']['location']) ? $record['Relationships']['Item']['location'] : '';
				// Add unique values to the arrays
				if (!in_array($className, $classNames)) {
					$classNames[] = $className;
				}
				if (!in_array($classLanguage, $classLanguages)) {
					$classLanguages[] = $classLanguage;
				}
				if (!in_array($deliveryMethod, $deliveryMethods)) {
					$deliveryMethods[] = $deliveryMethod;
				}
				if (!in_array($studentRole, $studentRoles)) {
					$studentRoles[] = $studentRole;
				}
				if (!in_array($location, $locations)) {
					$locations[] = $location;
				}

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
				//		if (str_contains($site_url, '/ja-jp/')) {
				//			$tempArray['_delivery_method'] = isset($record['Relationships']['Item']['_delivery_method']) ? ($record['Relationships']['Item']['_delivery_method'] === 'Virtual' ? 'Virtual: Instructor-led Japanese' : $record['Relationships']['Item']['_delivery_method']) : '';
				//			$tempArray['location_long'] = isset($record['Relationships']['Item']['location']) ? ($record['Relationships']['Item']['location'] === 'Remote' ? 'Remote, Anywhere Japanese' : $record['Relationships']['Item']['location']) : '';
				//		} else {
				//			$tempArray['_delivery_method'] = isset($record['Relationships']['Item']['_delivery_method']) ? ($record['Relationships']['Item']['_delivery_method'] === 'Virtual' ? 'Virtual: Instructor-led' : $record['Relationships']['Item']['_delivery_method']) : '';
				//			$tempArray['location_long'] = isset($record['Relationships']['Item']['location']) ? ($record['Relationships']['Item']['location'] === 'Remote' ? 'Remote, Anywhere' : $record['Relationships']['Item']['location']) : '';
				//		}
				$trainingItems[] = $tempArray;
			}
		}
	}
	?>

	<section id="short-hero" class="short-hero hero-banner bg-dblue">
		<div class="grid-container">
			<div class="grid-x grid-padding-x align-top">
				<div class="cell small-12 medium-10 hero-content">
					<div class="hero-content-inner">
						<?php if (get_field('training_classes_headline')) : ?>
							<h1 class="hero-headline"><?php echo get_field('training_classes_headline'); ?></h1>
						<?php else : ?>
							<h1 class="hero-headline"><?php the_title(''); ?></h1>
						<?php endif; ?>
						<?php if (get_field('training_classes_subhead')) : ?>
							<h5 class="hero-headline"><?php echo get_field('training_classes_subhead'); ?></h5>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<main id="training-section" data-sort-by="">
		<section class="training-filters smalltoppadding smallbottompadding">
			<div class="grid-container">
				<div class="grid-x grid-margin-x">
					<form id="training-filter-controls" class="cell small-12 training-filter-flex">

						<fieldset class="location custom-select training" data-filter-group="location" data-logic="and">
							<?php if (str_contains($site_url, '/ja-jp/')) { ?>
								<select id="location" name="location">
									<option value="">場所</option>
									<?php foreach ($locations as $location) : ?>
										<?php if (!empty($location)) : ?>
											<?php $sanitizedLocation = preg_replace('/[^a-zA-Z0-9\s]/', '', $location); ?>
											<option value=".<?php echo str_replace(' ', '-', htmlspecialchars($sanitizedLocation)); ?>">
												<?php if (htmlspecialchars($location) == 'Tokyo') {
													echo '東京';
												} else {
													echo htmlspecialchars($location);
												}; ?>
											</option>
										<?php endif; ?>
									<?php endforeach; ?>
								</select>
							<?php } else { ?>
								<select id="location" name="location">
									<option value="">Choose a Location</option>
									<?php foreach ($locations as $location) : ?>
										<?php if (!empty($location)) : ?>
											<?php $sanitizedLocation = preg_replace('/[^a-zA-Z0-9\s]/', '', $location); ?>
											<option value=".<?php echo str_replace(' ', '-', htmlspecialchars($sanitizedLocation)); ?>">
												<?php echo htmlspecialchars($location); ?>
											</option>
										<?php endif; ?>
									<?php endforeach; ?>
								</select>
							<?php } ?>
						</fieldset>

						<fieldset class="delivery_method custom-select training" data-filter-group="delivery_method" data-logic="and">
							<?php if (str_contains($site_url, '/ja-jp/')) { ?>
								<select id="delivery_method" name="delivery_method">
									<option value="">形式</option>
									<?php foreach ($deliveryMethods as $deliveryMethod) : ?>
										<?php if (!empty($deliveryMethod)) : ?>
											<?php $sanitizedDeliveryMethod = preg_replace('/[^a-zA-Z0-9\s]/', '', $deliveryMethod); ?>
											<option value=".<?php echo str_replace(' ', '-', htmlspecialchars($sanitizedDeliveryMethod)); ?>">
												<?php if (htmlspecialchars($deliveryMethod) == 'Virtual: Instructor-led') {
													echo '講師によるオンライントレーニング';
												} else {
													echo htmlspecialchars($deliveryMethod);
												}; ?>
											</option>
										<?php endif; ?>
									<?php endforeach; ?>
								</select>
							<?php } else { ?>
								<select id="delivery_method" name="delivery_method">
									<option value="">Choose a Delivery Method</option>
									<?php foreach ($deliveryMethods as $deliveryMethod) : ?>
										<?php if (!empty($deliveryMethod)) : ?>
											<?php $sanitizedDeliveryMethod = preg_replace('/[^a-zA-Z0-9\s]/', '', $deliveryMethod); ?>
											<option value=".<?php echo str_replace(' ', '-', htmlspecialchars($sanitizedDeliveryMethod)); ?>"><?php echo htmlspecialchars($deliveryMethod); ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								</select>
							<?php } ?>
						</fieldset>
						<fieldset class="class_name custom-select training" data-filter-group="class_name" data-logic="and">
							<select id="class_name" name="class_name">
								<?php if (str_contains($site_url, '/ja-jp/')) { ?>
									<option value="">セッション</option>
								<?php } else { ?>
									<option value="">Choose a Course</option>
								<?php	}; ?>
								<?php foreach ($classNames as $className) : ?>
									<?php if (!empty($className)) : ?>
										<?php $sanitizedClassName = preg_replace('/[^a-zA-Z0-9\s]/', '', $className); ?>
										<option value=".<?php echo str_replace(' ', '-', htmlspecialchars($sanitizedClassName)); ?>"><?php echo htmlspecialchars($className); ?></option>
									<?php endif; ?>
								<?php endforeach; ?>
							</select>
						</fieldset>
						<fieldset class="class_language custom-select training" data-filter-group="class_language" data-logic="and">
							<?php if (str_contains($site_url, '/ja-jp/')) { ?>
								<select id="class_language" name="class_language">
									<option value="">言語</option>
									<?php foreach ($classLanguages as $classLanguage) : ?>
										<?php if (!empty($classLanguage)) : ?>
											<?php $sanitizedClassLanguage = preg_replace('/[^a-zA-Z0-9\s]/', '', $classLanguage); ?>
											<option value=".<?php echo str_replace(' ', '-', htmlspecialchars($sanitizedClassLanguage)); ?>">
												<?php if (htmlspecialchars($classLanguage) == 'Japanese') {
													echo '日本語';
												} elseif (htmlspecialchars($classLanguage) == 'English') {
													echo '英語';
												} else {
													echo htmlspecialchars($classLanguage);
												}; ?>
											</option>
										<?php endif; ?>
									<?php endforeach; ?>
								</select>
							<?php } else { ?>
								<select id="class_language" name="class_language">
									<option value="">Choose a Language</option>
									<?php foreach ($classLanguages as $classLanguage) : ?>
										<?php if (!empty($classLanguage)) : ?>
											<?php $sanitizedClassLanguage = preg_replace('/[^a-zA-Z0-9\s]/', '', $classLanguage); ?>
											<option value=".<?php echo str_replace(' ', '-', htmlspecialchars($sanitizedClassLanguage)); ?>"><?php echo htmlspecialchars($classLanguage); ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								</select>
							<?php } ?>
						</fieldset>
						<fieldset class="student_role custom-select training" data-filter-group="student_role" data-logic="and">
							<?php if (str_contains($site_url, '/ja-jp/')) { ?>
								<select id="student_role" name="student_role">
									<option value="">対象者</option>
									<?php foreach ($studentRoles as $studentRole) : ?>
										<?php if (!empty($studentRole)) : ?>
											<?php $sanitizedStudentRole = preg_replace('/[^a-zA-Z0-9\s]/', '', $studentRole); ?>
											<option value=".<?php echo str_replace(' ', '-', htmlspecialchars($sanitizedStudentRole)); ?>">
												<?php if (htmlspecialchars($studentRole) == 'System Administrator') {
													echo 'システム管理者';
												} elseif (htmlspecialchars($studentRole) == 'Computer Programmer') {
													echo 'プログラマー';
												} else {
													echo htmlspecialchars($studentRole);
												}; ?>
											</option>
										<?php endif; ?>
									<?php endforeach; ?>
								</select>
							<?php } else { ?>
								<select id="student_role" name="student_role">
									<option value="">Choose a Student Role</option>
									<?php foreach ($studentRoles as $studentRole) : ?>
										<?php if (!empty($studentRole)) : ?>
											<?php $sanitizedStudentRole = preg_replace('/[^a-zA-Z0-9\s]/', '', $studentRole); ?>
											<option value=".<?php echo str_replace(' ', '-', htmlspecialchars($sanitizedStudentRole)); ?>"><?php echo htmlspecialchars($studentRole); ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								</select>
							<?php } ?>
						</fieldset>
						<fieldset class="order-select custom-select training">
							<?php if (str_contains($site_url, '/ja-jp/')) { ?>
								<select id="order-select">
									<option value="asc">昇順</option>
									<option value="desc">降順</option>
								</select>
							<?php } else { ?>
								<select id="order-select">
									<option value="asc">Ascending</option>
									<option value="desc">Descending</option>
								</select>
							<?php } ?>
						</fieldset>
						<fieldset class="clearbutton">
							<?php if (str_contains($site_url, '/ja-jp/')) { ?>
								<button aria-label="クリア" id="clear-button" class="aras-button" type="reset">クリア</button>
							<?php } else { ?>
								<button aria-label="Reset" id="clear-button" class="aras-button" type="reset">Reset</button>
							<?php } ?>
						</fieldset>
					</form>
				</div>
			</div>
		</section>


		<section class="training-content bg-white smalltoppadding mediumbottompadding">
			<div class="grid-container">
				<div class="grid-x grid-margin-x" id="multifilter-container">
					<?php
					usort($trainingItems, function ($a, $b) {
						$timestampA = strtotime($a['startdatelong']);
						$timestampB = strtotime($b['startdatelong']);
						return $timestampA <=> $timestampB;
					});
					$uniqueItems = array();
					foreach ($trainingItems as $item) {

						$uniqueIdentifier = $item['_class_name'] . '_' . $item['startdatelong'] . '_' . $item['_delivery_method'];
						if (!isset($uniqueItems[$uniqueIdentifier])) {
							$uniqueItems[$uniqueIdentifier] = true;
					?>
							<?php
							$startDateTimestamp = strtotime($item['startdatelong']);
							$dateHasPassed = $startDateTimestamp < $currentTimestamp;
							?>
							<?php if ($item['_class_name'] != '') : ?>
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
								<?php
								$sanitizedClasses = array();
								if (isset($item['location'])) {
									$sanitizedLocation = preg_replace('/[^a-zA-Z0-9\s]/', '', $item['location']); // Remove special characters except alphanumeric and whitespace
									$sanitizedClasses[] = str_replace(' ', '-', htmlspecialchars($sanitizedLocation));
								}
								if (isset($item['_delivery_method'])) {
									$sanitizedDeliveryMethod = preg_replace('/[^a-zA-Z0-9\s]/', '', $item['_delivery_method']); // Remove special characters except alphanumeric and whitespace
									$sanitizedClasses[] = str_replace(' ', '-', htmlspecialchars($sanitizedDeliveryMethod));
								}
								$sanitizedClassName = preg_replace('/[^a-zA-Z0-9\s]/', '', $item['_class_name']); // Remove special characters except alphanumeric and whitespace
								$sanitizedClasses[] = str_replace(' ', '-', htmlspecialchars($sanitizedClassName));
								if (isset($item['_class_language'])) {
									$sanitizedClassLanguage = preg_replace('/[^a-zA-Z0-9\s]/', '', $item['_class_language']); // Remove special characters except alphanumeric and whitespace
									$sanitizedClasses[] = str_replace(' ', '-', htmlspecialchars($sanitizedClassLanguage));
								}
								if (isset($item['_student_role'])) {
									$sanitizedStudentRole = preg_replace('/[^a-zA-Z0-9\s]/', '', $item['_student_role']); // Remove special characters except alphanumeric and whitespace
									$sanitizedClasses[] = str_replace(' ', '-', htmlspecialchars($sanitizedStudentRole));
								}
								$classString = implode(' ', $sanitizedClasses);
								?>
								<div class="cell training-item mix <?php echo $classString; ?>">
									<h2><?php echo $item['_class_name']; ?></h2>
									<?php if (str_contains($site_url, '/ja-jp/')) {
										if ($item['location_long'] == 'Tokyo') {
											$location_long = '東京';
										} else {
											$location_long = isset($item['location_long']) ? $item['location_long'] : '';;
										}
									} else {
										$location_long = isset($item['location_long']) ? $item['location_long'] : '';
									} ?>
									<h5><?php echo $location_long; ?> | <?php echo $timezone_locale; ?> | <?php echo $classdates; ?></h5>
									<div class="training-item-content">
										<div class="class-description-truncation">
											<?php echo isset($item['_class_description']) ? $item['_class_description'] : ''; ?>
											<?php $link = get_field('register_page');
											if ($link) : $link_url = $link['url'];
												$link_title = $link['title'];
												$link_target = $link['target'] ? $link['target'] : '_self';
											?>
												<a aria-label="<?php if (str_contains($site_url, '/ja-jp/')) {
																					echo 'もっと見る';
																				} else {
																					echo 'See More';
																				} ?>
																				" class="seemore-link" href="<?php echo esc_url($link_url); ?>?occurrenceid=<?php echo $item['id']; ?>&sign_up_on_site=false" target="<?php echo esc_attr($link_target); ?>">
													<?php if (str_contains($site_url, '/ja-jp/')) {
														echo 'もっと見る';
													} else {
														echo 'See More';
													} ?>
												</a>
											<?php else : ?>
												<button aria-label="<?php if (str_contains($site_url, '/ja-jp/')) {
																							echo 'もっと見る';
																						} else {
																							echo 'See More';
																						} ?>
" class="seemore-link" onclick="reloadWithParams('<?php echo $item['id']; ?>')">
													<?php if (str_contains($site_url, '/ja-jp/')) {
														echo 'もっと見る';
													} else {
														echo 'See More';
													} ?>
												</button>
											<?php endif; ?>
										</div>
										<div class="register-button">
											<?php $link = get_field('register_page');
											if ($link) : $link_url = $link['url'];
												$link_title = $link['title'];
												$link_target = $link['target'] ? $link['target'] : '_self';
											?>
												<a aria-label="<?php if (str_contains($site_url, '/ja-jp/')) {
																					echo '登録';
																				} else {
																					echo 'Register';
																				} ?>" class="aras-button" href="<?php echo esc_url($link_url); ?>?occurrenceid=<?php echo $item['id']; ?>&sign_up_on_site=false" target="<?php echo esc_attr($link_target); ?>">
													<?php if (str_contains($site_url, '/ja-jp/')) {
														echo '登録';
													} else {
														echo 'Register';
													} ?>
												</a>
											<?php else : ?>
												<button aria-label="<?php if (str_contains($site_url, '/ja-jp/')) {
																							echo '登録';
																						} else {
																							echo 'Register';
																						} ?>" class="aras-button" onclick="reloadWithParams('<?php echo $item['id']; ?>')">
													<?php if (str_contains($site_url, '/ja-jp/')) {
														echo '登録';
													} else {
														echo 'Register';
													} ?>
												</button>
											<?php endif; ?>

										</div>
									</div>
									<div class="training-item-tags">

										<?php if (str_contains($site_url, '/ja-jp/')) { ?>

											<span class="training-tag">
												<?php if ($item['_class_language'] == 'Japanese') {
													echo '日本語';
												} elseif ($item['_class_language'] == 'English') {
													echo '英語';
												} else {
													echo isset($item['_class_language']) ? $item['_class_language'] : '';
												}; ?>
											</span>
											<span class="training-tag">
												<?php if ($item['_delivery_method'] == 'Virtual: Instructor-led') {
													echo '講師によるオンライントレーニング';
												} else {
													echo isset($item['_delivery_method']) ? $item['_delivery_method'] : '';
												}; ?>
											</span>
											<span class="training-tag">
												<?php if ($item['_student_role'] == 'System Administrator') {
													echo 'システム管理者';
												} elseif ($item['_student_role'] == 'Computer Programmer') {
													echo 'プログラマー';
												} else {
													echo isset($item['_student_role']) ? $item['_student_role'] : '';
												}; ?>
											</span>
											<span class="training-tag">
												<?php echo $class_duration_long; ?>
											</span>
											<span class="training-tag">
												<?php echo isset($item['currency']) ? $item['currency'] : ''; ?>
											</span>
										<?php } else { ?>
											<span class="training-tag"><?php echo isset($item['_class_language']) ? $item['_class_language'] : ''; ?></span>
											<span class="training-tag"><?php echo isset($item['_delivery_method']) ? $item['_delivery_method'] : ''; ?></span>
											<span class="training-tag"><?php echo isset($item['_student_role']) ? $item['_student_role'] : ''; ?></span>
											<span class="training-tag"><?php echo $class_duration_long; ?></span>
											<span class="training-tag"><?php echo isset($item['currency']) ? $item['currency'] : ''; ?></span>
										<?php } ?>
									</div>
								</div>
							<?php endif; ?>
					<?php
						}
					}
					?>
					<div class="cell text-center">
						<h3 id="no-posts-text" style="display: none;">Sorry, no training courses meet this criteria. Please adjust your filter settings.</h3>
					</div>
				</div>
			</div>
		</section>
	</main>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/mixitup/3.3.1/mixitup.min.js"></script>
	<script src="<?php echo get_template_directory_uri(); ?>/assets/scripts/mixitup/mixitup-pagination.min.js"></script>
	<script src="<?php echo get_template_directory_uri(); ?>/assets/scripts/mixitup/mixitup-multifilter.min.js"></script>
	<script>
		function reloadWithParams(occurrenceId) {
			var signUpOnSite = "false";
			var url = window.location.pathname + '?occurrenceid=' + occurrenceId + '&sign_up_on_site=' + signUpOnSite;
			window.location.href = url;
		}
		jQuery('#training-section').each(function() {
			if (!jQuery(this).hasClass('active')) {
				jQuery(this).addClass('active');
				var filterVal = 'all';
				var dataSort = '';
				var thisContainer = jQuery(this).find('#multifilter-container');
				var dataSort = jQuery(this).attr('data-sort-by');
				if (dataSort === '') {
					var filterVal = 'all';
				} else {
					var filterVal = '.' + dataSort
				}
				var mixer = mixitup(thisContainer, {
					callbacks: {
						onMixStart: function(state, futureState) {
							console.log(futureState)
							if (futureState.hasFailed) {
								jQuery('#no-posts-text').css('display', 'block');
							} else {
								jQuery('#no-posts-text').css('display', 'none');
							}
						},
						onMixEnd: function(state) {
							console.log('Filtering complete');
						}
					},
					animation: {
						enable: false,
					},
					load: {
						//filter: filterVal
					},
					multifilter: {
						enable: true,
					},
					controls: {
						toggleLogic: 'and',
					},
				});

			}
			// preload based on URL ending with #filter
			if (location.hash) {
				var hash = location.hash.replace('#', '.')
				mixer.filter(hash)
				if (jQuery('#class_name option[value="' + hash + '"]').length > 0) {
					jQuery("#class_name").val(hash).change();
				}
				if (jQuery('#class_language option[value="' + hash + '"]').length > 0) {
					jQuery("#class_language").val(hash).change();
				}
				if (jQuery('#delivery_method option[value="' + hash + '"]').length > 0) {
					jQuery("#delivery_method").val(hash).change();
				}
				if (jQuery('#student_role option[value="' + hash + '"]').length > 0) {
					jQuery("#student_role").val(hash).change();
				}
				if (jQuery('#location option[value="' + hash + '"]').length > 0) {
					jQuery("#location").val(hash).change();
				}
			}

			// Add an event listener to the reset button
			document.getElementById('clear-button').addEventListener('click', function() {
				document.getElementById('class_name').value = '';
				document.getElementById('class_language').value = '';
				document.getElementById('delivery_method').value = '';
				document.getElementById('student_role').value = '';
				document.getElementById('location').value = '';
				mixer.filter('all');
			});

			/*Custom Select Script
			  var x, i, j, l, ll, selElmnt, a, b, c;
			  /* Look for any elements with the class "custom-select": */
			x = document.getElementsByClassName("custom-select");
			l = x.length;
			for (i = 0; i < l; i++) {
				selElmnt = x[i].getElementsByTagName("select")[0];
				ll = selElmnt.length;
				/* For each element, create a new DIV that will act as the selected item: */
				a = document.createElement("DIV");
				a.setAttribute("class", "select-selected");
				a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
				x[i].appendChild(a);
				/* For each element, create a new DIV that will contain the option list: */
				b = document.createElement("DIV");
				b.setAttribute("class", "select-items select-hide");
				for (j = 0; j < ll; j++) {
					/* For each option in the original select element,
					create a new DIV that will act as an option item: */
					c = document.createElement("DIV");
					c.innerHTML = selElmnt.options[j].innerHTML;
					c.dataset.targetclass = selElmnt.options[j].value;
					c.addEventListener("click", function(e) {
						/* When an item is clicked, update the original select box,
						and the selected item: */
						var y, i, k, s, h, sl, yl;
						s = this.parentNode.parentNode.getElementsByTagName("select")[0];
						sl = s.length;
						h = this.parentNode.previousSibling;
						for (i = 0; i < sl; i++) {
							if (s.options[i].innerHTML == this.innerHTML) {
								s.selectedIndex = i;
								h.innerHTML = this.innerHTML;
								y = this.parentNode.getElementsByClassName("same-as-selected");
								yl = y.length;
								for (k = 0; k < yl; k++) {
									y[k].removeAttribute("class");
								}
								this.setAttribute("class", "same-as-selected");
								break;
							}
						}
						h.click();
						console.log(this)
						if (this.dataset.targetclass == 'asc' || this.dataset.targetclass == 'desc') {
							var order = this.dataset.targetclass;
							mixer.sort(order === 'desc' ? 'default:desc' : 'default:asc');
						} else {
							const filter_value = this.dataset.targetclass;
							const filter_class = filter_value ? filter_value : 'all'
							mixer.filter(filter_class);
						}
					});
					b.appendChild(c);
				}
				x[i].appendChild(b);
				a.addEventListener("click", function(e) {
					/* When the select box is clicked, close any other select boxes,
					and open/close the current select box: */
					e.stopPropagation();
					closeAllSelect(this);
					this.nextSibling.classList.toggle("select-hide");
					this.classList.toggle("select-arrow-active");
				});
			}

			function closeAllSelect(elmnt) {
				/* A function that will close all select boxes in the document,
				except the current select box: */
				var x, y, i, xl, yl, arrNo = [];
				x = document.getElementsByClassName("select-items");
				y = document.getElementsByClassName("select-selected");
				xl = x.length;
				yl = y.length;
				for (i = 0; i < yl; i++) {
					if (elmnt == y[i]) {
						arrNo.push(i)
					} else {
						y[i].classList.remove("select-arrow-active");
					}
				}
				for (i = 0; i < xl; i++) {
					if (arrNo.indexOf(i)) {
						x[i].classList.add("select-hide");
					}
				}
			}

			/* If the user clicks anywhere outside the select box,
			then close all select boxes: */
			document.addEventListener("click", closeAllSelect);
		});
	</script>
<? } ?>