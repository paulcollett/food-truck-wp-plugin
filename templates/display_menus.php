<?php

    if(get_sub_field('menus_to_show') == 'custom') {
        $ids = get_sub_field('menus') ? get_sub_field('menus') : array();
    } else {
        $ids = null;
    }

    $query = new WP_Query(array(
        'post_type' => 'trucklot-menus',
        'posts_per_page' => -1,
        'post__in' => $ids,
        'orderby' => 'post__in'
    ));

    if(!$ids && isset($query->posts)) $query->posts = array_reverse($query->posts);

?>

<?php if ( $query->have_posts() ) : ?>

    <div class="contain contain--max900">
    
        <?php while ( $query->have_posts() ) : $query->the_post();
            $menu = @json_decode(get_the_content(), true); if(isset($menu['items']) && count($menu['items'])): ?>

        <div class="trucklot-menu">

            <?php do_action('trucklot/theme/menu/before', $menu); ?>

            <?php if($query->post_count > 1 || ( isset($menu['text_after_title']) && $menu['text_after_title'] ) ): ?>
                <div class="margin-bottom-md">
                    <?php if(isset($menu['display_title']) && $menu['display_title']): ?>
                        <div class="heading heading--small"><?php echo esc_html($menu['display_title']); ?></div>
                    <?php elseif(isset($menu['title'])): ?>
                        <div class="heading heading--small"><?php echo esc_html($menu['title']); ?></div>
                    <?php endif; ?>
                    <?php if(isset($menu['text_after_title']) && $menu['text_after_title']): ?>
                        <p><?php echo esc_html($menu['text_after_title']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php do_action('trucklot/theme/menu/after_title', $menu); ?>

            <div class="trucklot-menu-items">
            <?php foreach ($menu['items'] as $item): ?>
                <div class="trucklot-menu-item">

                    <?php do_action('trucklot/theme/menu/item/before', $item, $menu); ?>

                    <?php if(isset($item['image']['id'])): ?>
                        <div class="trucklot-menu-item_image"><?php site_image($item['image']['id'],array('w'=>300,'h'=>200)); ?></div>
                    <?php endif; ?>

                    <div class="trucklot-menu-item_content">
                    <?php if(isset($item['name'])): ?>
                        <div class="trucklot-menu-item_name accent fs16"><?php echo esc_html($item['name']); ?></div>
                    <?php endif; ?>
                    <?php if(isset($item['price'])): ?>
                        <div class="trucklot-menu-item_price"><?php echo esc_html($item['price']); ?></div>
                    <?php endif; ?>
                    <?php if(isset($item['desc'])): ?>
                        <div class="trucklot-menu-item_desc"><?php echo esc_html($item['desc']); ?></div>
                    <?php endif; ?>
                    </div>

                    <?php do_action('trucklot/theme/menu/item/after', $item, $menu); ?>

                </div>
            <?php endforeach; ?>
            </div>

            <?php if(isset($menu['text_after_menu']) && $menu['text_after_menu']): ?>
                <p><?php echo esc_html($menu['text_after_menu']); ?></p>
            <?php endif; ?>

            <?php do_action('trucklot/theme/menu/after', $menu); ?>
        </div>

        <?php endif; endwhile; wp_reset_postdata(); ?>

    </div>

<?php else : ?>

    <?php if(current_user_can('edit_posts')): ?>
    <div class="debug-section">
        <a href="#" class="debug-section_link">Add A Menu +</a>
        <div class="debug-section_sub">Admin Only Notice:</div>
        <div>No Menus to show in the "display menus" module</div>
    </div>
    <?php endif; ?>

<?php endif; ?>