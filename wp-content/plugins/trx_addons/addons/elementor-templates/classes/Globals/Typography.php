<?php
namespace TrxAddons\ElementorTemplates\Globals;

// use TrxAddons\ElementorTemplates\Options;
// use TrxAddons\ElementorTemplates\Utils as TrxAddonsUtils;

use Elementor\Core\Editor\Data\Globals\Endpoints\Base;

class Typography extends Base {

	// Elementor is not support the defis in the global names
	var $theme_fonts_setting = 'trx_addons_global_theme_fonts';		// Name of theme fonts setting in the Globals
	var $font_prefix = 'theme_font_';								// Prefix for the each font settings

	public function get_name() {
		return 'typography';
	}

	public function get_format() {
		return 'globals/typography/{id}';
	}

	protected function get_kit_items() {
		$result = array();
		$global_kit = \Elementor\Plugin::instance()->kits_manager->get_active_kit_for_frontend();

		// Use raw settings that doesn't have default values.
		$kit_raw_settings = $global_kit->get_data( 'settings' );

		if ( isset( $kit_raw_settings['system_typography'] ) ) {
			$system_items = $kit_raw_settings['system_typography'];
		} else {
			// Get default items, but without empty defaults.
			$control      = $global_kit->get_controls( 'system_typography' );
			$system_items = $control['default'];
		}

		$custom_items = $global_kit->get_settings( 'custom_typography' );

		if ( ! $custom_items ) {
			$custom_items = array();
		}

		$items = array_merge( $system_items, $custom_items );

		// Custom hack for getting the active kit on page.
		$kit = false;
		// $current_page_id = Options::get_instance()->get( 'current_page_id' );
		// if ( $current_page_id ) {
		// 	$kit = TrxAddonsUtils::get_document_kit( $current_page_id );
		// }

		// Fallback to global kit.
		if ( ! $kit ) {
			$kit = $global_kit;
		}

		// Add a theme-specific fonts
		//$fonts = trx_addons_get_theme_fonts();
		$fonts = $kit->get_settings_for_display( $this->theme_fonts_setting );

		if ( ! $fonts ) {
			$fonts = array();
		}

		// Filter for empty font presets.
		$filtered_fonts = array();
		foreach ( $fonts as $font ) {
			if ( ( isset( $font ) && isset( $font['typography_typography'] ) ) && 'custom' === $font['typography_typography'] ) {
				$filtered_fonts[] = $font;
			}
		}
		$items = array_merge( $items, $filtered_fonts );

		foreach ( $items as $index => &$item ) {
			foreach ( $item as $setting => $value ) {
				$new_setting = str_replace( 'styles_', '', $setting, $count );
				if ( $count ) {
					$item[ $new_setting ] = $value;
					unset( $item[ $setting ] );
				}
			}

			$id = $item['_id'];

			$result[ $id ] = array(
				'id'    => $id,
				'title' => $item['title'] ?? '',
			);

			unset( $item['_id'], $item['title'] );

			$result[ $id ]['value'] = $item;
		}

		return $result;
	}

	protected function convert_db_format( $item ) {
		$db_format = array(
			'_id'   => $item['id'],
			'title' => $item['title'],
		);

		$db_format = array_merge( $item['value'], $db_format );

		return $db_format;
	}
}
