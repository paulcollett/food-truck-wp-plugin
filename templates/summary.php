<?php

    $upcoming_items = trucklot_locations_get_upcoming();

    // Proceed if we have locations
    if(count($upcoming_items) > 0):
        $upcoming_items = array_slice($upcoming_items, 0 , 3);
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
                    'close_time' => trucklot_locations_get_formatted_closetime($item)
                ));
            ?>
        </div>
    <?php endforeach; ?>
<?php elseif(current_user_can('edit_posts')): ?>
    <div>
        <div class="debug-section center">
            <div class="debug-section_sub">Admin Only Notice:</div>
            No Locations &amp; Times
        </div>
    </div>
<?php endif; ?>
