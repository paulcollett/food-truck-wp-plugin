<?php
  $upcoming_items = trucklot_locations_get_upcoming();

  // Proceed if we have locations
  if(count($upcoming_items) > 0):

      $first_6_locations = array_slice($upcoming_items, 0 , 4);
      $remaining_locations = array_slice($upcoming_items, 4);
      $now = current_time('timestamp');
      $timestamp_today_end = strtotime('tomorrow + 2 hours', $now);
      $timestamp_tomorrow_end = strtotime('tomorrow + 26 hours', $now);
?>
<div class="js-location-module">
    <div class="js-location-list-container">
        <div class="contain contain--body contain--margin">
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
                        <script type="text/plain"><?php echo isset($item['address']) ? esc_html(trim($item['address'])) : ''; ?></script>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="frame frame--skinny locations-module-map">
            <div class="map map--fill js-locations-all-map"></div>
        </div>

        <div class="contain contain--body contain--margin">
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
                        <script type="text/plain"><?php echo esc_html($item['address']); ?></script>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="contain contain--max1200 js-location-container">
        <div class="locations-detail">
            <div class="locations-detail_back accent js-location-back">
                &lsaquo;
            </div>
            <div class="locations-detail_location js-location-details"></div>
            <div class="locations-detail_map">
                <div class="frame">
                    <div class="map map--fill js-location-map"></div>
                </div>
            </div>
        </div>
        <div class="center margin-bottom-md">
            <div class="button btn">&lsaquo; Back to list</div>
        </div>
    </div>
</div>
<script>window.FoodTruckFramework && FoodTruckFramework.locationsMapReady()</script>
<?php
    // Debug:
    echo '<!-- Now: ' . date('r', $now) . ' -->';
?>
<?php else: ?>

    <div class="center accent fs16">No Dates or Locations listed</div>

    <?php if(current_user_can('edit_posts')): ?>
    <div style="background:red;color:#fff;font-family:monospace;padding: 20px">
        <div style="color:#fff">Admin Only Notice:</div>
        <a href="<?php echo get_admin_url('','?page=trucklot-locations'); ?>" style="color:#fff">Add Locations &amp; Dates +</a>
    </div>
    <?php endif; ?>

<?php endif; ?>
