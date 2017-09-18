<?php

if(!function_exists('trucklot_locations_get_upcoming')) return;

?><div class="contain contain--margin">

    <div class="layout-featured">
        <div class="layout-featured_main">
            <div class="layout-featured_slider">

                <?php site_include('/templates/common_slider.php'); ?>

            </div>
        </div>
        <div class="layout-featured_side<?php echo count(trucklot_locations_get_upcoming()) > 0 ? '' : ' layout-featured_side--no-items'; ?>">

            <div class="layout-featured_schedule">

                <div class="layout-featured_schedule_times">

                    <?php site_include('/templates/home_module_upcoming.php'); ?>

                </div>
                <?php if(get_sub_field('button_a_label')): ?>
                <div class="layout-featured_schedule_cta">
                    <?php
                        site_include('/templates/common_button.php', array(
                            'sub_field' => true,
                            'label' => get_sub_field('button_a_label'),
                            'link' => get_sub_field('button_a_url') ? get_sub_field('button_a_url') : '#',
                            'class' => 'w100'
                        ));
                    ?>
                </div>
                <?php endif; ?>
            </div>
            <?php if(get_sub_field('button_b_label')): ?>
            <div class="layout-featured_cta">
                <?php
                    site_include('/templates/common_button.php', array(
                        'sub_field' => true,
                        'label' => get_sub_field('button_b_label'),
                        'link' => get_sub_field('button_b_url') ? get_sub_field('button_b_url') : '#',
                        'class' => 'w100'
                    ));
                ?>
            </div>
            <?php endif; ?>
        </div>

    </div>
    
</div>