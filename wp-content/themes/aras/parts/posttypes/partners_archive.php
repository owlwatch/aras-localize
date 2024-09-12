<?php $default_post_archive_url = get_permalink(get_option('page_for_posts'));
$site_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
<section class="partners-hero-banner largetoppadding largebottompadding bg-dblue">
  <div class="grid-container">
    <div class="grid-x grid-padding-x">
      <div class="cell small-12 medium-8 large-6 hero-content">
        <?php if (get_field('partners_archive_title', 'option')) : ?>
          <h1 class="hero-headline"><?php echo get_field('partners_archive_title', 'option'); ?></h1>
        <?php else : ?>
          <h1 class="hero-headline">Find a Partner</h1>
        <?php endif; ?>
        <?php if (get_field('partners_archive_description', 'option')) : ?>
          <p><?php echo get_field('partners_archive_description', 'option'); ?></p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php $filter_data = load_data_from_file('Partner_Categories.json');
// Function to generate slug from termname
function generate_slug($termname)
{
  // Convert to lowercase
  $slug = strtolower($termname);
  // Replace spaces with dashes
  $slug = str_replace(' ', '-', $slug);
  // Remove special characters
  $slug = preg_replace('/[^A-Za-z0-9\-]/', '', $slug);
  return $slug;
}
?>

<main id="partners-section" data-sort-by="">
  <section class="partners-filter bg-grey smalltoppadding smallbottompadding">
    <div class="grid-container">
      <form id="multifilter-controls" class="grid-x grid-margin-x align-center">
        <div class="cell small-12">
          <h2>
            <?php if (get_field('partner_filter_headline', 'option')) : ?>
              <?php echo get_field('partner_filter_headline', 'option'); ?>
            <?php else : ?>
              Filter
            <?php endif; ?>
          </h2>
        </div>
        <fieldset class="cell small-12 medium-6 large-4 custom-select partners" data-filter-group="partner-type" data-logic="and">
          <select id="partner-type">
            <option value="">
              <?php if (get_field('partner_type_label', 'option')) : ?>
                <?php echo get_field('partner_type_label', 'option'); ?>
              <?php else : ?>
                Choose Type
              <?php endif; ?>
            </option>
            <?php foreach ($filter_data['Type_Partner__c'] as $termname) : ?>
              <?php if (!empty($termname)) : ?>
                <?php $termslug = generate_slug($termname); ?>
                <option value=".<?php echo $termslug; ?>"><?php echo $termname; ?></option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </fieldset>
        <fieldset class="cell small-12 medium-6 large-4 custom-select partners" data-filter-group="partner-region" data-logic="and">
          <select id="partner-region">
            <option value="">
              <?php if (get_field('partner_region_filter_label', 'option')) : ?>
                <?php echo get_field('partner_region_filter_label', 'option'); ?>
              <?php else : ?>
                Choose Region
              <?php endif; ?>
            </option>
            <?php foreach ($filter_data['Regions_Partner__c'] as $termname) : ?>
              <?php if (!empty($termname)) : ?>
                <?php $termslug = generate_slug($termname); ?>
                <option value=".<?php echo $termslug; ?>"><?php echo $termname; ?></option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </fieldset>
        <fieldset class="cell small-12 medium-6 large-4 custom-select partners" data-filter-group="partner-industry" data-logic="and">
          <select id="partner-industry">
            <option value="">
              <?php if (get_field('partner_industry_filter_label', 'option')) : ?>
                <?php echo get_field('partner_industry_filter_label', 'option'); ?>
              <?php else : ?>
                Choose Industry
              <?php endif; ?>
            </option>
            <?php foreach ($filter_data['Industries_Partner__c'] as $termname) : ?>
              <?php if (!empty($termname)) : ?>
                <?php $termslug = generate_slug($termname); ?>
                <option value=".<?php echo $termslug; ?>"><?php echo $termname; ?></option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </fieldset>
        <fieldset class="cell small-12 medium-6 large-4 custom-select partners" data-filter-group="partner-integration" data-logic="and">
          <select id="partner-integration">
            <option value="">
              <?php if (get_field('partner_integration_filter_label', 'option')) : ?>
                <?php echo get_field('partner_integration_filter_label', 'option'); ?>
              <?php else : ?>
                Choose Integration
              <?php endif; ?>
            </option>
            <?php foreach ($filter_data['Partner_Integrations__c'] as $termname) : ?>
              <?php if (!empty($termname)) : ?>
                <?php $termslug = generate_slug($termname); ?>
                <option value=".<?php echo $termslug; ?>"><?php echo $termname; ?></option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </fieldset>
        <fieldset class="cell small-12 medium-6 large-4 custom-select partners" data-filter-group="partner-solution" data-logic="and">
          <select id="partner-solution">
            <option value="">
              <?php if (get_field('partner_solution_filter_label', 'option')) : ?>
                <?php echo get_field('partner_solution_filter_label', 'option'); ?>
              <?php else : ?>
                Choose Solution
              <?php endif; ?>
            </option>
            <?php foreach ($filter_data['Partner_Solutions__c'] as $termname) : ?>
              <?php if (!empty($termname)) : ?>
                <?php $termslug = generate_slug($termname); ?>
                <option value=".<?php echo $termslug; ?>"><?php echo $termname; ?></option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </fieldset>
        <fieldset class="cell small-12 medium-6 large-4">
          <button aria-label="Clear Filters" id="clear-button" type="reset">
            <?php if (get_field('partner_clear_filters_label', 'option')) : ?>
              <?php echo get_field('partner_clear_filters_label', 'option'); ?>
            <?php else : ?>
              Clear Filters
            <?php endif; ?>
          </button>
        </fieldset>
      </form>
    </div>
  </section>

  <main class="partner-archive largetoppadding largebottompadding bg-white" role="main">
    <div class="grid-container">
      <section class="grid-x align-center align-middle partner-post-loop" id="multifilter-container">
        <?php
        $args = array(
          'post_type' => 'partners',
          'posts_per_page' => -1,
          'orderby'        => 'title',
          'order'          => 'ASC',
        );
        $partners_query = new WP_Query($args);
        if ($partners_query->have_posts()) : $postCount = 0;
          while ($partners_query->have_posts()) : $partners_query->the_post();
            $postCount++; ?>
            <?php get_template_part('parts/loop-archive-partners'); ?>
          <?php endwhile; ?>
          <?php wp_reset_postdata(); ?>
        <?php else : ?>
          <?php get_template_part('parts/content', 'missing'); ?>
        <?php endif; ?>
        <div class="cell text-center">
          <h3 id="no-posts-text" style="display: none;">
            <?php if (get_field('partner_filter_no_results_label', 'option')) : ?>
              <?php echo get_field('partner_filter_no_results_label', 'option'); ?>
            <?php else : ?>
              Sorry, no partners meet this criteria. Please adjust your filter settings.
            <?php endif; ?>
          </h3>
        </div>
      </section>
    </div>
  </main>
</main>

<?php get_template_part('parts/_template_parts/footer_cta_partners'); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/mixitup/3.3.1/mixitup.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/scripts/mixitup/mixitup-pagination.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/scripts/mixitup/mixitup-multifilter.min.js"></script>
<script>
  jQuery('#partners-section').each(function() {
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
          enable: true
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
      if (jQuery('#partner-type option[value="' + hash + '"]').length > 0) {
        jQuery("#partner-type").val(hash).change();
      }
      if (jQuery('#partner-region option[value="' + hash + '"]').length > 0) {
        jQuery("#partner-region").val(hash).change();
      }
      if (jQuery('#partner-industry option[value="' + hash + '"]').length > 0) {
        jQuery("#partner-industry").val(hash).change();
      }
      if (jQuery('#partner-integration option[value="' + hash + '"]').length > 0) {
        jQuery("#partner-integration").val(hash).change();
      }
      if (jQuery('#partner-solution option[value="' + hash + '"]').length > 0) {
        jQuery("#partner-solution").val(hash).change();
      }
    }
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
          const filter_value = this.dataset.targetclass;
          const filter_class = filter_value ? filter_value : 'all'
          updateFilters();
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

    function updateFilters(){
      let selects = document.querySelectorAll('.custom-select select');
      let filters = '';
      selects.forEach( s => {
        if( s.value ){
          filters += s.value;
        }
      });

      if( filters ){
        mixer.filter( filters );
      }
      else {
        mixer.show();
      }
      
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