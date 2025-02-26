<?php
/**
 * Shortcode: Display a Dark/Light switcher (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v2.27.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_sc_layouts_dark_light] in the VC shortcodes list
if ( ! function_exists( 'trx_addons_sc_layouts_dark_light_add_in_vc' ) ) {
	function trx_addons_sc_layouts_dark_light_add_in_vc() {
		
		if ( ! trx_addons_cpt_layouts_sc_required() ) return;

		if ( ! trx_addons_exists_vc() ) return;
		
		vc_lean_map( "trx_sc_layouts_dark_light", 'trx_addons_sc_layouts_dark_light_add_in_vc_params' );
		class WPBakeryShortCode_Trx_Sc_Layouts_Dark_Light extends WPBakeryShortCode {}
	}
	add_action( 'init', 'trx_addons_sc_layouts_dark_light_add_in_vc', 15 );
}

// Return params
if ( ! function_exists( 'trx_addons_sc_layouts_dark_light_add_in_vc_params' ) ) {
	function trx_addons_sc_layouts_dark_light_add_in_vc_params() {
		return apply_filters( 'trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_dark_light",
				"name" => esc_html__("Layouts: Dark/Light switcher", 'trx_addons'),
				"description" => wp_kses_data( __("Insert the dark/light switcher to the custom layout", 'trx_addons') ),
				"category" => esc_html__('Layouts', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_dark_light',
				"class" => "trx_sc_layouts_dark_light",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"std" => "default",
							"value" => array_flip( apply_filters( 'trx_addons_sc_type', trx_addons_get_list_sc_dark_light_layouts(), 'trx_sc_layouts_dark_light' ) ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "permanent",
							"heading" => esc_html__("For whole site", 'trx_addons'),
							"description" => wp_kses_data( __("Apply the selected scheme for whole site or for the current page only", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "0",
							"value" => array(esc_html__("For whole site", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "effect",
							"heading" => esc_html__("Effect", 'trx_addons'),
							"description" => wp_kses_data( __("Effect of the switcher", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "slide",
							"value" => array_flip( trx_addons_get_list_sc_dark_light_effects() ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "position",
							"heading" => esc_html__("Position", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's position", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"admin_label" => true,
							"std" => "static",
							"value" => array_flip( trx_addons_get_list_sc_fixed_positions() ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "offset_x",
							"heading" => esc_html__("Horizontal offset", 'trx_addons'),
							"description" => wp_kses_data( __("Offset from the left/right side of the window", 'trx_addons') ),
							"dependency" => array(
								'element' => "position",
								'value' => array( 'tl', 'tr', 'bl', 'br' )
							),
							'edit_field_class' => 'vc_col-sm-4',
							"type" => "textfield"
						),
						array(
							"param_name" => "offset_y",
							"heading" => esc_html__("Vertical offset", 'trx_addons'),
							"description" => wp_kses_data( __("Offset from the top/bottom side of the window", 'trx_addons') ),
							"dependency" => array(
								'element' => "position",
								'value' => array( 'tl', 'tr', 'bl', 'br' )
							),
							'edit_field_class' => 'vc_col-sm-4',
							"type" => "textfield"
						),
						array(
							"param_name" => "icon_light",
							'group' => esc_html__( 'Light mode', 'trx_addons' ),
							'heading' => esc_html__('Icon', 'trx_addons'),
							'std' => '',
							'type' => 'attach_image'
						),
						array(
							"param_name" => "icon_color_light",
							'group' => esc_html__( 'Light mode', 'trx_addons' ),
							'heading' => esc_html__('Icon Color', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							'std' => '',
							'type' => 'colorpicker'
						),
						array(
							"param_name" => "bg_color_light",
							'group' => esc_html__( 'Light mode', 'trx_addons' ),
							'heading' => esc_html__('Bg Color', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							'std' => '',
							'type' => 'colorpicker'
						),
						array(
							"param_name" => "bd_color_light",
							'group' => esc_html__( 'Light mode', 'trx_addons' ),
							'heading' => esc_html__('Border Color', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							'std' => '',
							'type' => 'colorpicker'
						),
						array(
							'type' => 'param_group',
							'param_name' => 'schemes_light',
							'group' => esc_html__( 'Light mode', 'trx_addons' ),
							'heading' => esc_html__( 'Areas & Schemes', 'trx_addons' ),
							"description" => wp_kses_data( __("Specify scheme and selector for each area for the Light mode", 'trx_addons') ),
							'save_always' => true,
							'value' => urlencode( json_encode( apply_filters('trx_addons_sc_param_group_value', array(
											array(
												'area' => 'content',
												'scheme' => 'default',
												'selector' => 'html,body',
											),
											array(
												'area' => 'header',
												'scheme' => 'default',
												'selector' => '.top_panel',
											),
											array(
												'area' => 'footer',
												'scheme' => 'default',
												'selector' => '.footer_wrap',
											),
											array(
												'area' => 'sidebar',
												'scheme' => 'default',
												'selector' => '.sidebar',
											),
										), 'trx_sc_layouts_dark_light') ) ),
							'params' => apply_filters('trx_addons_sc_param_group_params', array(
											array(
												"param_name" => "area",
												"heading" => esc_html__("Area", 'trx_addons'),
												"description" => wp_kses_data( __("Area to change a color scheme", 'trx_addons') ),
												"std" => "content",
												'admin_label' => true,
												'edit_field_class' => 'vc_col-sm-4 vc_new_row',
												"value" => array_flip( trx_addons_get_list_color_scheme_areas() ),
												"type" => "dropdown"
											),
											array(
												'param_name' => 'scheme',
												'heading' => esc_html__( 'Color Scheme', 'trx_addons' ),
												'description' => esc_html__( 'Color scheme to apply to the area above', 'trx_addons' ),
												'admin_label' => true,
												'edit_field_class' => 'vc_col-sm-4',
												"value" => array_flip( trx_addons_get_list_color_schemes() ),
												"type" => "dropdown"
											),
											array(
												'param_name' => 'selector',
												'heading' => esc_html__( 'CSS Selector', 'trx_addons' ),
												'description' => esc_html__( 'CSS selector for the specified area', 'trx_addons' ),
												'edit_field_class' => 'vc_col-sm-4',
												'type' => 'textfield',
											),
										),
										'trx_sc_layouts_dark_light' )
						),
						array(
							"param_name" => "icon_dark",
							'group' => esc_html__( 'Dark mode', 'trx_addons' ),
							'heading' => esc_html__('Icon', 'trx_addons'),
							'std' => '',
							'type' => 'attach_image'
						),
						array(
							"param_name" => "icon_color_dark",
							'group' => esc_html__( 'Dark mode', 'trx_addons' ),
							'heading' => esc_html__('Icon Color', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							'std' => '',
							'type' => 'colorpicker'
						),
						array(
							"param_name" => "bg_color_dark",
							'group' => esc_html__( 'Dark mode', 'trx_addons' ),
							'heading' => esc_html__('Bg Color', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							'std' => '',
							'type' => 'colorpicker'
						),
						array(
							"param_name" => "bd_color_dark",
							'group' => esc_html__( 'Dark mode', 'trx_addons' ),
							'heading' => esc_html__('Border Color', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							'std' => '',
							'type' => 'colorpicker'
						),
						array(
							'type' => 'param_group',
							'param_name' => 'schemes_dark',
							'group' => esc_html__( 'Dark mode', 'trx_addons' ),
							'heading' => esc_html__( 'Areas & Schemes', 'trx_addons' ),
							"description" => wp_kses_data( __("Specify scheme and selector for each area for the Dark mode", 'trx_addons') ),
							'save_always' => true,
							'value' => urlencode( json_encode( apply_filters('trx_addons_sc_param_group_value', array(
											array(
												'area' => 'content',
												'scheme' => 'dark',
												'selector' => 'html,body',
											),
											array(
												'area' => 'header',
												'scheme' => 'dark',
												'selector' => '.top_panel',
											),
											array(
												'area' => 'footer',
												'scheme' => 'dark',
												'selector' => '.footer_wrap',
											),
											array(
												'area' => 'sidebar',
												'scheme' => 'dark',
												'selector' => '.sidebar',
											),
										), 'trx_sc_layouts_dark_light') ) ),
							'params' => apply_filters('trx_addons_sc_param_group_params', array(
											array(
												"param_name" => "area",
												"heading" => esc_html__("Area", 'trx_addons'),
												"description" => wp_kses_data( __("Area to change a color scheme", 'trx_addons') ),
												"std" => "content",
												'admin_label' => true,
												'edit_field_class' => 'vc_col-sm-4 vc_new_row',
												"value" => array_flip( trx_addons_get_list_color_scheme_areas() ),
												"type" => "dropdown"
											),
											array(
												'param_name' => 'scheme',
												'heading' => esc_html__( 'Color Scheme', 'trx_addons' ),
												'description' => esc_html__( 'Color scheme to apply to the area above', 'trx_addons' ),
												'admin_label' => true,
												'edit_field_class' => 'vc_col-sm-4',
												"value" => array_flip( trx_addons_get_list_color_schemes() ),
												"type" => "dropdown"
											),
											array(
												'param_name' => 'selector',
												'heading' => esc_html__( 'CSS Selector', 'trx_addons' ),
												'description' => esc_html__( 'CSS selector for the specified area', 'trx_addons' ),
												'edit_field_class' => 'vc_col-sm-4',
												'type' => 'textfield',
											),
										),
										'trx_sc_layouts_dark_light' )
						)
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_dark_light');
	}
}
