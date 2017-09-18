<div class="location-item">
    <?php if($is_today): ?>
        <div class="accent fs16"><div class="location-item_beacon"></div>Today</div>
        <strong><?php echo date('g:ia', $location['timestamp']); ?> <?php echo $close_time ? '&ndash; ' . esc_html($close_time) : ''; ?></strong>
        <?php echo date('l jS', $location['timestamp']); ?>
    <?php elseif($is_tomorrow): ?>
        <div class="accent fs16"><div class="location-item_beacon location-item_beacon--neutral"></div>Tomorrow</div>
        <strong><?php echo date('g:ia', $location['timestamp']); ?> <?php echo $close_time ? '&ndash; ' . esc_html($close_time) : ''; ?></strong>
        <?php echo date('l jS', $location['timestamp']); ?>
    <?php else: ?>
        <div class="fs16"><strong><?php echo date('l, M jS', $location['timestamp']); ?></strong></div>
        <?php echo date('g:ia', $location['timestamp']) . ' ' . ($close_time ? '&ndash; ' . esc_html($close_time) : ''); ?>
    <?php endif; ?>
    <?php if(isset($location['name'])): ?>
        <div><?php echo esc_html($location['name']); ?></div>
    <?php elseif(isset($location['address'])): ?>
        <div><?php echo esc_html($location['address']); ?></div>
    <?php endif; ?>
</div>
