<?php
/**
 * Post Meta Widget
 *
 * @package ThemeREX Addons
 * @since v2.30.2
 */

namespace TrxAddons\ElementorWidgets\Widgets\PostMeta;

use TrxAddons\ElementorWidgets\BaseWidget;
use TrxAddons\ElementorWidgets\Utils as TrxAddonsUtils;

// Elementor Classes.
use Elementor\Controls_Manager;
use \Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Post Meta Widget
 */
class PostMetaWidget extends BaseWidget {

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_post_meta_controls();

		/* Style Tab */
		$this->register_style_post_meta_controls();
	}

	/**
	 * Register post_meta controls
	 *
	 * @return void
	 */
	protected function register_content_post_meta_controls() {

		$this->start_controls_section(
			'section_post_meta',
			[
				'label' => __( 'Post Meta', 'trx_addons' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'type',
			[
				'label' => __( 'Meta', 'trx_addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => trx_addons_get_list_meta_parts( false, true )	// Get own list of meta parts (not from the theme options) and add WooCommerce parts
			]
		);

		$repeater->add_control(
			'type_custom',
			[
				'label' => __( 'Custom Field Name', 'trx_addons' ),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'type' => [ 'custom_meta', 'custom_taxonomy' ]
				]
			]
		);

		$repeater->add_control(
			'html_custom',
			[
				'label' => __( 'Custom HTML', 'trx_addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'condition' => [
					'type' => [ 'custom_html' ]
				]
			]
		);

		$repeater->add_control(
			'date_format',
			[
				'label' => esc_html__( 'Date Format', 'trx_addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'   => esc_html__( 'Default', 'trx_addons' ),
					'F j, Y'    => gmdate( 'F j, Y' ),
					'Y-m-d'     => gmdate( 'Y-m-d' ),
					'm/d/Y'     => gmdate( 'm/d/Y' ),
					'd.m.Y'     => gmdate( 'd.m.Y' ),
					'custom'    => esc_html__( 'Custom', 'trx_addons' ),
				],
				'condition' => [
					'type' => ['date', 'modified']
				]
			]
		);

		$repeater->add_control(
			'custom_format',
			[
				'label'     => esc_html__( 'Custom Format', 'trx_addons' ),
				'default'   => get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
				'description' => sprintf( '<a href="https://wordpress.org/documentation/article/customize-date-and-time-format/" target="_blank">%s</a>', esc_html__( 'Documentation on date and time formatting', 'trx_addons' ) ),
				'condition' => [
					'date_format' => 'custom',
					'type'        => ['date', 'modified']
				],
			]
		);

		$repeater->add_control(
			'before',
			[
				'label' => __( 'Text Before', 'trx_addons' ),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'type!' => 'none',
				]
			]
		);

		$repeater->add_control(
			'after',
			[
				'label' => __( 'Text After', 'trx_addons' ),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'type!' => 'none',
				]
			]
		);

		$repeater->add_control(
			'autor_display',
			[
				'label' => __( 'Display Type', 'trx_addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'name'   => __( 'Name', 'trx_addons' ),
					'full'   => __( 'Avatar & Name', 'trx_addons' ),
					'avatar' => __( 'Avatar', 'trx_addons' ),
				],
				'condition' => [
					'type' => 'author',
				],
			]
		);

		$repeater->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'trx_addons' ),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'type!' => 'none',
					'autor_display' => 'name',
				],
			]
		);

		$this->add_control(
			'meta_list',
			[
				'label' => esc_html__( 'Meta Data', 'trx_addons' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'type' => 'author'
					],
				],
				'title_field' => '<span style="text-transform: capitalize">{{{ type }}}</span>',
				'prevent_empty' => false,
			]
		);

		$this->end_controls_section();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	STYLE TAB
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Register post_meta style controls
	 *
	 * @return void
	 */
	protected function register_style_post_meta_controls() {
		$this->start_controls_section( 
			'section_post_meta_style',
			[
				'label' => esc_html__( 'Post Meta', 'trx_addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'trx_addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => trx_addons_get_list_sc_flex_aligns_for_elementor(),
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-post-meta' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_margin',
			[
				'label'      => esc_html__( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => [
					'{{WRAPPER}} .trx-addons-post-meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'meta_typography',
				'selector' => '{{WRAPPER}} .trx-addons-post-meta',
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label' => __( 'Text Color', 'trx_addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .trx-addons-post-meta' => 'color: {{VALUE}}',
					'{{WRAPPER}} .trx-addons-post-meta-item svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', 'trx_addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .trx-addons-post-meta-item-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .trx-addons-post-meta-item svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'link_color',
			[
				'label' => __( 'Link Color', 'trx_addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .trx-addons-post-meta-item a' => 'color: {{VALUE}}',

				],
			]
		);

		$this->add_control(
			'link_hover',
			[
				'label' => __( 'Link Hover', 'trx_addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .trx-addons-post-meta-item a:hover' => 'color: {{VALUE}}',

				],
			]
		);

		$this->add_control(
			'meta_bg_color',
			[
				'label' => __( 'Background Color', 'trx_addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .trx-addons-post-meta-item' => 'background-color: {{VALUE}}',

				],
			]
		);

		$this->add_responsive_control(
			'meta_padding',
			[
				'label'      => esc_html__( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => [
					'{{WRAPPER}} .trx-addons-post-meta-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'meta_radius',
			[
				'label'       => esc_html__( 'Border Radius', 'trx_addons' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'   => [
					'{{WRAPPER}} .trx-addons-post-meta-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'meta_shadow',
				'label' => esc_html__( 'Box Shadow', 'trx_addons' ),
				'selector' => '{{WRAPPER}} .trx-addons-post-meta-item',
			]
		);

		$this->add_responsive_control(
			'meta_gap',
			[
				'label'      => __( 'Items Gap', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 30,
					],
					'em' => [
						'min' => 0,
						'max' => 3,
						'step' => 0.1,
					],
					'rem' => [
						'min' => 0,
						'max' => 3,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .trx-addons-post-meta' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'meta_separator',
			[
				'label' => __( 'Separator', 'trx_addons' ),
				'type' => Controls_Manager::TEXT,
				'selectors' => [
					'{{WRAPPER}} .trx-addons-post-meta-item-separator:before' => "content: '{{VALUE}}';",
				],
			]
		);

		$this->end_controls_section();
	}


	/*-----------------------------------------------------------------------------------*/
	/*	RENDER
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Render a widget output on the frontend.
	 *
	 * @access protected
	 */
	protected function render() {
		$meta_list = $this->get_settings_for_display( 'meta_list' );
        if ( ! isset( $meta_list[0] ) || $meta_list[0]['type'] == '' ) {
            return;
        }
		$this->add_render_attribute( 'meta', 'class', 'trx-addons-post-meta' );
		?><div <?php $this->print_render_attribute_string( 'meta' ) ?>><?php
			$cnt = 0;
			foreach ( $meta_list as $meta ) {
				if ( $meta['type'] != 'none' ) {
					$output = $this->render_meta_item( $meta );
					if ( ! empty( $output ) ) {
						if ( $cnt > 0 ) {
							echo '<span class="trx-addons-post-meta-item-separator"></span>';
						}
						?><span class="trx-addons-post-meta-item trx-addons-post-meta-item-type-<?php echo esc_attr( $meta['type'] ); ?>"><?php
							trx_addons_show_layout( $output );
						?></span><?php
						$cnt++;
					}
				}
			}
		?></div><?php
		trx_addons_sc_layouts_showed( 'post_meta', true );
	}

	/**
	 * Render a separate meta item output on the frontend.
	 *
	 * @access protected
	 */
	protected function render_meta_item( $meta ) {
		$output = '';

		if ( ! empty(  $meta['type'] ) && $meta['type'] !== 'none' ) {

			ob_start();
			Icons_Manager::render_icon( $meta['icon'], array( 'aria-hidden' => 'true', 'class'=>'trx-addons-post-meta-item-icon' ), 'span' );
			$output = ob_get_contents();
			ob_end_clean();

			if ( ! empty( $meta['before'] ) ) {
				$output .= '<span class="trx-addons-post-meta-item-before">' . esc_html( $meta['before'] ) . '</span>';
			}

			if ( $meta['type'] === 'author' ) {
				$output .= $this->get_meta_author( $meta['autor_display'] );
			} else if ( $meta['type'] === 'date' ) {
				$output .= TrxAddonsUtils::format_date( get_the_date('U'), $meta['date_format'], $meta['custom_format'] );          // 'U' param returns the date in timestamp, necessary for format_date()
			} else if ( $meta['type'] === 'modified') {
				$output .= TrxAddonsUtils::format_date( get_the_modified_date('U'), $meta['date_format'], $meta['custom_format'] ); // 'U' param returns the date in timestamp, necessary for format_date()
			} else if ( $meta['type'] === 'comments' ) {
				$output .= esc_html( get_comments_number() );
			} else if ( $meta['type'] === 'views' ) {
				$output .= esc_html( trx_addons_get_post_views() + 1 );
			} else if ( $meta['type'] === 'likes' ) {
				if ( trx_addons_is_on( trx_addons_get_option( 'emotions_allowed', 0 ) ) ) {
					$post_emotions = trx_addons_get_post_emotions();
					$post_likes = 0;
					if ( is_array( $post_emotions ) ) {
						foreach ( $post_emotions as $v ) {
							$post_likes += (int)$v;
						}
					}
				} else {
					$post_likes = trx_addons_get_post_likes();
				}
				$output .= esc_html( $post_likes );
			} else if ( $meta['type'] === 'reading_time' ) {
				$output .= esc_html( trx_addons_get_post_reading_time() );
			} else if ( $meta['type'] === 'categories' ) {
				$output .= trx_addons_get_post_categories();
			} else if ( $meta['type'] === 'custom_taxonomy' ) {
				if ( ! empty( $meta['type_custom'] ) ) {
					$output .= trx_addons_get_post_terms( ', ', get_the_ID(), $meta['type_custom'] );
				}
			} else if ( $meta['type'] === 'custom_meta' ) {
				if ( ! empty( $meta['type_custom'] ) ) {
					$output .= esc_html( get_post_meta( get_the_ID(), $meta['type_custom'], true ) );
				}
			} else if ( $meta['type'] === 'custom_html' ) {
				if ( ! empty( $meta['html_custom'] ) ) {
					$output .= wp_kses_post( $meta['html_custom'] );
				}
			} else if ( strpos( $meta['type'], 'product_' ) === 0 ) {
				$output .= wp_kses_post( $this->get_meta_woocommerce( $meta['type'] ) );
			} else {
				$output .= apply_filters( 'trx_addobs_filter_get_meta_item_value', '', $meta['type'] );
			}

			if ( $meta['after'] ) {
				$output .= '<span class="trx-addons-post-meta-item-after">' . esc_html( $meta['after'] ) . '</span>';
			}
		}

		return $output;
	}

	/**
	 * Get the post author avatar and/or name.
	 * 
	 * @param string $mode  The display mode.
	 * 
	 * @return string  The author avatar and/or name.
	 */
	function get_meta_author( $mode ) {
		global $post;
		$author_id = $post->post_author;
		$author_name = get_the_author_meta( 'display_name', $author_id );
		$output = '';
		if ( in_array( $mode, array( 'avatar', 'full' ) ) ) {
			$output = '<img class="trx-addons-post-meta-item-avatar" src="' . esc_url( get_avatar_url( $author_id, array( 'size' => 100 ) ) ) . '" />';
		}
		if ( $mode !== 'avatar' ) {
			$output .= $author_name;
		}
		return sprintf( '<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
						esc_url( get_author_posts_url( $author_id) ),
						// translators: %s: Author's display name.
						esc_attr( sprintf( __( 'View %s&#8217;s posts', 'trx_addons' ), esc_html( $author_name ) ) ),
						$output
		);
	}

	/**
	 * Get the meta data from the WooCommerce product.
	 * 
	 * @param string $type  The type of meta data to get.
	 * 
	 * @return string  The meta data.
	 */
	function get_meta_woocommerce( $type ) {
		global $product;

		if ( empty( $product ) ) {
			return '';
		}

		switch ( $type ) {
			case 'product_price':
				return $product->get_price_html();
			case 'product_rating':
				return $product->get_average_rating();
			case 'product_stars':
				return wc_get_rating_html( $product->get_average_rating() );
			case 'product_category':
				return get_the_term_list( $product->get_id(), 'product_cat', '', ', ', '' );
			case 'product_tag':
				return get_the_term_list( $product->get_id(), 'product_tag', '', ', ', '' );
			case 'product_attribute':
				return wc_display_product_attributes( $product );
			default:
				return '';
		}
	}

	/**
	 * Render a widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function content_template() {
	}
}
