<?php

//Get the upcoming items as an array
$upcoming_items = trucklot_locations_get_upcoming();

// Proceed if we have locations
if(count($upcoming_items) > 0):
  // Variables used within this if bracket
  $display_count = !empty($display_count) && (int) $display_count > 1 ? (int) $display_count : 15;
  $upcoming_items = array_slice($upcoming_items, 0 , $display_count);
  $now = current_time('timestamp');
  $timestamp_today_end = strtotime('tomorrow + 2 hours', $now);
  $timestamp_tomorrow_end = strtotime('tomorrow + 26 hours', $now);
  $display_separator_type = in_array(strtolower($display_separator_type), array('bg', 'line')) ? strtolower($display_separator_type) : 'none';
?>
  <!-- SVG FoodTruck Icons -->
  <svg aria-hidden="true" style="display: none"><g id="foodtruck-svg-pina" fill="currentcolor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/><path d="M0 0h24v24H0z" fill="none"/></g></svg>

  <!--
    Food Truck List Layout
    https://wordpress.org/plugins/food-truck/
  -->
  <div class="foodtruck-reset">
    <div class="foodtruck-list-container">
      <div class="foodtruck-list-items">
        <?php foreach ($upcoming_items as $index => $item): ?>
          <?php
            // Variables for each location item
            $is_today = $item['timestamp'] < $timestamp_today_end;
            $is_tomorrow = $item['timestamp'] < $timestamp_tomorrow_end;
            $close_time = trucklot_locations_get_formatted_closetime($item);
            $same_day_as_last_item = isset($upcoming_items[$index - 1]) ? (date('Ymd', $upcoming_items[$index - 1]['timestamp']) === date('Ymd', $item['timestamp'])) : false;
            $is_item_even = !($index % 2);

            // Correct background separator color output for non-usual combination of supplied bg args
            if($display_separator_type === 'bg') {
              if($display_separator_color_even || $display_separator_color_odd) {
                $display_separator_color = 'transparent';
              } else if($display_separator_color) {
                $display_separator_color_odd = $display_separator_color;
                $display_separator_color_even = 'transparent';
              }
            }

            // Get correct separator for even/odd
            $item_separator_color = ($is_item_even ? $display_separator_color_even : $display_separator_color_odd) ?: $display_separator_color;
          ?>
          <div class="foodtruck-list-items_row">
            <div class="foodtruck-list-item">
              <div class="foodtruck-list-item_date">
                <?php if(!$same_day_as_last_item): ?>
                  <?php if($is_today || $is_tomorrow): ?>
                    <?php if($is_today): ?>
                      <h3 class="foodtruck-list-item-text foodtruck-list-item-text--lg">
                        <div class="foodtruck-list-item-now">
                          <div class="location-item_beacon location-item_beacon--float"></div><?php foodtruck_txt('Today'); ?>
                        </div>
                      </h3>
                    <?php elseif($is_tomorrow): ?>
                      <h3 class="foodtruck-list-item-text foodtruck-list-item-text--lg">
                        <div class="foodtruck-list-item-now">
                          <div class="location-item_beacon location-item_beacon--neutral location-item_beacon--float"></div><?php foodtruck_txt('Tomorrow'); ?>
                        </div>
                      </h3>
                    <?php endif; ?>
                  <?php endif; ?>
                  <h3 class="foodtruck-list-item-text foodtruck-list-item-text--lg"><?php echo date('D, M j', $item['timestamp']); ?></h3>
                <?php endif; ?>
              </div>
              <div class="foodtruck-list-item_name">
                <h3 class="foodtruck-list-item-text foodtruck-list-item-text--lg">
                  <?php if(!empty($item['name'])): ?>
                    <?php echo esc_html($item['name']); ?>
                  <?php elseif(!empty($item['address'])): ?>
                    <?php echo esc_html($item['address']); ?>
                  <?php endif; ?>
                </h3>
                <h3 class="foodtruck-list-item-text foodtruck-list-item-text--lg" style="font-family: inherit">
                  <div class="foodtruck-list-item-time">
                    <?php echo date('g:ia', $item['timestamp']); ?>
                    <?php echo $close_time ? '&ndash; ' . esc_html($close_time) : ''; ?>
                  </div>
                </h3>
              </div>
              <div class="foodtruck-list-item_address">
                <div class="foodtruck-list-item-addr-layout">
                  <div class="foodtruck-list-item-addr-layout_icon">
                    <svg aria-hidden="true" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><use xlink:href="#foodtruck-svg-pina" /></svg>
                  </div>
                  <div class="foodtruck-list-item-addr-layout_details">
                    <?php if(!empty($item['geocode']['formatted'])):
                        $item_gmap_map_url = 'https://maps.google.com?q=' . urlencode($item['geocode']['formatted']);
                        $item_gmap_dir_url = 'https://maps.google.com?saddr=Current+Location&daddr=' . urlencode($item['geocode']['formatted']);
                      ?>
                      <div class="foodtruck-list-item-text">
                        <?php echo esc_html($item['geocode']['formatted']); ?>
                      </div>
                      <div class="foodtruck-list-item-addr-layout_details_actions">
                        <div>
                          <div class="foodtruck-list-item-text">
                            <a class="foodtruck-list-item-link" target="_blank" href="<?php echo $item_gmap_map_url; ?>"><?php foodtruck_txt('Map'); ?></a>
                          </div>
                        </div>
                        <div>
                          <div class="foodtruck-list-item-text">
                            <a class="foodtruck-list-item-link" target="_blank" href="<?php echo $item_gmap_dir_url; ?>"><?php foodtruck_txt('Directions'); ?></a>
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
            <?php if($display_separator_type === 'bg'): ?>
              <div class="foodtruck-list-items_row_bg" style="<?php echo $item_separator_color ? ("background-color: $item_separator_color") : (!$is_item_even ? 'background-color: currentcolor; opacity: 0.05' : ''); ?>"></div>
            <?php endif; ?>
          </div>
          <?php if($display_separator_type === 'line' && isset($upcoming_items[$index + 1])): ?>
            <div class="foodtruck-list-items_line" style="<?php echo $item_separator_color ? ("background-color: $item_separator_color") : 'background-color: currentcolor; opacity: 0.2'; ?>"></div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php else: ?>
  <div class="foodtruck-reset">
    <h3 class="foodtruck-list-item-text"><?php foodtruck_txt('Check back soon for our updated schedule'); ?></h3>
    <?php if(current_user_can('edit_posts')): ?>
      <div style="background:red;color:#fff;font-family:monospace;padding: 20px">
        <div style="color:#fff">Admin Only Notice:</div>
        <a href="<?php echo get_admin_url('','?page=trucklot-locations'); ?>" style="color:#fff">Add Locations &amp; Dates +</a>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>
