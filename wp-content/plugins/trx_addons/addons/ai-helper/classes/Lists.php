<?php
namespace TrxAddons\AiHelper;

if ( ! class_exists( 'Lists' ) ) {

	/**
	 * Return arrays with the lists used in the addon
	 */
	class Lists {

		use ListsTextGeneration;
		use ListsImageGeneration;
		use ListsMusicGeneration;
		use ListsAudioGeneration;
		use ListsVideoGeneration;

		/**
		 * Return a list of generation periods (used for the limits)
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of generation periods
		 */
		static function get_list_periods() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_periods', array(
				'hour'  => __( 'Hour', 'trx_addons' ),
				'day'   => __( 'Day', 'trx_addons' ),
				'week'  => __( 'Week', 'trx_addons' ),
				'month' => __( 'Month', 'trx_addons' ),
				'year'  => __( 'Year', 'trx_addons' ),
			) );
		}

	}
}
