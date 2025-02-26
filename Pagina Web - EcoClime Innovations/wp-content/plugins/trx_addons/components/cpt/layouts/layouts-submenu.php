<?php
/**
 * ThemeREX Addons Layouts: Use layouts as submenu
 *
 * @package ThemeREX Addons
 * @since v1.6.39
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! defined( 'TRX_ADDONS_MENU_SETTINGS_META_KEY' ) )		define( 'TRX_ADDONS_MENU_SETTINGS_META_KEY', 'trx_addons_nav_menu_item_data' );
if ( ! defined( 'TRX_ADDONS_MENU_SETTINGS_META_KEY_OLD' ) )	define( 'TRX_ADDONS_MENU_SETTINGS_META_KEY_OLD', '_menu_item_layout_submenu' );

// Add layout's type in the list
if ( ! function_exists( 'trx_addons_cpt_layouts_submenu_add_layout_type' ) ) {
 	add_filter( 'trx_addons_filter_layout_types', 'trx_addons_cpt_layouts_submenu_add_layout_type' );
	function trx_addons_cpt_layouts_submenu_add_layout_type( $list ) {
		trx_addons_array_insert_after( $list, 'footer', array( 'submenu' => esc_html__( 'Submenu', 'trx_addons' ) ) );
		return $list;
	}
}

// Store layout id to the menu item's post_meta
if ( ! function_exists( 'trx_addons_cpt_layouts_submenu_save_settings_to_menu_items' ) ) {
 	add_action( 'wp_update_nav_menu', 'trx_addons_cpt_layouts_submenu_save_settings_to_menu_items', 10, 1 );
	function trx_addons_cpt_layouts_submenu_save_settings_to_menu_items( $nav_menu_selected_id ) {
		$menu_items = wp_get_nav_menu_items( $nav_menu_selected_id, array(
																		'orderby' => 'ID',
																		'output' => ARRAY_A,
																		'output_key' => 'ID',
																		'post_status' => 'draft,publish'
																		) );
		foreach ( $menu_items as $item ) {
			if ( ! empty( $_POST['menu-item-settings'][ $item->db_id ] ) ) {
				$settings = json_decode( trx_addons_stripslashes( $_POST['menu-item-settings'][ $item->db_id ] ), true );
				if ( is_array( $settings ) ) {
					update_post_meta( $item->db_id, TRX_ADDONS_MENU_SETTINGS_META_KEY, $settings );
				}
			}
		}
	}
}

// Add settings to the menu item object
if ( ! function_exists( 'trx_addons_cpt_layouts_submenu_load_settings_to_menu_items' ) ) {
 	add_filter( 'wp_setup_nav_menu_item', 'trx_addons_cpt_layouts_submenu_load_settings_to_menu_items' );
	function trx_addons_cpt_layouts_submenu_load_settings_to_menu_items( $menu_item ) {
		if ( isset( $menu_item->post_type ) && 'nav_menu_item' == $menu_item->post_type && ! isset( $menu_item->settings)) {
			$menu_item->settings = get_post_meta( $menu_item->ID, TRX_ADDONS_MENU_SETTINGS_META_KEY, true );
		}
		return $menu_item;
	}
}

// Add class 'menu-item-has-children' to the items with layouts submenu
if ( ! function_exists( 'trx_addons_cpt_layouts_submenu_add_class_has_children_to_menu_items' ) ) {
 	add_filter( 'wp_nav_menu_objects', 'trx_addons_cpt_layouts_submenu_add_class_has_children_to_menu_items', 10, 2 );
	function trx_addons_cpt_layouts_submenu_add_class_has_children_to_menu_items( $menu_items, $args = null ) {
		if ( is_array( $menu_items ) ) {
			foreach ( $menu_items as $k => $item ) {
				if ( ! empty( $item->settings['layout_submenu'] ) && (int)$item->settings['layout_submenu'] > 0 ) {
					$menu_items[$k]->classes[] = 'menu-item-has-children';
					$menu_items[$k]->classes[] = 'menu-item-has-children-layout';
					if ( ! empty( $args->menu_class ) && strpos( $args->menu_class, 'trx-addons-nav-menu' ) !== false ) {
						$menu_items[$k]->classes[] = 'trx-addons-mega-nav-item';	// For the NavMenu widget
					}
				}
				if ( empty( $args->menu_class ) || strpos( $args->menu_class, 'trx-addons-nav-menu' ) === false ) {
					if ( ! empty( $item->settings['submenu_pos'] ) && $item->settings['submenu_pos'] == 'static' ) {
						$menu_items[$k]->classes[] = 'menu-item-position-static';
					}
					if ( ! empty( $item->settings['badge_text'] ) ) {
						$menu_items[$k]->classes[] = 'menu-item-has-badge';
					}
					if ( ! empty( $item->settings['icon'] ) ) {
						$menu_items[$k]->classes[] = 'menu-item-has-icon';
					}
				}
				if ( ! empty( $item->settings['submenu_fullwidth'] ) ) {
					$menu_items[$k]->classes[] = 'trx_addons_' . $item->settings['submenu_fullwidth'];
				}
				if ( ! empty( $item->settings['submenu_width'] ) && ( empty( $item->settings['submenu_fullwidth'] ) || $item->settings['submenu_fullwidth'] == 'no_stretch' ) ) {
					$menu_items[$k]->classes[] = trx_addons_add_inline_css_class( 'width: ' . $item->settings['submenu_width'] . 'px !important;', ' > ul' );
				}
			}
		}
		return $menu_items;
	}
}

// Add layout content to the item's output ( if 'layouts submenu' is set for this item )
if ( ! function_exists( 'trx_addons_cpt_layouts_submenu_show_layout' ) ) {
 	add_filter( 'walker_nav_menu_start_el', 'trx_addons_cpt_layouts_submenu_show_layout', 10, 4 );
	function trx_addons_cpt_layouts_submenu_show_layout( $output, $item, $depth, $args ) {
		// Add an icon and a badge to the item's output (only for sc_layouts menu, not for NavMenu widget)
		if ( empty( $args->menu_class ) || strpos( $args->menu_class, 'trx-addons-nav-menu' ) === false ) {
			$icon_html = $badge_html = '';
			if ( ! empty( $item->settings['icon'] ) ) {
				$icon_html = '<i class="menu-item-icon '
									. esc_attr( $item->settings['icon'] )
									. ( ! empty( $item->settings['icon_color'] ) ? ' ' . trx_addons_add_inline_css_class( 'color:' . $item->settings['icon_color'] ) : '' )
									. ( ! empty( $item->settings['icon_hover'] ) ? ' ' . trx_addons_add_inline_css_class( 'color:' . $item->settings['icon_hover'], '', '.menu-item > a:hover > ' ) : '' )
							. '"></i>';
			}
			if ( ! empty( $item->settings['badge_text'] ) ) {
				$css = '';
				$badge_class = 'menu-item-badge';
				if ( ! empty( $item->settings['badge_color'] ) ) {
					$css .= 'color:' . $item->settings['badge_color'] . ';';
				}
				if ( ! empty( $item->settings['badge_bg_color'] ) ) {
					$css .= 'background-color:' . $item->settings['badge_bg_color'] . ';';
				}
				if ( ! empty( $item->settings['badge_font_size'] ) && (int)$item->settings['badge_font_size'] > 0 ) {
					$css .= 'font-size:' . $item->settings['badge_font_size'] . 'px !important;';
				}
				if ( ! empty( $item->settings['badge_font_bold'] ) && (int)$item->settings['badge_font_bold'] > 0 ) {
					$css .= 'font-weight:bold !important;';
				}
				if ( ! empty( $item->settings['badge_offset_x'] ) && (int)$item->settings['badge_offset_x'] != 0 ) {
					$css .= 'left:' . $item->settings['badge_offset_x'] . 'px;';
				}
				if ( ! empty( $item->settings['badge_offset_y'] ) && (int)$item->settings['badge_offset_y'] != 0 ) {
					$css .= 'top:' . $item->settings['badge_offset_y'] . 'px;';
				}
				if ( ! empty( $css ) ) {
					$badge_class .= ' ' . trx_addons_add_inline_css_class( $css );
				}
				$badge_html = '<i class="' . esc_attr( $badge_class ) . '">'
								. esc_html( $item->settings['badge_text'] )
							. '</i>';
			}
			// Add icon and badge to the item's output (inside the link)
			if ( ! empty( $icon_html ) || ! empty( $badge_html ) ) {
				$output = preg_replace( '/(<a[^>]*>)/', '$1' . $icon_html . $badge_html, $output );
			}
		}
		// Add layout content to the item's output ( if 'layouts submenu' is set for this item )
		if ( ! empty( $item->settings['layout_submenu'] ) && (int)$item->settings['layout_submenu'] > 0 ) {
			ob_start();
			// Start submenu creation
			trx_addons_sc_stack_push( 'trx_sc_layouts_submenu' );
			// Simulate usage shortcode to prevent wrap widgets to the class 'sc_layouts_item'
			trx_addons_sc_stack_push( 'trx_sc_layouts' );
			// Make layout with submenu
			do_action( 'trx_addons_action_before_show_layout_submenu' );
			trx_addons_cpt_layouts_show_layout( $item->settings['layout_submenu'] );
			do_action( 'trx_addons_action_after_show_layout_submenu' );
			// End simulation
			trx_addons_sc_stack_pop();
			// End submenu creation
			trx_addons_sc_stack_pop();
			// Get layout and output it (if not empty)
			$submenu = ob_get_contents();
			ob_end_clean();
			if ( ! empty( $submenu ) ) {
				$output .= '<ul class="'
								. ( ! empty( $args->menu_class ) && strpos( $args->menu_class, 'trx-addons-nav-menu' ) !== false
									? 'trx-addons-mega-content-container'
									: 'sc_layouts_submenu'
									)
								. '"'
								. ( ! empty( $item->settings['submenu_width'] ) && (int)$item->settings['submenu_width'] > 0
									? ' style="width: ' . $item->settings['submenu_width'] . 'px;"'
									: ''
									)
							. '>'
								. '<li class="sc_layouts_submenu_wrap">' . trim( $submenu ) . '</li>'
							. '</ul>';
			}
		}
		return $output;
	}
}


//-------------------------------------------------------
// ADMIN AREA: MENU EDITOR
//-------------------------------------------------------

if ( ! function_exists( 'trx_addons_cpt_layouts_submenu_load_scripts_admin' ) ) {
	add_action( 'admin_footer', 'trx_addons_cpt_layouts_submenu_load_scripts_admin' );
	/**
	 * Load required styles and scripts for admin mode for the menu editor
	 * 
	 * @hooked admin_footer
	 */
	function trx_addons_cpt_layouts_submenu_load_scripts_admin() {
		static $loaded = false;
		if ( $loaded ) return;
		$loaded = true;

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
		if ( ! empty( $screen->id ) && 'nav-menus' === $screen->id ) {
			// wp_enqueue_style(  'wp-color-picker' );
			include_once trx_addons_get_file_dir( 'templates/tpl.admin-menu-settings.php' );
		}
	}
}

if ( ! function_exists( 'trx_addons_cpt_layouts_submenu_need_options' ) ) {
	add_filter( 'trx_addons_filter_need_options', 'trx_addons_cpt_layouts_submenu_need_options' );
	/**
	 * Check if current screen need to load options scripts and styles
	 * 
	 * @hooked trx_addons_filter_need_options
	 *
	 * @param bool $need  Filter value
	 * 
	 * @return bool     True if current screen need to load options scripts and styles
	 */
	function trx_addons_cpt_layouts_submenu_need_options( $need = false ) {
		if ( ! $need ) {
			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			if ( ! empty( $screen->id ) && 'nav-menus' === $screen->id ) {
				$need = true;
			}
		}
		return $need;
	}
}

if ( ! function_exists( 'trx_addons_cpt_layouts_submenu_load_scripts' ) ) {
	add_action( "admin_enqueue_scripts", 'trx_addons_cpt_layouts_submenu_load_scripts' );
	/**
	 * Enqueue scripts and styles for the user profile
	 * 
	 * @hooked admin_enqueue_scripts
	 */
	function trx_addons_cpt_layouts_submenu_load_scripts() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
		if ( ! empty( $screen->id ) && 'nav-menus' === $screen->id ) {
			wp_localize_script( 'trx_addons-options', 'TRX_ADDONS_DEPENDENCIES', 
								trx_addons_get_options_dependencies( trx_addons_cpt_layouts_submenu_get_fields() ) );
		}
	}
}

if ( ! function_exists( 'trx_addons_cpt_layouts_submenu_get_fields' ) ) {
	/**
	 * Return fields for the menu item settings
	 * 
	 * @return array     Menu setting fields
	 */
	function trx_addons_cpt_layouts_submenu_get_fields() { 
		return apply_filters( 'trx_addons_filter_menu_item_settings', array(
			'layout_submenu' => array(
				'title' => esc_html__( 'Layout of submenu (optional)', 'trx_addons' ),
				'desc'  => wp_kses(
								__( 'Select a pre-created Layout to use as a submenu.', 'trx_addons' )
								. ' '
								. sprintf( '<a href="%1$s" class="trx_addons_post_editor trx_addons_hidden" target="_blank">%2$s</a>',
									admin_url( apply_filters( 'trx_addons_filter_post_edit_link', sprintf( "post.php?post=%d&amp;action=edit", 0 ), 0 ) ),
									__( 'Open selected layout in a new tab to edit', 'trx_addons' )
								),
								'trx_addons_kses_content'
							),
				'std'     => 'none',
				'val'     => 'none',	// Need to specify 'val' equal to 'std' (for all options) because options are not loaded from the database (its an option list for the popup dialog)
				'options' => trx_addons_get_list_layouts( true, 'submenu', 'title' ),
				'type'    => 'select',
			),
			'submenu_pos' => array(
				'title' => esc_html__( 'Submenu position', 'trx_addons' ),
				'desc'  => wp_kses( __( 'A position of the parent item.', 'trx_addons' ), 'trx_addons_kses_content' ),
				'dependency' => array(
					'layout_submenu' => array( '^none' ),
				),
				'std'     => 'default',
				'val'     => 'default',
				'options' => array(
					'default' => esc_html__( 'Default', 'trx_addons' ),
					'static'  => esc_html__( 'Static', 'trx_addons' ),
				),
				'type'    => 'select',
			),
			'submenu_depth' => array(
				'title' => esc_html__( 'Submenu depth', 'trx_addons' ),
				// 'desc'  => wp_kses_data( __('Depth of current menu level', 'trx_addons') ),
				'dependency' => array(
					'layout_submenu' => array( '^none' ),
				),
				'std'   => '0',
				'val'   => '0',
				'hidden' => true,	// 'hidden' => 'true' - hide this field in the 'Menu Settings' dialog
				'type'  => 'text'
			),
			// 'submenu_fullwidth' => array(
			// 	'title' => esc_html__( 'Submenu fullwidth', 'trx_addons' ),
			// 	'desc'  => wp_kses_data( __('Make this submenu fullwidth (only for the first level menu)', 'trx_addons') ),
			// 	'dependency' => array(
			// 		'layout_submenu' => array( '^none' ),
			// 		'submenu_depth' => array( '0' ),
			// 	),
			// 	'std'   => '0',
			// 	'val'   => '0',
			// 	'type'  => 'switch'
			// ),
			'submenu_fullwidth' => array(
				'title' => esc_html__( 'Submenu stretch', 'trx_addons' ),
				'desc'  => wp_kses_data( __('Stretch this submenu (only for the first level menu)', 'trx_addons') ),
				'dependency' => array(
					// 'layout_submenu' => array( '^none' ),
					'submenu_depth' => array( '0' ),
				),
				'std'   => '',
				'val'   => '',
				'options' => array(
					''                     => esc_html__( 'Default', 'trx_addons' ),
					'no_stretch'           => esc_html__( 'No stretch', 'trx_addons' ),
					'stretch_content'      => esc_html__( 'Boxed bg & content', 'trx_addons' ),
					'stretch_window_boxed' => esc_html__( 'Fullwidth bg, boxed content', 'trx_addons' ),
					'stretch_window'       => esc_html__( 'Fullwidth bg & content', 'trx_addons' ),
				),
				'type'  => 'select'
			),
			'submenu_width' => array(
				'title' => esc_html__( 'Submenu width', 'trx_addons' ),
				'desc'  => wp_kses_data( __('If 0 - a default width is used.', 'trx_addons') ),
				'dependency' => array(
					'layout_submenu' => array( '^none' ),
					'submenu_fullwidth' => array( 'is_empty', 'no_stretch' ),
				),
				'std'   => 0,
				'val'   => 0,
				'min'   => 0,
				'max'   => 2000,
				'type'  => 'slider'
			),
			'icon' => array(
				'title' => esc_html__( 'Item Icon', 'trx_addons'),
				// 'desc' => wp_kses_data( __('Select an icon for the item', 'trx_addons') ),
				'std' => '',
				'val' => '',
				'options' => array(),
				'style' => trx_addons_get_setting('icons_type'),
				'type' => 'icons'
			),
			'icon_color' => array(
				'title' => esc_html__( 'Icon Color', 'trx_addons' ),
				// 'desc'  => wp_kses_data( __('Color of the icon', 'trx_addons') ),
				'dependency' => array(
					'icon' => array( 'not_empty' ),
				),
				'std'   => '',
				'val'   => '',
				'type'  => 'color'
			),
			'icon_hover' => array(
				'title' => esc_html__( 'Icon Hover', 'trx_addons' ),
				// 'desc'  => wp_kses_data( __('Hover color of the icon', 'trx_addons') ),
				'dependency' => array(
					'icon' => array( 'not_empty' ),
				),
				'std'   => '',
				'val'   => '',
				'type'  => 'color'
			),
			'badge_text' => array(
				'title' => esc_html__( 'Badge text', 'trx_addons' ),
				// 'desc'  => wp_kses_data( __('Text of the badge', 'trx_addons') ),
				'std'   => '',
				'val'   => '',
				'type'  => 'text'
			),
			'badge_color' => array(
				'title' => esc_html__( 'Badge color', 'trx_addons' ),
				// 'desc'  => wp_kses_data( __('Color of the badge', 'trx_addons') ),
				'dependency' => array(
					'badge_text' => array( 'not_empty' ),
				),
				'std'   => '',
				'val'   => '',
				'type'  => 'color'
			),
			'badge_bg_color' => array(
				'title' => esc_html__( 'Badge background', 'trx_addons' ),
				// 'desc'  => wp_kses_data( __('Background color of the badge', 'trx_addons') ),
				'dependency' => array(
					'badge_text' => array( 'not_empty' ),
				),
				'std'   => '',
				'val'   => '',
				'type'  => 'color'
			),
			'badge_font_size' => array(
				'title' => esc_html__( 'Badge font size', 'trx_addons' ),
				'desc'  => wp_kses_data( __('Font size (in px) of the badge. If 0 - a default size is used.', 'trx_addons') ),
				'dependency' => array(
					'badge_text' => array( 'not_empty' ),
				),
				'std'   => 0,
				'val'   => 0,
				'min'   => 0,
				'max'   => 30,
				'type'  => 'slider'
			),
			'badge_font_bold' => array(
				'title' => esc_html__( 'Badge font bold', 'trx_addons' ),
				// 'desc'  => wp_kses_data( __('Font weight of the badge.', 'trx_addons') ),
				'dependency' => array(
					'badge_text' => array( 'not_empty' ),
				),
				'std'   => '0',
				'val'   => '0',
				'type'  => 'switch'
			),
			'badge_offset_x' => array(
				'title' => esc_html__( 'Badge X offset', 'trx_addons' ),
				'desc'  => wp_kses_data( __('Offset of the badge on the X axis.', 'trx_addons') ),
				'dependency' => array(
					'badge_text' => array( 'not_empty' ),
				),
				'std'   => 0,
				'val'   => 0,
				'min'   => -20,
				'max'   => 20,
				'type'  => 'slider'
			),
			'badge_offset_y' => array(
				'title' => esc_html__( 'Badge Y offset', 'trx_addons' ),
				'desc'  => wp_kses_data( __('Offset of the badge on the Y axis.', 'trx_addons') ),
				'dependency' => array(
					'badge_text' => array( 'not_empty' ),
				),
				'std'   => 0,
				'val'   => 0,
				'min'   => -20,
				'max'   => 20,
				'type'  => 'slider'
			),
		) );
	}
}

// Substitute standard WordPress menu edit walker
if ( ! function_exists( 'trx_addons_cpt_layouts_submenu_set_nav_menu_edit_walker_class' ) ) {
	add_filter( 'wp_edit_nav_menu_walker', 'trx_addons_cpt_layouts_submenu_set_nav_menu_edit_walker_class', 10, 2 );
	function trx_addons_cpt_layouts_submenu_set_nav_menu_edit_walker_class( $class = '', $menu_id = 0 ) {
		return 'THEMEREX_ADDONS_NAV_MENU_EDIT_WALKER';
	}
}

// Standard WordPress Walker_Nav_Menu_Edit class
require_once( ABSPATH . 'wp-admin/includes/class-walker-nav-menu-edit.php' );

if ( ! class_exists( 'THEMEREX_ADDONS_NAV_MENU_EDIT_WALKER' ) ) {
	class THEMEREX_ADDONS_NAV_MENU_EDIT_WALKER extends Walker_Nav_Menu_Edit {

		var $layouts = false;
		
		/**
		 * Start the element output.
		 *
		 * @see Walker_Nav_Menu::start_el()
		 * @since 3.0.0
		 *
		 * @global int $_wp_nav_menu_max_depth
		 *
		 * @param string $output Used to append additional content (passed by reference).
		 * @param object $item   Menu item data object.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   Not used.
		 * @param int    $id     Not used.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			$tmp = $output;
			parent::start_el( $output, $item, $depth, $args, $id );
			$item_id = $item->ID;
			$item_html = substr( $output, strlen( $tmp ) );

			// Get list of layouts
			if ( $this->layouts === false ) {
				$this->layouts = trx_addons_get_list_layouts( true, 'submenu', 'title' );
			}

			// Add layouts selector if at least one layout is present
			// (item 0 is '- Not Selected -')
			/*
			if ( is_array( $this->layouts ) && count( $this->layouts ) > 1 ) {
				$html = '<p class="field-layout-submenu description description-wide">
						<label for="edit-menu-item-layout-submenu-' . esc_attr( $item_id ) . '">'
							. '<span class="field-title-layout-submenu">' . esc_html__( 'Layout of submenu (optional)', 'trx_addons' ) . '</span>'
							. sprintf( '<a href="%1$s" class="trx_addons_post_editor' . ( empty( $item->layout_submenu ) || intval( $item->layout_submenu ) == 0 ? ' trx_addons_hidden' : '' ) . '" target="_blank">%2$s</a>',
										admin_url( apply_filters( 'trx_addons_filter_post_edit_link', sprintf( "post.php?post=%d&amp;action=edit", $item->layout_submenu ), $item->layout_submenu) ),
										__( "Open selected layout in a new tab to edit", 'trx_addons' )
									)
							.'<select id="edit-menu-item-layout-submenu-' . esc_attr( $item_id ) . '"'
								. ' name="menu-item-layout-submenu[' . esc_attr( $item_id ) . ']"'
								. ' class="widefat code edit-menu-item-layout-submenu trx_addons_layout_selector"'
							. '>';
				foreach ( $this->layouts as $id => $title ) {
					$html .= '<option value="' . esc_attr( $id ) . '"' . ( ! empty( $item->layout_submenu ) && $item->layout_submenu == $id ? ' selected="selected"' : '' ) .'>'
								. esc_html( $title )
							. '</option>';
				}
				$html .= '
							</select>
						</label>
					</p>';
				$item_html = str_replace( '<fieldset class="field-move', $html . '<fieldset class="field-move', $item_html );
			}
			*/

			// Mark the item as 'menu-item-has-submenu-settings' and 'menu-item-has-submenu-layout' if it has a correspond settings
			$add_classes = '';
			if ( ! empty( $item->settings['icon'] ) || ! empty( $item->settings['badge_text'] ) ) {
				$add_classes = ' menu-item-has-submenu-settings';
			}
			if ( ! empty( $item->settings['layout_submenu'] ) && (int)$item->settings['layout_submenu'] > 0 ) {
				$add_classes .= ' menu-item-has-submenu-layout';
			}
			if ( ! empty( $add_classes ) ) {
				$item_html = preg_replace( '/(<li[^>]*class="menu-item[\s]*)/U', '$1' . $add_classes . ' ', $item_html );
			}

			// Add a link "Settings" to the item
			$item_html = preg_replace(
				'/(<div class="menu-item-actions[^>]+>)/',
				'$1<a class="item-settings" id="settings-' . $item_id . '" href="#">' . __( 'Settings', 'trx_addons' ) . '</a>'
				. '<input type="hidden" id="edit-menu-item-settings-' . $item_id . '"'
					. ' name="menu-item-settings[' . $item_id . ']"'
					. ' value="' . esc_attr( json_encode( $item->settings ) ) . '"'
					. '>',
				$item_html
			);
			
			// Add a new item html to the output
			$output = $tmp . $item_html;
		}
	}
}

// Convert old parameters of menu items to the new format
//-------------------------------------------------------

if ( ! function_exists( 'trx_addons_cpt_layouts_submenu_convert_params' ) ) {
	add_action( 'trx_addons_action_is_new_version_of_plugin', 'trx_addons_cpt_layouts_submenu_convert_params', 10, 2 );
	add_action( 'trx_addons_action_importer_import_end', 'trx_addons_cpt_layouts_submenu_convert_params' );
	add_action( 'admin_init', 'trx_addons_cpt_layouts_submenu_convert_params' );
	/**
	 * Convert old parameters of menu items (submenu layout id) to the new format (settings array)
	 * after update plugin ThemeREX Addons to the new version or after import demo data.
	 * Get all metadata '_menu_item_layout_submenu' and convert it to the new format.
	 *
	 * @hooked trx_addons_action_is_new_version_of_plugin
	 * @hooked trx_addons_action_importer_import_end
	 * @hooked admin_init
	 * 
	 * @param string $new_version New version of the plugin.
	 * @param string $old_version Old version of the plugin.
	 */
	function trx_addons_cpt_layouts_submenu_convert_params( $new_version = '', $old_version = '' ) {
		$db_key    = 'cpt_layouts_submenu_convert_params';
		$db_limit  = trx_addons_db_update_get_limit();
		$db_offset = trx_addons_db_update_get_state( $db_key );
		if ( $db_offset === 0 ) {
			return;
		} else if ( $db_offset === false ) {
			$db_offset = 0;
		}
		if ( empty( $old_version ) ) {
			$old_version = get_option( 'trx_addons_version', '1.0' );
		}
		if ( version_compare( $old_version, '2.29.4', '<' ) || current_action() == 'trx_addons_action_importer_import_end' || $db_offset > 0 ) {
			global $wpdb;
			$rows = $wpdb->get_results( "SELECT post_id, meta_id, meta_value
											FROM {$wpdb->postmeta}
											WHERE meta_key='" . TRX_ADDONS_MENU_SETTINGS_META_KEY_OLD . "' && meta_value!=''
											ORDER BY meta_id ASC
											LIMIT {$db_offset}, {$db_limit}"
										);
			$total = is_array( $rows ) ? count( $rows ) : 0;
			if ( $total > 0 ) {
				foreach ( $rows as $row ) {
					$meta = get_post_meta( $row->post_id, TRX_ADDONS_MENU_SETTINGS_META_KEY, true );
					if ( ! is_array( $meta ) ) {
						$meta = array();
					}
					$meta['layout_submenu'] = $row->meta_value;
					update_post_meta( $row->post_id, TRX_ADDONS_MENU_SETTINGS_META_KEY, $meta );
				}
				if ( $total == $db_limit ) {
					$db_offset += $total;
					trx_addons_db_update_set_state( $db_key, $db_offset );
				} else {
					trx_addons_db_update_set_state( $db_key, 0 );
				}
			} else {
				trx_addons_db_update_set_state( $db_key, 0 );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_cpt_layouts_submenu_convert_params_2' ) ) {
	add_action( 'trx_addons_action_is_new_version_of_plugin', 'trx_addons_cpt_layouts_submenu_convert_params_2', 10, 2 );
	add_action( 'trx_addons_action_importer_import_end', 'trx_addons_cpt_layouts_submenu_convert_params_2' );
	add_action( 'admin_init', 'trx_addons_cpt_layouts_submenu_convert_params_2' );
	/**
	 * Convert old parameters of menu items (submenu layout id) to the new format (settings array)
	 * after update plugin ThemeREX Addons to the new version or after import demo data.
	 * Get all items with classes 'trx_addons_stretch_window%' or 'trx_addons_no_stretch' and convert its to the new format
	 *
	 * @hooked trx_addons_action_is_new_version_of_plugin
	 * @hooked trx_addons_action_importer_import_end
	 * @hooked admin_init
	 * 
	 * @param string $new_version New version of the plugin.
	 * @param string $old_version Old version of the plugin.
	 */
	function trx_addons_cpt_layouts_submenu_convert_params_2( $new_version = '', $old_version = '' ) {
		$db_key    = 'cpt_layouts_submenu_convert_params_2';
		$db_limit  = trx_addons_db_update_get_limit();
		$db_offset = trx_addons_db_update_get_state( $db_key );
		if ( $db_offset === 0 ) {
			return;
		} else if ( $db_offset === false ) {
			$db_offset = 0;
		}
		if ( empty( $old_version ) ) {
			$old_version = get_option( 'trx_addons_version', '1.0' );
		}
		if ( version_compare( $old_version, '2.29.4', '<' ) || current_action() == 'trx_addons_action_importer_import_end' || $db_offset > 0 ) {
			global $wpdb;
			$rows = $wpdb->get_results( "SELECT post_id, meta_id, meta_value
											FROM {$wpdb->postmeta}
											WHERE meta_key='_menu_item_classes' && ( meta_value LIKE '%trx_addons_stretch_%' OR meta_value LIKE '%trx_addons_no_stretch%' )
											ORDER BY meta_id ASC
											LIMIT {$db_offset}, {$db_limit}"
										);
			$total = is_array( $rows ) ? count( $rows ) : 0;
			if ( $total > 0 ) {
				foreach ( $rows as $row ) {
					$meta = get_post_meta( $row->post_id, TRX_ADDONS_MENU_SETTINGS_META_KEY, true );
					if ( ! is_array( $meta ) ) {
						$meta = array();
					}
					$classes = get_post_meta( $row->post_id, '_menu_item_classes', true );
					if ( is_array( $classes ) ) {
						foreach( $classes as $k => $class ) {
							if ( strpos( $class, 'trx_addons_stretch_' ) !== false || strpos( $class, 'trx_addons_no_stretch' ) !== false ) {
								$meta['submenu_fullwidth'] = str_replace( 'trx_addons_', '', $class );
								unset( $classes[$k] );
								break;
							}
						}
					}
					update_post_meta( $row->post_id, '_menu_item_classes', $classes );
					update_post_meta( $row->post_id, TRX_ADDONS_MENU_SETTINGS_META_KEY, $meta );
				}
				if ( $total == $db_limit ) {
					$db_offset += $total;
					trx_addons_db_update_set_state( $db_key, $db_offset );
				} else {
					trx_addons_db_update_set_state( $db_key, 0 );
				}
			} else {
				trx_addons_db_update_set_state( $db_key, 0 );
			}
		}
	}
}
