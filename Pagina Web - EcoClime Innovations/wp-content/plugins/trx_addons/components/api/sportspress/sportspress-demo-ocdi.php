<?php
/**
 * Plugin support: SportsPress (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v2.25.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_sportspress_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_sportspress_set_options' );
	/**
	 * Set plugin's specific importer options for OCDI
	 *
	 * @hooked trx_addons_filter_ocdi_options
	 *
	 * @param array $ocdi_options  OCDI options
	 *
	 * @return array               Modified options
	 */
	function trx_addons_ocdi_sportspress_set_options( $ocdi_options ) {
		$ocdi_options['import_sportspress_file_url'] = 'sportspress.txt';
		return $ocdi_options;		
	}
}

if ( ! function_exists( 'trx_addons_ocdi_sportspress_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_sportspress_export' );
	/**
	 * Export sportspress data to the file
	 *
	 * @hooked trx_addons_filter_ocdi_export_files
	 *
	 * @param string $output  Export data
	 *
	 * @return string         Modified output
	 */
	function trx_addons_ocdi_sportspress_export( $output ) {
		$list = array();
		if ( trx_addons_exists_sportspress() && in_array( 'sportspress', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Get plugin data from database
			$options = array('sportspress%');
			$list = trx_addons_ocdi_export_options( $options, $list );
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/sportspress.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );
			
			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__( 'SportsPress', 'trx_addons' ) . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_sportspress_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_sportspress_import_field' );
	/**
	 * Add checkbox to the one-click importer to allow import sportspress data
	 *
	 * @hooked trx_addons_filter_ocdi_import_fields
	 *
	 * @param string $output  Import fields HTML output
	 *
	 * @return string         Modified output
	 */
	function trx_addons_ocdi_sportspress_import_field( $output ) {
		if ( trx_addons_exists_sportspress() && in_array( 'sportspress', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="sportspress" value="sportspress">' . esc_html__( 'SportsPress', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_sportspress_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_sportspress_import', 10, 1 );
	/**
	 * Import sportspress data from the file
	 *
	 * @param array $import_plugins  List of plugins to import
	 */
	function trx_addons_ocdi_sportspress_import( $import_plugins ) {
		if ( trx_addons_exists_sportspress() && in_array( 'sportspress', $import_plugins ) ) {
			trx_addons_ocdi_import_dump('sportspress');
			echo esc_html__('SportsPress import complete.', 'trx_addons') . "\r\n";
		}
	}
}
