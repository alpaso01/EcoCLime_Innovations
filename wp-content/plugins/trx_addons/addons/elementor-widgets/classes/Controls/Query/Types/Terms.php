<?php
/**
 * Query Control Type: Terms
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Controls\Query\Types;

use TrxAddons\ElementorWidgets\BaseTypeModule;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Terms extends BaseTypeModule {

	/**
	 * Get the name of the module
	 *
	 * @return string  The name of the module.
	 */
	public function get_name() {
		return 'terms';
	}

	/**
	 * Get the title of the module
	 *
	 * @return string  The title of the module.
	 */
	public function get_title() {
		return __( 'Terms', 'trx_addons' );
	}

	/**
	 * Gets autocomplete values
	 *
	 * @return array  Autocomplete values.
	 */
	public function get_autocomplete_values( array $data ) {
		$results = array();

		$taxonomies = get_object_taxonomies('');

		$query_params = [
			'taxonomy' 		=> $taxonomies,
			'search' 		=> $data['q'],
			'hide_empty' 	=> false,
		];

		$terms = get_terms( $query_params );

		foreach ( $terms as $term ) {
			$taxonomy = get_taxonomy( $term->taxonomy );

			$results[] = [
				'id' 	=> $term->term_id,
				'text' 	=> $taxonomy->labels->singular_name . ': ' . $term->name,
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

		$query_params = array(
			'include' => $ids,
		);

		$terms = get_terms( $query_params );

		foreach ( $terms as $term ) {
			$results[ $term->term_id ] = $term->name;
		}

		return $results;
	}
}
