<?php
namespace TrxAddons\ElementorTemplates\Globals;

// use TrxAddons\ElementorTemplates\Options;
// use TrxAddons\ElementorTemplates\Utils as TrxAddonsUtils;

use Elementor\Core\Editor\Data\Globals\Endpoints\Base;

class Colors extends Base {

	var $color_prefix = 'theme_color_';		// Elementor is not support the defis in the global names
	var $scheme_prefix = 'trx_addons_global_colors_scheme_';

	public function get_name() {
		return 'colors';
	}

	public function get_format() {
		return 'globals/colors/{id}';
	}

	protected function get_kit_items() {
		$result     = array();
		$global_kit = \Elementor\Plugin::instance()->kits_manager->get_active_kit_for_frontend();

		$system_items = $global_kit->get_settings_for_display( 'system_colors' );
		$custom_items = $global_kit->get_settings_for_display( 'custom_colors' );

		if ( ! $system_items ) {
			$system_items = array();
		}

		if ( ! $custom_items ) {
			$custom_items = array();
		}

		$items = array_merge( $system_items, $custom_items );

		// Custom hack for getting the active kit on page.
		$kit = false;
		// $current_page_id = Options::instance()->get( 'current_page_id' );
		// if ( $current_page_id ) {
		// 	$kit = TrxAddonsUtils::get_document_kit( $current_page_id );
		// }

		// Fallback to global kit.
		if ( ! $kit ) {
			$kit = $global_kit;
		}

		// Add a theme-specific colors from the default scheme
		$schemes = trx_addons_get_theme_color_schemes();
		if ( ! empty( $schemes ) && is_array( $schemes ) ) {
			$default_scheme = trx_addons_get_theme_option( 'color_scheme', 'default' );
			if ( ! empty( $default_scheme ) && ! empty( $schemes[ $default_scheme ]['colors'] ) ) {
				$colors = $kit->get_settings_for_display( $this->scheme_prefix . $default_scheme );
				if ( ! $colors ) {
					$colors = array();
				}
				$items = array_merge( $items, $colors );
			}
		}

		foreach ( $items as $item ) {
			$id            = $item['_id'];
			$result[ $id ] = array(
				'id'    => $id,
				'title' => $item['title'] ?? '',
				'value' => $item['color'] ?? '',
			);
		}

		return $result;
	}

	protected function convert_db_format( $item ) {
		return array(
			'_id'   => $item['id'],
			'title' => $item['title'],
			'color' => $item['value'],
		);
	}
}
