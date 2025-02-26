<?php
    // Add settings links to the plugin page
    function rselements_setting_link($links) {
        $settings_links = array(
            
            sprintf("<a href='%s' target='_blank'>%s</a>", 'https://rstheme.com/support/', __('Support', 'shortcode-elementor')),
        );

        $links = array_merge($links, $settings_links);

        return $links;
    }

    add_filter('plugin_action_links_' . RS_Elements_PLUGIN_BASE, 'rselements_setting_link');
