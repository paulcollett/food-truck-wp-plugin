<?php

$upcoming_items = trucklot_locations_get_upcoming();

// Proceed if we have locations
if(count($upcoming_items) > 0):
  // Variables used within this if bracket
  $display_count = !empty($display_count) && (int) $display_count > 0 ? (int) $display_count : null;
  $display_count_before_map = max(0, (int) apply_filters('foodtruck-full-count-before-map', 4));
  $upcoming_items = array_slice($upcoming_items, 0, $display_count);
  $first_6_locations = array_slice($upcoming_items, 0, $display_count_before_map);
  $remaining_locations = array_slice($upcoming_items, $display_count_before_map);
  $now = current_time('timestamp');
  $timestamp_today_end = strtotime('tomorrow + 2 hours', $now);
  $timestamp_tomorrow_end = strtotime('tomorrow + 26 hours', $now);

?>
<!--
  Food Truck Full Map Layout
  https://wordpress.org/plugins/food-truck/
-->
<script>window.FOODTRUCK_TXT_DIRECTIONS = "<?php foodtruck_txt('Directions'); ?>";</script>
<div class="foodtruck-reset js-location-module">
    <div class="js-location-list-container">
        <div class="locations-contain locations-contain--body">
            <div class="locations-module-list">
                <?php foreach ($first_6_locations as $item): ?>
                    <div class="locations-module-list_item js-location-expand-container">
                        <?php
                            trucklot_include('templates/full_item.php',array(
                                'location' => $item,
                                'is_today' => $item['timestamp'] < $timestamp_today_end,
                                'is_tomorrow' => $item['timestamp'] < $timestamp_tomorrow_end,
                                'close_time' => trucklot_locations_get_formatted_closetime($item)
                            ));
                        ?>
                        <script data-geocode="" type="text/plain"><?php echo isset($item['geocode']['lat']) ? json_encode($item['geocode']) : '{}'; ?></script>
                        <script data-addr="" type="text/plain"><?php echo isset($item['address']) ? esc_html(trim($item['address'])) : ''; ?></script>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="locations-contain locations-contain--body locations-contain--map">
          <div class="locations-map-container">
              <div class="locations-map js-locations-all-map"></div>
          </div>
        </div>

        <div class="locations-contain locations-contain--body">
            <div class="locations-module-list">
                <?php foreach ($remaining_locations as $item): ?>
                    <div class="locations-module-list_item js-location-expand-container">
                        <?php
                            trucklot_include('templates/full_item.php',array(
                                'location' => $item,
                                'is_today' => $item['timestamp'] < $timestamp_today_end,
                                'is_tomorrow' => $item['timestamp'] < $timestamp_tomorrow_end,
                                'close_time' => trucklot_locations_get_formatted_closetime($item)
                            ));
                        ?>
                        <script data-geocode="" type="text/plain"><?php echo isset($item['geocode']['lat']) ? json_encode($item['geocode']) : '{}'; ?></script>
                        <script data-addr="" type="text/plain"><?php echo esc_html($item['address']); ?></script>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="locations-contain locations-contain--body locations-contain--details js-location-container">
        <div class="locations-detail">
            <div class="locations-detail_back js-location-back">
                &lsaquo;
            </div>
            <div class="locations-detail_location js-location-details"></div>
            <div class="locations-detail_map">
                <div class="locations-map-container locations-map-container--detail">
                    <div class="locations-map js-location-map"></div>
                </div>
            </div>
        </div>
        <div class="locations-center margin-bottom-md">
            <a href="#" class="button btn js-location-back">&lsaquo; <?php foodtruck_txt('Back to list'); ?></a>
        </div>
    </div>
</div>
<script>window.FoodTruckFramework && FoodTruckFramework.locationsMapReady()</script>
<?php
    // Debug:
    echo '<!-- Now: ' . date('r', $now) . ' -->';
?>
<?php else: ?>

<div class="foodtruck-reset">
  <div class="locations-center"><?php foodtruck_txt('Check back soon for our updated schedule'); ?></div>

  <?php if(current_user_can('edit_posts')): ?>
  <div style="background:red;color:#fff;font-family:monospace;padding: 20px">
      <div style="color:#fff">Admin Only Notice:</div>
      <a href="<?php echo get_admin_url('','?page=trucklot-locations'); ?>" style="color:#fff">Add Locations &amp; Dates +</a>
  </div>
  <?php endif; ?>
</div>

<?php endif; ?>
