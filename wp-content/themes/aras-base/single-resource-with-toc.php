<?php
/*
Template Post Type: resource
Template Name: Resource with Table of Contents
//This is the tempalate for gated resources.
//The template label has been replaced from 'Default' to 'Gated Resource'
*/
get_header(); ?>

<?php get_template_part('parts/_template_parts/hero_banner'); ?>

<?php if (have_posts()) :
  while (have_posts()) : the_post(); ?>
<div class="resource-toc post-content" id="post-content" >
  <div class="resource-toc__toc-container">
    <div class="grid-container">
      <div class="resource-toc__toc-scroller">
        <div id="toc_container" class="resource-toc__toc tableofcontents">
          <h2><?php esc_html_e('Table of Contents', 'aras'); ?></h2>
          <ul id="toc">
            <!-- Table of Contents will be populated here -->
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="resource-toc__content">
    <?php get_template_part('parts/_flexible_content/_flexible_content'); ?>
  </div>
</div>


<?php endwhile;
endif; ?>

<?php get_template_part('parts/_template_parts/footer_cta'); ?>

<style>
@media( min-width: 924px ) {
  /* 
  We want the Table of Contents to be fixed in the right column,
  but we want to allow the post-content to have full screen sections,
  and simply target the section > .grid-container > .grid-x to add
  a margin to the right to account for that
  */
  .resource-toc {
    z-index: 1;
    position: relative;
  }
  .resource-toc__toc-container {
    position: absolute;
    top: auto;
    width: 100%;
    height: 100%;
    display: flex;
    > .grid-container {
      flex-grow: 1;
      display: flex;
      justify-content: flex-end;
    }
  }
  .resource-toc__toc {
    position: sticky;
    top: calc( var(--wp-admin--admin-bar--height, 0) + var(--aras-header-height) + 20px );
    width: 250px;
    margin-top: 4rem;
    overflow-y: auto;
    background-color: #fff;
    z-index: 2;
  }
  .resource-toc__content {
    position: relative;
    z-index: 1;
  }
  .resource-toc__content section > .grid-container > .grid-x {
    margin-right: 300px; /* Adjust this value to match the width of the TOC */
  }
}
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const contentContainer = document.querySelector('.resource-toc__content');
    const tocContainer = document.getElementById('toc');
    const headings = contentContainer.querySelectorAll('h2');

    let scrolling = false;

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
      if( scrolling ) return; // Prevent updates while scrolling
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
    if (headings.length > 0  ) {
      const firstAnchor = document.querySelector('a[href="#' + sanitizeForId(headings[0].textContent) + '"]');
      const firstItem = firstAnchor.parentElement;
      firstItem.classList.add('toc-active');
    }

    window.addEventListener('scrollend', function() {
      scrolling = false;
    });
    tocContainer.addEventListener('click', function(event) {
      if (event.target.tagName === 'A') {
        const clickedItem = event.target.parentElement;
        const activeItems = document.querySelectorAll('.toc-active');

        activeItems.forEach(function(item) {
          item.classList.remove('toc-active');
        });
        clickedItem.classList.add('toc-active');
        scrolling = true;
      }
    });
  });
</script>

<?php get_footer(); ?>