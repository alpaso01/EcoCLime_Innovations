<?php
/**
 * Query Control Type: Posts
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Controls\Query\Types;

use TrxAddons\ElementorWidgets\BaseTypeModule;
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
class Posts extends BaseTypeModule {

	/**
	 * Get the name of the module
	 *
	 * @return string  The name of the module.
	 */
	public function get_name() {
		return 'posts';
	}

	/**
	 * Get the title of the module
	 *
	 * @return string  The title of the module.
	 */
	public function get_title() {
		return __( 'Posts', 'trx_addons' );
	}

	/**
	 * Gets autocomplete values
	 *
	 * @since  1.2.9
	 * @return array
	 */
	public function get_autocomplete_values( array $data ) {
		$results = array();

		$query_params = [
			'post_type' 		=> $data['object_type'],
			's' 				=> $data['q'],
			'posts_per_page' 	=> -1,
		];

		if ( 'attachment' === $query_params['post_type'] ) {
			$query_params['post_status'] = 'inherit';
		}

		$query = new \WP_Query( $query_params );

		foreach ( $query->posts as $post ) {
			$results[] = [
				'id' 	=> $post->ID,
				'text' 	=> $post->post_title,
			];
		}

		return $results;
	}

	/**
	 * Gets control values titles
	 *
	 * @since  1.2.9
	 * @return array
	 */
	public function get_value_titles( array $request ) {
		$ids = (array) $request['id'];
		$results = array();

		$query = new \WP_Query( [
			'post_type' 		=> 'any',
			'post__in' 			=> $ids,
			'posts_per_page' 	=> -1,
		] );

		foreach ( $query->posts as $post ) {
			$results[ $post->ID ] = $post->post_title;
		}

		return $results;
	}
}
