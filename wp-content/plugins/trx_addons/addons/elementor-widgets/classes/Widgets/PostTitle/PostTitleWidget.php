<?php
/**
 * Post Title Widget
 *
 * @package ThemeREX Addons
 * @since v2.30.2
 */

namespace TrxAddons\ElementorWidgets\Widgets\PostTitle;

use TrxAddons\ElementorWidgets\BaseWidget;
use TrxAddons\ElementorWidgets\Utils as TrxAddonsUtils;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Post Title Widget
 */
class PostTitleWidget extends BaseWidget {

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_title_controls();

		/* Style Tab */
		$this->register_style_title_controls();
		$this->register_style_description_controls();
	}

	/**
	 * Register title controls
	 *
	 * @return void
	 */
	protected function register_content_title_controls() {

		$this->start_controls_section(
			'section_title',
			[
				'label'                 => __( 'Title', 'trx_addons' ),
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => esc_html__( 'HTML Tag', 'trx_addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => trx_addons_get_list_sc_title_tags( '', true ),
				'default' => 'h2',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'trx_addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => trx_addons_get_list_sc_aligns_for_elementor( true ),
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'enable_description',
			array(
				'label'        => __( 'Enable category/tag description', 'trx_addons' ),
				'description'  => __( 'Toggle to hide description on category/tag archive pages.', 'trx_addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'on',
			)
		);

		$this->end_controls_section();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	STYLE TAB
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Register title style controls
	 *
	 * @return void
	 */
	protected function register_style_title_controls() {
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Title', 'trx_addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .elementor-heading-title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', 'trx_addons' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-heading-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'trx_addons' ),
				'type' => Controls_Manager::COLOR,
				// 'global' => [
				// 	'default' => Global_Colors::COLOR_PRIMARY,
				// ],
				'selectors' => [
					'{{WRAPPER}} .elementor-heading-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_stroke',
				'selector' => '{{WRAPPER}} .elementor-heading-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_shadow',
				'selector' => '{{WRAPPER}} .elementor-heading-title',
			]
		);

		$this->add_responsive_control(
			'title_border_radius',
			[
				'label'                 => esc_html__( 'Border Radius', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .elementor-heading-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label'                 => esc_html__( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .elementor-heading-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .elementor-heading-title' => 'margin: {{top}}{{UNIT}} {{right}}{{UNIT}} {{bottom}}{{UNIT}} {{left}}{{UNIT}};',
				),
			]
		);

		// $this->add_control(
		// 	'blend_mode',
		// 	[
		// 		'label' => esc_html__( 'Blend Mode', 'trx_addons' ),
		// 		'type' => Controls_Manager::SELECT,
		// 		'options' => trx_addons_get_list_blend_modes(),
		// 		'selectors' => [
		// 			'{{WRAPPER}} .elementor-heading-title' => 'mix-blend-mode: {{VALUE}}',
		// 			'{{WRAPPER}} .elementor-heading-title-description' => 'mix-blend-mode: {{VALUE}}',
		// 		],
		// 		'separator' => 'none',
		// 	]
		// );

		$this->end_controls_section();
	}

	/**
	 * Register description style controls
	 *
	 * @return void
	 */
	protected function register_style_description_controls() {
		$this->start_controls_section(
			'section_description_style',
			[
				'label' => esc_html__( 'Description', 'trx_addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'enable_description' => 'on'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .elementor-heading-title-description',
				'condition' => [
					'enable_description' => 'on'
				]
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => esc_html__( 'Text Color', 'trx_addons' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-heading-title-description' => 'color: {{VALUE}};',
				],
				'condition' => [
					'enable_description' => 'on'
				]
			]
		);

		$this->add_responsive_control(
			'description_padding',
			[
				'label'                 => esc_html__( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .elementor-heading-title-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'enable_description' => 'on'
				]
			]
		);

		$this->add_responsive_control(
			'description_margin',
			[
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .elementor-heading-title-description' => 'margin: {{top}}{{UNIT}} {{right}}{{UNIT}} {{bottom}}{{UNIT}} {{left}}{{UNIT}};',
				),
				'condition' => [
					'enable_description' => 'on'
				]
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

		$this->add_render_attribute( 'title_wrap', 'class', 'trx-addons-post-title-wrap' );
		$this->add_render_attribute( 'title', 'class', 'elementor-heading-title trx-addons-post-title' );
		$this->add_render_attribute( 'description', 'class', 'elementor-heading-title-description trx-addons-post-title-description' );

		?><div <?php $this->print_render_attribute_string( 'title_wrap' ) ?>><?php

		$settings = $this->get_settings_for_display();

		$title_tag = TrxAddonsUtils::validate_html_tag( $settings['title_tag'] );

		if ( ! \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
			$title = trx_addons_get_blog_title();
			$title_text = $title_class = $title_link = $title_link_text = '';
			if ( is_array( $title ) ) {
				$title_text      = $title['text'];
				$title_class     = ! empty( $title['class'] )     ? ' ' . $title['class'] : '';
				$title_link      = ! empty( $title['link'] )      ? $title['link'] : '';
				$title_link_text = ! empty( $title['link_text'] ) ? $title['link_text'] : '';
				if ( ! empty( $title_class ) ) {
					$this->add_render_attribute( 'title', 'class', $title_class );
				}
			} else {
				$title_text = $title;
			}

			?><<?php echo esc_html( $title_tag ); trx_addons_seo_snippets('headline'); echo ' ' . $this->get_render_attribute_string( 'title' ); ?>><?php
				$trx_addons_top_icon = trx_addons_get_term_image_small();
				if ( ! empty( $trx_addons_top_icon ) ) {
					$trx_addons_attr = trx_addons_getimagesize( $trx_addons_top_icon );
					?><img src="<?php echo esc_url( $trx_addons_top_icon ); ?>" alt="<?php esc_attr_e( 'Icon', 'trx_addons' ); ?>" <?php if ( ! empty( $trx_addons_attr[3] ) ) trx_addons_show_layout($trx_addons_attr[3] ); ?>><?php
				}
				echo wp_kses_data( $title_text );
			?></<?php echo esc_html( $title_tag ); ?>><?php
			
			if ( ! empty( $title_link ) && ! empty( $title_link_text ) ) {
				?><a href="<?php echo esc_url( $title_link ); ?>" class="theme_button trx-addons-post-title-link"><?php echo esc_html( $title_link_text ); ?></a><?php
			}
				
			// Category/Tag description
			if ( ! is_paged() && ! is_post_type_archive() && ( is_category() || is_tag() || is_tax() ) && 'on' === $settings['enable_description'] ) {
				the_archive_description( '<div ' . $this->get_render_attribute_string( 'description' ) . '>', '</div>' );
			}

			trx_addons_sc_layouts_showed( 'title', true );

        } else {

			// Title placeholder
			?><<?php echo esc_html( $title_tag ); echo ' ' . $this->get_render_attribute_string( 'title' ); ?>><?php
				esc_html_e( 'Title Placeholder', 'trx_addons' );
			?></<?php echo esc_html( $title_tag ); ?>><?php

			if ( 'on' === $settings['enable_description'] ) {
				// Description placeholder
				?><div <?php echo $this->get_render_attribute_string( 'description' ); ?>><?php
					esc_html_e( 'Category/Tag description placeholder', 'trx_addons' );
				?></div><?php
			}

		}

		?></div><?php
	}

	/**
	 * Render Post Title widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function content_template() {
		?><#
		// Title wrap
		view.addRenderAttribute( 'title_wrap', 'class', 'trx-addons-post-title-wrap' );
		#><div <# print( view.getRenderAttributeString( 'title_wrap' ) ); #>>
			<#
			// Title placeholder
			view.addRenderAttribute( 'title', 'class', 'elementor-heading-title trx-addons-post-title' );
			var title_html = '<' + settings.title_tag + ' ' + view.getRenderAttributeString( 'title' ) + '>' + "<?php echo addslashes( esc_html__( 'Title Placeholder', 'trx_addons' ) ); ?>" + '</' + settings.title_tag + '>';
			print( title_html );

			if ( 'on' === settings.enable_description ) {
				// Description placeholder
				view.addRenderAttribute( 'description', 'class', 'elementor-heading-title-description trx-addons-post-title-description' );
				var description_html = '<div ' + view.getRenderAttributeString( 'description' ) + '>' + "<?php echo addslashes( esc_html__( 'Category/Tag description placeholder', 'trx_addons' ) ); ?>" + '</div>';
				print( description_html );
			}
			#>
		</div><?php
	}
}
