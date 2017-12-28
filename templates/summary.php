<?php

    $upcoming_items = trucklot_locations_get_upcoming();
    $display_count = !empty($display_count) && (int) $display_count > 1 ? (int) $display_count : 3;

    // Proceed if we have locations
    if(count($upcoming_items) > 0):
        $upcoming_items = array_slice($upcoming_items, 0 , $display_count);
        $now = current_time('timestamp');
        $timestamp_today_end = strtotime('tomorrow + 2 hours', $now);
        $timestamp_tomorrow_end = strtotime('tomorrow + 26 hours', $now);

?>
    <?php foreach ($upcoming_items as $item): ?>
        <div class="locations-summary-item">
            <?php
                trucklot_include('templates/full_item.php',array(
                    'location' => $item,
                    'is_today' => $item['timestamp'] < $timestamp_today_end,
                    'is_tomorrow' => $item['timestamp'] < $timestamp_tomorrow_end,
                    'close_time' => trucklot_locations_get_formatted_closetime($item),
                    'gmap_link_addr' => apply_filters('foodtruck_summary_link_addr_gmap', true)
                ));
            ?>
        </div>
    <?php endforeach; ?>
<?php elseif(current_user_can('edit_posts')): ?>
    <div>
      <div style="background:red;color:#fff;font-family:monospace;padding: 20px">
        <div style="color:#fff; font-size: 14px">Admin Only Notice:</div>
        <div style="color:#fff; font-size: 14px"> No Locations &amp; Times</div>
        <a href="<?php echo get_admin_url('','?page=trucklot-locations'); ?>" style="color:#fff; font-size: 14px; text-decoration: underline">Add Locations &amp; Dates +</a>
      </div>
    </div>
<?php endif; ?>
