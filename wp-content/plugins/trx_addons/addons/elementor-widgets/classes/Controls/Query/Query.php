<?php
/**
 * Query Control Module
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Controls\Query;

use TrxAddons\ElementorWidgets\BaseControlModule;
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
 
/**
 * Query Control module
 */
class Query extends BaseControlModule {
 
	/**
	 * Module constructor.
	 */
	public function __construct() {
		parent::__construct();

		// ACF 5+
		// if ( class_exists( '\acf' ) && function_exists( 'acf_get_field_groups' ) ) {
		// 	$this->add_component( 'acf', new Types\Acf() );
		// }

		// Pods
		// if ( function_exists( 'pods' ) ) {
		// 	$this->add_component( 'pods', new Types\Pods() );
		// }

		// Toolset
		// if ( function_exists( 'wpcf_admin_fields_get_groups' ) ) {
		// 	$this->add_component( 'toolset', new Types\Toolset() );
		// }

		// Other components
		$this->add_component( 'posts', new Types\Posts() );
		$this->add_component( 'terms', new Types\Terms() );
		$this->add_component( 'authors', new Types\Authors() );
		$this->add_component( 'users', new Types\Users() );
		// $this->add_component( 'templates', new Types\Templates() );
		// $this->add_component( 'templates-all', new Types\TemplatesAll() );
		$this->add_component( 'templates-page', new Types\TemplatesPage() );
		$this->add_component( 'templates-section', new Types\TemplatesSection() );
		// $this->add_component( 'templates-widget', new Types\TemplatesWidget() );

		$this->add_actions();
	}

	/**
	 * Get the name of the module
	 *
	 * @return string  The name of the module.
	 */
	public function get_name() {
		return 'query-control';
	}

	/**
	 * Get the type of the control
	 *
	 * @return string  The type of the control: control | group
	 */
	public function get_type() {
		return 'control';
	}

	/**
	 * Registeres actions to Elementor hooks
	 */
	protected function add_actions() {
		add_action( 'elementor/ajax/register_actions', [ $this, 'register_ajax_actions' ] );
	}

	/**
	 * Calls function depending on ajax query data
	 *
	 * @return array  The results of the query.
	 */
	public function ajax_call_filter_autocomplete( array $data ) {
		if ( empty( $data['query_type'] ) || empty( $data['q'] ) ) {
			throw new \Exception( 'Bad Request' );
		}
		return array(
			'results' => $this->get_component( $data['query_type'] )->get_autocomplete_values( $data )
		);
	}

	/**
	 * Calls function to get value titles depending on ajax query type
	 * 
	 * @return array  The results of the query.
	 */
	public function ajax_call_control_value_titles( array $request ) {
		return $this->get_component( $request['query_type'] )->get_value_titles( $request );
	}

	/**
	 * Register Elementor Ajax Actions
	 */
	public function register_ajax_actions( $ajax_manager ) {
		$ajax_manager->register_ajax_action( 'trx_addons_query_control_value_titles', [ $this, 'ajax_call_control_value_titles' ] );
		$ajax_manager->register_ajax_action( 'trx_addons_query_control_filter_autocomplete', [ $this, 'ajax_call_filter_autocomplete' ] );
	}
}
