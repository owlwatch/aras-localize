<?php get_header(); ?>
<section id="documentation-hero" class="short-hero hero-banner bg-dblue">
  <div class="grid-container">
    <div class="grid-x grid-padding-x align-top">
      <div class="cell small-12 medium-10 hero-content">
        <div class="hero-content-inner">
          <?php if (get_field('documentation_archive_headline', 'option')) : ?>
            <h1 class="hero-headline"><?php echo get_field('documentation_archive_headline', 'option'); ?></h1>
          <?php else : ?>
            <h1 class="hero-headline">Documentation</h1>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
<main class="documentation-archive mediumtoppadding largebottompadding" role="main">
  <div class="grid-container">
    <div class="grid-x grid-margin-x content-before">
      <div class="cell small-12 medium-auto">
        <h2><?php echo get_field('documentation_content_headline', 'option'); ?></h2>
      </div>
      <?/*
      //<div class="cell small-12 medium-shrink custom-select docs">
      //  <?php
      //  $documentation_posts = get_posts(array(
      //    'post_type' => 'documentation',
      //    'posts_per_page' => -1,
      //  ));
      //  echo '<select id="documentation-dropdown">';
      //  echo '<option value="' . get_post_type_archive_link('documentation') . '">Latest Version</option>';
      //  foreach ($documentation_posts as $post) {
      //    echo '<option value="' . get_permalink($post->ID) . '">' . $post->post_title . '</option>';
      //  }
      //  echo '</select>';
      //  wp_reset_postdata();
      //  ?>
      //  <script type="text/javascript">
      //    function updatePage() {
      //      const dropdown = document.getElementById('documentation-dropdown')
      //      var selectedValue = dropdown.value;
      //      if (selectedValue !== '') {
      //        window.location = selectedValue;
      //      }
      //    };
      //    /*Custom Select Script
      //      var x, i, j, l, ll, selElmnt, a, b, c;
      //      /* Look for any elements with the class "custom-select": */
      //    x = document.getElementsByClassName("custom-select");
      //    l = x.length;
      //    for (i = 0; i < l; i++) {
      //      selElmnt = x[i].getElementsByTagName("select")[0];
      //      ll = selElmnt.length;
      //      /* For each element, create a new DIV that will act as the selected item: */
      //      a = document.createElement("DIV");
      //      a.setAttribute("class", "select-selected");
      //      a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
      //      x[i].appendChild(a);
      //      /* For each element, create a new DIV that will contain the option list: */
      //      b = document.createElement("DIV");
      //      b.setAttribute("class", "select-items select-hide");
      //      for (j = 0; j < ll; j++) {
      //        /* For each option in the original select element,
      //        create a new DIV that will act as an option item: */
      //        c = document.createElement("DIV");
      //        c.innerHTML = selElmnt.options[j].innerHTML;
      //        c.addEventListener("click", function(e) {
      //          /* When an item is clicked, update the original select box,
      //          and the selected item: */
      //          var y, i, k, s, h, sl, yl;
      //          s = this.parentNode.parentNode.getElementsByTagName("select")[0];
      //          sl = s.length;
      //          h = this.parentNode.previousSibling;
      //          for (i = 0; i < sl; i++) {
      //            if (s.options[i].innerHTML == this.innerHTML) {
      //              s.selectedIndex = i;
      //              h.innerHTML = this.innerHTML;
      //              y = this.parentNode.getElementsByClassName("same-as-selected");
      //              yl = y.length;
      //              for (k = 0; k < yl; k++) {
      //                y[k].removeAttribute("class");
      //              }
      //              this.setAttribute("class", "same-as-selected");
      //              break;
      //            }
      //          }
      //          h.click();
      //
      //
      //          updatePage()
      //
      //        });
      //        b.appendChild(c);
      //      }
      //      x[i].appendChild(b);
      //      a.addEventListener("click", function(e) {
      //        /* When the select box is clicked, close any other select boxes,
      //        and open/close the current select box: */
      //        e.stopPropagation();
      //        closeAllSelect(this);
      //        this.nextSibling.classList.toggle("select-hide");
      //        this.classList.toggle("select-arrow-active");
      //      });
      //    }
      //
      //    function closeAllSelect(elmnt) {
      //      /* A function that will close all select boxes in the document,
      //      except the current select box: */
      //      var x, y, i, xl, yl, arrNo = [];
      //      x = document.getElementsByClassName("select-items");
      //      y = document.getElementsByClassName("select-selected");
      //      xl = x.length;
      //      yl = y.length;
      //      for (i = 0; i < yl; i++) {
      //        if (elmnt == y[i]) {
      //          arrNo.push(i)
      //        } else {
      //          y[i].classList.remove("select-arrow-active");
      //        }
      //      }
      //      for (i = 0; i < xl; i++) {
      //        if (arrNo.indexOf(i)) {
      //          x[i].classList.add("select-hide");
      //        }
      //      }
      //    }
      //
      //    /* If the user clicks anywhere outside the select box,
      //    then close all select boxes: */
      //    document.addEventListener("click", closeAllSelect);
      //  </script>
      //
      //</div>
      ?>
    </div>
    <div class="grid-x grid-margin-x">
      <div class="cell small-12 wysiwyg-content">
        <?php echo get_field('documentation_archive_main_content', 'option'); ?>
      </div>
    </div>
  </div>
</main>


<?php get_template_part('parts/_template_parts/footer_cta_documentation'); ?>
<?php get_footer(); ?>