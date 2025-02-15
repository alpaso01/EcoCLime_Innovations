<?php
/**
 * Plugin support: SportsPress
 *
 * @package ThemeREX Addons
 * @since v2.25.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! defined( 'TRX_ADDONS_CPT_SPORTPRESS_TEAM_PT' ) ) define( 'TRX_ADDONS_CPT_SPORTPRESS_TEAM_PT', 'sp_team' );
if ( ! defined( 'TRX_ADDONS_CPT_SPORTPRESS_PLAYER_PT' ) ) define( 'TRX_ADDONS_CPT_SPORTPRESS_PLAYER_PT', 'sp_player' );
if ( ! defined( 'TRX_ADDONS_CPT_SPORTPRESS_STAFF_PT' ) ) define( 'TRX_ADDONS_CPT_SPORTPRESS_STAFF_PT', 'sp_staff' );
if ( ! defined( 'TRX_ADDONS_CPT_SPORTPRESS_POSITION_TAXONOMY' ) ) define( 'TRX_ADDONS_CPT_SPORTPRESS_POSITION_TAXONOMY', 'sp_position' );

if ( ! function_exists( 'trx_addons_exists_sportspress' ) ) {
	/**
	 * Check if SportsPress plugin is installed and activated
	 *
	 * @return bool  True if plugin is installed and activated
	 */
	function trx_addons_exists_sportspress() {
		return class_exists( 'SportsPress' );
	}
}


// Add 'Team', 'Player' and 'Staff' to the team-compatible post types
if ( !function_exists( 'trx_addons_sportpress_add_cpt_to_team_list' ) ) {
	add_filter( 'trx_addons_filter_get_list_team_posts_types', 'trx_addons_sportpress_add_cpt_to_team_list' );
	function trx_addons_sportpress_add_cpt_to_team_list( $list ) {
		return array_merge( $list, array(
				TRX_ADDONS_CPT_SPORTPRESS_TEAM_PT => __( 'SportsPress Team', 'trx_addons' ),
				TRX_ADDONS_CPT_SPORTPRESS_PLAYER_PT => __( 'SportsPress Player', 'trx_addons' ),
				TRX_ADDONS_CPT_SPORTPRESS_STAFF_PT => __( 'SportsPress Staff', 'trx_addons' )
		) );
	}
}


// Add a team name and a staff position to the post subtitle for the team-compatible post types
if ( !function_exists( 'trx_addons_sportpress_add_team_and_staff_to_meta' ) ) {
	add_filter( 'trx_addons_filter_sc_team_meta', 'trx_addons_sportpress_add_team_and_staff_to_meta' );
	function trx_addons_sportpress_add_team_and_staff_to_meta( $meta ) {
		if ( trx_addons_exists_sportspress() ) {
			if ( in_array( get_post_type(), array( TRX_ADDONS_CPT_SPORTPRESS_PLAYER_PT, TRX_ADDONS_CPT_SPORTPRESS_STAFF_PT ) ) ) {
				$team = get_post_meta( get_the_ID(), 'sp_team', true );
				if ( ! empty( $team ) ) {
					$meta['subtitle'] = '<span class="sc_team_meta_team">' . get_the_title( $team ) . '</span>';
				}
				$position = trx_addons_get_post_terms( ', ', get_the_ID(), TRX_ADDONS_CPT_SPORTPRESS_POSITION_TAXONOMY );
				if ( ! empty( $position ) ) {
					$meta['subtitle'] .= ( empty( $meta['subtitle'] ) ? '' : '<span class="sc_team_meta_delimiter">, </span>' )
									. '<span class="sc_team_meta_position">' . $position . '</span>';
				}
			}
		}
		return $meta;
	}
}

// Return a 'sp_team' as a parent post type for the 'sp_player' and 'sp_staff' post types
if ( !function_exists( 'trx_addons_sportspress_parent_post_type' ) ) {
	add_filter('trx_addons_filter_parent_post_type', 'trx_addons_sportspress_parent_post_type', 10, 2);
	function trx_addons_sportspress_parent_post_type( $ppt, $post_type ) {
		if ( trx_addons_exists_sportspress() && in_array( $post_type, array( TRX_ADDONS_CPT_SPORTPRESS_PLAYER_PT, TRX_ADDONS_CPT_SPORTPRESS_STAFF_PT ) ) ) {
			$ppt = TRX_ADDONS_CPT_SPORTPRESS_TEAM_PT;
		}
		return $ppt;
	}
}

// Return a meta key name 'sp_team' as a parent post meta key for the 'sp_player' and 'sp_staff' post types
if ( !function_exists( 'trx_addons_sportspress_parent_post_meta_key' ) ) {
	add_filter('trx_addons_filter_parent_post_meta_key', 'trx_addons_sportspress_parent_post_meta_key', 10, 2);
	function trx_addons_sportspress_parent_post_meta_key( $key, $post_type ) {
		if ( trx_addons_exists_sportspress() && in_array( $post_type, array( TRX_ADDONS_CPT_SPORTPRESS_PLAYER_PT, TRX_ADDONS_CPT_SPORTPRESS_STAFF_PT ) ) ) {
			$key = 'sp_team';	// 'sp_current_team' ?
		}
		return $key;
	}
}



// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'sportspress/sportspress-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_sportspress() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'sportspress/sportspress-demo-ocdi.php';
}
