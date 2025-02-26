<?php
/**
 * The style "panel-menu" of the Layouts
 *
 * @package ThemeREX Addons
 * @since v1.6.06
 */

$args = get_query_var('trx_addons_args_sc_layouts');

if (!empty($args['layout']) || !empty($args['content'])) {?>
    <div class="sc_layouts_panel_menu_overlay<?php if (!empty($args['scheme'])) echo ' scheme_'.esc_attr($args['scheme']);?>"></div>
    <div <?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?>
        class="sc_layouts sc_layouts_panel_menu sc_layouts_panel_menu_<?php echo esc_attr($args['panel_menu_style']);?> sc_layouts_vertical_menu_<?php echo esc_attr($args['vertical_menu_style']);

        if (!empty($args['scheme'])) echo ' scheme_'.esc_attr($args['scheme']);?>">
        <div class="sc_layouts_panel_menu_inner"><?php
            // Show layout
            if (!empty($args['layout'])) {
                $args['content'] = trx_addons_cpt_layouts_show_layout($args['layout'], 0, false);
            }
            if (!empty($args['content'])) {
                trx_addons_show_layout($args['content']);
            }
            // Add Close button
            ?><a href="#" class="sc_layouts_panel_menu_close">
                <span class="sc_layouts_panel_menu_close_text"><?php esc_html_e('Close', 'planty')?></span>
                <span class="sc_layouts_panel_menu_close_icon"></span>
                </a>
            </div>
    </div>
    <?php
}
