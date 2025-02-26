<?php
/**
 * Post Breadcrumbs Widget
 *
 * @package ThemeREX Addons
 * @since v2.30.2
 */

namespace TrxAddons\ElementorWidgets\Widgets\PostBreadcrumbs;

use TrxAddons\ElementorWidgets\BaseWidget;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Post Breadcrumbs Widget
 */
class PostBreadcrumbsWidget extends BaseWidget {

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_breadcrumbs_controls();

		/* Style Tab */
		$this->register_style_breadcrumbs_controls();
	}

	/**
	 * Register breadcrumbs controls
	 *
	 * @return void
	 */
	protected function register_content_breadcrumbs_controls() {

		$this->start_controls_section(
			'section_breadcrumbs',
			[
				'label'                 => __( 'Breadcrumbs', 'trx_addons' ),
			]
		);

		$this->add_control(
			'breadcrumbs_truncate_title',
			[
				'label'   => __( 'Title Max. Length', 'trx_addons' ),
				'description' => __( 'Truncate all titles to this length (if 0 - no truncate)', 'trx_addons' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => '',
				],
			]
		);

		$this->add_control(
			'breadcrumbs_truncate_add',
			[
				'label'   => __( 'Add to Title', 'trx_addons' ),
				'description' => __( 'Append truncated title with this string.', 'trx_addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '...',
			]
		);

		$this->add_control(
			'breadcrumbs_max_levels',
			[
				'label'   => __( 'Max. Items', 'trx_addons' ),
				'description' => __( 'How many items will be shown in breadcrumbs? If 0 - no limits.', 'trx_addons' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => [
					'px' => [
						'min' => 2,
						'max' => 100,
					],
				],
				'default' => [
					'size' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	STYLE TAB
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Register breadcrumbs style controls
	 *
	 * @return void
	 */
	protected function register_style_breadcrumbs_controls() {
		$this->start_controls_section(
			'section_breadcrumbs_style',
			[
				'label' => esc_html__( 'Breadcrumbs', 'trx_addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'trx_addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => trx_addons_get_list_sc_aligns_for_elementor(),
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'breadcrumbs_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .trx-addons-post-breadcrumbs .breadcrumbs',
			]
		);

		$this->add_control(
			'breadcrumbs_text_color',
			[
				'label' => esc_html__( 'Text Color', 'trx_addons' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .trx-addons-post-breadcrumbs span.breadcrumbs_item' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-post-breadcrumbs span.breadcrumbs_delimiter' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'breadcrumbs_link_color',
			[
				'label' => esc_html__( 'Link Color', 'trx_addons' ),
				'type' => Controls_Manager::COLOR,
				// 'global' => [
				// 	'default' => Global_Colors::COLOR_PRIMARY,
				// ],
				'selectors' => [
					'{{WRAPPER}} .trx-addons-post-breadcrumbs a.breadcrumbs_item' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'breadcrumbs_link_hover',
			[
				'label' => esc_html__( 'Hover Color', 'trx_addons' ),
				'type' => Controls_Manager::COLOR,
				// 'global' => [
				// 	'default' => Global_Colors::COLOR_PRIMARY,
				// ],
				'selectors' => [
					'{{WRAPPER}} .trx-addons-post-breadcrumbs a.breadcrumbs_item:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'breadcrumbs_separator',
			[
				'label' => esc_html__( 'Separator', 'trx_addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .breadcrumbs_delimiter:before' => "content: '{{VALUE}}';",
				],
			]
		);

		$this->add_responsive_control(
			'breadcrumbs_items_spacing',
			[
				'label'      => __( 'Items Gaps', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px'    => [
						'min' => 0,
						'max' => 200,
					],
					'em'    => [
						'min' => 0,
						'max' => 2,
						'step' => 0.1
					],
					'rem'    => [
						'min' => 0,
						'max' => 2,
						'step' => 0.1
					],
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'default'    => [
					'size' => 0.5,
					'unit' => 'em',
				],
				'selectors'  => [
					'{{WRAPPER}} .breadcrumbs_delimiter' => 'margin-left:{{SIZE}}{{UNIT}}; margin-right:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'breadcrumbs_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-post-breadcrumbs',
			]
		);

		$this->add_responsive_control(
			'breadcrumbs_margin',
			[
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-post-breadcrumbs' => 'margin: {{top}}{{UNIT}} {{right}}{{UNIT}} {{bottom}}{{UNIT}} {{left}}{{UNIT}};',
				),
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
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute( 'breadcrumbs', 'class', 'trx-addons-post-breadcrumbs' );
		?><div <?php $this->print_render_attribute_string( 'breadcrumbs' ) ?>><?php
			do_action( 'trx_addons_action_breadcrumbs', array(
				'truncate_title' => (int)$settings['breadcrumbs_truncate_title']['size'],
				'truncate_add'   => $settings['breadcrumbs_truncate_add'],
				'max_levels'     => (int)$settings['breadcrumbs_max_levels']['size'],
			) );
			trx_addons_sc_layouts_showed( 'breadcrumbs', true );
		?></div><?php
	}

	/**
	 * Render a widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.4.2
	 * @access protected
	 */
	protected function content_template() {
		?><#
		var items = [
			{ title: '<?php echo addslashes( esc_html( __( 'Home', 'trx_addons' ) ) ); ?>', url: '#' },
			{ title: '<?php echo addslashes( esc_html( __( 'Category Name', 'trx_addons' ) ) ); ?>', url: '#' },
			{ title: '<?php echo addslashes( esc_html( __( 'Post Title', 'trx_addons' ) ) ); ?>', url: '' },
		];
		view.addRenderAttribute( 'breadcrumbs', 'class', 'trx-addons-post-breadcrumbs' );
		#><div <# print( view.getRenderAttributeString( 'breadcrumbs' ) ); #>>
			<div class="breadcrumbs"><#
				var max_levels = settings.breadcrumbs_max_levels.size > 0 ? Math.max( settings.breadcrumbs_max_levels.size, 2 ) : 999;
				for ( var i = 0; i < items.length; i++ ) {
					if ( max_levels > 0 && max_levels < items.length && i > 0 ) {
						if ( i == items.length - 1 ) {
							#><span class="breadcrumbs_delimiter"></span><#
							#><span class="breadcrumbs_item">...</span><#
						} else {
							continue;
						}
					}
					if ( i > 0 ) {
						#><span class="breadcrumbs_delimiter"></span><#
					}
					// Truncate title
					if ( settings.breadcrumbs_truncate_title.size > 0 && items[i].title.length > settings.breadcrumbs_truncate_title.size ) {
						items[i].title = items[i].title.slice( 0, settings.breadcrumbs_truncate_title.size - settings.breadcrumbs_truncate_add.length ) + settings.breadcrumbs_truncate_add;
					}
					if ( items[i].url != '' ) {
						#><a class="breadcrumbs_item" href="{{ items[i].url }}">{{ items[i].title }}</a><#
					} else {
						#><span class="breadcrumbs_item current">{{ items[i].title }}</span><#
					}
				}
			#></div>
		</div><?php
	}
}
