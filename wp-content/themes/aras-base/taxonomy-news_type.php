<?php
get_header(); ?>

<section id="short-hero" class="short-hero hero-banner bg-dblue">
  <div class="grid-container">
    <div class="grid-x grid-padding-x align-top">
      <div class="cell small-12 medium-10 hero-content">
        <div class="hero-content-inner">
          <?php if (get_field('news_archive_headline', 'option')) : ?>
            <h1 class="hero-headline <?php echo $h1color; ?>"><?php echo get_field('news_archive_headline', 'option'); ?></h1>
          <?php else : ?>
            <h1 class="hero-headline <?php echo $h1color; ?>"><?php esc_html_e('News', 'aras'); ?></h1>
          <?php endif; ?>
          <?php if (get_field('news_archive_subheadline', 'option')) : ?>
            <h2 class="hero-subhead <?php echo $h1color; ?>"><?php echo get_field('news_archive_subheadline', 'option'); ?></h2>
          <?php else : ?>
            <h2 class="hero-subhead <?php echo $h1color; ?>"><?php esc_html_e('Latest News & Press Coverage', 'aras'); ?></h2>
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
        <?php
        $terms = get_terms(array(
          'taxonomy' => 'news_type',
          'hide_empty' => true,
        ));
        echo '<select id="news-type-dropdown">';
        echo '<option value="' . get_post_type_archive_link('news') . '">' . esc_html__('All News', 'aras') . '</option>';
        foreach ($terms as $term) {
          echo '<option value="' . get_term_link($term) . '">' . $term->name . '</option>';
        }
        echo '</select>';
        wp_reset_postdata();
        ?>
        <script type="text/javascript">
          document.getElementById('news-type-dropdown').addEventListener('change', function() {
            var selectedValue = this.value;
            console.log(selectedValue);
            if (selectedValue !== '') {
              window.location = selectedValue;
            }
          });
          /* Check the page title and apply it to the dropdown */
          document.addEventListener("DOMContentLoaded", function() {
            var pageTitle = '<?php single_term_title(); ?>';
            var dropdown = document.getElementById('news-type-dropdown');
            for (var i = 0; i < dropdown.options.length; i++) {
              if (dropdown.options[i].text === pageTitle) {
                dropdown.selectedIndex = i;
                break;
              }
            }
          });
        </script>
        <form role="search" method="get" class="news-search-form" action="<?php echo home_url('/'); ?>">
          <label>
            <span class="screen-reader-text"><?php echo _x('Search for:', 'label', 'aras') ?></span>
            <input type="hidden" name="post_type" value="news" />
            <input aria-label="<?php echo esc_attr__('Search', 'aras'); ?>" type="search" class="search-field" placeholder="" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x('Search for:', 'label', 'aras') ?>" />
          </label>
          <input class="search-arrow-icon" type="submit" value=" " alt="<?php echo esc_attr__('Search', 'aras'); ?>" style="background-image:url(<?php echo get_template_directory_uri(); ?>/assets/images/icons/searchicon.svg)" />
        </form>
      </div>
    </div>
  </div>
</section>



<main class="news-archive box-bg-white mediumtoppadding largebottompadding" role="main">
  <div class="grid-container">
    <section class="grid-x grid-margin-x news-post-loop">
      <?php if (have_posts()) : $postCount = 0;
        while (have_posts()) : the_post();
          $postCount++; ?>
          <?php get_template_part('parts/loop', 'archive-news'); ?>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
      <?php else : ?>
        <?php get_template_part('parts/content', 'missing'); ?>
      <?php endif; ?>
    </section>
    <section class="grid-x grid-margin-x text-center align-center">
      <?php if (get_field('load_more_news_label', 'option')) : ?>
        <button aria-label="<?php echo get_field('load_more_news_label', 'option'); ?>" class="aras-button" id="load-more-posts"><?php echo get_field('load_more_news_label', 'option'); ?></button>
      <?php else : ?>
        <button aria-label="<?php echo esc_attr__('Load more news', 'aras'); ?>" class="aras-button" id="load-more-posts"><?php esc_html_e('Load More', 'aras'); ?></button>
      <?php endif; ?>
    </section>
  </div>
</main>

<?php get_template_part('parts/_template_parts/footer_cta_news'); ?>


<script>
  var loadMoreAjaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
  var page = <?php echo max(2, get_query_var('paged') ? get_query_var('paged') + 1 : 2); ?>;
  var category_id = '<?php echo get_query_var('news_type'); ?>';
  var canLoadMore = true;
  jQuery(document).ready(function($) {
    jQuery('#load-more-posts').on('click', function() {
      if (canLoadMore) {
        jQuery.ajax({
          type: 'POST',
          url: loadMoreAjaxUrl,
          data: {
            action: 'load_more_type_news',
            page: page,
            category_id: category_id,
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
            }
          },
        });
      }
    });
  });
</script>
<?php get_footer(); ?>