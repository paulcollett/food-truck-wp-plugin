<div class="location-item" itemscope itemtype="http://schema.org/Event">
    <?php $schema_date = date('Y-m-d\TH:i', $location['timestamp']); ?>
    <?php if($is_today): ?>
        <h3 itemprop="startDate" content="<?php echo $schema_date; ?>"><div class="location-item_beacon"></div><?php foodtruck_txt('Today'); ?></h3>
        <strong><?php echo date('g:ia', $location['timestamp']); ?> <?php echo $close_time ? '&ndash; ' . esc_html($close_time) : ''; ?></strong>
        <?php echo date('D j', $location['timestamp']); ?>
    <?php elseif($is_tomorrow): ?>
        <h3 itemprop="startDate" content="<?php echo $schema_date; ?>"><div class="location-item_beacon location-item_beacon--neutral"></div><?php foodtruck_txt('Tomorrow'); ?></h3>
        <strong><?php echo date('g:ia', $location['timestamp']); ?> <?php echo $close_time ? '&ndash; ' . esc_html($close_time) : ''; ?></strong>
        <?php echo date('D j', $location['timestamp']); ?>
    <?php else: ?>
        <div itemprop="startDate" content="<?php echo $schema_date; ?>" class="fs16"><strong><?php echo date('l, M j', $location['timestamp']); ?></strong></div>
        <?php echo date('g:ia', $location['timestamp']) . ' ' . ($close_time ? '&ndash; ' . esc_html($close_time) : ''); ?>
    <?php endif; ?>
    <?php
      $display_location = '';

      if(isset($location['name'])) {
        $display_location = esc_html($location['name']);

        echo sprintf('<meta itemprop="name" content="%s" />', esc_attr($display_location));
      } else if(isset($location['address'])) {
        $display_location = esc_html($location['address']);
      }

      if(isset($gmap_link_addr) && $gmap_link_addr && isset($location['geocode']['formatted'])) {
        echo sprintf('<div itemprop="location" itemscope itemtype="http://schema.org/Place"><a itemprop="hasMap" itemtype="https://schema.org/Map" class="locations-summary-addr-link" href="https://maps.google.com?q=%s" target="_blank">%s</a><meta itemprop="address" content="%s" /></div>',  urlencode($location['geocode']['formatted']), $display_location, esc_attr($location['geocode']['formatted']));
      } else {
        echo sprintf('<div itemprop="location" itemscope itemtype="http://schema.org/Place"><div itemprop="name">%s</div></div>', $display_location);
      }
    ?>
</div>
