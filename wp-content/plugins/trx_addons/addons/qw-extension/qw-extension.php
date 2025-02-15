<?php
/**
 * Provides additional theme functionality: new shortcode types and styles.
 *
 * @addon qw-extension
 * @version 1.6.0
 *
 * @package ThemeREX Addons
 * @since v2.15.0
 */

if ( ! function_exists( 'qw_extensions_get_file_dir' ) ) {
    add_filter( 'trx_addons_filter_get_file_dir', 'qw_extensions_get_file_dir', 9999, 3 );
	/**
	 * Return file path (or url) in the addon folder (if present)
	 * 
	 * @hooked trx_addons_filter_get_file_dir
	 * 
	 * @param string $file		     File path or URL to the file if a file is found in previous handlers
	 * @param string $relative_path	 Relative path to the file from the plugin root
	 * @param bool $return_url	     Return URL (true) or path (false)
	 * 
	 * @return string			     File path (or url) if found in the addon folder
	 */
	function qw_extensions_get_file_dir( $file, $relative_path, $return_url ) {
		if ( empty( $file ) ) {
			$addon_name = 'qw-extension';
			$addon_templates = TRX_ADDONS_PLUGIN_ADDONS . $addon_name . '/templates/';
			if ( file_exists( TRX_ADDONS_PLUGIN_DIR . $addon_templates . $relative_path ) ) {
				$file = ( $return_url ? TRX_ADDONS_PLUGIN_URL : TRX_ADDONS_PLUGIN_DIR ) . $addon_templates . $relative_path;
			}
		}
		return $file;
	}
}


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_qw_extension_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_qw_extension_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_qw_extension_load_scripts_front() {
		if ( trx_addons_is_on( trx_addons_get_option('debug_mode') ) ) {
			wp_enqueue_style( 'trx_addons_qw_extension', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . "qw-extension/qw-extension.css" ), array(), null );
			wp_enqueue_script( 'trx_addons_qw_extension', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/qw-extension.js' ), array('jquery'), null, true );
		}
	}
}
if ( !function_exists( 'qw_extensions_load_styles_editor' ) ) {
	add_action("admin_enqueue_scripts", 'qw_extensions_load_styles_editor', 0);
	add_action( 'elementor/editor/before_enqueue_scripts', 'qw_extensions_load_styles_editor' );
	function qw_extensions_load_styles_editor() {
		wp_enqueue_style( 'trx_addons_qw_extension_editor', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . "qw-extension/css/qw-extension.editor.css" ), array(), null );		
	}
}
// Merge styles to the single stylesheet
if ( ! function_exists( 'trx_addons_qw_extension_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_qw_extension_merge_styles');
	function trx_addons_qw_extension_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . "qw-extension/qw-extension.css" ] = true;
		return $list;
	}
}

// Load responsive styles
if ( !function_exists( 'trx_addons_qw_extension_load_scripts_responsive' ) ) {
	add_action('wp_enqueue_scripts', 'trx_addons_qw_extension_load_scripts_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY);
	function trx_addons_qw_extension_load_scripts_responsive() {
		// If 'debug_mode' is off - load merged styles and scripts
		if ( trx_addons_is_on(trx_addons_get_option('debug_mode')) ) {
			wp_enqueue_style( 'trx_addons_qw_extension-responsive', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . "qw-extension/qw-extension.responsive.css" ), array(), null );
		}
	}
}
// Merge responsive styles
if ( ! function_exists( 'trx_addons_qw_extension_merge_styles_responsive' ) ) {
	add_filter('trx_addons_filter_merge_styles_responsive', 'trx_addons_qw_extension_merge_styles_responsive');
	function trx_addons_qw_extension_merge_styles_responsive( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/qw-extension.responsive.css' ] = true;
		return $list;
	}
}

// Merge specific scripts into single file
if ( ! function_exists( 'trx_addons_qw_extension_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_qw_extension_merge_scripts');
	function trx_addons_qw_extension_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/qw-extension.js' ] = true;
		return $list;
	}
}

// Font with icons must be loaded before main stylesheet
if ( !function_exists( 'qw_extensions_load_icons_front' ) ) {
	add_action("wp_enqueue_scripts", 'qw_extensions_load_icons_front', 0);
	add_action("admin_enqueue_scripts", 'qw_extensions_load_icons_front', 0);
	add_action( 'elementor/editor/before_enqueue_scripts', 'qw_extensions_load_icons_front' );
	function qw_extensions_load_icons_front() {
		wp_enqueue_style( 'qw_extensions-icons', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . "qw-extension/css/font-icons/css/qw_extension_icons.css" ), array(), null );
		//wp_enqueue_style( 'qw_extensions-icons-animation', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . "qw-extension/css/font-icons/css/animation.css" ), array(), null );
	}
}

// Return theme-specific icons
if ( ! function_exists( 'qw_extensions_addon_get_list_icons_classes' ) ) {
	add_filter( 'trx_addons_filter_get_list_icons_classes', 'qw_extensions_addon_get_list_icons_classes', 20, 2 );
	function qw_extensions_addon_get_list_icons_classes( $list, $prepend_inherit ) {
		$list_new = trx_addons_parse_icons_classes( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_ADDONS . "qw-extension/css/font-icons/css/qw_extension_icons.css" ) );
		$list = array_merge( $list, $list_new );
		return $prepend_inherit ? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx-addons' ) ), $list ) : $list;
	}
}






 // Add new output types (layouts) in the shortcodes
if ( ! function_exists( 'qw_extension_trx_addons_sc_type' ) ) {
	add_filter( 'trx_addons_sc_type', 'qw_extension_trx_addons_sc_type', 10, 2 );
	function qw_extension_trx_addons_sc_type( $list, $sc ) {
		if ( 'trx_sc_services' == $sc ) {
            $list['qw-panel'] = esc_html__( 'QW Panel', 'trx_addons' );
			$list['qw-stylish'] = esc_html__( 'QW Stylish', 'trx_addons' );
			$list['qw-price'] = esc_html__( 'QW Price', 'trx_addons' );
			$list['qw-card'] = esc_html__( 'QW Card', 'trx_addons' );
			$list['qw-plaque'] = esc_html__( 'QW Plaque', 'trx_addons' );
			$list['qw-tricolore'] = esc_html__( 'QW Tricolore', 'trx_addons' );
			$list['qw-nodes'] = esc_html__( 'QW Nodes', 'trx_addons' );
        }
		if ( 'trx_sc_icons' == $sc ) {
            $list['qw-stylish'] = esc_html__( 'QW Stylish', 'trx_addons' );
        }
		if ( 'trx_sc_portfolio' == $sc ) {
            $list['qw-pack'] = esc_html__('QW Pack', 'trx_addons' );
			$list['qw-board'] = esc_html__( 'QW Board', 'trx_addons' );
			$list['qw-chess'] = esc_html__( 'QW Chess', 'trx_addons' );
			$list['qw-simple'] = esc_html__( 'QW Simple', 'trx_addons' );
			$list['qw-case'] = esc_html__( 'QW Case', 'trx_addons' );
        }
		if ( 'trx_sc_testimonials' == $sc ) {
			$list['qw-date'] = esc_html__( 'QW Date', 'trx_addons' );
			$list['qw-big'] = esc_html__( 'QW Big', 'trx_addons' );
		}
		return $list;
	}
}


// Add/Remove params to the existings sections: use templates as Tab content
if (!function_exists('qw_extension_elm_add_params_new_set')) {
	add_action( 'elementor/element/before_section_end', 'qw_extension_elm_add_params_new_set', 11, 3 );
	function qw_extension_elm_add_params_new_set($element, $section_id, $args) {

		if ( ! is_object($element) ) return;
		$el_name = $element->get_name();

		/* Services */
		if ('trx_sc_services' == $el_name && $section_id == 'section_sc_services') {

			$control_pagination   = $element->get_controls('pagination');
			$condition_pagination = $control_pagination['condition'];
			array_push($condition_pagination['type!'], 'qw-panel');
			$element->update_control(
				'pagination', array(
					'condition' => $condition_pagination
				)
			);
			
			$control_featured_position   = $element->get_controls('featured_position');
			$condition_featured_position = $control_featured_position['condition'];
			if (! array_key_exists('type!', $condition_featured_position)) {
				$condition_featured_position['type!'] = array();
			}
			array_push($condition_featured_position['type!'], 'qw-price');
			$element->update_control(
				'featured_position', array(
					'condition' => $condition_featured_position
				)
			);

			$control_thumb_size   = $element->get_controls('thumb_size');
			$condition_thumb_size = $control_thumb_size['condition'];
			if (! array_key_exists('type!', $condition_thumb_size)) {
				$condition_thumb_size['type!'] = array();
			}
			array_push($condition_thumb_size['type!'], 'qw-price', 'qw-panel', 'qw-stylish', 'qw-card', 'qw-plaque', 'qw-tricolore');
			$element->update_control(
				'thumb_size', array(
					'condition' => $condition_thumb_size
				)
			);
			
			$control_featured_position   = $element->get_controls('featured_position');
			$condition_featured_position = $control_featured_position['condition'];
			array_push($condition_featured_position['type'], 'qw-nodes');
			$element->update_control(
				'featured_position', array(
					'condition' => $condition_featured_position
				)
			);

			$control_featured   = $element->get_controls('featured');
			$condition_featured = $control_featured['condition'];
			array_push($condition_featured['type'], 'qw-panel', 'qw-stylish', 'qw-price', 'qw-card', 'qw-plaque', 'qw-tricolore', 'qw-nodes');
			$element->update_control(
				'featured', array(
					'condition' => $condition_featured
				)
			);

			$control_columns   = $element->get_controls('columns');
			$condition_columns = $control_columns['condition'];
			array_push($condition_columns['type'], 'qw-panel', 'qw-stylish', 'qw-price', 'qw-card', 'qw-plaque', 'qw-tricolore', 'qw-nodes');
			$element->update_responsive_control(
				'columns', array(
					'condition' => $condition_columns
				)
			);
		}

		if ('trx_sc_services' == $el_name && $section_id == 'section_sc_services_details') {

			$control_show_subtitle   = $element->get_controls('show_subtitle');
			$condition_show_subtitle = $control_show_subtitle['condition'];
			array_push($condition_show_subtitle['type'], 'qw-stylish', 'qw-card', 'qw-plaque', 'qw-tricolore', 'qw-nodes');
			$element->update_control(
				'show_subtitle', array(
					'condition' => $condition_show_subtitle
				)
			);

			$control_more_text   = $element->get_controls('more_text');
			$condition_more_text = $control_more_text['condition'];
			array_push($condition_more_text['type'], 'qw-panel', 'qw-card', 'qw-plaque', 'qw-tricolore', 'qw-nodes');
			$element->update_control(
				'more_text', array(
					'condition' => $condition_more_text
				)
			);

			$control_hide_bg_image   = $element->get_controls('hide_bg_image');
			$condition_hide_bg_image = $control_hide_bg_image['condition'];
			array_push($condition_hide_bg_image['type'], 'qw-stylish', 'qw-plaque', 'qw-tricolore');
			$element->update_control(
				'hide_bg_image', array(
					'condition' => $condition_hide_bg_image
				)
			);

			$control_no_margin   = $element->get_controls('no_margin');
			$condition_no_margin = isset($control_no_margin['condition']) && is_array($control_no_margin['condition']) ? $control_no_margin['condition'] : array( 'type!' => array() );
			array_push($condition_no_margin['type!'], 'qw-stylish', 'qw-panel');
			$element->update_control(
				'no_margin', array(
					'condition' => $condition_no_margin
				)
			);

		}

		if ('trx_sc_services' == $el_name && $section_id == 'section_slider_params') {
			$control_slider   = $element->get_controls('slider');
			$condition_slider = is_array($control_slider['condition']) ? $control_slider['condition'] : array( 'type' => array() );
			array_push($condition_slider['type'], 'qw-card', 'qw-plaque', 'qw-tricolore', 'qw-nodes');
			$element->update_control(
				'slider', array(
					'condition' => $condition_slider
				)
			);
		}	

		/* Portfolio */
		if ('trx_sc_portfolio' == $el_name && $section_id == 'section_sc_portfolio') {
			$control_columns   = $element->get_controls('columns');
			$condition_columns = $control_columns['condition'];
			array_push($condition_columns['type'], 'qw-pack', 'qw-board', 'qw-chess', 'qw-simple');
			$element->update_responsive_control(
				'columns', array(
					'condition' => $condition_columns
				)
			);

			$control_use_masonry   = $element->get_controls('use_masonry');
			$condition_use_masonry = $control_use_masonry['condition'];
			array_push($condition_use_masonry['type'], 'qw-chess');
			$element->update_control(
				'use_masonry', array(
					'condition' => $condition_use_masonry
				)
			);

			$control_no_margin   = $element->get_controls('no_margin');
			$condition_no_margin = isset($control_no_margin['condition']) && is_array($control_no_margin['condition']) ? $control_no_margin['condition'] : array( 'type!' => array() );
			array_push($condition_no_margin['type!'], 'qw-case');
			$element->update_control(
				'no_margin', array(
					'condition' => $condition_no_margin
				)
			);

			/*
			$is_edit_mode = trx_addons_elm_is_edit_mode();
			$element->add_control(
				'hover',
				[
					'type' => \Elementor\Controls_Manager::SELECT,
					'label' => __( 'Image hover', 'trx_addons' ),
					'label_block' => false,
					'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_image_hover(),
					'default' => 'inherit',
					'condition' => [
						'type' => ['qw-chess']
					],
				]
			);
			*/
		}

		if ('trx_sc_portfolio' == $el_name && $section_id == 'section_slider_params') {
			$control_slider   = $element->get_controls('slider');
			$condition_slider = isset($control_slider['condition']) && is_array($control_slider['condition']) ? $control_slider['condition'] : array( 'type!' => array() );
			array_push($condition_slider['type!'], 'qw-case');
			$element->update_control(
				'slider', array(
					'condition' => $condition_slider
				)
			);
			$element->add_control(
				'qw_case_no_slider_info',
				[
					'label' => esc_html__( '* Slider is not available for this layout', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'condition' => [
						'type' => ['qw-case']
					],
				]
			);
		}	


		/* Testimonials */		
		/*
		if ('trx_sc_testimonials' == $el_name && $section_id == 'section_sc_testimonials') {
			$is_edit_mode = trx_addons_elm_is_edit_mode();
			$element->add_control(
				'use_masonry',
				[
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __( 'Use masonry', 'trx_addons' ),
					'label_block' => false,
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'return_value' => '1',
					'condition' => [
						'type' => ['qw-date']
					],
				]
			);
		}
		*/
	}
}


// Add new params to the default shortcode's atts - OLD wey
if ( ! function_exists( 'trx_addons_qw_extension_sc_atts' ) ) {
    add_filter( 'trx_addons_sc_atts', 'trx_addons_qw_extension_sc_atts', 10, 2 );
    function trx_addons_qw_extension_sc_atts( $atts, $sc ) {
		// if ( $sc == 'trx_sc_portfolio' ) {
        //     $atts['hover'] = '';
        // }
        return $atts;
    }
}

// or/and Add a new shortcode params (NEW wey)
add_action( 'after_setup_theme', function() {    
    if ( function_exists( 'trx_addons_sc_add_params' ) ) {
		trx_addons_sc_add_params( array(
			'sc' => 'trx_sc_testimonials',
			'section' => 'section_sc_testimonials',
			'params' => array(
				'use_masonry' => array(
					// Common
					'title' => esc_html__( 'Use masonry', 'trx_addons' ),
					'type' => 'switch',
					'default' =>  '',
					'return_value' => '1',
					'dependency' => array(
						'type' => ['qw-date', true],    // Value 'true' is need for GB
					),
					// VC-specific
					'edit_field_class' => 'vc_col-sm-4',
				),
			)
		) );
    }
} );





// Add parameter to the list controls styles
if ( ! function_exists( 'trx_addons_qw_extension_filter_get_list_sc_slider_controls_styles' ) ) {
	add_filter( 'trx_addons_filter_get_list_sc_slider_controls_styles', 'trx_addons_qw_extension_filter_get_list_sc_slider_controls_styles', 10, 2 );
	function trx_addons_qw_extension_filter_get_list_sc_slider_controls_styles( $list ) {
		$list['simple'] = esc_html__( 'Simple', 'trx_addons' );
		return $list;
	}
}







// Enqueue styles for frontend (Services)
if ( ! function_exists( 'trx_addons_qw_extension_cpt_services_frontend_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_qw_extension_cpt_services_frontend_scripts', 1100 );
	add_action( 'trx_addons_action_load_scripts_front_cpt_services', 'trx_addons_qw_extension_cpt_services_frontend_scripts', 10, 1 );
	function trx_addons_qw_extension_cpt_services_frontend_scripts( $force = false ) {
		static $loaded = false;
		if ( ! $loaded && (
			current_action() == 'wp_enqueue_scripts' && trx_addons_need_frontend_scripts( 'cpt_services' )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded = true;
			$url = trx_addons_get_file_url(TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/css/qw_extension_services.css');
			if ( '' != $url ) {
				wp_enqueue_style( 'trx_addons_qw_extension-services', $url, array(), null );
			}
		}
	}
}
// Merge custom styles (Services)
if ( ! function_exists( 'trx_addons_qw_extension_cpt_services_merge_styles' ) ) {
	add_filter('trx_addons_filter_merge_styles', 'trx_addons_qw_extension_cpt_services_merge_styles', 100);
	function trx_addons_qw_extension_cpt_services_merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/css/qw_extension_services.css' ] = false;
		return $list;
	}
}
// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_qw_extension_cpt_services_frontend_scripts_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_qw_extension_cpt_services_frontend_scripts_responsive', 2000 );
	add_action( 'trx_addons_action_load_scripts_front_cpt_services', 'trx_addons_qw_extension_cpt_services_frontend_scripts_responsive', 10, 1 );
	function trx_addons_qw_extension_cpt_services_frontend_scripts_responsive( $force = false ) {
		static $loaded = false;
		if ( ! $loaded && (
			current_action() == 'wp_enqueue_scripts' && trx_addons_need_frontend_scripts( 'cpt_services' )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded = true;
			$url = trx_addons_get_file_url(TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/css/qw_extension_services.responsive.css');
			if ( '' != $url ) {
				wp_enqueue_style( 'trx_addons_qw_extension-services-responsive', $url, array(), null );
			}
		}
	}
}
// Merge responsive styles
if ( ! function_exists( 'trx_addons_qw_extension_cpt_services_merge_styles_responsive' ) ) {
	add_filter('trx_addons_filter_merge_styles_responsive', 'trx_addons_qw_extension_cpt_services_merge_styles_responsive', 100);
	function trx_addons_qw_extension_cpt_services_merge_styles_responsive( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/css/qw_extension_services.responsive.css' ] = false;
		return $list;
	}
}



// Enqueue styles for frontend (Icons)
if ( ! function_exists( 'trx_addons_qw_extension_sc_icons_frontend_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_qw_extension_sc_icons_frontend_scripts', 1100 );
	add_action( 'trx_addons_action_load_scripts_front_sc_icons', 'trx_addons_qw_extension_sc_icons_frontend_scripts', 10, 1 );
	function trx_addons_qw_extension_sc_icons_frontend_scripts( $force = false ) {
		static $loaded = false;
		if ( ! $loaded && (
			current_action() == 'wp_enqueue_scripts' && trx_addons_need_frontend_scripts( 'sc_icons' )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded = true;
			$url = trx_addons_get_file_url(TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/css/qw_extension_icons.css');
			if ( '' != $url ) {
				wp_enqueue_style( 'trx_addons_qw_extension-icons', $url, array(), null );
			}
		}
	}
}
// Merge custom styles (Icons)
if ( ! function_exists( 'trx_addons_qw_extension_sc_icons_merge_styles' ) ) {
	add_filter('trx_addons_filter_merge_styles', 'trx_addons_qw_extension_sc_icons_merge_styles');
	function trx_addons_qw_extension_sc_icons_merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/css/qw_extension_icons.css' ] = false;
		return $list;
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_qw_extension_sc_icons_frontend_scripts_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_qw_extension_sc_icons_frontend_scripts_responsive', 2000 );
	add_action( 'trx_addons_action_load_scripts_front_sc_icons', 'trx_addons_qw_extension_sc_icons_frontend_scripts_responsive', 10, 1 );
	function trx_addons_qw_extension_sc_icons_frontend_scripts_responsive( $force = false ) {
		static $loaded = false;
		if ( ! $loaded && (
			current_action() == 'wp_enqueue_scripts' && trx_addons_need_frontend_scripts( 'sc_icons' )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded = true;
			$url = trx_addons_get_file_url(TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/css/qw_extension_icons.responsive.css');
			if ( '' != $url ) {
				wp_enqueue_style( 'trx_addons_qw_extension-icons-responsive', $url, array(), null );
			}
		}
	}
}
// Merge responsive styles
if ( ! function_exists( 'trx_addons_qw_extension_sc_icons_merge_styles_responsive' ) ) {
	add_filter('trx_addons_filter_merge_styles_responsive', 'trx_addons_qw_extension_sc_icons_merge_styles_responsive');
	function trx_addons_qw_extension_sc_icons_merge_styles_responsive( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/css/qw_extension_icons.responsive.css' ] = false;
		return $list;
	}
}




// Enqueue styles for frontend (Portfolio)
if ( ! function_exists( 'trx_addons_qw_extension_cpt_portfolio_frontend_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_qw_extension_cpt_portfolio_frontend_scripts', 1100 );
	add_action( 'trx_addons_action_load_scripts_front_cpt_portfolio', 'trx_addons_qw_extension_cpt_portfolio_frontend_scripts', 10, 1 );
	function trx_addons_qw_extension_cpt_portfolio_frontend_scripts( $force = false ) {
		static $loaded = false;
		if ( ! $loaded && (
			current_action() == 'wp_enqueue_scripts' && trx_addons_need_frontend_scripts( 'cpt_portfolio' )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded = true;
			$url = trx_addons_get_file_url(TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/css/qw_extension_portfolio.css');
			if ( '' != $url ) {
				wp_enqueue_style( 'trx_addons_qw_extension-portfolio', $url, array(), null );
			}
		}
	}
}
// Merge custom styles (portfolio)
if ( ! function_exists( 'trx_addons_qw_extension_cpt_portfolio_merge_styles' ) ) {
	add_filter('trx_addons_filter_merge_styles', 'trx_addons_qw_extension_cpt_portfolio_merge_styles');
	function trx_addons_qw_extension_cpt_portfolio_merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/css/qw_extension_portfolio.css' ] = false;
		return $list;
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_qw_extension_cpt_portfolio_frontend_scripts_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_qw_extension_cpt_portfolio_frontend_scripts_responsive', 2000 );
	add_action( 'trx_addons_action_load_scripts_front_cpt_portfolio', 'trx_addons_qw_extension_cpt_portfolio_frontend_scripts_responsive', 10, 1 );
	function trx_addons_qw_extension_cpt_portfolio_frontend_scripts_responsive( $force = false ) {
		static $loaded = false;
		if ( ! $loaded && (
			current_action() == 'wp_enqueue_scripts' && trx_addons_need_frontend_scripts( 'cpt_portfolio' )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded = true;
			$url = trx_addons_get_file_url(TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/css/qw_extension_portfolio.responsive.css');
			if ( '' != $url ) {
				wp_enqueue_style( 'trx_addons_qw_extension-portfolio-responsive', $url, array(), null );
			}
		}
	}
}
// Merge responsive styles
if ( ! function_exists( 'trx_addons_qw_extension_cpt_portfolio_merge_styles_responsive' ) ) {
	add_filter('trx_addons_filter_merge_styles_responsive', 'trx_addons_qw_extension_cpt_portfolio_merge_styles_responsive');
	function trx_addons_qw_extension_cpt_portfolio_merge_styles_responsive( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/css/qw_extension_portfolio.responsive.css' ] = false;
		return $list;
	}
}





// Enqueue styles for frontend (Testimonials)
if ( ! function_exists( 'trx_addons_qw_extension_cpt_testimonials_frontend_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_qw_extension_cpt_testimonials_frontend_scripts', 1100 );
	add_action( 'trx_addons_action_load_scripts_front_cpt_testimonials', 'trx_addons_qw_extension_cpt_testimonials_frontend_scripts', 10, 1 );
	function trx_addons_qw_extension_cpt_testimonials_frontend_scripts( $force = false ) {
		static $loaded = false;
		if ( ! $loaded && (
			current_action() == 'wp_enqueue_scripts' && trx_addons_need_frontend_scripts( 'cpt_testimonials' )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded = true;
			$url = trx_addons_get_file_url(TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/css/qw_extension_testimonials.css');
			if ( '' != $url ) {
				wp_enqueue_style( 'trx_addons_qw_extension-testimonials', $url, array(), null );
			}
		}
	}
}
// Merge custom styles (Testimonials)
if ( ! function_exists( 'trx_addons_qw_extension_cpt_testimonials_merge_styles' ) ) {
	add_filter('trx_addons_filter_merge_styles', 'trx_addons_qw_extension_cpt_testimonials_merge_styles', 100);
	function trx_addons_qw_extension_cpt_testimonials_merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/css/qw_extension_testimonials.css' ] = false;
		return $list;
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_qw_extension_cpt_testimonials_frontend_scripts_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_qw_extension_cpt_testimonials_frontend_scripts_responsive', 2000 );
	add_action( 'trx_addons_action_load_scripts_front_cpt_testimonials', 'trx_addons_qw_extension_cpt_testimonials_frontend_scripts_responsive', 10, 1 );
	function trx_addons_qw_extension_cpt_testimonials_frontend_scripts_responsive( $force = false ) {
		static $loaded = false;
		if ( ! $loaded && (
			current_action() == 'wp_enqueue_scripts' && trx_addons_need_frontend_scripts( 'cpt_testimonials' )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded = true;
			$url = trx_addons_get_file_url(TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/css/qw_extension_testimonials.responsive.css');
			if ( '' != $url ) {
				wp_enqueue_style( 'trx_addons_qw_extension-testimonials-responsive', $url, array(), null );
			}
		}
	}
}
// Merge responsive styles
if ( ! function_exists( 'trx_addons_qw_extension_cpt_testimonials_merge_styles_responsive' ) ) {
	add_filter('trx_addons_filter_merge_styles_responsive', 'trx_addons_qw_extension_cpt_testimonials_merge_styles_responsive', 100);
	function trx_addons_qw_extension_cpt_testimonials_merge_styles_responsive( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'qw-extension/css/qw_extension_testimonials.responsive.css' ] = false;
		return $list;
	}
}