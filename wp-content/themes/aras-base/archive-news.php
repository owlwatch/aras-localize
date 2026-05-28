<?php
get_header();
$site_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>

<section id="short-hero" class="short-hero hero-banner bg-dblue">
  <div class="grid-container">
    <div class="grid-x grid-padding-x align-top">
      <div class="cell small-12 medium-10 hero-content">
        <div class="hero-content-inner">
          <?php if (get_field('news_archive_headline', 'option')) : ?>
            <h1 class="hero-headline"><?php echo get_field('news_archive_headline', 'option'); ?></h1>
          <?php else : ?>
            <h1 class="hero-headline"><?php esc_html_e('News', 'aras'); ?></h1>
          <?php endif; ?>
          <?php if (get_field('news_archive_subheadline', 'option')) : ?>
            <h2 class="hero-subhead"><?php echo get_field('news_archive_subheadline', 'option'); ?></h2>
          <?php else : ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
<?/*
<section class="news-filter bg-grey smalltoppadding smallbottompadding">
  <div class="grid-container">
    <div class="grid-x grid-margin-x align-center">
      <div class="cell small-12 news-filter-flex">
        <?php
        $terms = get_terms(array(
          'taxonomy' => 'news_type',
          'hide_empty' => true,
        ));
        echo '<select id="news-type-dropdown">';
        echo '<option value="' . get_post_type_archive_link('news') . '">All News</option>';
        foreach ($terms as $term) {
          echo '<option value="' . get_term_link($term) . '">' . $term->name . '</option>';
        }
        echo '</select>';
        wp_reset_postdata();
        ?>
        <script type="text/javascript">
          document.getElementById('news-type-dropdown').addEventListener('change', function() {
            var selectedValue = this.value;
            if (selectedValue !== '') {
              window.location = selectedValue;
            }
          });
        </script>
        <form role="search" method="get" class="news-search-form" action="<?php echo home_url('/'); ?>">
          <label>
          <span class="screen-reader-text"><?php echo esc_html_x('Search for:', 'label', 'aras'); ?></span>
            <input type="hidden" name="post_type" value="news" />
          <input aria-label="<?php echo esc_attr__('Search', 'aras'); ?>" type="search" class="search-field" placeholder="" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x('Search for:', 'label', 'aras') ?>" />
          </label>
          <input class="search-arrow-icon" type="submit" value=" " alt="<?php echo esc_attr__('Search', 'aras'); ?>" style="background-image:url(<?php echo get_template_directory_uri(); ?>/assets/images/icons/searchicon.svg)" />
        </form>
      </div>
    </div>
  </div>
</section>
*/ ?>
<main class="news-archive box-bg-white mediumtoppadding largebottompadding" role="main">
  <div class="grid-container">
    <section class="grid-x grid-margin-x news-post-loop">
      <?php
      $args = array(
        'post_type'      => 'news',
        'posts_per_page' => 12,
        'paged'          => $page,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish',
        'tax_query' => array(
          array(
            'taxonomy' => 'news_type',
            'field' => 'slug',
            'terms' => 'media-coverage',
            'operator' => 'NOT IN',
          ),
        ),
      );
      if (strpos($site_url, '/page/') !== false) {
        $pos = strpos($site_url, '/page/');
        $page_substr = substr($site_url, $pos + 6); // 6 is the length of '/page/'
        $page_number = intval($page_substr);
        $args['paged'] = $page_number;
      }

      $query = new WP_Query($args);

      if ($query->have_posts()) :
        $postCount = 0;
        while ($query->have_posts()) : $query->the_post();
          $postCount++;  ?>

          <?php get_template_part('parts/loop', 'archive-news'); ?>

        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
      <?php else : ?>
        <?php get_template_part('parts/content', 'missing'); ?>
      <?php endif; ?>
    </section>
    <section class="grid-x grid-margin-x text-center align-center">
      <?php
      $load_more_label = get_field('load_more_news_label', 'option') ?: __('Load More', 'aras');
      $next_link = next_posts(0, false);
      ?>
        <a href="<?php echo $next_link ?>" aria-label="<?php echo esc_attr( $load_more_label ) ?>" class="aras-button" id="load-more-posts"><?php echo $load_more_label ?></a>
    </section>
  </div>
</main>
<?php get_template_part('parts/_template_parts/footer_cta_news'); ?>
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
            action: 'load_more_news',
            page: page,
          },
          success: function(response) {
            jQuery('.news-post-loop').append(response);
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


<?php get_footer(); ?>