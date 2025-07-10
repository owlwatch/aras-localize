<?php

use Aras\WPML\WPML_Helper;

 $default_post_archive_url = get_permalink(get_option('page_for_posts'));
$site_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$blog_backlink = 'Back to Blog';
$blog_backlink = get_field('blog_backlink_label', 'option') ?: $blog_backlink;
?>
<section class="archive-hero-banner">
  <div class="grid-container">
    <div class="grid-x grid-padding-x">
      <div class="cell small-12 medium-6 large-7 hero-content">
        <?php if (is_author() || is_category() || is_tag()) : ?>
          <h6><a aria-label="Back to blog link" class="breadcrumb" href="<?php echo $default_post_archive_url; ?>">←&nbsp;<?php echo $blog_backlink; ?></a></h6>
          <?php if (is_author()) : ?>
            <?php $author = get_user_by('slug', get_query_var('author_name'));
            $author_intro = 'Author:';
            $author_intro = get_field('author_page_heading', 'option') ?: $author_intro;
            ?>
            <h1 class="hero-headline"><?php echo $author_intro . ' ' . $author->display_name ?></h1>
          <?php elseif (is_category()) : ?>
            <?php $category = get_queried_object();
            $cat_label = single_cat_title('', false);
            // $cat_intro = 'Category:';
            // $cat_intro = get_field('category_page_heading', 'option') ?: $cat_intro;
            if (str_contains($site_url, '/ja-jp/')) {
              $cat_label = get_field('cat_label_japanese', $category) ?: $cat_label;
            } elseif (str_contains($site_url, '/fr-fr/')) {
              $cat_label = get_field('cat_label_french', $category) ?: $cat_label;
            } elseif (str_contains($site_url, '/de-de/')) {
              $cat_label = get_field('cat_label_german', $category) ?: $cat_label;
            }
            /* <h1 class="hero-headline"><?php echo $cat_intro . ' ' . $cat_label; ?></h1> */
            ?>
            <h1 class="hero-headline"><?php echo $cat_label . ' ' . __('Blog', 'aras'); ?></h1>
          <?php elseif (is_tag()) : ?>

            <?php $tag = get_queried_object();
            $tag_label = single_tag_title('', false);
            $tag_intro = 'Tag:';
            $tag_intro = get_field('tag_page_heading', 'option') ?: $tag_intro;
            if (str_contains($site_url, '/ja-jp/')) {
              $tag_label = get_field('cat_label_japanese', $tag) ?: $tag_label;
            } elseif (str_contains($site_url, '/fr-fr/')) {
              $tag_label = get_field('cat_label_french', $tag) ?: $tag_label;
            } elseif (str_contains($site_url, '/de-de/')) {
              $tag_label = get_field('cat_label_german', $tag) ?: $tag_label;
            }
            ?>
            <h1 class="hero-headline"><?php echo $tag_intro . ' ' . $tag_label; ?></h1>
          <?php endif; ?>
        <?php else : ?>
          <?php if (get_field('blog_archive_title', 'option')) : ?>
            <h1 class="hero-headline"><?php echo get_field('blog_archive_title', 'option'); ?></h1>
          <?php else : ?>
            <h1 class="hero-headline">Aras Blog</h1>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php if (is_author() || is_category() || is_tag() || is_paged()) : ?>
<?php else : ?>
  <section class="blogs-archive-feat mediumtoppadding largebottompadding">
    <div class="grid-container">
      <div class="grid-x grid-padding-x align-top">
        <div class="cell small-12 content-before">
          <?php if (get_field('blog_archive_title', 'option')) : ?>
            <h3 class="feat-headline"><?php echo get_field('blog_featured_headline', 'option'); ?></h3>
          <?php else : ?>
            <h3 class="feat-headline">Trending Topics</h3>
          <?php endif; ?>
        </div>
        <?php
        $cta_label = get_field('post_cta_label', 'option');
        $fargs = array(
          'posts_per_page' => 4,
          'orderby'        => 'date',
          'order'          => 'DESC',
          'post_type'      => 'post',
          'suppress_filters' => true,
          'tax_query'      => array(
            array(
              'taxonomy' => 'featured-blog',
              'field'    => 'slug',
              'terms'    => 'featured',
            ),
          ),
        );
        $lang_codes = [];
        if (str_contains($site_url, '/ja-jp/')) {
          $fargs['meta_query'] = array(
            array(
              'key' => 'post_lang_code',
              'value' => 'ja-jp',
              'compare' => 'IN',
            )
          );
        } elseif (str_contains($site_url, '/fr-fr/')) {
          $fargs['meta_query'] = array(
            'relation' => 'OR',
            array(
              'key'     => 'post_lang_code',
              'value'   => 'en',
              'compare' => 'IN',
            ),
            array(
              'key'     => 'post_lang_code',
              'value'   => 'fr-fr',
              'compare' => 'IN',
            ),
          );
          $lang_codes = ['fr-fr', 'en'];
        } elseif (str_contains($site_url, '/de-de/')) {
          $fargs['meta_query'] = array(
            'relation' => 'OR',
            array(
              'key'     => 'post_lang_code',
              'value'   => 'en',
              'compare' => 'IN',
            ),
            array(
              'key'     => 'post_lang_code',
              'value'   => 'de-de',
              'compare' => 'IN',
            ),
          );
          $lang_codes = ['de-de', 'en'];
        } else {
          $fargs['meta_query'] = array(
            array(
              'key' => 'post_lang_code',
              'value' => 'en',
              'compare' => 'IN',
            )
          );
        }
        if( count($lang_codes) > 1 ){
          $featposts = Aras\WPML\get_wp_query( $fargs, $lang_codes );
        }
        else {
          $featposts = new WP_Query($fargs);
        }

        if ($featposts->have_posts()) : ?>
          <?php while ($featposts->have_posts()) : $featposts->the_post();
            $featured_img_url = get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>
            <article id="post-<?php the_ID(); ?>" class="feat-card cell small-12 medium-6" role="article">
              <div class="feat-img-container">
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
              <div class="feat-content-container">
                <?php if (get_field('hero_headline')) : ?>
                  <h3 class="card-headline"><?php echo get_field('hero_headline'); ?></h3>
                <?php else : ?>
                  <h3 class="card-headline"><?php the_title(''); ?></h3>
                <?php endif; ?>
                <a aria-label="<?php if ($cta_label) {
                                  echo esc_html($cta_label) . '&nbsp;→';
                                } else {
                                  echo 'Read the Blog&nbsp;→';
                                }
                                ?>" class="card-link" title="<?php the_title() ?>" href="<?php the_permalink() ?>">
                  <?php if ($cta_label) {
                    echo esc_html($cta_label) . '&nbsp;→';
                  } else {
                    echo 'Read the Blog&nbsp;→';
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
<?php endif; ?>


<section class="blog-filter bg-dblue">
  <div class="grid-container">
    <div class="grid-x grid-margin-x align-right">

      <?php if (!(strpos($site_url, '/blog/tag/') !== false || strpos($site_url, '/blog/author/') !== false)) : ?>
        <div class="cell small-12 medium-auto">
        <?php else : ?>
          <div class="cell small-12 medium-auto">
          <?php endif; ?>

          <form id="filter-form">
            <?php if (have_rows('blog_category_filter', 'option')) : ?>
              <?php if (!(strpos($site_url, '/blog/tag/') !== false || strpos($site_url, '/blog/author/') !== false)) : ?>
                <div class="custom-select">
                  <select id="category-filter" name="category-filter">
                    <option value="all">
                      <?php echo get_field('all_categories_label', 'option'); ?>
                    </option>
                    <?php while (have_rows('blog_category_filter', 'option')) : the_row(); ?>
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
            <?php endif; ?>

            <div class="custom-select">

              <?php 
              $language_filter = get_field( 'blog_language_filter', 'option' );
              ?>
              <select id="language-filter" name="language-filter">
                <option value="">
                  <?php echo $language_filter['all_languages_label'] ?: __('All Languages', 'aras'); ?>
                </option>
                  
                <?php if (str_contains($site_url, '/ja-jp/')) { ?>
                  
                  <option value="en__ja-jp">
                    <?php echo $language_filter['combined_label'] ?: __('Japanese + English', 'aras'); ?>
                  </option>
                  <option value="en">
                    <?php echo $language_filter['english_label'] ?: __('English', 'aras'); ?>
                  </option>
                  <option value="de-de">
                    <?php echo $language_filter['german_label'] ?: __('German', 'aras'); ?>
                  </option>
                  <option value="ja-jp">
                    <?php echo $language_filter['japanese_label'] ?: __('Japanese', 'aras'); ?>
                  </option>

                <?php } elseif (str_contains($site_url, '/fr-fr/')) { ?>

                  <option value="en__fr-fr">
                    <?php echo $language_filter['combined_label'] ?: __('French + English', 'aras'); ?>
                  </option>
                  <option value="en">
                    <?php echo $language_filter['english_label'] ?: __('English', 'aras'); ?>
                  </option>
                  <option value="de-de">
                    <?php echo $language_filter['german_label'] ?: __('German', 'aras'); ?>
                  </option>
                  <option value="ja-jp">
                    <?php echo $language_filter['japanese_label'] ?: __('Japanese', 'aras'); ?>
                  </option>

                <?php } elseif (str_contains($site_url, '/de-de/')) { ?>

                  <option value="en__de-de">
                    <?php echo $language_filter['combined_label'] ?: __('German + English', 'aras'); ?>
                  </option>
                  <option value="en">
                    <?php echo $language_filter['english_label'] ?: __('English', 'aras'); ?>
                  </option>
                  <option value="de-de">
                    <?php echo $language_filter['german_label'] ?: __('German', 'aras'); ?>
                  </option>
                  <option value="ja-jp">
                    <?php echo $language_filter['japanese_label'] ?: __('Japanese', 'aras'); ?>
                  </option>

                  
                <?php } elseif (str_contains($site_url, '/en/')) { ?>

                  <option value="en">
                    <?php echo $language_filter['english_label'] ?: __('English', 'aras'); ?>
                  </option>
                  <option value="de-de">
                    <?php echo $language_filter['german_label'] ?: __('German', 'aras'); ?>
                  </option>
                  <option value="ja-jp">
                    <?php echo $language_filter['japanese_label'] ?: __('Japanese', 'aras'); ?>
                  </option>
                <?php } ?>
              </select>
            </div>
            <?php if (get_field('blog_filter_clear_button_label', 'option')) : ?>
              <button aria-label="<?php echo esc_attr( get_field('blog_filter_clear_button_label', 'option') ); ?>" class="aras-button" id="clear-filters">
                <?php echo get_field('blog_filter_clear_button_label', 'option'); ?>
              </button>
            <?php else : ?>
              <button aria-label="<?php esc_attr__('Clear Filters', 'aras'); ?>" class="aras-button" id="clear-filters">
                <?php _e('Clear', 'aras'); ?>
              </button>
            <?php endif; ?>
          </form>
          </div>
          <div class="cell small-12 medium-shrink">
            <form role="search" method="get" class="blog-search-form" action="<?php echo home_url('/'); ?>">
              <label>
                <span class="screen-reader-text"><?php echo _x('Search for:', 'label', 'jointswp') ?></span>
                <input type="hidden" name="post_type" value="post" />
                <input aria-label="search" type="search" class="search-field" placeholder="Search" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x('Search for:', 'jointswp') ?>" />
              </label>
              <input class="search-arrow-icon" type="submit" value=" " alt="Search" style="background-image:url(<?php echo get_template_directory_uri(); ?>/assets/images/icons/searchicon.svg)" />
            </form>
          </div>
        </div>
    </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var postCatFilter = document.getElementById('category-filter');
    var postLangFilter = document.getElementById('language-filter');
    var clearButton = document.getElementById('clear-filters');
    //Reload the page with the specified filter parameters
    function reloadPage() {
      var params = new URLSearchParams(window.location.search);
      var url = '<?php echo esc_url($default_post_archive_url); ?>';
      var postLang = postLangFilter.value;
      // Construct the base URL without any query parameters
      var baseUrl = window.location.origin + window.location.pathname;
      // Check if the current URL already contains a category
      var categoryIndex = baseUrl.indexOf('/category/');
      var newUrl = '<?php echo $default_post_archive_url ?>';

      // If postCatFilter exists and a category is selected, put it in the url
      if (postCatFilter && postCatFilter.value !== 'all') {
        if (postCatFilter.value) {
          if (categoryIndex !== -1) {
            // Remove existing category
            newUrl = baseUrl.substring(0, categoryIndex);
          }
          newUrl += '/category/' + encodeURIComponent(postCatFilter.value);
        }
      } else {
        // If 'all' is selected or postCatFilter doesn't exist, remove existing category
        if (categoryIndex !== -1) {
          newUrl = baseUrl.substring(0, categoryIndex);
        } else {
          newUrl = baseUrl; // No category, so set to base
        }
      }

      // If a language is selected, add query parameter
      if (postLang) {
        newUrl += (newUrl.includes('?') ? '&' : '?') + 'language=' + encodeURIComponent(postLang);
      }
      // Redirect to the new URL
      window.location.href = newUrl;
    }

    if (postCatFilter && postLangFilter) {
      postCatFilter.addEventListener('change', reloadPage);
      postLangFilter.addEventListener('change', reloadPage);
    }

    // Set default values based on URL parameters
    var params = new URLSearchParams(window.location.search);
    var postLangParam = params.get('language');
    if (postLangParam) {
      var postLangOption = document.querySelector('#language-filter option[value="' + postLangParam + '"]');
      if (postLangOption) {
        postLangOption.selected = true;
      }
    }
    // Get the slug from the URL
    var pgurl = new URL(window.location.href);
    var postCatParam = pgurl.pathname.split('/').pop();
    if (postCatParam) {
      var postCatOption = document.querySelector('#category-filter option[value="' + postCatParam + '"]');
      if (postCatOption) {
        postCatOption.selected = true;
      }
    }

    // Clear button
    if (clearButton) {
      clearButton.addEventListener('click', function(event) {
        event.preventDefault();
        window.location.href = '<?php echo esc_url($default_post_archive_url); ?>';
      });
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


<main class="blog-archive bg-grey mediumtoppadding largebottompadding" role="main">
  <div class="grid-container">
    <section class="grid-x grid-margin-x blog-post-loop">

      <?php
      $current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
      $query_string = parse_url($current_url, PHP_URL_QUERY);
      parse_str($query_string, $variables);
      $lang_switcher = '';
      $category_switcher = '';
      $tag_switcher = '';
      $author_switcher = '';
      $lang_codes = [];
      $lang_switcher = $variables['language'] ?? null;

      $args = array(
        'posts_per_page' => 12,
        'post_type' => 'post',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish',
        'suppress_filters' => true,
      );

      // Extract category slug
      $category_var = get_query_var('category_name');
      if ($category_var) {
        // $category_slug_with_query = $parts[1];
        // $category_slug_parts = explode('?', $category_slug_with_query);
        // $category_slug = rtrim($category_slug_parts[0], '/');
        // $category_switcher = $category_slug;
        $category_switcher = $category_var;
      }
      // Taxonomy Query for Category Filter
      if (!empty($category_switcher)) {
        $args['tax_query'] = array(
          array(
            'taxonomy' => 'category',
            'field' => is_numeric($category_switcher) ? 'term_id' : 'slug',
            'terms' => $category_switcher,
          )
        );
      }

      // Extract tag slug
      $parts = explode('/tag/', $current_url);
      if (count($parts) === 2) {
        $tag_slug_with_query = $parts[1];
        $tag_slug_parts = explode('?', $tag_slug_with_query);
        $tag_slug = rtrim($tag_slug_parts[0], '/');
        $tag_switcher = $tag_slug;
      }
      // Taxonomy Query for Tag Filter
      if (!empty($tag_switcher)) {
        $args['tax_query'] = array(
          array(
            'taxonomy' => 'post_tag',
            'field' => is_numeric($tag_switcher) ? 'term_id' : 'slug',
            'terms' => $tag_switcher,
          )
        );
      }
      // Extract author slug
      // $parts = explode('/author/', $current_url);
      // if (count($parts) === 2) {
      //   $author_slug_with_query = $parts[1];
      //   $author_slug_parts = explode('?', $author_slug_with_query);
      //   $author_slug = rtrim($author_slug_parts[0], '/');
      //   $author_switcher = $author_slug;
      // }
      // Taxonomy Query for Author Filter
      if( is_author() ){
        // $args['_author_query'] = is_author();
        // $args['author_name'] = get_query_var( 'author_name' );
        $args['author'] = get_queried_object_id();
      }
      if (!empty($author_switcher)) {
        $user = get_user_by('slug', $author_switcher);
        if ($user) {
          $author_id = $user->ID;
          $args['author'] = $author_id;
        }
      }

      // Language filter
      if ($lang_switcher == 'de-de' || $lang_switcher == 'fr-fr' || $lang_switcher == 'ja-jp' || $lang_switcher == 'en') {
        $args['meta_query'] = array(
          array(
            'key' => 'post_lang_code',
            'value' => $lang_switcher,
            'compare' => 'IN',
          )
        );
      } elseif ($lang_switcher == 'en__de-de' || $lang_switcher == 'en__fr-fr' || $lang_switcher == 'en__ja-jp') {
        //this method is kind of sloppy and should be expanded on if this becomes a multiselect
        $lang_codes = explode('__', $lang_switcher);
        $args['meta_query'] = array(
          'relation' => 'OR',
          array(
            'key'     => 'post_lang_code',
            'value'   => $lang_codes[0], // First language code
            'compare' => 'IN',
          ),
          array(
            'key'     => 'post_lang_code',
            'value'   => $lang_codes[1], // Second language code
            'compare' => 'IN',
          ),
        );
      } else {
        if (str_contains($site_url, '/ja-jp/')) {
          $args['meta_query'] = array(
            array(
              'key' => 'post_lang_code',
              'value' => 'ja-jp',
              'compare' => 'IN',
            )
          );
        } elseif (str_contains($site_url, '/fr-fr/')) {
          $lang_codes = ['fr-fr', 'en'];
          $args['meta_query'] = array(
            'relation' => 'OR',
            array(
              'key'     => 'post_lang_code',
              'value'   => 'en',
              'compare' => 'IN',
            ),
            array(
              'key'     => 'post_lang_code',
              'value'   => 'fr-fr',
              'compare' => 'IN',
            ),
          );
        } elseif (str_contains($site_url, '/de-de/')) {
          $lang_codes = ['de-de', 'en'];
          $args['meta_query'] = array(
            'relation' => 'OR',
            array(
              'key'     => 'post_lang_code',
              'value'   => 'en',
              'compare' => 'IN',
            ),
            array(
              'key'     => 'post_lang_code',
              'value'   => 'de-de',
              'compare' => 'IN',
            ),
          );
        } else {
          $args['meta_query'] = array(
            array(
              'key' => 'post_lang_code',
              'value' => 'en',
              'compare' => 'IN',
            )
          );
        }
      }

      if (strpos($site_url, '/page/') !== false) {
        $pos = strpos($site_url, '/page/');
        $page_substr = substr($site_url, $pos + 6); // 6 is the length of '/page/'
        $page_number = intval($page_substr);
        $args['paged'] = $page_number;
      }

      // Fix duplicate posts (only one should be returned per language)

      /// FINALLY, the post query
      // $posts_query = new WP_Query($args);
      if( count($lang_codes) > 1 ){
        $posts_query = Aras\WPML\get_wp_query( $args, $lang_codes );
      }
      else {
        global $sitepress, $wpml_query_filter;
        $args['suppress_filters'] = false; // Allow WPML to filter the query
        remove_filter('parse_query', array($sitepress, 'parse_query'), 10);
        remove_filter('pre_get_posts', array($sitepress, 'pre_get_posts'), 10);
        remove_filter( 'posts_join', array( $wpml_query_filter, 'posts_join_filter' ), 10 );
		    remove_filter( 'posts_where', array( $wpml_query_filter, 'posts_where_filter' ), 10 );
        $posts_query = new WP_Query($args);
        add_filter('parse_query', array($sitepress, 'parse_query'), 10);
        add_filter('pre_get_posts', array($sitepress, 'pre_get_posts'), 10);
        add_filter( 'posts_join', array( $wpml_query_filter, 'posts_join_filter' ), 10, 2 );
		    add_filter( 'posts_where', array( $wpml_query_filter, 'posts_where_filter' ), 10, 2 );
      }

      if ($posts_query->have_posts()) : $postCount = 0;
        while ($posts_query->have_posts()) :  $posts_query->the_post();
          $postCount++;
          $post_id = get_the_ID();
          get_template_part('parts/loop', 'archive');
        endwhile;
        wp_reset_postdata();
      else :
        get_template_part('parts/content', 'missing');
      endif;
      ?>
    </section>
    <section class="grid-x grid-margin-x text-center align-center">
      <!--
      <?php if (get_field('load_more_blogs_label', 'option')) : ?>
        <button aria-label="<?php echo get_field('load_more_blogs_label', 'option'); ?>" class="aras-button" id="load-more-posts"><?php echo get_field('load_more_blogs_label', 'option'); ?></button>
      <?php else : ?>
        <button aria-label="Load More Posts" class="aras-button" id="load-more-posts">Load More</button>
      <?php endif; ?>
      -->
      <?php
      global $wp_query;
      $_wp_query = $wp_query;
      $wp_query = $posts_query;
      the_posts_pagination( array(
        'mid_size' => 2,
        'prev_text' => __( '←', 'aras' ),
        'next_text' => __( '→', 'aras' ),
        ) );
      $wp_query = $_wp_query; // Restore the original query
      wp_reset_query();
      ?>
    </section>
  </div>
  </div>
</main>


<?php if (get_field('blog_footer_cta_style', 'option') == 'form') : ?>
  <?php if (have_rows('blog_archive_subscribe_form', 'option')) : ?>
    <?php while (have_rows('blog_archive_subscribe_form', 'option')) : the_row(); ?>
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
<?php elseif (get_field('blog_footer_cta_style', 'option') == 'cta') : ?>
  <?php get_template_part('parts/_template_parts/footer_cta_blog'); ?>
<?php endif; ?>


<?php if (get_field('blog_footer_cta_style', 'option') == 'form') : ?>
  <?php if (have_rows('blog_archive_subscribe_form', 'option')) : ?>
    <?php while (have_rows('blog_archive_subscribe_form', 'option')) : the_row(); ?>
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
  //Hides the Load More if there is less than 12 results
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
<script>
  var loadMoreAjaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
  var page = <?php echo max(2, get_query_var('paged') ? get_query_var('paged') + 1 : 2); ?>;
  var canLoadMore = true;
  jQuery(document).ready(function($) {
    jQuery('#load-more-posts').on('click', function() {
      if (canLoadMore) {
        jQuery.ajax({
          type: 'POST',
          url: loadMoreAjaxUrl,
          data: {
            action: 'load_more_posts',
            page: page,
            language: '<?php echo $lang_switcher; ?>',
            category: '<?php echo $category_switcher; ?>',
            tag: '<?php echo $tag_switcher; ?>',
            author: '<?php echo $author_switcher; ?>',
            site_url: '<?php echo $site_url; ?>'
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
              <?php if (is_category() || is_tag()) : ?>
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
                currentUrl = currentUrl.replace(/\/page\/\d+/, '');
                // Construct the new URL with the pagination segment
                var newUrl = currentUrl;
                // Check if current URL contains '?/page/'
                if (currentUrl.endsWith('?')) {
                  // Append pagination segment without '?'
                  newUrl += '/page/' + (page - 1);
                } else {
                  // Append pagination segment with '?'
                  newUrl += '?/page/' + (page - 1);
                }
                // Append the language parameter, if it exists
                if (languageParam !== '') {
                  newUrl += '?' + languageParam.substring(1);
                }
                // Update browser URL with new page
                history.pushState(null, null, newUrl);
              <?php else : ?>
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
                currentUrl = currentUrl.replace(/\/page\/\d+/, '');
                // Construct the new URL with the pagination segment
                var newUrl = currentUrl;
                // Append pagination segment
                newUrl += '/page/' + (page - 1);
                // Append the language parameter, if it exists
                if (languageParam !== '') {
                  newUrl += '?' + languageParam.substring(1);
                }
                // Update browser URL with new page
                history.pushState(null, null, newUrl);
              <?php endif; ?>
            }
          },
        });
      }
    });
  });
</script>