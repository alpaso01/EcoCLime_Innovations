<?php
/**
 * Plugin support: QuickCal (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_quickcal_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_quickcal_set_options' );
	/**
	 * Set plugin's specific importer options
	 *
	 * @hooked trx_addons_filter_ocdi_options
	 *
	 * @param array $ocdi_options		Importer options
	 *
	 * @return array					Modified options
	 */
	function trx_addons_ocdi_quickcal_set_options( $ocdi_options ) {
		$ocdi_options['import_quickcal_file_url'] = 'quickcal.txt';
		return $ocdi_options;		
	}
}

if ( ! function_exists( 'trx_addons_ocdi_quickcal_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_quickcal_export' );
	/**
	 * Export QuickCal Calendar data to the file via OCDI
	 *
	 * @hooked trx_addons_filter_ocdi_export_files
	 *
	 * @param array $output		HTML output with the list of files to export
	 *
	 * @return array			Modified output
	 */
	function trx_addons_ocdi_quickcal_export( $output ) {
		$list = array();
		if ( trx_addons_exists_quickcal() && in_array( 'quickcal', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Get plugin data from database
			$options = array('quickcal_%', 'booked_%');
			$list = trx_addons_ocdi_export_options( $options, $list );

			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/quickcal.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );

			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__('QuickCal Calendar', 'trx_addons') . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_quickcal_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_quickcal_import_field' );
	/**
	 * Add checkbox to the one-click importer to allow import QuickCal Calendar data
	 *
	 * @hooked trx_addons_filter_ocdi_import_fields
	 *
	 * @param string $output		HTML output with the list of checkboxes
	 *
	 * @return string				Modified output
	 */
	function trx_addons_ocdi_quickcal_import_field( $output ){
		$list = array();
		if ( trx_addons_exists_quickcal() && in_array( 'quickcal', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="quickcal" value="quickcal">' . esc_html__( 'QuickCal Calendar', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_quickcal_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_quickcal_import', 10, 1 );
	/**
	 * Import QuickCal Calendar data from the file
	 *
	 * @hooked trx_addons_action_ocdi_import_plugins
	 *
	 * @param array $import_plugins		List of plugins to import
	 */
	function trx_addons_ocdi_quickcal_import( $import_plugins){
		if ( trx_addons_exists_quickcal() && in_array( 'quickcal', $import_plugins ) ) {
			trx_addons_ocdi_import_dump('quickcal');
			echo esc_html__('QuickCal Calendar import complete.', 'trx_addons') . "\r\n";
		}
	}
}
