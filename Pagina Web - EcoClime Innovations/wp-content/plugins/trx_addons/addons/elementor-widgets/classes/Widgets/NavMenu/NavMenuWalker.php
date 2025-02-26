<?php
/**
 * NavMenu Walker
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Widgets\NavMenu;

use TrxAddons\ElementorWidgets\Utils as TrxAddonsUtils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class NavMenuWalker
 */
class NavMenuWalker extends \Walker_Nav_Menu {

	/**
	 * Menu Settings.
	 *
	 * @var settings
	 */
	private $settings = null;

	/**
	 * Is mobile menu flag.
	 *
	 * @var is_mobile_menu
	 */
	private $is_mobile_menu = null;


	/**
	 * Class Constructor.
	 *
	 * @param array $widget_settings    widget settings.
	 * @param bool  $is_mobile_menu     is toggle menu flag.
	 */
	public function __construct( $widget_settings, $is_mobile_menu = false ) {
		$this->settings = $widget_settings;
		$this->is_mobile_menu = $is_mobile_menu;
	}

	/**
	 * Get Item Postmeta data.
	 *
	 * @param int|string $item_id  menu item id.
	 *
	 * @return object  item meta data.
	 */
	public function get_item_postmeta( $item_id ) {

		$defauls = array(
			// 'item_id'        => '',
			// 'item_depth'     => '',
			'icon'              => '',
			'icon_color'        => '',
			'icon_hover'        => '',
			'icon_type'         => 'icon',	// icon | lottie
			'lottie_url'        => '',
			'badge_text'        => '',
			'badge_color'       => '',
			'badge_bg_color'    => '',
			'badge_font_size'   => '',
			'badge_font_bold'   => '',
			'badge_offset_x'    => '',
			'badge_offset_y'    => '',
			'layout_submenu'    => '',
			'submenu_pos'       => 'default',
			'submenu_width'     => '',
			'submenu_fullwidth' => '',
		);

		// $item_meta = array_merge( $defauls, (array) json_decode( get_post_meta( $item_id, 'trx_addons_nav_menu_item_data', true ) ) );
		$item_meta = array_merge( $defauls, (array)get_post_meta( $item_id, 'trx_addons_nav_menu_item_data', true ) );

		return (object) $item_meta;
	}

	/**
	 * Get Mega Content ID.
	 * 
	 * Retrieves mega content id from postmeta table.
	 *
	 * @param string $item_id   menu item id.
	 *
	 * @return string  mega content id.
	 */
	public function get_mega_content_id( $item_id ) {
		return get_post_meta( $item_id, 'trx_addons_nav_menu_item_content', true );
	}

	/**
	 * Get default submenu icon.
	 *
	 * @param string $layout   main menu layout.
	 *
	 * @return string  
	 */
	public function get_default_submenu_icon( $depth ) {

		// Don't show default submenu icons if user has disabled them (select 'None')
		return '';

		// toggle menu icon.
		if ( $this->is_mobile_menu ) {
			return 'fas fa-angle-down';
		}

		$icon   = 'fas fa-angle-right';
		$layout = $this->settings['nav_menu_layout'];

		switch ( $layout ) {
			case 'hor':
				if ( $depth === 0 ) {
					$icon = 'fas fa-angle-down';
				} else if ( is_rtl() ) {
					$icon = 'fas fa-angle-left';
				}
				break;

			case 'slide':
			case 'dropdown':
				$icon = 'fas fa-angle-down';
				break;

			case 'ver':
				$icon = 'fas fa-angle-' . $this->settings['nav_ver_submenu'];
				break;
		}

		return $icon;
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @since 3.0.0
	 *
	 * @see Walker::start_lvl()
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}

		$indent = str_repeat( $t, $depth );

		$classes = array( 'trx-addons-submenu' );

		/**
		 * Filters the CSS class(es) applied to a menu list element.
		 *
		 * @since 4.8.0
		 *
		 * @param string[] $classes Array of the CSS classes that are applied to the menu `<ul>` element.
		 * @param stdClass $args    An object of `wp_nav_menu()` arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 */
		$class_names = implode( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$output .= "{$n}{$indent}<ul$class_names>{$n}";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @since 3.0.0
	 *
	 * @see Walker::end_lvl()
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent  = str_repeat( $t, $depth );
		$output .= "$indent</ul>{$n}";
	}

	/**
	 * Starts the element output.
	 *
	 * @since 3.0.0
	 * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
	 * @since 5.9.0 Renamed `$item` to `$data_object` and `$id` to `$current_object_id`
	 *              to match parent class for PHP 8 named parameter support.
	 *
	 * @see Walker::start_el()
	 *
	 * @param string   $output            Used to append additional content (passed by reference).
	 * @param WP_Post  $data_object       Menu item data object.
	 * @param int      $depth             Depth of menu item. Used for padding.
	 * @param stdClass $args              An object of wp_nav_menu() arguments.
	 * @param int      $current_object_id Optional. ID of the current menu item. Default 0.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = array(), $current_object_id = 0 ) {

		$settings = $this->settings;

		// Restores the more descriptive, specific name for use within this method.
		$menu_item = $data_object;

		if ( is_null( $menu_item ) ) {
			return;
		}

		$item_meta = $this->get_item_postmeta( $menu_item->ID );

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}

		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

		$classes = empty( $menu_item->classes ) ? array() : (array) $menu_item->classes; // has default classes.

		$classes[] = 'trx-addons-nav-menu-item'; // add our own class too.

		if ( 0 < $depth ) {
			$classes[] = 'trx-addons-submenu-item';
		}

		if ( (int)$item_meta->layout_submenu > 0 ) {
			$classes[] = 'trx-addons-mega-nav-item menu-item-has-children';

			if ( ! empty( $item_meta->submenu_pos ) && $item_meta->submenu_pos == 'static' ) {
				$classes[] = 'trx-addons-mega-item-static';
			}
		}

		// we can later add other classes here based on the user settings.
		if ( in_array( 'current-menu-item', $classes, true ) ) {
			$classes[] = 'trx-addons-active-item';
		}

		// Add badge marker.
		if ( ! empty( $item_meta->badge_text ) ) {
			$classes[] = 'has-trx-addons-badge';

			// check for sub item badge effects.
			$badge_effect = $settings['sub_badge_hv_effects'];

			if ( 0 < $depth && '' !== $badge_effect ) {
				$classes[] = 'trx-addons-badge-' . $badge_effect;
			}
		}

		/**
		 * Filters the arguments for a single nav menu item.
		 *
		 * @since 4.4.0
		 *
		 * @param stdClass $args      An object of wp_nav_menu() arguments.
		 * @param WP_Post  $menu_item Menu item data object.
		 * @param int      $depth     Depth of menu item. Used for padding.
		 */
		$args = apply_filters( 'nav_menu_item_args', $args, $menu_item, $depth ); // default fitler.

		/**
		 * Filters the CSS classes applied to a menu item's list item element.
		 *
		 * @since 3.0.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param string[] $classes   Array of the CSS classes that are applied to the menu item's `<li>` element.
		 * @param WP_Post  $menu_item The current menu item object.
		 * @param stdClass $args      An object of wp_nav_menu() arguments.
		 * @param int      $depth     Depth of menu item. Used for padding.
		 */
		$class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $menu_item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filters the ID applied to a menu item's list item element.
		 *
		 * @since 3.0.1
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param string   $menu_id   The ID that is applied to the menu item's `<li>` element.
		 * @param WP_Post  $menu_item The current menu item.
		 * @param stdClass $args      An object of wp_nav_menu() arguments.
		 * @param int      $depth     Depth of menu item. Used for padding.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'trx-addons-nav-menu-item-' . $menu_item->ID, $menu_item, $args, $depth ); // change the default id.
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$full_width = (int)$item_meta->submenu_fullwidth > 0 ? ' data-full-width="true"' : '';

		$output .= $indent . '<li' . $id . $class_names . $full_width . '>';
		// link attributes.
		$atts           = array();
		$atts['title']  = ! empty( $menu_item->attr_title ) ? $menu_item->attr_title : '';
		$atts['target'] = ! empty( $menu_item->target ) ? $menu_item->target : '';

		if ( '_blank' === $menu_item->target && empty( $menu_item->xfn ) ) {

			$atts['rel'] = 'noopener';
		} else {

			$atts['rel'] = $menu_item->xfn;
		}

		$atts['href']         = ! empty( $menu_item->url ) ? $menu_item->url : '';
		$atts['aria-current'] = $menu_item->current ? 'page' : '';

		/**
		 * Page-Transition Experiment Fix.
		 * add elementor's custom attribute to Toggle menu links
		 * && if the element has sub|mega menu.
		 */
		$is_parent = in_array( 'menu-item-has-children', $classes, true ) || (int)$item_meta->layout_submenu > 0;
		$is_toggle = in_array( $settings['nav_menu_layout'], array( 'dropdown', 'slide' ), true ) || wp_is_mobile();

		if ( $is_toggle && $is_parent ) {
			$atts['data-e-disable-page-transition'] = 'true';
		}

		/**
		 * Filters the HTML attributes applied to a menu item's anchor element.
		 *
		 * @since 3.6.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 *     @type string $title        Title attribute.
		 *     @type string $target       Target attribute.
		 *     @type string $rel          The rel attribute.
		 *     @type string $href         The href attribute.
		 *     @type string $aria-current The aria-current attribute.
		 * }
		 * @param WP_Post  $menu_item The current menu item object.
		 * @param stdClass $args      An object of wp_nav_menu() arguments.
		 * @param int      $depth     Depth of menu item. Used for padding.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $menu_item, $args, $depth );

		// add our own class.
		if ( empty( $atts['class'] ) ) {
			$atts['class'] = 'trx-addons-menu-link';
		} else {
			$atts['class'] .= ' trx-addons-menu-link';
		}

		if ( 0 == $depth ) {
			$atts['class'] .= ' trx-addons-menu-link-parent';
		}

		$dropdown_icon = '';
		$item_icon     = '';
		$item_badge    = '';
		$icon_class    = 0 < $depth ? ' trx-addons-sub-item-icon' : ' trx-addons-item-icon';
		$badge_class   = 0 < $depth ? 'trx-addons-sub-item-badge' : 'trx-addons-item-badge';

		// add dropdown icon.
		if ( in_array( 'menu-item-has-children', $classes, true ) || (int)$item_meta->layout_submenu > 0 ) {
			$dropdown_icon = TrxAddonsUtils::render_icon(
								$settings[ 0 === $depth ? 'submenu_icon' : 'submenu_item_icon' ],
								'trx-addons-dropdown-icon trx-addons-icon',
								$this->get_default_submenu_icon( $depth )
							);
		}

		// add item icon.
		if ( 'icon' === $item_meta->icon_type && ! empty( $item_meta->icon ) ) {
			$item_icon = TrxAddonsUtils::render_icon( $item_meta->icon,
														$icon_class . ' trx-addons-icon'
														. ( ! empty( $item_meta->icon_color ) ? ' ' . trx_addons_add_inline_css_class( 'color:' . $item_meta->icon_color ) : '' )
														. ( ! empty( $item_meta->icon_hover ) ? ' ' . trx_addons_add_inline_css_class( 'color:' . $item_meta->icon_hover, '', '.trx-addons-menu-link:hover > ' ) : '' )
													);
		} elseif ( 'lottie' === $item_meta->icon_type && ! empty( $item_meta->lottie_url ) ) {
			$item_icon = sprintf( '<div class="%1$s" data-lottie-url="%2$s" data-lottie-loop="true"></div>', $icon_class . ' trx-addons-lottie-animation', $item_meta->lottie_url );
		}

		// add item badge.
		if ( ! empty( $item_meta->badge_text ) ) {
			$css = '';
			if ( ! empty( $item_meta->badge_color ) ) {
				$css .= 'color:' . $item_meta->badge_color . ';';
			}
			if ( ! empty( $item_meta->badge_bg_color ) ) {
				$css .= 'background-color:' . $item_meta->badge_bg_color . ';';
			}
			if ( ! empty( $item_meta->badge_font_size ) && (int)$item_meta->badge_font_size > 0 ) {
				$css .= 'font-size:' . $item_meta->badge_font_size . 'px;';
			}
			if ( ! empty( $item_meta->badge_font_bold ) && (int)$item_meta->badge_font_bold > 0 ) {
				$css .= 'font-weight:bold;';
			}
			if ( ! empty( $item_meta->badge_offset_x ) && (int)$item_meta->badge_offset_x != 0 ) {
				$css .= 'margin-left:' . $item_meta->badge_offset_x . 'px;';
			}
			if ( ! empty( $item_meta->badge_offset_y ) && (int)$item_meta->badge_offset_y != 0 ) {
				$css .= 'margin-top:' . $item_meta->badge_offset_y . 'px;';
			}
			if ( ! empty( $css ) ) {
				$badge_class .= ' ' . trx_addons_add_inline_css_class( $css );
			}
			$item_badge = sprintf( '<span class="%1$s">%2$s</span>', $badge_class, $item_meta->badge_text );
		}

		if ( 0 < $depth ) {
			$atts['class'] .= ' trx-addons-submenu-link';
		}

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $menu_item->title, $menu_item->ID );

		/**
		 * Filters a menu item's title.
		 *
		 * @since 4.4.0
		 *
		 * @param string   $title     The menu item's title.
		 * @param WP_Post  $menu_item The current menu item object.
		 * @param stdClass $args      An object of wp_nav_menu() arguments.
		 * @param int      $depth     Depth of menu item. Used for padding.
		 */
		$title = apply_filters( 'nav_menu_item_title', $title, $menu_item, $args, $depth );

		$item_output  = $args->before;
		$item_output .= '<a' . $attributes . '>';
		$item_output .= '<span class="trx-addons-menu-link-text' . ( 0 < $depth ? ' trx-addons-submenu-link-text' : '' ) . '">' . $args->link_before . $item_icon . $title . $item_badge . $dropdown_icon . $args->link_after . '</span>';
		$item_output .= '</a>';
		$item_output .= $args->after;

		/**
		 * Filters a menu item's starting output.
		 *
		 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
		 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
		 * no filter for modifying the opening and closing `<li>` for a menu item.
		 *
		 * @since 3.0.0
		 *
		 * @param string   $item_output The menu item's starting HTML output.
		 * @param WP_Post  $menu_item   Menu item data object.
		 * @param int      $depth       Depth of menu item. Used for padding.
		 * @param stdClass $args        An object of wp_nav_menu() arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $menu_item, $depth, $args );
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @since 3.0.0
	 * @since 5.9.0 Renamed `$item` to `$data_object` to match parent class for PHP 8 named parameter support.
	 *
	 * @see Walker::end_el()
	 *
	 * @param string   $output      Used to append additional content (passed by reference).
	 * @param WP_Post  $data_object Menu item data object. Not used.
	 * @param int      $depth       Depth of page. Not Used.
	 * @param stdClass $args        An object of wp_nav_menu() arguments.
	 */
	public function end_el( &$output, $data_object, $depth = 0, $args = null ) {

		/**
		 * Add mega content to main nav menu items only.
		 * Disabled for now. Mega content is added in the filter 'walker_nav_menu_start_el' in the file 'layouts-submenu.php'
		 */
		if ( false && 0 === $depth ) {

			$item_meta = $this->get_item_postmeta( $data_object->ID );

			if ( (int)$item_meta->layout_submenu > 0 && class_exists( 'Elementor\Plugin' ) ) {

				$template_id = $this->get_mega_content_id( $data_object->ID );
				$content     = TrxAddonsUtils::get_template_content( $template_id, true );
				$style       = 'width:' . $item_meta->submenu_width;
				$output     .= sprintf( '<div id="trx-addons-mega-content-%1$s" class="trx-addons-mega-content-container" style="%2$s">%3$s</div>', $data_object->ID, $style, $content );
			}
		}

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}

		$output .= "</li>{$n}";
	}
}
