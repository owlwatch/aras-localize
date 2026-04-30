<?php $default_post_archive_url = get_permalink(get_option('page_for_posts'));
$site_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$background_image = get_field('partner_archive_hero_background_image', 'option');
?>
<?php if( $background_image ) { ?>
<style>
.partners-hero-banner::after {
  background-image: url('<?php echo esc_url($background_image['url']); ?>');
}
</style>
<?php } ?>
<section 
  class="partners-hero-banner largetoppadding largebottompadding bg-dblue"
>
  <div class="grid-container">
    <div class="grid-x grid-padding-x">
      <div class="cell small-12 medium-8 large-6 hero-content">
        <?php if (get_field('partners_archive_title', 'option')) : ?>
          <h1 class="hero-headline"><?php echo get_field('partners_archive_title', 'option'); ?></h1>
        <?php else : ?>
		  <h1 class="hero-headline"><?php esc_html_e('Find a Partner', 'aras'); ?></h1>
        <?php endif; ?>
        <?php if (get_field('partners_archive_description', 'option')) : ?>
          <p><?php echo get_field('partners_archive_description', 'option'); ?></p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php $filter_data = load_data_from_file('Partner_Categories.json');

// lets go through each of these and sort them alphabetically
foreach( $filter_data as $key => &$values ){
  if( is_array($values) ){
    sort( $values );
  }
}

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
      
      <form id="multifilter-controls">
        <div class="partner-filters__heading">
          <h2>
            <?php if (get_field('partner_filter_headline', 'option')) : ?>
              <?php echo get_field('partner_filter_headline', 'option'); ?>
            <?php else : ?>
			  <?php esc_html_e('Filter', 'aras'); ?>
            <?php endif; ?>

          </h2>
          <div class="partner-filters__clear">
      		  <button aria-label="<?php echo esc_attr__('Clear Filters', 'aras'); ?>" id="clear-button" type="reset" disabled>
              <?php if (get_field('partner_clear_filters_label', 'option')) : ?>
                <?php echo get_field('partner_clear_filters_label', 'option'); ?>
              <?php else : ?>
      				<?php esc_html_e('Clear Filters', 'aras'); ?>
              <?php endif; ?>
            </button>
          </div>
        </div>
        <div  class="partner-filters">

          <fieldset class="partner-filters__item" data-filter-group="certification" data-logic="and">
            <label for="partner-certification">
				<?php echo get_field('partner_certification_filter_label', 'option') ?: esc_html__('Certification', 'aras'); ?>
            </label>
              <select id="certification">
                <option value="">
				  <?php echo get_field('partner_certification_filter_label_all', 'option') ?: esc_html__('All Certifications', 'aras'); ?>
                </option>
                <?php foreach ($filter_data['Certifications__c'] as $termname) : ?>
                  <?php if (!empty($termname)) : ?>
                    <?php $termslug = generate_slug('cert_'.$termname); ?>
                    <option value=".<?php echo $termslug; ?>"><?php echo $termname; ?></option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
          </fieldset>
          
          <fieldset class="partner-filters__item" data-filter-group="partner-type" data-logic="and">
            <label for="partner-type">
				<?php echo get_field('partner_type_label', 'option') ?: esc_html__('Type', 'aras'); ?>
            </label>
              <select id="partner-type">
                <option value="">
				  <?php echo get_field('partner_type_label_all', 'option') ?: esc_html__('All Types', 'aras'); ?>
                </option>
                <?php foreach ($filter_data['Type_Partner__c'] as $termname) : ?>
                  <?php if (!empty($termname)) : ?>
                    <?php $termslug = generate_slug($termname); ?>
                    <option value=".<?php echo $termslug; ?>"><?php echo $termname; ?></option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
          </fieldset>

          <fieldset class="partner-filters__item" style="display: none;" data-filter-group="partner-solution" data-logic="and">
            <label for="partner-solution">
			  <?php echo get_field('partner_solution_filter_label', 'option') ?: esc_html__('Solution', 'aras'); ?>
            </label>
            <select id="partner-solution">
              <option value="">
				<?php echo get_field('partner_solution_filter_label_all', 'option') ?: esc_html__('All Solutions', 'aras'); ?>
              </option>
              <?php foreach ($filter_data['Partner_Solutions__c'] as $termname) : ?>
                <?php if (!empty($termname)) : ?>
                  <?php $termslug = generate_slug($termname); ?>
                  <option value=".<?php echo $termslug; ?>"><?php echo $termname; ?></option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
          </fieldset>

          <fieldset class="partner-filters__item" data-filter-group="partner-region" data-logic="and">
            
            <label for="partner-region">
              <?php echo get_field('partner_region_filter_label', 'option') ?: 'Region' ?>
            </label>

            <select id="partner-region">
              <option value="">
              <?php echo get_field('partner_region_filter_label_all', 'option') ?: 'All Regions' ?>
              </option>
              <?php foreach ($filter_data['Regions_Partner__c'] as $termname) : ?>
                <?php if (!empty($termname)) : ?>
                  <?php $termslug = generate_slug($termname); ?>
                  <option value=".<?php echo $termslug; ?>"><?php echo $termname; ?></option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
          </fieldset>


          <fieldset class="partner-filters__item" data-filter-group="partner-industry" data-logic="and">
            <label for="partner-industry">
              <?php echo get_field('partner_industry_filter_label', 'option') ?: 'Industry' ?>
            </label>
            
            <select id="partner-industry">
              <option value="">
                <?php echo get_field('partner_industry_filter_label_all', 'option') ?: 'All Industries' ?>
              </option>
              <?php foreach ($filter_data['Industries_Partner__c'] as $termname) : ?>
                <?php if (!empty($termname)) : ?>
                  <?php $termslug = generate_slug($termname); ?>
                  <option value=".<?php echo $termslug; ?>"><?php echo $termname; ?></option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
            
          </fieldset>
        </div>
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
    

    function updateFilters(){

      let selects = document.querySelectorAll('.partner-filters select');
      let filters = '';

      jQuery('.partner-filters select').off('change', updateFilters );

      // check for solutions
      if( jQuery('#partner-type').val() == '.solutions' ){
        jQuery('fieldset[data-filter-group="partner-solution"]').show()
      }
      else {
        jQuery('fieldset[data-filter-group="partner-solution"]')
          .hide().find('select').val('');
      }

      setTimeout( () => {
        
        selects.forEach( s => {
          if( s.value ){
            filters += s.value;
          }
        });

        if( filters ){
          jQuery('#clear-button').prop('disabled', false);
          mixer.filter( filters );
        }
        else {
          jQuery('#clear-button').prop('disabled', true);
          mixer.show();
        }
        jQuery('.partner-filters select').on('change', updateFilters );
      },1);
      
    }

    jQuery('.partner-filters select').on('change', updateFilters );

    document.querySelector('#clear-button').addEventListener('click', onClear);
    document.querySelector('#multifilter-controls').addEventListener('reset', onClear);
    function onClear(){
      setTimeout( updateFilters, 100)
    }



    
  });
</script>