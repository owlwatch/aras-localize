<?php
$data = load_data_from_file('MyI_Public_Roadmap_Query.json');
if ($data != null) {
?>
  <?php
  $unique_state = array();
  foreach ($data['SOAP-ENV:Envelope']['SOAP-ENV:Body']['ApplyItemResponse']['Result']['Item'] as $record) {
    if (isset($record['state']) && $record['state'] != '') {
      $unique_state[$record['state']] = true;
    };
  };
  ?>
  <?php
  $unique_product_area = array();
  foreach ($data['SOAP-ENV:Envelope']['SOAP-ENV:Body']['ApplyItemResponse']['Result']['Item'] as $record) {
    if (isset($record['product_area']) && $record['product_area'] != '') {
      $terms = explode(',', $record['product_area']);
      foreach ($terms as $term) {
        $term = trim($term);
        $unique_product_area[$term] = true;
      }
    }
  }
  $unique_product_name = array();
  foreach ($data['SOAP-ENV:Envelope']['SOAP-ENV:Body']['ApplyItemResponse']['Result']['Item'] as $record) {
    if (isset($record['product_name']) && $record['product_name'] != '') {
      $terms = explode(',', $record['product_name']);
      foreach ($terms as $term) {
        $term = trim($term);
        $unique_product_name[$term] = true;
      }
    }
  }
  ksort($unique_product_area);
  ?>
  <section class="roadmap-filter-section bg-white mediumtoppadding nobottompadding">
    <div class="grid-container">
      <form id="roadmap-filter-controls" class="grid-x grid-margin-x">
        <div class="cell small-12">
		  <h3><?php esc_html_e('Filter', 'aras'); ?></h3>
        </div>
        <div class="cell small-12 medium-6 roadmap-filter-flex">
          <fieldset class="product-area custom-select roadmap" data-filter-group="product-area">
            <select id="product-area">
            <option value=""><?php esc_html_e('All Product Areas', 'aras'); ?></option>
              <?php foreach ($unique_product_area as $uproduct_area => $value) :
                $css_class_uproduct_area = sanitize_css_class($uproduct_area);
              ?>
                <option value="<?php echo $css_class_uproduct_area; ?>"><?php echo $uproduct_area; ?></option>
              <?php endforeach; ?>
              <?php foreach ($unique_product_name as $uproduct_name => $value) :
                $css_class_uproduct_name = sanitize_css_class($uproduct_name);
              ?>
                <option value="<?php echo $css_class_uproduct_name; ?>"><?php echo $uproduct_name; ?></option>
              <?php endforeach; ?>
            </select>
          </fieldset>
          <fieldset>
          <button aria-label="<?php echo esc_attr__('Reset filters', 'aras'); ?>" id="clear-button" class="aras-button" type="reset"><?php esc_html_e('Reset', 'aras'); ?></button>
          </fieldset>
        </div>
      </form>
    </div>
  </section>

  <?php
  // Group items based on common criteria
  $grouped_items = [];
  foreach ($data['SOAP-ENV:Envelope']['SOAP-ENV:Body']['ApplyItemResponse']['Result']['Item'] as $record) {
    $key = $record['product_name'] . $record['epic_name'] . $record['state'] . $record['release_name'] . $record['target_release_date'] . (isset($record['product_area']) ? $record['product_area'] : '');
    if (!isset($grouped_items[$key])) {
      $grouped_items[$key] = [];
    }
    $grouped_items[$key][] = $record;
  }
  // sort the unique states
  $sort_order = ['Released', 'Implementing', 'Backlog', 'Analyzing'];
  uksort( $unique_state, function($a,$b) use($sort_order){
    $ai = array_search( $a, $sort_order );
    $bi = array_search( $b, $sort_order );
    return $ai - $bi;
  });
  ?>
  <section class="product-roadmap-section smalltoppadding nobottompadding">
    <div class="grid-container">
      <div class="grid-x grid-margin-x">
        <?php foreach ($unique_state as $ustate => $value) : ?>
          <?php if ($ustate != 'Done') : ?>
            <div class="cell small-12 medium-6 large-3 roadmap-state">
              <h3><?php echo $ustate; ?></h3>
              <div class="roadmap-container">
                <?php foreach ($grouped_items as $group) : ?>
                  <?php
                  // Extract common details from the first item in the group
                  $first_item = $group[0];
                  $product_name = $first_item['product_name'];
                  $epic_name = $first_item['epic_name'];
                  $state = $first_item['state'];
                  $release_name = $first_item['release_name'];
                  $target_release_date = $first_item['target_release_date'];
                  $product_area = '';
                  foreach ($group as $item) {
                    if (isset($item['product_area'])) {
                      $product_area = $item['product_area'];
                      break;
                    }
                  }

                  // Sanitize product name
                  $sanitized_product_name = strtolower(str_replace(' ', '_', trim($product_name)));

                  // Sanitize product area
                  $sanitized_product_area = isset($product_area) ? strtolower(str_replace(' ', '_', trim($product_area))) : '';
                  ?>

                  <?php if ($state == $ustate) : ?>
                    <div class="state-item
                                        <?php if ($product_area != '') {
                                          echo $sanitized_product_area;
                                        } else {
                                          echo $sanitized_product_name;
                                        } ?>
                                    ">
                      <h4 class="epic"><?php echo $epic_name; ?></h4>
                      <?php if ($product_area != '') : ?>
                      <p class="product-area pa"><?php esc_html_e('Platform:', 'aras'); ?> <?php echo $product_area; ?></p>
                      <?php else : ?>
                        <p class="product-area pn"><?php echo $product_name; ?></p>
                      <?php endif; ?>
                      <?php if ($release_name != 'ZZZ' && $release_name != '') : ?>
                        <?php
                        $formattedTargetReleaseDate = date("F Y", strtotime($target_release_date));
                        if( strpos( $formattedTargetReleaseDate, '2100' ) == -1 ){
                          $formattedTargetReleaseDate = __('TBD', 'aras');
                        }
                        ?>
                        <p class="product-date"><strong><?php echo $release_name; ?></strong> - <?php echo $formattedTargetReleaseDate; ?></p>
                      <?php endif; ?>
                      <div class="product-desc">
                        <?php foreach ($group as $item) : ?>
                          <p class=""><?php echo $item['feature_name']; ?></p>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <script>
    jQuery(document).ready(function() {
      jQuery('.state-item').click(function() {
        // Check if clicked item already has the class 'highlighted'
        if (!jQuery(this).hasClass('highlighted')) {
          var epicValue = jQuery(this).find('.epic').text(); // Get the value of the .epic field
          // Remove .highlighted class from all .state-item boxes
          jQuery('.state-item').removeClass('highlighted');
          // Add .highlighted class to all .state-item boxes with the same value in the .epic field
          jQuery('.state-item').each(function() {
            if (jQuery(this).find('.epic').text() === epicValue) {
              jQuery(this).addClass('highlighted');
            }
          });
        } else {
          // If clicked item already has the class 'highlighted', remove it instead of doing the above
          jQuery('.state-item').removeClass('highlighted');
        }
      });
    });
  </script>

<?php } ?>
<?php
$rrq_data = load_data_from_file('MyI_Public_Roadmap_Release_Query.json');
if ($rrq_data != null) {
?>
  <section class="roadmap-releases smalltoppadding mediumbottompadding">
    <div class="grid-container">
      <div class="grid-x grid-margin-x">
        <div class="cell small-12 medium-6">
          <h3><?php esc_html_e('Recent Releases', 'aras'); ?></h3>
          <table>
            <tr>
            <th style="width:60%"><?php esc_html_e('Release', 'aras'); ?></th>
            <th><?php esc_html_e('Date', 'aras'); ?></th>
            </tr>
            <?php //var_dump($rrq_data); 
            foreach ($rrq_data['SOAP-ENV:Envelope']['SOAP-ENV:Body']['ApplyItemResponse']['Result']['Item'] as $record) : ?>
              <?php
              // if (isset($record['product_name']) && $record['product_name'] != '') {
              // $product_name = $record['product_name'];
              // };
              // if (isset($record['product_name']) && $record['product_name'] != '') {
              // $product_name = $record['product_name'];
              // };
              if (isset($record['release_name']) && $record['release_name'] != '') {
                $release_name = $record['release_name'];
              } else {
                $release_name = '';
              };
              if (isset($record['release_date']) && $record['release_date'] != '') {
                $release_date = $record['release_date'];
                $timestamp = strtotime($release_date);
                $formattedDate = date("F Y", $timestamp);
              } else {
                $release_date = '';
                $formattedDate = '';
              };
              if ($record['sa_is_released'] != '') {
                $sa_is_released = $record['sa_is_released'];
              } else {
                $sa_is_released = '';
              };
              ?>
              <?php if ($sa_is_released == '1') : ?>
                <tr>
                  <td>
                    <?php echo $release_name; ?>
                  </td>
                  <td><?php echo $formattedDate; ?></td>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </table>
        </div>
        <div class="cell small-12 medium-6">
          <h3><?php esc_html_e('Upcoming Releases', 'aras'); ?></h3>
          <table>
            <tr>
            <th style="width:60%"><?php esc_html_e('Release', 'aras'); ?></th>
            <th><?php esc_html_e('Date', 'aras'); ?></th>
            </tr>
            <?php
            // var_dump($rrq_data); 
            foreach ($rrq_data['SOAP-ENV:Envelope']['SOAP-ENV:Body']['ApplyItemResponse']['Result']['Item'] as $record) : ?>
              <?php
              if (isset($record['release_name']) && $record['release_name'] != '') {
                $release_name = $record['release_name'];
              } else {
                $release_name = '';
              };
              if (isset($record['target_release_date']) && $record['target_release_date'] != '') {
                $target_release_date = $record['target_release_date'];
                $timestamp_releasedate = strtotime($target_release_date);
                $formattedReleaseDate = date("F Y", $timestamp_releasedate);
              } else {
                $target_release_date = '';
                $formattedReleaseDate = '';
              };
              if ($record['sa_is_released'] != '') {
                $sa_is_released = $record['sa_is_released'];
              } else {
                $sa_is_released = '';
              };
              if( strpos( $formattedReleaseDate, '2100' ) == -1 ){
              $formattedReleaseDate = __('TBD', 'aras');
              }
              ?>
              <?php if ($sa_is_released == '0') : ?>
                <tr>
                  <td>
                    <?php echo $release_name; ?>
                  </td>
                  <td><?php echo $formattedReleaseDate; ?></td>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </table>
        </div>
      </div>
    </div>
  </section>
<?php } ?>






<script>
  document.addEventListener('DOMContentLoaded', function() {
    const selectFilter = document.getElementById('product-area');
    const clearButton = document.getElementById('clear-button');
    const roadmapContainers = document.querySelectorAll('.roadmap-container');
    // Function to filter items based on selected option
    function filterItems() {
      const selectedValue = selectFilter.value;
      roadmapContainers.forEach(container => {
        const items = container.querySelectorAll('.state-item');
        items.forEach(item => {
          if (selectedValue === '' || item.classList.contains(selectedValue)) {
            item.classList.remove('hide');
          } else {
            item.classList.add('hide');
          }
        });
      });
    }
    // Event listener for select change
    selectFilter.addEventListener('change', filterItems);
    // Event listener for clear button click
    clearButton.addEventListener('click', function() {
      selectFilter.value = '';
      filterItems();
    });
    // preload based on URL ending with #filter
    if (location.hash) {
      var hash = location.hash.replace('#', '')
      if (jQuery('#product-area option[value="' + hash + '"]').length > 0) {
        jQuery("#product-area").val(hash).change();
        filterItems();
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
          filterItems()


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