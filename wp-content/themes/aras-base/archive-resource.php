<?php
get_header();
$default_resource_archive_url = get_post_type_archive_link('resource');
$site_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$labels = get_field('resource_labels', 'option');
if( !is_array($labels) ){
  $labels = [];
}
?>
<section class="archive-hero-banner">
  <div class="grid-container">
    <div class="grid-x grid-padding-x">
      <div class="cell small-12 medium-6 large-7 hero-content">
        <?php if( is_tax() ): ?>
          <h1 class="hero-headline">
            <?php
            $term = get_queried_object();
            $field_parts = ['cat','label'];
            
            switch( true ){
              case str_contains($site_url, '/ja-jp/'):
                $field_parts[] = 'japanese';
                break;
              case str_contains($site_url, '/fr-fr/'):
                $field_parts[] = 'french';
                break;
              case str_contains($site_url, '/de-de/'):
                $field_parts[] = 'german';
                break;
            }
            $field_name = implode('_', $field_parts);
            $cta_label = get_field($field_name, $term) ?: $term->name;
            ?>
            <?php echo $cta_label ?>
            <?php _e('Resources', 'aras') ?>
        </h1>
        <?php elseif (get_field('resource_archive_title', 'option')) : ?>
          <h1 class="hero-headline"><?php echo get_field('resource_archive_title', 'option'); ?></h1>
        <?php else : ?>
          <h1 class="hero-headline">Resource Library</h1>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<section class="resource-archive-feat smalltoppadding largebottompadding">
  <div class="grid-container">
    <?php
    $featposts = new WP_Query(array(
      'posts_per_page' => 1,
      'orderby'        => 'date',
      'order'          => 'DESC',
      'post_type'      => 'resource',
      'tax_query'      => array(
        array(
          'taxonomy' => 'featured-resource',
          'field'    => 'slug',
          'terms'    => 'featured',
        ),
      ),
    ));
    ?>

    <?php if ($featposts->have_posts()) : ?>
      <?php while ($featposts->have_posts()) : $featposts->the_post();
        $featured_img_url = get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>
        <?php
        $terms = get_the_terms(get_the_ID(), 'format');
        if ($terms && !is_wp_error($terms)) {
          $term = array_shift($terms);

          $cta_label = get_field('format_cta_label', $term);;
          // Check if URL contains '/ja-jp/'
          if (str_contains($site_url, '/ja-jp/')) {
            $cta_label = get_field('format_cta_label_japanese', $term) ?: $cta_label;
          }
          // Check if URL contains '/fr-fr/'
          elseif (str_contains($site_url, '/fr-fr/')) {
            $cta_label = get_field('format_cta_label_french', $term) ?: $cta_label;
          }
          // Check if URL contains '/de-de/'
          elseif (str_contains($site_url, '/de-de/')) {
            $cta_label = get_field('format_cta_label_german', $term) ?: $cta_label;
          }
        }
        ?>
        <article id="post-<?php the_ID(); ?>" class="feat-card grid-x grid-margin-x align-middle" role="article">

          <div class="cell small-12 medium-6">
            <div class="feat-img-container">
              <?/*
              <span class="feat-label">Featured</span>
              */ ?>
              <?php //the_post_thumbnail('full');
              $image_id = get_post_thumbnail_id();
              $image_url = wp_get_attachment_image_src($image_id, 'full'); // 'full' size image
              if (get_post_meta($image_id, '_wp_attachment_image_alt', true) != '') {
                $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
              } else {
                $image_alt = get_the_title('');
              }
              $image_data = wp_get_attachment_metadata($image_id);
              $image_width = $image_data['width'];
              $image_height = $image_data['height'];
              $image_loading = 'lazy';
              $image_decoding = 'async';
              $image_srcset = wp_get_attachment_image_srcset($image_id, 'full'); // 'full' size image srcset
              ?>
              <img src="<?php echo $image_url[0]; ?>" srcset="<?php echo esc_attr($image_srcset); ?>" alt="<?php echo $image_alt; ?>" width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" loading="<?php echo $image_loading; ?>" decoding="<?php echo $image_decoding; ?>" />
            </div>
          </div>

          <div class="cell small-12 medium-6 feat-content-container">
            <?/*
            <h6 class="card-subhead"><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo get_the_time('F j, Y'); ?></h6>
              */ ?>
            <a aria-label="<?php the_title_attribute() ?>" class="card-headline-a" title="<?php the_title_attribute() ?>" href="<?php the_permalink() ?>">
              <?php if (get_field('hero_headline')) : ?>
                <h3 class="card-headline"><?php echo get_field('hero_headline'); ?></h3>
              <?php else : ?>
                <h3 class="card-headline"><?php the_title(''); ?></h3>
              <?php endif; ?>
            </a>

            <?php if (get_the_excerpt()) : ?>
              <p class="content"><?php echo wp_trim_words(get_the_excerpt(), 30); ?></p>
            <?php else : ?>
              <?php if (get_field('hero_content')) : ?>
                <p class="content"><?php echo wp_trim_words(get_field('hero_content'), 30); ?></p>
              <?php endif; ?>
            <?php endif; ?>

            <a aria-label="<?php the_title() ?>" class="card-link" title="<?php the_title() ?>" href="<?php the_permalink() ?>">
              <?php if ($cta_label) {
                echo esc_html($cta_label) . '&nbsp;→';
              } else {
                echo 'Read More&nbsp;→';
              }
              ?>
            </a>
          </div>

        </article>
      <?php endwhile; ?>
      <?php wp_reset_postdata(); ?>
    <?php endif; ?>
  </div>
  </div>
</section>

<section class="blog-filter bg-dblue smalltoppadding smallbottompadding">
  <div class="grid-container">
    <div class="grid-x grid-margin-x align-center">

      <div class="cell small-12">
        <form id="filter-form">
          
          <?php if (have_rows('resource_format_filter', 'option')) : ?>
            <div class="custom-select">
              <select id="format-filter" name="format">
                <option value="">
                  <?php echo !empty( $labels['all_formats'] ) ? $labels['all_formats'] : 'All Formats'; ?>
                </option>
                <?php while (have_rows('resource_format_filter', 'option')) : the_row(); ?>
                  <?php $term = get_sub_field('category_item', 'option');
                  if ($term) : ?>
                    <?php
                    $termname = $term->name;
                    if (str_contains($site_url, '/ja-jp/')) {
                      $termname = get_field('cat_label_japanese', $term) ?: $termname;
                    } elseif (str_contains($site_url, '/fr-fr/')) {
                      $termname = get_field('cat_label_french', $term) ?: $termname;
                    } elseif (str_contains($site_url, '/de-de/')) {
                      $termname = get_field('cat_label_german', $term) ?: $termname;
                    } ?>
                    <?php echo '<option value="' . $term->slug . '">' . $termname . '</option>'; ?>
                  <?php endif; ?>
                <?php endwhile; ?>
              </select>
            </div>
          <?php endif; ?>


          <?php if (have_rows('resource_application_filter', 'option')) : ?>
            <div class="custom-select">
              <select id="application-filter" name="application">
                <option value="">
                  <?php echo !empty( $labels['all_applications'] ) ? $labels['all_applications'] : 'All Applications'; ?>
                </option>
                <?php while (have_rows('resource_application_filter', 'option')) : the_row(); ?>
                  <?php $term = get_sub_field('category_item', 'option');
                  if ($term) : ?>
                    <?php
                    $termname = $term->name;
                    if (str_contains($site_url, '/ja-jp/')) {
                      $termname = get_field('cat_label_japanese', $term) ?: $termname;
                    } elseif (str_contains($site_url, '/fr-fr/')) {
                      $termname = get_field('cat_label_french', $term) ?: $termname;
                    } elseif (str_contains($site_url, '/de-de/')) {
                      $termname = get_field('cat_label_german', $term) ?: $termname;
                    } ?>
                    <?php echo '<option value="' . $term->slug . '">' . $termname . '</option>'; ?>
                  <?php endif; ?>
                <?php endwhile; ?>
              </select>
            </div>
          <?php endif; ?>

          <?php if (have_rows('resource_industry_filter', 'option')) : ?>
            <div class="custom-select">
              <select id="industry-filter" name="industry">
                <option value="">
                  <?php echo !empty( $labels['all_industries'] ) ? $labels['all_industries'] : 'All Industries'; ?>
                </option>
                <?php while (have_rows('resource_industry_filter', 'option')) : the_row(); ?>
                  <?php $term = get_sub_field('category_item', 'option');
                  if ($term) : ?>
                    <?php
                    $termname = $term->name;
                    if (str_contains($site_url, '/ja-jp/')) {
                      $termname = get_field('cat_label_japanese', $term) ?: $termname;
                    } elseif (str_contains($site_url, '/fr-fr/')) {
                      $termname = get_field('cat_label_french', $term) ?: $termname;
                    } elseif (str_contains($site_url, '/de-de/')) {
                      $termname = get_field('cat_label_german', $term) ?: $termname;
                    } ?>
                    <?php echo '<option value="' . $term->slug . '">' . $termname . '</option>'; ?>
                  <?php endif; ?>
                <?php endwhile; ?>
              </select>
            </div>
          <?php endif; ?>

          <?php if (have_rows('resource_topic_filter', 'option')) : ?>
            <div class="custom-select">
              <select id="topic-filter" name="topic">
                <option value="">
                  <?php echo !empty( $labels['all_topics'] ) ? $labels['all_topics'] : 'All Topics'; ?>
                </option>
                <?php while (have_rows('resource_topic_filter', 'option')) : the_row(); ?>
                  <?php $term = get_sub_field('category_item', 'option');
                  if ($term) : ?>
                    <?php
                    $termname = $term->name;
                    if (str_contains($site_url, '/ja-jp/')) {
                      $termname = get_field('cat_label_japanese', $term) ?: $termname;
                    } elseif (str_contains($site_url, '/fr-fr/')) {
                      $termname = get_field('cat_label_french', $term) ?: $termname;
                    } elseif (str_contains($site_url, '/de-de/')) {
                      $termname = get_field('cat_label_german', $term) ?: $termname;
                    } ?>
                    <?php echo '<option value="' . $term->slug . '">' . $termname . '</option>'; ?>
                  <?php endif; ?>
                <?php endwhile; ?>
              </select>
            </div>
          <?php endif; ?>
          <?php if (get_field('blog_filter_clear_button_label', 'option')) : ?>
            <button aria-label="<?php echo esc_attr( get_field('blog_filter_clear_button_label', 'option') ); ?>" class="aras-button" id="clear-filters">
              <?php echo get_field('blog_filter_clear_button_label', 'option'); ?>
            </button>
          <?php else: ?>
            <button aria-label="<?php esc_attr__('Clear Filters', 'aras'); ?>" class="aras-button" id="clear-filters">
              <?php _e('Clear', 'aras'); ?>
            </button>
          <?php endif; ?>
        </form>

        <script>
          document.addEventListener('DOMContentLoaded', function() {
            var formatFilter = document.getElementById('format-filter');
            var industryFilter = document.getElementById('industry-filter');
            var applicationFilter = document.getElementById('application-filter');
            var topicFilter = document.getElementById('topic-filter');
            var clearButton = document.getElementById('clear-filters');

            function reloadPage() {
              var url = '<?php echo esc_url($default_resource_archive_url); ?>';
              var params = new URLSearchParams(window.location.search);
              <?php if (have_rows('resource_format_filter', 'option')) : ?>
                var format = formatFilter.value;
                if (params.has('format') && !format) {
                  params.delete('format');
                } else if (format) {
                  params.set('format', format);
                }
              <?php endif; ?>
              <?php if (have_rows('resource_industry_filter', 'option')) : ?>
                var industry = industryFilter.value;
                if (params.has('industry') && !industry) {
                  params.delete('industry');
                } else if (industry) {
                  params.set('industry', industry);
                }
              <?php endif; ?>
              <?php if (have_rows('resource_application_filter', 'option')) : ?>
                var application = applicationFilter.value;
                if (params.has('application') && !application) {
                  params.delete('application');
                } else if (application) {
                  params.set('application', application);
                }
              <?php endif; ?>
              <?php if (have_rows('resource_topic_filter', 'option')) : ?>
                var topic = topicFilter.value;
                if (params.has('topic') && !topic) {
                  params.delete('topic');
                } else if (topic) {
                  params.set('topic', topic);
                }
              <?php endif; ?>

              window.location.href = url + (params.toString() ? '?' + params.toString() : '');
            }

            <?php if (have_rows('resource_format_filter', 'option')) : ?>
              formatFilter.addEventListener('change', reloadPage);
            <?php endif; ?>
            <?php if (have_rows('resource_industry_filter', 'option')) : ?>
              industryFilter.addEventListener('change', reloadPage);
            <?php endif; ?>
            <?php if (have_rows('resource_application_filter', 'option')) : ?>
              applicationFilter.addEventListener('change', reloadPage);
            <?php endif; ?>
            <?php if (have_rows('resource_topic_filter', 'option')) : ?>
              topicFilter.addEventListener('change', reloadPage);
            <?php endif; ?>

            // Set default values based on URL parameters
            var params = new URLSearchParams(window.location.search);
            <?php if (have_rows('resource_format_filter', 'option')) : ?>
              var formatParam = params.get('format');
              if (formatParam) {
                var formatOption = document.querySelector('#format-filter option[value="' + formatParam + '"]');
                if (formatOption) {
                  formatOption.selected = true;
                }
              }
            <?php endif; ?>
            <?php if (have_rows('resource_industry_filter', 'option')) : ?>
              var industryParam = params.get('industry');
              if (industryParam) {
                var industryOption = document.querySelector('#industry-filter option[value="' + industryParam + '"]');
                if (industryOption) {
                  industryOption.selected = true;
                }
              }
            <?php endif; ?>
            <?php if (have_rows('resource_application_filter', 'option')) : ?>
              var applicationParam = params.get('application');
              if (applicationParam) {
                var applicationOption = document.querySelector('#application-filter option[value="' + applicationParam + '"]');
                if (applicationOption) {
                  applicationOption.selected = true;
                }
              }
            <?php endif; ?>
            <?php if (have_rows('resource_topic_filter', 'option')) : ?>
              var topicParam = params.get('topic');
              if (topicParam) {
                var topicOption = document.querySelector('#topic-filter option[value="' + topicParam + '"]');
                if (topicOption) {
                  topicOption.selected = true;
                }
              }
            <?php endif; ?>

            // Clear button
            clearButton.addEventListener('click', function(event) {
              event.preventDefault();
              window.location.href = '<?php echo esc_url($default_resource_archive_url); ?>';
            });
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
                  reloadPage();
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
      </div>
      <?/*
      <div class="cell small-12 medium-shrink">
        <form role="search" method="get" class="blog-search-form" action="<?php echo home_url('/'); ?>">
          <label>
            <span class="screen-reader-text"><?php echo _x('Search for:', 'label', 'jointswp') ?></span>
            <input type="hidden" name="post_type" value="resource" />
            <input aria-label="search" type="search" class="search-field" placeholder="" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x('Search for:', 'jointswp') ?>" />
          </label>
          <input class="search-arrow-icon" type="submit" value=" " alt="Search" style="background-image:url(<?php echo get_template_directory_uri(); ?>/assets/images/icons/searchicon.svg)" />
        </form>
      </div>
      */ ?>
    </div>
  </div>
</section>

<main class="blog-archive bg-grey mediumtoppadding largebottompadding" role="main">
  <div class="grid-container">
    <section class="grid-x grid-margin-x blog-post-loop">
      <?php if (have_posts()) : $postCount = 0;
        while (have_posts()) : the_post();
          $postCount++; ?>
          <?php get_template_part('parts/loop', 'archive-resources'); ?>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
      <?php else : ?>
        <?php get_template_part('parts/content', 'missing'); ?>
      <?php endif; ?>
    </section>
    <section class="grid-x grid-margin-x text-center align-center">
      <?php

      $load_more_label = get_field('load_more_resources_label', 'option') ?: 'Load More';
      // get next page
      $next_link = next_posts(0, false);
      ?>
        <a href="<?php echo $next_link ?>" aria-label="<?php echo esc_attr( $load_more_label ) ?>" class="aras-button" id="load-more-posts"><?php echo $load_more_label ?></a>
    </section>
  </div>
</main>



<?php if (get_field('resources_footer_cta_style', 'option') == 'form') : ?>
  <?php if (have_rows('resource_archive_subscribe_form', 'option')) : ?>
    <?php while (have_rows('resource_archive_subscribe_form', 'option')) : the_row(); ?>
      <?php if (get_sub_field('subscribe_form_embed', 'option')) : ?>
        <section class="blog-archive-cta bg-dblue mediumtoppadding mediumbottompadding">
          <div class="grid-container">
            <div class="grid-x grid-margin-x align-middle">
              <div class="cell small-12 medium-auto">
                <?php if (get_sub_field('subscribe_form_cta', 'option')) : ?>
                  <h2><?php echo get_sub_field('subscribe_form_cta', 'option'); ?></h2>
                <?php else : ?>
                  <h2>Subscribe for Updates</h2>
                <?php endif; ?>
              </div>
              <div class="cell small-12 medium-shrink">
                <button class="aras-button" data-open="blog-footer-form">
                  <?php if (get_sub_field('subscribe_form_label', 'option')) {
                    echo get_sub_field('subscribe_form_label', 'option');
                  } else {
                    echo 'Subscribe';
                  } ?>
                </button>
              </div>
            </div>
          </div>
        </section>
      <?php endif; ?>
    <?php endwhile; ?>
  <?php endif; ?>
<?php elseif (get_field('resources_footer_cta_style', 'option') == 'cta') : ?>
  <?php get_template_part('parts/_template_parts/footer_cta_resources'); ?>
<?php endif; ?>


<?php if (get_field('resources_footer_cta_style', 'option') == 'form') : ?>
  <?php if (have_rows('resource_archive_subscribe_form', 'option')) : ?>
    <?php while (have_rows('resource_archive_subscribe_form', 'option')) : the_row(); ?>
      <?php if (get_sub_field('subscribe_form_embed', 'option')) : ?>

        <div class="reveal medium" id="blog-footer-form" data-reveal>
          <div class="heroform">
            <?php if (get_sub_field('subscribe_form_headline', 'option')) {
              echo '<h3 class="text-center" style="margin-bottom: 1rem; font-weight: 400;">' . get_sub_field('subscribe_form_headline', 'option') . '<h3>';
            } ?>
            <?php $gravity_form_id = get_sub_field('subscribe_form_embed', 'option');
            echo do_shortcode('[gravityform ajax="true" id="' . $gravity_form_id . '" title="false" description="false"]'); ?>
          </div>
          <?php get_template_part('parts/_template_parts/gform_variables'); ?>
          <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

      <?php endif; ?>
    <?php endwhile; ?>
  <?php endif; ?>
<?php endif; ?>





<script>
  document.addEventListener("DOMContentLoaded", function() {
    var blogItemContents = document.querySelectorAll('.blog-item-cell');
    if (blogItemContents.length < 12) {
      var loadMoreButton = document.getElementById('load-more-posts');
      if (loadMoreButton) {
        loadMoreButton.style.display = 'none';
      }
    }
  });
</script>
<?php
// Get the current URL parameters
$current_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$query_string = parse_url($current_url, PHP_URL_QUERY);
parse_str($query_string, $variables); ?>
<?php
// Retrieve the variables
$formattax = $variables['format'] ?? null;
$industrytax = $variables['industry'] ?? null;
$applicationtax = $variables['application'] ?? null;
$topictax = $variables['topic'] ?? null;
$tax_queries = array();

if ($formattax !== null) {
  $tax_queries[] = array(
    'taxtype' => 'format',
    'taxterm' => $formattax,
  );
}

if ($industrytax !== null) {
  $tax_queries[] = array(
    'taxtype' => 'industry',
    'taxterm' => $industrytax,
  );
}

if ($applicationtax !== null) {
  $tax_queries[] = array(
    'taxtype' => 'application',
    'taxterm' => $applicationtax,
  );
}

if ($topictax !== null) {
  $tax_queries[] = array(
    'taxtype' => 'topic',
    'taxterm' => $topictax,
  );
}
?>

<?php if ($query_string == '') : ?>
  <script>
    var loadMoreAjaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
    var page = <?php echo max(2, get_query_var('paged') ? get_query_var('paged') + 1 : 2); ?>;
    var canLoadMore = true;
    jQuery(document).ready(function($) {
      jQuery('#load-more-posts').on('click', function(e) {
        e.preventDefault();
        if (canLoadMore) {
          jQuery.ajax({
            type: 'POST',
            url: loadMoreAjaxUrl,
            data: {
              action: 'load_more_resources',
              page: page,
            },
            success: function(response) {
              jQuery('.blog-post-loop').append(response);
              page++;
              // Check if there are more posts to load
              if (response.trim() === '') {
                canLoadMore = false;
                jQuery('#load-more-posts').hide();
              } else {
                // Update browser URL with new page
                //history.pushState(null, null, '?paged=' + (page - 1));

                // Get the current URL
                var currentUrl = window.location.href;
                // Extract the language parameter, if it exists
                var languageParam = '';
                if (currentUrl.includes('?language')) {
                  var languageIndex = currentUrl.indexOf('?language');
                  languageParam = currentUrl.substring(languageIndex);
                  // Remove the language parameter from the current URL
                  currentUrl = currentUrl.substring(0, languageIndex);
                }

                // Remove existing page segment if it exists
                var baseUrl = currentUrl.replace(/\/page\/\d+/, '');

                // Construct the new URL with the pagination segment
                var newUrl = baseUrl + '/page/' + (page - 1);

                // Append the language parameter, if it exists
                if (languageParam !== '') {
                  newUrl += '' + languageParam;
                }

                var nextUrl = baseUrl + '/page/' + page;
                jQuery('#load-more-posts').attr('href', nextUrl );

                // Update browser URL with new page
                history.pushState(null, null, newUrl);
              }
            },
          });
        }
      });
    });
  </script>
<?php else : ?>
  <script>
    var loadMoreAjaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
    var page = <?php echo max(2, get_query_var('paged') ? get_query_var('paged') + 1 : 2); ?>;
    var canLoadMore = true;
    var tax_queries = <?php echo json_encode($tax_queries); ?>;

    jQuery(document).ready(function($) {
      jQuery('#load-more-posts').on('click', function(e) {
        e.preventDefault();
        
        if (canLoadMore) {
          jQuery.ajax({
            type: 'POST',
            url: loadMoreAjaxUrl,
            data: {
              action: 'load_more_resources_by_taxonomy',
              page: page,
              tax_queries: tax_queries,
            },
            success: function(response) {
              console.log( response );
              jQuery('.blog-post-loop').append(response);
              page++;
              // Check if there are more posts to load
              if (response.trim() === '') {
                canLoadMore = false;
                jQuery('#load-more-posts').hide();
              } else {
                // Update browser URL with new page
                //history.pushState(null, null, '?paged=' + (page - 1));

                // Get the current URL
                var currentUrl = window.location.href;

                // Extract the language parameter, if it exists
                var languageParam = '';
                if (currentUrl.includes('?language')) {
                  var languageIndex = currentUrl.indexOf('?language');
                  languageParam = currentUrl.substring(languageIndex);
                  // Remove the language parameter from the current URL
                  currentUrl = currentUrl.substring(0, languageIndex);
                }

                // Extract the query parameters, if they exist
                var queryParams = '';
                if (currentUrl.includes('?')) {
                  var queryIndex = currentUrl.indexOf('?');
                  queryParams = currentUrl.substring(queryIndex);
                  // Remove the query parameters from the current URL
                  currentUrl = currentUrl.substring(0, queryIndex);
                }

                // Remove existing page segment if it exists
                var baseUrl = currentUrl.replace(/\/page\/\d+/, '');

                // Construct the new URL with the pagination segment
                var newUrl = baseUrl + '/page/' + (page - 1);

                // Append the query parameters, if they exist
                if (queryParams !== '') {
                  newUrl += queryParams;
                }

                // Append the language parameter, if it exists
                if (languageParam !== '') {
                  newUrl += '' + languageParam;
                }

                // Update browser URL with new page
                history.pushState(null, null, newUrl);
              }
            },
          });
        }
      });
    });
  </script>
<?php endif; ?>

<?php get_footer(); ?>