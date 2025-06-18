<?php

/**
 * Template Name: Resource List
 */
/*
get_header(); 
?>

<?php
get_header();
$default_resource_archive_url = get_post_type_archive_link('resource');
$options = get_field('additional_filter_options');
?>

<section class="archive-hero-banner">
  <div class="grid-container">
    <div class="grid-x grid-padding-x">
      <div class="cell small-12 hero-content">
        <?php if (get_field('hero_headline')) : ?>
          <h1 class="hero-headline <?php echo $h1color; ?>"><?php echo get_field('hero_headline'); ?></h1>
        <?php else : ?>
          <h1 class="hero-headline <?php echo $h1color; ?>"><?php the_title(''); ?></h1>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<section class="blog-filter bg-dblue smalltoppadding smallbottompadding">
  <div class="grid-container">
    <div class="grid-x grid-margin-x align-center">

      <div class="cell small-12">
        <form id="filter-form">
          <?php $filteroptions = get_field('filter_types'); ?>
          <?php $formats = get_field('format_filter'); ?>
          <?php $industries = get_field('industry_filter'); ?>
          <?php $applications = get_field('application_filter'); ?>
          <?php $topics = get_field('topic_filter'); ?>
          <?php $locations = get_field('location_filter'); ?>

          <?php if ($filteroptions && in_array('format', $filteroptions) && $formats) : ?>
            <select id="format-filter" name="format">
              <option value="">Choose Format</option>
              <?php foreach ($formats as $format) : ?>
                <?php echo '<option value="' . $format->slug . '">' . $format->name . '</option>'; ?>
              <?php endforeach; ?>
            </select>
          <?php endif; ?>
          <?php if ($filteroptions && in_array('industry', $filteroptions) && $industries) : ?>
            <select id="industry-filter" name="industry">
              <option value="">Choose Industry</option>
              <?php foreach ($industries as $industry) : ?>
                <?php echo '<option value="' . $industry->slug . '">' . $industry->name . '</option>'; ?>
              <?php endforeach; ?>
            </select>
          <?php endif; ?>
          <?php if ($filteroptions && in_array('application', $filteroptions) && $applications) : ?>
            <select id="application-filter" name="application">
              <option value="">Choose Application</option>
              <?php foreach ($applications as $application) : ?>
                <?php echo '<option value="' . $application->slug . '">' . $application->name . '</option>'; ?>
              <?php endforeach; ?>
            </select>
          <?php endif; ?>
          <?php if ($filteroptions && in_array('topic', $filteroptions) && $topics) : ?>
            <select id="topic-filter" name="topic">
              <option value="">Choose Topic</option>
              <?php foreach ($topics as $topic) : ?>
                <?php echo '<option value="' . $topic->slug . '">' . $topic->name . '</option>'; ?>
              <?php endforeach; ?>
            </select>
          <?php endif; ?>
          <?php if ($filteroptions && in_array('location', $filteroptions) && $locations) : ?>
            <select id="location-filter" name="location">
              <option value="">Choose Location</option>
              <?php foreach ($locations as $location) : ?>
                <?php echo '<option value="' . $location->slug . '">' . $location->name . '</option>'; ?>
              <?php endforeach; ?>
            </select>
          <?php endif; ?>
          <button aria-label="Clear Filters" class="aras-button" id="clear-filters">Clear</button>
        </form>

        <script>
          document.addEventListener('DOMContentLoaded', function() {
            var formatFilter = document.getElementById('format-filter');
            var industryFilter = document.getElementById('industry-filter');
            var applicationFilter = document.getElementById('application-filter');
            var topicFilter = document.getElementById('topic-filter');
            var locationFilter = document.getElementById('location-filter');
            var clearButton = document.getElementById('clear-filters');

            function reloadPage() {
              <?php if ($filteroptions && in_array('format', $filteroptions) && $formats) : ?>
                var format = formatFilter.value;
              <?php endif; ?>
              <?php if ($filteroptions && in_array('industry', $filteroptions) && $industries) : ?>
                var industry = industryFilter.value;
              <?php endif; ?>
              <?php if ($filteroptions && in_array('application', $filteroptions) && $applications) : ?>
                var application = applicationFilter.value;
              <?php endif; ?>
              <?php if ($filteroptions && in_array('topic', $filteroptions) && $topics) : ?>
                var topic = topicFilter.value;
              <?php endif; ?>
              <?php if ($filteroptions && in_array('location', $filteroptions) && $locations) : ?>
                var location = locationFilter.value;
              <?php endif; ?>
              var url = '<?php echo the_permalink(); ?>';
              // Check if any parameters are already present in the URL
              var params = new URLSearchParams(window.location.search);
              // Preserve existing parameters

              <?php if ($filteroptions && in_array('format', $filteroptions) && $formats) : ?>
                if (params.has('format') && !format) {
                  params.delete('format');
                } else if (format) {
                  params.set('format', format);
                }
              <?php endif; ?>
              <?php if ($filteroptions && in_array('industry', $filteroptions) && $industries) : ?>
                if (params.has('industry') && !industry) {
                  params.delete('industry');
                } else if (industry) {
                  params.set('industry', industry);
                }
              <?php endif; ?>
              <?php if ($filteroptions && in_array('application', $filteroptions) && $applications) : ?>
                if (params.has('application') && !application) {
                  params.delete('application');
                } else if (application) {
                  params.set('application', application);
                }
              <?php endif; ?>
              <?php if ($filteroptions && in_array('topic', $filteroptions) && $topics) : ?>
                if (params.has('topic') && !topic) {
                  params.delete('topic');
                } else if (topic) {
                  params.set('topic', topic);
                }
              <?php endif; ?>
              <?php if ($filteroptions && in_array('location', $filteroptions) && $locations) : ?>
                if (params.has('location') && !location) {
                  params.delete('location');
                } else if (location) {
                  params.set('location', location);
                }
              <?php endif; ?>

              window.location.href = url + (params.toString() ? '?' + params.toString() : '');
            }

            <?php if ($filteroptions && in_array('format', $filteroptions) && $formats) : ?>
              formatFilter.addEventListener('change', reloadPage);
            <?php endif; ?>
            <?php if ($filteroptions && in_array('industry', $filteroptions) && $industries) : ?>
              industryFilter.addEventListener('change', reloadPage);
            <?php endif; ?>
            <?php if ($filteroptions && in_array('application', $filteroptions) && $applications) : ?>
              applicationFilter.addEventListener('change', reloadPage);
            <?php endif; ?>
            <?php if ($filteroptions && in_array('topic', $filteroptions) && $topics) : ?>
              topicFilter.addEventListener('change', reloadPage);
            <?php endif; ?>
            <?php if ($filteroptions && in_array('location', $filteroptions) && $locations) : ?>
              locationFilter.addEventListener('change', reloadPage);
            <?php endif; ?>

            // Set default values based on URL parameters
            var params = new URLSearchParams(window.location.search);
            <?php if ($filteroptions && in_array('format', $filteroptions) && $formats) : ?>
              var formatParam = params.get('format');
              if (formatParam) {
                var formatOption = document.querySelector('#format-filter option[value="' + formatParam + '"]');
                if (formatOption) {
                  formatOption.selected = true;
                }
              }
            <?php endif; ?>
            <?php if ($filteroptions && in_array('industry', $filteroptions) && $industries) : ?>
              var industryParam = params.get('industry');
              if (industryParam) {
                var industryOption = document.querySelector('#industry-filter option[value="' + industryParam + '"]');
                if (industryOption) {
                  industryOption.selected = true;
                }
              }
            <?php endif; ?>
            <?php if ($filteroptions && in_array('application', $filteroptions) && $applications) : ?>
              var applicationParam = params.get('application');
              if (applicationParam) {
                var applicationOption = document.querySelector('#application-filter option[value="' + applicationParam + '"]');
                if (applicationOption) {
                  applicationOption.selected = true;
                }
              }
            <?php endif; ?>
            <?php if ($filteroptions && in_array('topic', $filteroptions) && $topics) : ?>
              var topicParam = params.get('topic');
              if (topicParam) {
                var topicOption = document.querySelector('#topic-filter option[value="' + topicParam + '"]');
                if (topicOption) {
                  topicOption.selected = true;
                }
              }
            <?php endif; ?>
            <?php if ($filteroptions && in_array('location', $filteroptions) && $locations) : ?>
              var locationParam = params.get('location');
              if (locationParam) {
                var locationOption = document.querySelector('#location-filter option[value="' + locationParam + '"]');
                if (locationOption) {
                  locationOption.selected = true;
                }
              }
            <?php endif; ?>

            // Clear button
            clearButton.addEventListener('click', function(event) {
              event.preventDefault();
              var newUrl = window.location.pathname;
              window.location.href = newUrl;
            });
          });
        </script>

      </div>
    </div>
  </div>
</section>

<?php
$resourcetypes = get_field('resource_types');
if ($resourcetypes) : ?>
  <?php
  // Get the current URL parameters
  $current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  $query_string = parse_url($current_url, PHP_URL_QUERY);
  parse_str($query_string, $variables);

  $haystack = $query_string;
  $needleformat   = 'format';
  $needleindustry   = 'industry';
  $needleapplication   = 'application';
  $needletopic   = 'topic';
  $needlelocation   = 'location';
  if (
    // If the query string contains any of the needles, use a filter query
    ($needleformat !== '' && str_contains($haystack, $needleformat)) ||
    ($needleindustry !== '' && str_contains($haystack, $needleindustry)) ||
    ($needleapplication !== '' && str_contains($haystack, $needleapplication)) ||
    ($needletopic !== '' && str_contains($haystack, $needletopic)) ||
    ($needlelocation !== '' && str_contains($haystack, $needlelocation))
  ) : ?>
    <?php if ($needleformat !== '' && str_contains($haystack, $needleformat)) : ?>
      <?php
      // If the filter is using the 'format' filter
      $formattax = $variables['format'] ?? null;
      $industrytax = $variables['industry'] ?? null;
      $applicationtax = $variables['application'] ?? null;
      $topictax = $variables['topic'] ?? null;
      $locationtax = $variables['location'] ?? null;

      $tax_queries = array();
      if (isset($formattax)) {
        $tax_queries[] = array(
          'taxtype' => 'format',
          'taxterm' => $formattax,
        );
      }
      if (isset($industrytax)) {
        $tax_queries[] = array(
          'taxtype' => 'industry',
          'taxterm' => $industrytax,
        );
      }
      if (isset($applicationtax)) {
        $tax_queries[] = array(
          'taxtype' => 'application',
          'taxterm' => $applicationtax,
        );
      }
      if (isset($topictax)) {
        $tax_queries[] = array(
          'taxtype' => 'topic',
          'taxterm' => $topictax,
        );
      }
      if (isset($locationtax)) {
        $tax_queries[] = array(
          'taxtype' => 'location',
          'taxterm' => $locationtax,
        );
      }
      $args = array(
        'post_type' => 'resource',
        'posts_per_page' => -1,
        'order'          => 'DESC',
        'post_status'    => 'publish',
      );
      if ($options && in_array('upcoming', $options)) {
        // settings to only upcoming events based on the acf field
        $args['meta_query'] = array(
          array(
            'key' => 'event_date',
            'compare' => 'EXISTS',
          ),
        );
        $args['meta_key'] = 'event_date';
        $args['orderby'] = 'meta_value';
        $args['order'] = 'ASC';
      };
      if (!empty($tax_queries)) {
        $args['tax_query'] = array(
          'relation' => 'AND',
        );
        foreach ($tax_queries as $tax_query) {
          $args['tax_query'][] = array(
            'taxonomy' => $tax_query['taxtype'],
            'field'    => 'slug',
            'terms'    => $tax_query['taxterm'],
          );
        }
      };
      //echo '<pre>';
      //print_r($args['tax_query']);
      //echo '</pre>';
      $query = new WP_Query($args);  ?>
    <?php else : ?>
      <?php
      // If the content is filtered WITHOUT the 'format' filter, things get more complex.
      // Need to make the 'format' filter use 'OR' logic while making the rest of the filters use 'AND' logic
      // $formattax = $variables['format'] ?? null; // The format tax is not needed here
      $industrytax = $variables['industry'] ?? null;
      $applicationtax = $variables['application'] ?? null;
      $topictax = $variables['topic'] ?? null;
      $locationtax = $variables['location'] ?? null;

      $tax_query = array(
        'relation' => 'AND',
      );
      // Construct the 'OR' relation part for the format taxonomy
      $format_queries = array();
      foreach ($resourcetypes as $resourcetype) {
        $format = $resourcetype->slug;
        $format_queries[] = array(
          'taxonomy' => 'format',
          'terms'    => $format,
          'field'    => 'slug',
          'operator' => 'IN',
        );
      }
      // Merge the 'OR' relation part with the main tax query
      $tax_query[] = array(
        'relation' => 'OR',
        $format_queries,
      );
      // Add other taxonomy queries as needed
      if (isset($location)) {
        $tax_query[] = array(
          'taxonomy' => 'location',
          'terms'    => $location,
          'field'    => 'slug',
          'operator' => 'IN',
        );
      }
      if (isset($application)) {
        $tax_query[] = array(
          'taxonomy' => 'application',
          'terms'    => $application,
          'field'    => 'slug',
          'operator' => 'IN',
        );
      }
      if (isset($topic)) {
        $tax_query[] = array(
          'taxonomy' => 'topic',
          'terms'    => $topic,
          'field'    => 'slug',
          'operator' => 'IN',
        );
      }
      if (isset($industry)) {
        $tax_query[] = array(
          'taxonomy' => 'industry',
          'terms'    => $industry,
          'field'    => 'slug',
          'operator' => 'IN',
        );
      }

      $args = array(
        'post_type' => 'resource',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish',
        'tax_query'      => $tax_query,
      );
      if ($options && in_array('upcoming', $options)) {
        // settings to only upcoming events based on the acf field
        $args['meta_query'] = array(
          array(
            'key' => 'event_date',
            'compare' => 'EXISTS',
          ),
        );
        $args['meta_key'] = 'event_date';
        $args['orderby'] = 'meta_value';
        $args['order'] = 'ASC';
      };
      if (!empty($tax_queries)) {
        $args['tax_query'] = array();
        foreach ($tax_queries as $tax_query) {
          $args['tax_query'][] = array(
            'taxonomy' => $tax_query['taxtype'],
            'field'    => 'slug',
            'terms'    => $tax_query['taxterm'],
          );
        }
      };
      //echo '<pre>';
      //print_r($args['tax_query']);
      //echo '</pre>';
      $query = new WP_Query($args);  ?>
    <?php endif; ?>
  <?php else : ?>
    <?php
    /// This is the standard query for the page.
    // Has an 'or' filter for the resource types
    $tax_queries = array();
    foreach ($resourcetypes as $resourcetype) {
      $tax_queries[] = array(
        'taxtype' => 'format',
        'taxterm' => $resourcetype->slug,
      );
    }
    ?>
    <?php
    $args = array(
      'post_type' => 'resource',
      'posts_per_page' => -1,
      'orderby'        => 'date',
      'order'          => 'DESC',
      'post_status'    => 'publish',
    );
    if ($options && in_array('upcoming', $options)) {
      // settings to only upcoming events based on the acf field
      $args['meta_query'] = array(
        array(
          'key' => 'event_date',
          'compare' => 'EXISTS',
        ),
      );
      $args['meta_key'] = 'event_date';
      $args['orderby'] = 'meta_value';
      $args['order'] = 'ASC';
    };
    if (!empty($tax_queries)) {
      $args['tax_query'] = array(
        'relation' => 'OR',
      );
      foreach ($tax_queries as $tax_query) {
        $args['tax_query'][] = array(
          'taxonomy' => $tax_query['taxtype'],
          'field'    => 'slug',
          'terms'    => $tax_query['taxterm'],
        );
      }
    };
    $query = new WP_Query($args);  ?>
  <?php endif; ?>
  <main class="blog-archive bg-grey mediumtoppadding largebottompadding" role="main">
    <div class="grid-container">
      <section class="grid-x grid-margin-x blog-post-loop">
        <?php if ($query->have_posts()) : $postCount = 0;
          while ($query->have_posts()) : $query->the_post();
            $postCount++; ?>
            <?php get_template_part('parts/loop', 'archive-resources'); ?>
          <?php endwhile; ?>
          <?php wp_reset_postdata(); ?>
        <?php else : ?>
          <?php get_template_part('parts/content', 'missing'); ?>
        <?php endif; ?>
      </section>
    </div>
  </main>
<?php endif; ?>

<?php get_template_part('parts/_template_parts/footer_cta'); ?>

<?php get_footer(); ?>
*/
