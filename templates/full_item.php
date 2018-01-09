<div class="location-item">
    <?php if($is_today): ?>
        <h3><div class="location-item_beacon"></div><?php foodtruck_txt('Today'); ?></h3>
        <strong><?php echo date('g:ia', $location['timestamp']); ?> <?php echo $close_time ? '&ndash; ' . esc_html($close_time) : ''; ?></strong>
        <?php echo date('l jS', $location['timestamp']); ?>
    <?php elseif($is_tomorrow): ?>
        <h3><div class="location-item_beacon location-item_beacon--neutral"></div><?php foodtruck_txt('Tomorrow'); ?></h3>
        <strong><?php echo date('g:ia', $location['timestamp']); ?> <?php echo $close_time ? '&ndash; ' . esc_html($close_time) : ''; ?></strong>
        <?php echo date('l jS', $location['timestamp']); ?>
    <?php else: ?>
        <div class="fs16"><strong><?php echo date('l, M jS', $location['timestamp']); ?></strong></div>
        <?php echo date('g:ia', $location['timestamp']) . ' ' . ($close_time ? '&ndash; ' . esc_html($close_time) : ''); ?>
    <?php endif; ?>
    <?php
      $display_location = '';

      if(isset($location['name'])) {
        $display_location = esc_html($location['name']);
      } else if(isset($location['address'])) {
        $display_location = esc_html($location['address']);
      }

      if(isset($gmap_link_addr) && $gmap_link_addr && isset($location['geocode']['formatted'])) {
        echo sprintf('<div><a class="locations-summary-addr-link" href="https://maps.google.com?q=%s" target="_blank">%s</a></div>',  urlencode($location['geocode']['formatted']), $display_location);
      } else {
        echo sprintf('<div>%s</div>', $display_location);
      }
    ?>
</div>
