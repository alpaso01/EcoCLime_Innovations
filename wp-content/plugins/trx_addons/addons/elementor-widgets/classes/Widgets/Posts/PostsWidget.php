<?php
/**
 * Posts Widget
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Widgets\Posts;

use TrxAddons\ElementorWidgets\BaseWidget;
use TrxAddons\ElementorWidgets\Utils as TrxAddonsUtils;

// Elementor Classes.
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Posts Widget
 */
class PostsWidget extends BaseWidget {

	protected $query         = null;
	protected $query_filters = null;

	protected $_has_template_content = false;

	/**
	 * Register Skins.
	 */
	protected function register_skins() {
		$this->add_skin( new Skins\SkinClassic( $this ) );
		// $this->add_skin( new Skins\SkinCard( $this ) );
		// $this->add_skin( new Skins\SkinCheckerboard( $this ) );
		// $this->add_skin( new Skins\SkinCreative( $this ) );
		// $this->add_skin( new Skins\SkinEvent( $this ) );
		// $this->add_skin( new Skins\SkinNews( $this ) );
		// $this->add_skin( new Skins\SkinOverlap( $this ) );
		// $this->add_skin( new Skins\SkinPortfolio( $this ) );
		// $this->add_skin( new Skins\SkinTemplate( $this ) );
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_skin_field',
			array(
				'label' => __( 'Layout', 'trx_addons' ),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'     => __( 'Posts Per Page', 'trx_addons' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 6,
				'condition' => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->end_controls_section();

		$this->register_query_section_controls( array(), 'posts', '', 'yes' );
	}


	/**
	 * Register posts grid widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	public function register_query_section_controls( $condition = array(), $widget_type = 'posts', $old_code = '', $advanced_controls = 'no' ) {

		/**
		 * Content Tab: Query
		 */
		$this->start_controls_section(
			'section_query',
			array(
				'label' => __( 'Query', 'trx_addons' ),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'query_type',
			array(
				'label'       => __( 'Query Type', 'trx_addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'default'     => 'custom',
				'options'     => array(
					'main'   => __( 'Main Query', 'trx_addons' ),
					'custom' => __( 'Custom Query', 'trx_addons' ),
				),
			)
		);

		$post_types            = TrxAddonsUtils::get_post_types();
		$post_types['related'] = __( 'Related', 'trx_addons' );

		$this->add_control(
			'post_type',
			array(
				'label'     => __( 'Post Type', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $post_types,
				'default'   => 'post',
				'condition' => array(
					'query_type' => 'custom',
				),

			)
		);

		foreach ( $post_types as $post_type_slug => $post_type_label ) {

			$taxonomy = TrxAddonsUtils::get_post_taxonomies( $post_type_slug );

			if ( ! empty( $taxonomy ) ) {

				foreach ( $taxonomy as $index => $tax ) {

					$terms = TrxAddonsUtils::get_tax_terms( $index );

					$tax_terms = array();

					if ( ! empty( $terms ) ) {

						foreach ( $terms as $term_index => $term_obj ) {

							$tax_terms[ $term_obj->term_id ] = $term_obj->name;
						}

						$tax_control_key = $index . '_' . $post_type_slug;

						if ( 'yes' === $old_code ) {
							if ( $post_type_slug == 'post' ) {
								if ( $index == 'post_tag' ) {
									$tax_control_key = 'tags';
								} elseif ( $index == 'category' ) {
									$tax_control_key = 'categories';
								}
							}
						}

						// Taxonomy filter type.
						$this->add_control(
							$index . '_' . $post_type_slug . '_filter_type',
							array(
								/* translators: %s Label */
								'label'       => sprintf( __( '%s Filter Type', 'trx_addons' ), $tax->label ),
								'label_block' => false,
								'type'        => Controls_Manager::SELECT,
								'default'     => 'IN',
								'options'     => array(
									/* translators: %s label */
									'IN'     => sprintf( __( 'Include %s', 'trx_addons' ), $tax->label ),
									/* translators: %s label */
									'NOT IN' => sprintf( __( 'Exclude %s', 'trx_addons' ), $tax->label ),
								),
								'separator'   => 'before',
								'condition'   => array(
									'query_type' => 'custom',
									'post_type'  => $post_type_slug,
								),
							)
						);

						$this->add_control(
							$tax_control_key,
							array(
								'label'        => $tax->label,
								'label_block'  => false,
								'type'         => 'trx-addons-query',
								'post_type'    => $post_type_slug,
								'options'      => array(),
								'multiple'     => true,
								'query_type'   => 'terms',
								'object_type'  => $index,
								'include_type' => true,
								'condition'    => array(
									'query_type' => 'custom',
									'post_type'  => $post_type_slug,
								),
							)
						);

					}
				}
			}
		}

		$this->add_control(
			'author_filter_type',
			array(
				'label'       => __( 'Authors Filter Type', 'trx_addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'default'     => 'author__in',
				'separator'   => 'before',
				'options'     => array(
					'author__in'     => __( 'Include Authors', 'trx_addons' ),
					'author__not_in' => __( 'Exclude Authors', 'trx_addons' ),
				),
				'condition'   => array(
					'query_type' => 'custom',
					'post_type!' => 'related',
				),
			)
		);

		$this->add_control(
			'authors',
			array(
				'label'       => __( 'Authors', 'trx_addons' ),
				'label_block' => false,
				'type'        => 'trx-addons-query',
				'multiple'    => true,
				'query_type'  => 'authors',
				'condition'   => array(
					'query_type' => 'custom',
					'post_type!' => 'related',
				),
			)
		);

		foreach ( $post_types as $post_type_slug => $post_type_label ) {
			$this->add_control(
				$post_type_slug . '_filter_type',
				array(
					/* translators: %s: post type label */
					'label'       => sprintf( __( '%s Filter Type', 'trx_addons' ), $post_type_label ),
					'label_block' => false,
					'type'        => Controls_Manager::SELECT,
					'default'     => 'post__not_in',
					'separator'   => 'before',
					'options'     => array(
						/* translators: %s: post type label */
						'post__in'     => sprintf( __( 'Include %s', 'trx_addons' ), $post_type_label ),
						/* translators: %s: post type label */
						'post__not_in' => sprintf( __( 'Exclude %s', 'trx_addons' ), $post_type_label ),
					),
					'condition'   => array(
						'query_type' => 'custom',
						'post_type'  => $post_type_slug,
					),
				)
			);

			$this->add_control(
				$post_type_slug . '_filter',
				array(
					/* translators: %s Label */
					'label'       => $post_type_label,
					'label_block' => false,
					'type'        => 'trx-addons-query',
					'default'     => '',
					'multiple'    => true,
					'query_type'  => 'posts',
					'object_type' => $post_type_slug,
					'condition'   => array(
						'query_type' => 'custom',
						'post_type'  => $post_type_slug,
					),
				)
			);
		}

		$taxonomy   = TrxAddonsUtils::get_post_taxonomies( $post_type_slug );
		$taxonomies = array();
		foreach ( $taxonomy as $index => $tax ) {
			$taxonomies[ $tax->name ] = $tax->label;
		}

		$this->start_controls_tabs(
			'tabs_related',
			array(
				'condition' => array(
					'query_type' => 'custom',
					'post_type'  => 'related',
				),
			)
		);

		$this->start_controls_tab(
			'tab_related_include',
			array(
				'label'     => __( 'Include', 'trx_addons' ),
				'condition' => array(
					'query_type' => 'custom',
					'post_type'  => 'related',
				),
			)
		);

		$this->add_control(
			'related_include_by',
			array(
				'label'       => __( 'Include By', 'trx_addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT2,
				'default'     => '',
				'multiple'    => true,
				'options'     => array(
					'terms'   => __( 'Term', 'trx_addons' ),
					'authors' => __( 'Author', 'trx_addons' ),
				),
				'condition'   => array(
					'query_type' => 'custom',
					'post_type'  => 'related',
				),
			)
		);

		$this->add_control(
			'related_filter_include',
			array(
				'label'       => __( 'Term', 'trx_addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT2,
				'default'     => '',
				'multiple'    => true,
				'options'     => TrxAddonsUtils::get_taxonomies_list(),
				'condition'   => array(
					'query_type'         => 'custom',
					'post_type'          => 'related',
					'related_include_by' => 'terms',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_related_exclude',
			array(
				'label'     => __( 'Exclude', 'trx_addons' ),
				'condition' => array(
					'query_type' => 'custom',
					'post_type'  => 'related',
				),
			)
		);

		$this->add_control(
			'related_exclude_by',
			array(
				'label'       => __( 'Exclude By', 'trx_addons' ),
				'type'        => Controls_Manager::SELECT2,
				'default'     => '',
				'label_block' => true,
				'multiple'    => true,
				'options'     => array(
					'current_post' => __( 'Current Post', 'trx_addons' ),
					'authors'      => __( 'Author', 'trx_addons' ),
				),
				'condition'   => array(
					'query_type' => 'custom',
					'post_type'  => 'related',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'related_fallback',
			array(
				'label'       => __( 'Fallback', 'trx_addons' ),
				'description' => __( 'Displayed if no relevant results are found.', 'trx_addons' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'none'   => __( 'None', 'trx_addons' ),
					'recent' => __( 'Recent Posts', 'trx_addons' ),
				),
				'default'     => 'none',
				'label_block' => false,
				'separator'   => 'before',
				'condition'   => array(
					'query_type' => 'custom',
					'post_type'  => 'related',
				),
			)
		);

		$this->add_control(
			'select_date',
			array(
				'label'       => __( 'Date', 'trx_addons' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'anytime' => __( 'All', 'trx_addons' ),
					'today'   => __( 'Past Day', 'trx_addons' ),
					'week'    => __( 'Past Week', 'trx_addons' ),
					'month'   => __( 'Past Month', 'trx_addons' ),
					'quarter' => __( 'Past Quarter', 'trx_addons' ),
					'year'    => __( 'Past Year', 'trx_addons' ),
					'exact'   => __( 'Custom', 'trx_addons' ),
				),
				'default'     => 'anytime',
				'label_block' => false,
				'multiple'    => false,
				'separator'   => 'before',
				'condition'   => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'date_before',
			array(
				'label'       => __( 'Before', 'trx_addons' ),
				'description' => __( 'Setting a ‘Before’ date will show all the posts published until the chosen date (inclusive).', 'trx_addons' ),
				'type'        => Controls_Manager::DATE_TIME,
				'label_block' => false,
				'multiple'    => false,
				'placeholder' => __( 'Choose', 'trx_addons' ),
				'condition'   => array(
					'query_type'  => 'custom',
					'select_date' => 'exact',
				),
			)
		);

		$this->add_control(
			'date_after',
			array(
				'label'       => __( 'After', 'trx_addons' ),
				'description' => __( 'Setting an ‘After’ date will show all the posts published since the chosen date (inclusive).', 'trx_addons' ),
				'type'        => Controls_Manager::DATE_TIME,
				'label_block' => false,
				'multiple'    => false,
				'placeholder' => __( 'Choose', 'trx_addons' ),
				'condition'   => array(
					'query_type'  => 'custom',
					'select_date' => 'exact',
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'     => __( 'Order', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'DESC' => __( 'Descending', 'trx_addons' ),
					'ASC'  => __( 'Ascending', 'trx_addons' ),
				),
				'default'   => 'DESC',
				'separator' => 'before',
				'condition' => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'     => __( 'Order By', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'date'          => __( 'Date', 'trx_addons' ),
					'modified'      => __( 'Last Modified Date', 'trx_addons' ),
					'rand'          => __( 'Random', 'trx_addons' ),
					'comment_count' => __( 'Comment Count', 'trx_addons' ),
					'title'         => __( 'Title', 'trx_addons' ),
					'ID'            => __( 'Post ID', 'trx_addons' ),
					'author'        => __( 'Post Author', 'trx_addons' ),
					'menu_order'    => __( 'Menu Order', 'trx_addons' ),
					'relevance'     => __( 'Relevance', 'trx_addons' ),
				),
				'default'   => 'date',
				'condition' => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'sticky_posts',
			array(
				'label'        => __( 'Sticky Posts', 'trx_addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'trx_addons' ),
				'label_off'    => __( 'No', 'trx_addons' ),
				'return_value' => 'yes',
				'separator'    => 'before',
				'condition'    => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'all_sticky_posts',
			array(
				'label'        => __( 'Show Only Sticky Posts', 'trx_addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'trx_addons' ),
				'label_off'    => __( 'No', 'trx_addons' ),
				'return_value' => 'yes',
				'condition'    => array(
					'query_type'   => 'custom',
					'sticky_posts' => 'yes',
				),
			)
		);

		$this->add_control(
			'offset',
			array(
				'label'       => __( 'Offset', 'trx_addons' ),
				'description' => __( 'Use this setting to skip this number of initial posts', 'trx_addons' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 0,
				'condition'   => array(
					'query_type' => 'custom',
					'post_type!' => 'related',
				),
			)
		);

		$this->add_control(
			'exclude_current',
			array(
				'label'        => __( 'Exclude Current Post', 'trx_addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'trx_addons' ),
				'label_off'    => __( 'No', 'trx_addons' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Enable this option to remove current post from the query.', 'trx_addons' ),
				'condition'    => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'avoid_duplicates',
			[
				'label'       => esc_html__( 'Avoid Duplicates', 'trx_addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => '',
				'description' => esc_html__( 'Set to Yes to avoid duplicate posts from showing up on the page. This only affects the frontend.', 'trx_addons' ),
				'condition'   => array(
					'query_type' => 'custom',
				),
			]
		);

		$this->add_control(
			'query_id',
			array(
				'label'       => __( 'Query ID', 'trx_addons' ),
				'description' => __( 'Give your Query a custom unique id to allow server side filtering', 'trx_addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'ai'          => [
					'active' => false,
				],
				'separator'   => 'before',
			)
		);

		if ( 'yes' === $advanced_controls ) {
			$this->add_control(
				'heading_nothing_found',
				array(
					'label'     => __( 'If Nothing Found!', 'trx_addons' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'nothing_found_message',
				array(
					'label'   => __( 'Nothing Found Message', 'trx_addons' ),
					'type'    => Controls_Manager::TEXTAREA,
					'rows'    => 3,
					'default' => __( 'It seems we can\'t find what you\'re looking for.', 'trx_addons' ),
				)
			);

			$this->add_control(
				'show_search_form',
				array(
					'label'        => __( 'Show Search Form', 'trx_addons' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'trx_addons' ),
					'label_off'    => __( 'No', 'trx_addons' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Get post query arguments.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	public function query_posts_args( $filter = '', $taxonomy_filter = '', $search = '', $all_posts = '', $paged_args = '', $widget_type = 'posts', $old_code = '', $posts_count_var = '', $posts_count = '' ) {
		$settings  = $this->get_settings_for_display();
		$paged     = ( 'yes' === $paged_args ) ? $this->get_paged() : '';
		$tax_count = 0;
		$post__not_in = array();

		if ( 'main' === $settings['query_type'] ) {
			$current_query_vars = $GLOBALS['wp_query']->query_vars;
			return apply_filters( "ppe_{$widget_type}_query_args", $current_query_vars, $settings );
		}

		$query_args = array(
			'post_status'         => array( 'publish' ),
			'orderby'             => $settings['orderby'],
			'order'               => $settings['order'],
			'ignore_sticky_posts' => ( 'yes' === $settings['sticky_posts'] ) ? 0 : 1,
			'posts_per_page'      => -1,
		);

		if ( ! $posts_count ) {
			$posts_per_page = ( $posts_count_var ) ? $settings[ $posts_count_var ] : ( isset( $settings['posts_per_page'] ) ? $settings['posts_per_page'] : '' );
		} else {
			$posts_per_page = $posts_count;
		}

		if ( '' === $all_posts ) {
			$query_args['posts_per_page'] = $posts_per_page;
		}

		if ( 'related' === $settings['post_type'] ) {

			$related_terms = $settings['related_filter_include'];
			$post_terms    = wp_get_object_terms( get_the_ID(), $settings['related_filter_include'], array( 'fields' => 'ids' ) );

			// Query Arguments.
			$query_args['post_type'] = get_post_type();

			if ( ! empty( $settings['related_include_by'] ) ) {
				if ( in_array( 'authors', $settings['related_include_by'], true ) ) {
					$query_args['author'] = get_the_author_meta( 'ID' );
				}

				if ( in_array( 'terms', $settings['related_include_by'], true ) ) {
					if ( ! empty( $related_terms ) && ! is_wp_error( $related_terms ) ) {

						foreach ( $related_terms as $index => $tax ) {

							$query_args['tax_query'][] = array(
								'taxonomy' => $tax,
								'field'    => 'term_id',
								'terms'    => $post_terms,
							);

						}
					}
				}
			}

			if ( ! empty( $settings['related_exclude_by'] ) ) {
				if ( in_array( 'current_post', $settings['related_exclude_by'], true ) ) {
					$post__not_in = array( get_the_ID() );
				}

				if ( in_array( 'authors', $settings['related_exclude_by'], true ) ) {
					$query_args['author'] = '-' . get_the_author_meta( 'ID' );
				}
			}

			if ( 'recent' === $settings['related_fallback'] ) {
				$query = $this->get_query();

				if ( ! $query->found_posts ) {
					$query_args = array(
						'post_status'         => array( 'publish' ),
						'post_type'           => get_post_type(),
						'orderby'             => $settings['orderby'],
						'order'               => $settings['order'],
						'ignore_sticky_posts' => ( 'yes' === $settings['sticky_posts'] ) ? 0 : 1,
						'showposts'           => $posts_per_page,
					);
				}
			}
		} else {

			// Query Arguments.
			$query_args['post_type'] = $settings['post_type'];
			if ( 0 < $settings['offset'] ) {

				/**
				 * Offset break the pagination. Using WordPress's work around
				 *
				 * @see https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
				 */
				$query_args['offset_to_fix'] = $settings['offset'];
			}
			$query_args['paged'] = $paged;

			// Author Filter.
			if ( ! empty( $settings['authors'] ) ) {
				$query_args[ $settings['author_filter_type'] ] = $settings['authors'];
			}

			// Posts Filter.
			$post_type = $settings['post_type'];

			if ( ! empty( $settings[ $post_type . '_filter' ] ) ) {
				if ( 'post__not_in' === $settings[ $post_type . '_filter_type' ] ) {
					$post__not_in = $settings[ $post_type . '_filter' ];
				} else {
					$query_args[ $settings[ $post_type . '_filter_type' ] ] = $settings[ $post_type . '_filter' ];
				}
			}

			// Taxonomy Filter.
			$taxonomy = TrxAddonsUtils::get_post_taxonomies( $post_type );

			$tax_cat_in     = '';
			$tax_cat_not_in = '';
			$tax_tag_in     = '';
			$tax_tag_not_in = '';

			if ( ! empty( $taxonomy ) && ! is_wp_error( $taxonomy ) ) {

				foreach ( $taxonomy as $index => $tax ) {

					$tax_control_key = $index . '_' . $post_type;

					if ( 'yes' === $old_code ) {
						if ( 'post' === $post_type ) {
							if ( 'post_tag' === $index ) {
								$tax_control_key = 'tags';
							} elseif ( 'category' === $index ) {
								$tax_control_key = 'categories';
							}
						}
					}

					if ( ! empty( $settings[ $tax_control_key ] ) ) {

						$operator = $settings[ $index . '_' . $post_type . '_filter_type' ];

						$query_args['tax_query'][] = array(
							'taxonomy' => $index,
							'field'    => 'term_id',
							'terms'    => $settings[ $tax_control_key ],
							'operator' => $operator,
						);

						switch ( $index ) {
							case 'category':
								if ( 'IN' === $operator ) {
									$tax_cat_in = $settings[ $tax_control_key ];
								} elseif ( 'NOT IN' === $operator ) {
									$tax_cat_not_in = $settings[ $tax_control_key ];
								}
								break;

							case 'post_tag':
								if ( 'IN' === $operator ) {
									$tax_tag_in = $settings[ $tax_control_key ];
								} elseif ( 'NOT IN' === $operator ) {
									$tax_tag_not_in = $settings[ $tax_control_key ];
								}
								break;
						}
					}
				}
			}

			if ( '' !== $filter && '*' !== $filter ) {
				// Taxonomy Filter.
				$taxonomy = TrxAddonsUtils::get_post_taxonomies( $post_type );

				$tax_cat_in     = '';
				$tax_cat_not_in = '';
				$tax_tag_in     = '';
				$tax_tag_not_in = '';

				if ( ! empty( $taxonomy ) && ! is_wp_error( $taxonomy ) ) {

					foreach ( $taxonomy as $index => $tax ) {

						$tax_control_key = $index . '_' . $post_type;

						if ( 'yes' === $old_code ) {
							if ( 'post' === $post_type ) {
								if ( 'post_tag' === $index ) {
									$tax_control_key = 'tags';
								} elseif ( 'category' === $index ) {
									$tax_control_key = 'categories';
								}
							}
						}

						if ( ! empty( $settings[ $tax_control_key ] ) ) {

							$operator = $settings[ $index . '_' . $post_type . '_filter_type' ];

							$query_args['tax_query'][] = array(
								'taxonomy' => $index,
								'field'    => 'term_id',
								'terms'    => $settings[ $tax_control_key ],
								'operator' => $operator,
							);

							switch ( $index ) {
								case 'category':
									if ( 'IN' === $operator ) {
										$tax_cat_in = $settings[ $tax_control_key ];
									} elseif ( 'NOT IN' === $operator ) {
										$tax_cat_not_in = $settings[ $tax_control_key ];
									}
									break;

								case 'post_tag':
									if ( 'IN' === $operator ) {
										$tax_tag_in = $settings[ $tax_control_key ];
									} elseif ( 'NOT IN' === $operator ) {
										$tax_tag_not_in = $settings[ $tax_control_key ];
									}
									break;
							}
						}
					}
				}

				$query_args['tax_query'][ $tax_count ]['taxonomy'] = $taxonomy_filter;
				$query_args['tax_query'][ $tax_count ]['field']    = 'slug';
				$query_args['tax_query'][ $tax_count ]['terms']    = $filter;
				$query_args['tax_query'][ $tax_count ]['operator'] = 'IN';

				/*
				if ( ! empty( $tax_cat_in ) ) {
					$query_args['category__in'] = $tax_cat_in;
				}

				if ( ! empty( $tax_cat_not_in ) ) {
					$query_args['category__not_in'] = $tax_cat_not_in;
				}

				if ( ! empty( $tax_tag_in ) ) {
					$query_args['tag__in'] = $tax_tag_in;
				}

				if ( ! empty( $tax_tag_not_in ) ) {
					$query_args['tag__not_in'] = $tax_tag_not_in;
				}
				*/
			}

			if ( '' !== $search ) {
				$query_args['s'] = $search;
			}
		}

		if ( 'anytime' !== $settings['select_date'] ) {
			$select_date = $settings['select_date'];
			if ( ! empty( $select_date ) ) {
				$date_query = array();
				if ( 'today' === $select_date ) {
					$date_query['after'] = '-1 day';
				} elseif ( 'week' === $select_date ) {
					$date_query['after'] = '-1 week';
				} elseif ( 'month' === $select_date ) {
					$date_query['after'] = '-1 month';
				} elseif ( 'quarter' === $select_date ) {
					$date_query['after'] = '-3 month';
				} elseif ( 'year' === $select_date ) {
					$date_query['after'] = '-1 year';
				} elseif ( 'exact' === $select_date ) {
					$after_date = $settings['date_after'];
					if ( ! empty( $after_date ) ) {
						$date_query['after'] = $after_date;
					}
					$before_date = $settings['date_before'];
					if ( ! empty( $before_date ) ) {
						$date_query['before'] = $before_date;
					}
					$date_query['inclusive'] = true;
				}

				$query_args['date_query'] = $date_query;
			}
		}

		// Sticky Posts Filter.
		if ( 'yes' === $settings['sticky_posts'] && 'yes' === $settings['all_sticky_posts'] ) {
			$post__in = get_option( 'sticky_posts' );

			$query_args['ignore_sticky_posts'] = 1;
			$query_args['post__in'] = $post__in;
		}

		// Exclude current post.
		if ( 'yes' === $settings['exclude_current'] ) {
			if ( is_singular() ) {
				$query_args['post__not_in'] = array( get_queried_object_id() );
			}
		}

		if ( 'yes' === $settings['avoid_duplicates'] ) {
			$post__not_in = array_merge( $post__not_in, Posts::$displayed_ids );
		}

		if ( ! empty( $post__not_in ) ) {
			$query_args['post__not_in'] = $post__not_in;
		}

		return apply_filters( "ppe_{$widget_type}_query_args", $query_args, $settings );
	}

	/**
	 * pre_get_posts_query_filter
	 *
	 * @param  mixed $wp_query
	 */
	public function pre_get_posts_query_filter( $wp_query ) {
		$settings = $this->get_settings_for_display();

		$query_id = $settings['query_id'];
		/**
		 * Query args.
		 *
		 * It allows developers to alter individual posts widget queries.
		 *
		 * The dynamic portion of the hook name '$query_id', refers to the Query ID.
		 *
		 * @param \WP_Query     $wp_query
		 */
		do_action( "trx_addons_filter_elementor_widgets_posts_query_{$query_id}", $wp_query );

	}

	public function query_posts( $filter = '', $taxonomy = '', $search = '', $all_posts = '', $paged_args = '', $widget_type = 'posts', $old_code = '', $posts_count_var = '', $posts_count = '' ) {
		$settings = $this->get_settings_for_display();
		$query_id = $settings['query_id'];

		if ( ! empty( $query_id ) ) {
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts_query_filter' ) );
		}
		$query_args = $this->query_posts_args( $filter, $taxonomy, $search, '', 'yes', $widget_type, $old_code, $posts_count_var, $posts_count );

		$post_type = $settings['post_type'];
		$offset_control = $settings['offset'];

		if ( 'related' !== $post_type && 0 < $offset_control ) {
			add_action( 'pre_get_posts', [ $this, 'fix_query_offset' ], 1 );
			add_filter( 'found_posts', [ $this, 'fix_query_found_posts' ], 1, 2 );
		}

		$this->query = new \WP_Query( $query_args );

		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts_query_filter' ) );
		remove_action( 'pre_get_posts', [ $this, 'fix_query_offset' ], 1 );
		remove_filter( 'found_posts', [ $this, 'fix_query_found_posts' ], 1 );

		Posts::add_to_avoid_list( wp_list_pluck( $this->query->posts, 'ID' ) );
	}

	public function query_filters_posts( $filter = '', $taxonomy = '', $search = '' ) {
		$settings = $this->get_settings();
		$query_id = $settings['query_id'];

		if ( ! empty( $query_id ) ) {
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts_query_filter' ) );
		}
		$query_filter_args   = $this->query_posts_args( $filter, $taxonomy, $search, 'yes', 'yes' );
		$this->query_filters = new \WP_Query( $query_filter_args );
		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts_query_filter' ) );
	}

	/**
	 * Get a current query.
	 * 
	 * @return object  WP_Query
	 */
	public function get_query() {
		return $this->query;
	}

	/**
	 * Get a current query filters.
	 * 
	 * @return object  WP_Query filters
	 */
	public function get_query_filters() {
		return $this->query_filters;
	}

	/**
	 * Returns the paged number for the query.
	 * 
	 * @return int
	 */
	public function get_paged() {
		$settings = $this->get_settings_for_display();

		global $wp_the_query, $paged;

		if ( isset( $settings['_skin'] ) ) {
			$skin_id         = $settings['_skin'];
			$pagination_ajax = $settings[ $skin_id . '_pagination_ajax' ];
			$pagination_type = $settings[ $skin_id . '_pagination_type' ];
		} else {
			$pagination_ajax = '';
			$pagination_type = '';
		}

		if ( 'yes' === $pagination_ajax || 'load_more' === $pagination_type || 'infinite' === $pagination_type ) {
			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'trx-addons-posts-widget-nonce' ) ) {
				if ( isset( $_POST['page_number'] ) && '' !== $_POST['page_number'] ) {
					return $_POST['page_number'];
				}
			}

			// Check the 'paged' query var.
			$paged_qv = $wp_the_query->get( 'paged' );

			if ( is_numeric( $paged_qv ) ) {
				return $paged_qv;
			}

			// Check the 'page' query var.
			$page_qv = $wp_the_query->get( 'page' );

			if ( is_numeric( $page_qv ) ) {
				return $page_qv;
			}

			// Check the $paged global?
			if ( is_numeric( $paged ) ) {
				return $paged;
			}

			return 0;
		} else {
			return max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
		}
	}

	public function get_posts_nav_link( $page_limit = null ) {
		if ( ! $page_limit ) {
			$page_limit = $this->query->max_num_pages;
		}

		$return = array();

		$paged = $this->get_paged();

		$link_template     = '<a class="page-numbers %s" href="%s">%s</a>';
		$disabled_template = '<span class="page-numbers %s">%s</span>';

		if ( $paged > 1 ) {
			$next_page = intval( $paged ) - 1;
			if ( $next_page < 1 ) {
				$next_page = 1;
			}

			$return['prev'] = sprintf( $link_template, 'prev', $this->get_wp_link_page( $next_page ), $this->get_settings( 'pagination_prev_label' ) );
		} else {
			$return['prev'] = sprintf( $disabled_template, 'prev', $this->get_settings( 'pagination_prev_label' ) );
		}

		$next_page = intval( $paged ) + 1;

		if ( $next_page <= $page_limit ) {
			$return['next'] = sprintf( $link_template, 'next', $this->get_wp_link_page( $next_page ), $this->get_settings( 'pagination_next_label' ) );
		} else {
			$return['next'] = sprintf( $disabled_template, 'next', $this->get_settings( 'pagination_next_label' ) );
		}

		return $return;
	}

	private function get_wp_link_page( $i ) {
		if ( ! is_singular() || is_front_page() ) {
			return get_pagenum_link( $i );
		}

		// Based on wp-includes/post-template.php:957 `_wp_link_page`.
		global $wp_rewrite;
		$post       = get_post();
		$query_args = array();
		$url        = get_permalink();

		if ( $i > 1 ) {
			if ( '' === get_option( 'permalink_structure' ) || in_array( $post->post_status, array( 'draft', 'pending' ), true ) ) {
				$url = add_query_arg( 'page', $i, $url );
			} elseif ( get_option( 'show_on_front' ) === 'page' && (int) get_option( 'page_on_front' ) === $post->ID ) {
				$url = trailingslashit( $url ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . $i, 'single_paged' );
			} else {
				$url = trailingslashit( $url ) . user_trailingslashit( $i, 'single_paged' );
			}
		}

		if ( is_preview() ) {
			if ( ( 'draft' !== $post->post_status ) && isset( $_GET['preview_id'], $_GET['preview_nonce'] ) ) { //phpcs:ignore
				$query_args['preview_id']    = wp_unslash( $_GET['preview_id'] ); //phpcs:ignore
				$query_args['preview_nonce'] = wp_unslash( $_GET['preview_nonce'] ); //phpcs:ignore
			}

			$url = get_preview_post_link( $post, $query_args, $url );
		}

		return $url;
	}

	/**
	 * Fix query offset.
	 * 
	 * @param \WP_Query $query
	 */
	public function fix_query_offset( &$query ) {
		$settings = $this->get_settings_for_display();
		$offset = $settings['offset'];

		if ( $offset && $query->is_paged ) {
			$query->query_vars['offset'] = $offset + ( ( $query->query_vars['paged'] - 1 ) * $query->query_vars['posts_per_page'] );
		} else {
			$query->query_vars['offset'] = $offset;
		}
	}

	/**
	 * Fix query found posts.
	 * 
	 * @param int       $found_posts
	 * @param \WP_Query $query
	 *
	 * @return int
	 */
	public function fix_query_found_posts( $found_posts, $query ) {
		$settings = $this->get_settings_for_display();
		$offset = $settings['offset'];

		if ( $offset ) {
			$found_posts -= $offset;
		}

		return $found_posts;
	}
}
