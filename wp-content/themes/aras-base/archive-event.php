<?php
get_header();
$default_event_archive_url = get_post_type_archive_link('event');
$site_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
<section id="short-hero" class="short-hero hero-banner bg-dblue">
  <div class="grid-container">
    <div class="grid-x grid-padding-x align-top">
      <div class="cell small-12 medium-10 hero-content">
        <div class="hero-content-inner">
          <?php if (get_field('events_archive_headline', 'option')) : ?>
            <h1 class="hero-headline"><?php echo get_field('events_archive_headline', 'option'); ?></h1>
          <?php else : ?>
            <h1 class="hero-headline"><?php esc_html_e('Events', 'aras'); ?></h1>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
<section class="news-filter bg-grey smalltoppadding smallbottompadding">
  <div class="grid-container">
    <div class="grid-x grid-margin-x align-center">
      <div class="cell small-12 news-filter-flex">
        <?php function get_terms_with_posts($taxonomy)
        {
          $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => true,
          ));
          $terms_with_posts = array();
          foreach ($terms as $term) {
            $term_count = $term->count;
            if ($term_count > 0) {
              $terms_with_posts[] = $term;
            }
          }
          return $terms_with_posts;
        } ?>

        <?php // Event Type Filter
        $event_type_terms = get_terms_with_posts('event_type');
        if (!empty($event_type_terms)) :
        ?>
          <div class="custom-select events">
            <select style="margin: 0;" id="event-type-filter" name="event_type">
              <option value=""><?php esc_html_e('All Event Types', 'aras'); ?></option>
              <?php foreach ($event_type_terms as $term) : ?>
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
              <?php endforeach; ?>
            </select>
          </div>
        <?php endif; ?>

        <?php // Event Region Filter
        $event_region_terms = get_terms_with_posts('event_region');
        if (!empty($event_region_terms)) :
        ?>
          <div class="custom-select events">
            <select style="margin: 0;" id="event-region-filter" name="event_region">
              <option value=""><?php esc_html_e('All Event Regions', 'aras'); ?></option>
              <?php foreach ($event_region_terms as $term) : ?>
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
              <?php endforeach; ?>
            </select>
          </div>
        <?php endif; ?>

        <button aria-label="<?php echo esc_attr__('Clear filters', 'aras'); ?>" class="aras-button" id="clear-filters"><?php esc_html_e('Clear', 'aras'); ?></button>
      </div>
    </div>
  </div>
</section>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var eventTypeFilter = document.getElementById('event-type-filter');
    var eventRegionFilter = document.getElementById('event-region-filter');
    var clearButton = document.getElementById('clear-filters');

    function reloadPage() {
      var url = '<?php echo esc_url($default_event_archive_url); ?>';
      var params = new URLSearchParams(window.location.search);
      var eventType = eventTypeFilter.value;
      if (params.has('event_type') && !eventType) {
        params.delete('event_type');
      } else if (eventType) {
        params.set('event_type', eventType);
      }
      var eventRegion = eventRegionFilter.value;
      if (params.has('event_region') && !eventRegion) {
        params.delete('event_region');
      } else if (eventRegion) {
        params.set('event_region', eventRegion);
      }
      window.location.href = url + (params.toString() ? '?' + params.toString() : '');
    }
    eventTypeFilter.addEventListener('change', reloadPage);
    eventRegionFilter.addEventListener('change', reloadPage);
    // Set default values based on URL parameters
    var params = new URLSearchParams(window.location.search);
    var eventTypeParam = params.get('event_type');
    if (eventTypeParam) {
      var eventTypeOption = document.querySelector('#event-type-filter option[value="' + eventTypeParam + '"]');
      if (eventTypeOption) {
        eventTypeOption.selected = true;
      }
    }
    var isJaJp = <?php echo str_contains( $site_url, '/ja-jp/' ) ? 'true' : 'false'; ?>;
    var eventRegionParam = params.get('event_region');
    // On ja-jp, pre-select APAC when no event_region param is present in the URL
    var defaultRegion = ( isJaJp && ! params.has('event_region') ) ? 'apac' : eventRegionParam;
    if (defaultRegion) {
      var eventRegionOption = document.querySelector('#event-region-filter option[value="' + defaultRegion + '"]');
      if (eventRegionOption) {
        eventRegionOption.selected = true;
      }
    }
    // Clear button
    // On ja-jp, navigate to ?event_region= (explicit empty) so the default APAC filter is not re-applied
    clearButton.addEventListener('click', function(event) {
      event.preventDefault();
      <?php if ( str_contains( $site_url, '/ja-jp/' ) ) : ?>
      window.location.href = '<?php echo esc_url($default_event_archive_url); ?>?event_region=';
      <?php else : ?>
      window.location.href = '<?php echo esc_url($default_event_archive_url); ?>';
      <?php endif; ?>
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
          reloadPage()
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

<main class="blog-archive bg-white mediumtoppadding largebottompadding" role="main">
  <div class="grid-container">
    <section class="grid-x grid-margin-x blog-post-loop">
      <?php
      // Get the current URL parameters
      $current_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
      $query_string = parse_url($current_url, PHP_URL_QUERY);
      parse_str($query_string, $variables);
      // Retrieve the variables
      $typetax = $variables['event_type'] ?? null;

      // When on ja-jp and no event_region param is present, default to APAC.
      // An explicit ?event_region= (empty string) means "show all regions".
      if ( array_key_exists( 'event_region', $variables ) ) {
        $regiontax = $variables['event_region']; // empty string = all regions
      } elseif ( str_contains( $site_url, '/ja-jp/' ) ) {
        $regiontax = 'apac'; // default slug — adjust if your term slug differs
      } else {
        $regiontax = null;
      }

      $tax_queries = array();
      if ($typetax !== null) {
        $tax_queries[] = array(
          'taxtype' => 'event_type',
          'taxterm' => $typetax,
        );
      }
      if ($regiontax !== null && $regiontax !== '') {
        $tax_queries[] = array(
          'taxtype' => 'event_region',
          'taxterm' => $regiontax,
        );
      }
      $current_date = date('Ymd');
      $meta_query = array(
        array(
          'key' => 'event_date', // Make sure this meta key exists for 'event' post type
          'value' => $current_date,
          'compare' => '>=',
          'type' => 'DATE'
        )
      );
      $main_tax_query = array();
      if (!empty($tax_queries)) {
        foreach ($tax_queries as $tax_query) {
          $main_tax_query[] = array(
            'taxonomy' => $tax_query['taxtype'],
            'field'    => 'slug',
            'terms'    => $tax_query['taxterm'],
          );
        }
      }
      $args = array(
        'post_type' => 'event',
        'posts_per_page' => -1,
        'order'          => 'ASC',
        'orderby' => 'meta_value',
        'meta_key' => 'event_date',
        'meta_query' => $meta_query,
        'order' => 'ASC',
      );
      $args['tax_query'] = $main_tax_query;
      $event_query = new WP_Query($args);
      ?>

      <?php if ($event_query->have_posts()) : $postCount = 0;
        while ($event_query->have_posts()) : $event_query->the_post();
          $postCount++; ?>

          <?php get_template_part('parts/loop', 'archive-events'); ?>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
      <?php else : ?>
        <?php get_template_part('parts/content', 'missing'); ?>
      <?php endif; ?>
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
                  <h2><?php esc_html_e('Subscribe for Updates', 'aras'); ?></h2>
                <?php endif; ?>
              </div>
              <div class="cell small-12 medium-shrink">
                <button class="aras-button" data-open="blog-footer-form">
                  <?php if (get_sub_field('subscribe_form_label', 'option')) {
                    echo get_sub_field('subscribe_form_label', 'option');
                  } else {
                    echo esc_html__('Subscribe', 'aras');
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


<?php get_footer(); ?>