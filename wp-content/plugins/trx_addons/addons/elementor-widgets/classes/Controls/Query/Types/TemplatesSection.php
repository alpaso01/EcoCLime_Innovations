<?php
/**
 * Query Control Type: Elementor Templates - Section
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Controls\Query\Types;

use TrxAddons\ElementorWidgets\BaseTypeModule;

// Elementor Classes
use Elementor\TemplateLibrary\Source_Local;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TemplatesSection extends BaseTypeModule {

	/**
	 * Get Name
	 *
	 * Get the name of the module
	 *
	 * @return string  The name of the module.
	 */
	public function get_name() {
		return 'templates-section';
	}

	/**
	 * Get the title of the module
	 *
	 * @return string  The title of the module.
	 */
	public function get_title() {
		return __( 'Section Templates', 'trx_addons' );
	}

	/**
	 * Gets autocomplete values
	 *
	 * @return array  Autocomplete values.
	 */
	public function get_autocomplete_values( array $data ) {
		$results = array();

		// $document_types = \Elementor\Plugin::instance()->documents->get_document_types( [
		// 	'show_in_library' => true,
		// ] );

		$query_params = [
			's'                 => $data['q'],
			'post_type'         => Source_Local::CPT,
			'posts_per_page'    => -1,
			'tax_query' => [
				[
					'taxonomy'  => Source_Local::TAXONOMY_TYPE_SLUG,
					'field'     => 'slug',
					'terms'     => [ 'section', 'container' ],
				],
			],
		];

		$query = new \WP_Query( $query_params );

		foreach ( $query->posts as $post ) {
			$document = \Elementor\Plugin::instance()->documents->get( $post->ID );
			if ( ! $document ) {
				continue;
			}
			$results[] = [
				'id'    => $post->ID,
				'text'  => $post->post_title . ' (' . ucfirst( $document->get_name() ) . ')',
			];
		}

		return $results;
	}

	/**
	 * Gets control values titles
	 *
	 * @return array  Control values titles.
	 */
	public function get_value_titles( array $request ) {
		$ids = (array) $request['id'];

		$query = new \WP_Query( [
			'post_type'         => Source_Local::CPT,
			'post__in'          => $ids,
			'posts_per_page'    => -1,
		]);

		foreach ( $query->posts as $post ) {
			$document = \Elementor\Plugin::instance()->documents->get( $post->ID );
			if ( ! $document ) {
				continue;
			}
			$results[ $post->ID ] = $post->post_title . ' (' . $document->get_post_type_title() . ')';
		}

		return $results;
	}
}
