<?php
/**
 * ThemeREX Addons: Bulk updates DB data after the plugin's update or demo-data import
 *
 * @package ThemeREX Addons
 * @since v2.32.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Limit the number of records to process in one step while DB update
if ( ! defined( 'TRX_ADDONS_DB_UPDATE_LIMIT' ) ) define( 'TRX_ADDONS_DB_UPDATE_LIMIT', 100 );

// Timeout to next DB update allowed (in seconds)
if ( ! defined( 'TRX_ADDONS_DB_UPDATE_TIMEOUT' ) ) define( 'TRX_ADDONS_DB_UPDATE_TIMEOUT', 600 );

// The number of records to process in one step while DB update
$GLOBALS['trx_addons_db_update_state'] = false;


if ( ! function_exists( 'trx_addons_db_update_get_state' ) ) {
	/**
	 * Get the current DB update state for the specified process
	 * 
	 * @param string $key - the key of the process
	 * 
	 * @return mixed - the current state of the process: false - not started, integer - the number of records processed ( if 0 - the process is finished )
	 */
	function trx_addons_db_update_get_state( $key ) {
		global $trx_addons_db_update_state;
		if ( $trx_addons_db_update_state === false ) {
			trx_addons_db_update_load_state();
		}
		return isset( $trx_addons_db_update_state[ $key ] ) ? $trx_addons_db_update_state[ $key ] : false;
	}
}

if ( ! function_exists( 'trx_addons_db_update_set_state' ) ) {
	/**
	 * Set the current DB update state for the specified process
	 */
	function trx_addons_db_update_set_state( $key, $value ) {
		global $trx_addons_db_update_state;
		if ( ! isset( $trx_addons_db_update_state[ $key ] ) || $trx_addons_db_update_state[ $key ] !== $value ) {
			$trx_addons_db_update_state[ $key ] = $value;
			trx_addons_db_update_save_state();
		}
	}
}

if ( ! function_exists( 'trx_addons_db_update_get_limit' ) ) {
	/**
	 * Get the DB update limit
	 * 
	 * @return int - the number of records to process in one step while DB update
	 */
	function trx_addons_db_update_get_limit() {
		return apply_filters( 'trx_addons_filter_db_update_limit', TRX_ADDONS_DB_UPDATE_LIMIT );
	}
}

if ( ! function_exists( 'trx_addons_db_update_load_state' ) ) {
	/**
	 * Load the current DB update state from DB - its array with separate entries for each update process
	 */
	function trx_addons_db_update_load_state() {
		global $trx_addons_db_update_state;
		if ( $trx_addons_db_update_state === false ) {
			$trx_addons_db_update_state = get_option( 'trx_addons_db_update_state', array() );
			if ( ! is_array( $trx_addons_db_update_state ) || empty( $trx_addons_db_update_state['_expired'] ) || $trx_addons_db_update_state['_expired'] < time() ) {
				$trx_addons_db_update_state = array();
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_db_update_save_state' ) ) {
	/**
	 * Save the current DB update state before the current PHP script ends
	 */
	function trx_addons_db_update_save_state() {
		global $trx_addons_db_update_state;
		$trx_addons_db_update_state['_expired'] = time() + TRX_ADDONS_DB_UPDATE_TIMEOUT;
		update_option( 'trx_addons_db_update_state', $trx_addons_db_update_state );
	}
}