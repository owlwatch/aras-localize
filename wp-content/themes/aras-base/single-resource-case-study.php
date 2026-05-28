<?php
/*
Template Name: Customer Story
Template Post Type: resource
*/
get_header(); ?>

<?php get_template_part('parts/_template_parts/hero_banner_cs'); ?>
<section class="blog-share smalltoppadding nobottompadding">
  <div class="grid-container">
    <div class="grid-x grid-padding-x align-middle">
      <div class="cell small-12 medium-6 large-7 share-links">
        <h6><?php esc_html_e('Share this post:', 'aras'); ?></h6>
        <a aria-label="<?php echo esc_attr__('Share on LinkedIn', 'aras'); ?>" class="share-link" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo get_permalink(); ?>" target="_blank">
          <img class="share-icon" src="<?php echo get_template_directory_uri(); ?>/assets/images/social/social-linkedin.svg" alt="<?php echo esc_attr__('Share on LinkedIn', 'aras'); ?>" />
        </a>
        <a aria-label="<?php echo esc_attr__('Share on Twitter', 'aras'); ?>" class="share-link" href="http://www.twitter.com/share?url=<?php echo get_permalink(); ?>" target="_blank">
          <img class="share-icon" src="<?php echo get_template_directory_uri(); ?>/assets/images/social/social-x.svg" alt="<?php echo esc_attr__('Share on Twitter', 'aras'); ?>" />
        </a>
        <a aria-label="<?php echo esc_attr__('Share on Facebook', 'aras'); ?>" class="share-link" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo get_permalink(); ?>" target="_blank">
          <img class="share-icon" src="<?php echo get_template_directory_uri(); ?>/assets/images/social/social-facebook.svg" alt="<?php echo esc_attr__('Share on Facebook', 'aras'); ?>" />
        </a>
      </div>
    </div>
  </div>
</section>
<section class="cs-content post-content mediumtoppadding mediumbottompadding">
  <div class="grid-container">
    <div class="grid-x grid-padding-x">
      <div class="cell small-12 medium-shrink postsidebar">
        <div id="toc-container" class="tableofcontents">
          <?php if (get_field('resources_customer_story_toc_headline', 'option')) : ?>
            <h2><?php echo get_field('resources_customer_story_toc_headline', 'option'); ?></h2>
          <?php else : ?>
            <h2><?php esc_html_e('Explore this Story', 'aras'); ?></h2>
          <?php endif; ?>
          <ul id="toc"></ul>
        </div>
      </div>
      <div id="post-content" class="cell small-12 medium-auto">
        <?php if (have_posts()) :
          while (have_posts()) : the_post(); ?>
            <?php get_template_part('parts/_flexible_post_content/_flexible_post_content'); ?>
        <?php endwhile;
        endif; ?>
      </div>
    </div>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const contentContainer = document.getElementById('post-content');
    const tocContainer = document.getElementById('toc');
    const headings = contentContainer.querySelectorAll('h2');

    function sanitizeForId(string) {
      return string.toLowerCase().replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-');
    }

    headings.forEach(function(heading) {
      let isExcluded = false;
      let parent = heading.parentElement;
      while (parent !== contentContainer) {
        if (parent.classList.contains('floating-cta') /*|| parent.classList.contains('other-example')*/ ) {
          isExcluded = true;
          break;
        }
        parent = parent.parentElement;
      }
      if (!isExcluded) {
        const listItem = document.createElement('li');
        const anchor = document.createElement('a');
        anchor.setAttribute('href', '#' + sanitizeForId(heading.textContent));
        anchor.textContent = heading.textContent;
        listItem.appendChild(anchor);
        tocContainer.appendChild(listItem);
        heading.setAttribute('id', sanitizeForId(heading.textContent));
      }
    });

    function updateActiveItem() {
      const scrollPosition = window.scrollY;
      const windowHeight = window.innerHeight;

      headings.forEach(function(heading) {
        const bounding = heading.getBoundingClientRect();
        if (bounding.top <= windowHeight / 2 && bounding.bottom >= windowHeight / 2) {
          const activeAnchor = document.querySelector('a[href="#' + sanitizeForId(heading.textContent) + '"]');
          const activeItem = activeAnchor.parentElement;
          const activeItems = document.querySelectorAll('.toc-active');
          activeItems.forEach(function(item) {
            item.classList.remove('toc-active');
          });
          activeItem.classList.add('toc-active');
        }
      });
    }
    window.addEventListener('scroll', updateActiveItem);
    if (headings.length > 0) {
      const firstAnchor = document.querySelector('a[href="#' + sanitizeForId(headings[0].textContent) + '"]');
      const firstItem = firstAnchor.parentElement;
      firstItem.classList.add('toc-active');
    }
    tocContainer.addEventListener('click', function(event) {
      if (event.target.tagName === 'A') {
        const clickedItem = event.target.parentElement;
        const activeItems = document.querySelectorAll('.toc-active');
        activeItems.forEach(function(item) {
          item.classList.remove('toc-active');
        });
        clickedItem.classList.add('toc-active');
      }
    });
  });
</script>

<?php get_footer(); ?>