<?php
namespace ElementorModal\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Frontend;
use WP_Query;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ElementorModalLoad extends Widget_Base {
	
	protected $_has_template_content = false;
	
	public function get_name() {
		return 'popup-load';
	}
	public function get_title() {
		return __( 'PopBox: On Load', 'modal-for-elementor' );
	}
	public function get_icon() {
		return 'eicon-text-field';
	}
	public function get_categories() {
		return [ 'norewp-elements' ];
	}
	public static function get_button_sizes() {
		return [
			'xs' => __( 'Extra Small', 'modal-for-elementor' ),
			'sm' => __( 'Small', 'modal-for-elementor' ),
			'md' => __( 'Medium', 'modal-for-elementor' ),
			'lg' => __( 'Large', 'modal-for-elementor' ),
			'xl' => __( 'Extra Large', 'modal-for-elementor' ),
		];
	}
	protected function get_popups() {
		$popups_query = new WP_Query( array(
			'post_type' => 'elementor-popup',
			'posts_per_page' => -1,
		) );

		if ( $popups_query->have_posts() ) {
			$popups_array = array();
			$popups = $popups_query->get_posts();
			
			$i = 0;
			foreach( $popups as $popap ) {
				$popups_array[$popap->ID] = $popap->post_title;
				if($i === 0)
					$selected = $popap->ID;
				$i++;
			}
			
			$popups = array(
				'first_popup' => $selected,
				'popups' => $popups_array,
			);
			return $popups;
		}
	}
	protected function _register_controls() {
		
		$this->start_controls_section(
			'section_button',
			[
				'label' => __( 'PopBox Settings', 'modal-for-elementor' ),
			]
		);
		
		$this->add_control(
			'popbox_onload_info',
			[
				'label' => __( 'NOTE: Mobile Devices Are Not Supported!', 'elementor-designer' ),
				'type' => Controls_Manager::RAW_HTML,
			]
		);
		
		$this->add_control(
			'popbox_load_delay',
			[
				'label' => __( 'Popup Delay', 'elementor-designer' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 3000,
				'title' => __( 'Numbers Only!', 'modal-for-elementor' ),
				'description' => __( 'The number of seconds to wait before displaying the popup - ex: 3000 = 3 seconds', 'modal-for-elementor' ),
			]
		);
		
		$this->add_control(
			'modal_dismissable',
			[
				'label' => __( 'Never Show Again', 'modal-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'YES',
				'label_off' => 'NO',
				'return_value' => 'yes',
				'description' => __( 'Allow visitors to dismiss the popup indefinately - this allows for better UX for those who have already taken action.', 'modal-for-elementor' ),
			]
		);
		
		$this->add_control(
			'dismissable_text',
			[
				'label' => __( 'Dismiss Text', 'modal-for-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' 		=> __( 'Don\'t Show Again', 'modal-for-elementor' ),
				'title' 		=> __( 'Dismis Text!', 'modal-for-elementor' ),
				'description' 	=> __( 'Text to show for the dismiss option', 'modal-for-elementor' ),
				'condition' => [
					'modal_dismissable' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'dismiss_align',
			[
				'label' => __( 'Dismiss Alignment', 'modal-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => __( 'Left', 'modal-for-elementor' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'modal-for-elementor' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'modal-for-elementor' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'tablet_default' => 'center',
				'mobile_default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .modal-footer' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'modal_dismissable' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'dismiss_footer_padding',
			[
				'label' => __( 'Footer Padding', 'modal-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .modal-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition' => [
					'modal_dismissable' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'dismiss_text_padding',
			[
				'label' => __( 'Dismiss Button Padding', 'modal-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .modal-footer .nothanks' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition' => [
					'modal_dismissable' => 'yes',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'dismiss_typography',
				'label' => __( 'Close Typography', 'modal-for-elementor' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .modal-footer .nothanks',
				'condition' => [
					'modal_dismissable' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'dismiss_button_radius',
			[
				'label' => __( 'Border Radius', 'modal-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .modal-footer .nothanks' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
				
		$this->add_control(
			'view',
			[
				'label' => __( 'View', 'modal-for-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);
		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_popup',
			[
				'label' => __( 'PopBox Content', 'modal-for-elementor' ),
			]
		);
		$this->add_control(
			'popup',
			[
				'label' => __( 'Select Popup Content', 'modal-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => $this->get_popups()['first_popup'],
				'options' => $this->get_popups()['popups'],
			]
		);
		
		$this->add_control(
			'modal_has_video',
			[
				'label' => __( 'Video Content?', 'modal-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'YES',
				'label_off' => 'NO',
				'return_value' => 'yes',
				'description' => __( 'Does the PopBox content contain a video? Setting this to Yes will stop the video from playing when the popup is closed.', 'modal-for-elementor' ),
			]
		);
        
        $this->add_control(
			'close_button',
			[
				'label' => __( 'Show Close Button', 'modal-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'modal-for-elementor' ),
				'label_on' => __( 'Show', 'modal-for-elementor' ),
				'default' => 'yes',			
				'selectors' => [
					'{{WRAPPER}} button.close' => 'display: inherit;',
                ],
            ]
		);
		
		$this->add_control(
			'close_button_pos',
			[
				'label' => __( 'Switch Button Position', 'modal-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Right', 'modal-for-elementor' ),
				'label_on' => __( 'Left', 'modal-for-elementor' ),
				'default' => '',			
				'selectors' => [
					'{{WRAPPER}} button.close' => 'left: 0;',
                ],
            ]
		);
		
		$this->add_control(
			'close_size',
			[
				'label' => __( 'Icon Size', 'modal-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} button.close i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'close_padding',
			[
				'label' => __( 'Close Padding', 'modal-for-elementor' ),
				'description' => __( 'Please note that padding bottom has no effect - Left/Right padding will depend on button position!', 'modal-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} button.close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		
		$this->add_control(
			'close_text',
			[
				'label' => __( 'Close Text', 'modal-for-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' 	=> __( 'Close', 'modal-for-elementor' ),
				'default' 		=> '',
				'description' 	=> __( 'Add call to action i.e "Close" before the popup close X', 'modal-for-elementor' ),
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'close_typography',
				'label' => __( 'Close Typography', 'modal-for-elementor' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} button.close:not(i)',
			]
		);
        
		$this->end_controls_section();     

        //Modal Container Optins Start Here
        $this->start_controls_section(
			'modalstyle',
			[
				'label' => __( 'Modal Container', 'modal-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_responsive_control(
			'modal_content_max_width',
			[
				'label' => __( 'Container Max-Width', 'modal-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 720,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1920,
						'step' => 1,
					],
					'%' => [
						'min' => 5,
						'max' => 100,
					],
				],
				'size_units' => [ '%', 'px' ],
				'selectors' => [
					'{{WRAPPER}} .modal-content' => 'max-width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);
		
		$this->add_control(
			'overlay_hint',
			[
				'label' => __( 'Select and configure the required modal overlay background type below', 'modal-for-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'modal_bgcolor',
				'types' => [ 'classic', 'gradient' ],
				'default' => 'rgba(0,0,0,0.7)',
				'selector' => '{{WRAPPER}} .modal',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'modalcontentstyle',
			[
				'label' => __( 'Modal Content', 'modal-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'modal_window_hint',
			[
				'label' => __( 'Select and configure the required popup modal window\'s background type below', 'modal-for-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
			]
		);
		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'label' => __( 'Popup Window Background', 'modal-for-elementor' ),
				'name' => 'modal_window_bg',
				'types' => [ 'none', 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .modal-content',
			]
		);
        
        $this->add_control(
			'button_close_text_color',
			[
				'label' => __( 'Close Button Color', 'modal-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} button.close' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'modal_content_width',
			[
				'label' => __( 'Modal Width', 'modal-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 60,
					'unit' => '%',
				],
				'range' => [
					'px' => [
							'min' => 0,
							'max' => 1920,
							'step' => 1,
					],
					'%' => [
							'min' => 25,
							'max' => 100,
					],
				],
				'size_units' => [ '%', 'px' ],
				'selectors' => [
					'{{WRAPPER}} .modal-content' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);
		
		$this->add_responsive_control(
			'modal_content_top',
			[
				'label' => __( 'Top Offset', 'modal-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 5,
					'unit' => '%',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ '%', 'px' ],
				'selectors' => [
					'{{WRAPPER}} .modal-content' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'modal_content_padding',
			[
				'label' => __( 'Padding', 'modal-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => 0,
					'left' => 0,
					'right' => 0,
					'bottom' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .modal-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'modal_border',
				'label' => __( 'Border', 'modal-for-elementor' ),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .modal-content',
			]
		);

		$this->add_control(
			'modal_border_radius',
			[
				'label' => __( 'Border Radius', 'modal-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .modal-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'popbox_content_box_shadow',
				'selector' => '{{WRAPPER}} .modal-content',
			]
		);
		
		$this->end_controls_section();

		$this->start_controls_section(
			'modaldismisstyle',
			[
				'label' => __( 'Dismiss Options', 'modal-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->start_controls_tabs( 'dismiss_tabs' );

		$this->start_controls_tab( 'dismiss_footer_settings', [ 'label' => __( 'Dismiss Settings', 'modal-for-elementor' ) ] );
		
		$this->add_control(
			'dimiss_footer_hint',
			[
				'label' => __( 'Set background for the dimiss footer section', 'modal-for-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
			]
		);
		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'label' => __( 'Footer Background', 'modal-for-elementor' ),
				'name' => 'dimiss_footer_bg',
				'types' => [ 'none', 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .modal-footer',
			]
		);
		
		$this->add_control(
			'dimiss_border_color',
			[
				'label' => __( 'Footer Border Color', 'modal-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .modal-footer' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		
		$this->start_controls_tab( 'dismiss_button_settings', [ 'label' => __( 'Dismiss Button', 'modal-for-elementor' ) ] );
		
		$this->add_control(
			'dimiss_text_color',
			[
				'label' => __( 'Dismiss Button Color', 'modal-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .modal-footer .nothanks' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'dimiss_button_hint',
			[
				'label' => __( 'Set background for the dimiss button', 'modal-for-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
			]
		);
		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'label' => __( 'Button Background', 'modal-for-elementor' ),
				'name' => 'dismiss_button_bg',
				'types' => [ 'none', 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .modal-footer .nothanks',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
        
	}
	protected function render() {
		$settings = $this->get_settings();
		$close 			= $settings['close_text'];
		$dismiss 		= $settings['modal_dismissable'];
		$dismiss_text 	= $settings['dismissable_text'];
		$has_video 		= $settings['modal_has_video'];
		$delay 			= ! empty( $settings['popbox_load_delay'] ) ? (int)$settings['popbox_load_delay'] : 3000;
		$selectedPopup 	= new WP_Query( array( 'p' => $settings['popup'], 'post_type' => 'elementor-popup' ) );
		if ( $selectedPopup->have_posts() ) {
			
			$selectedPopup->the_post();

			?>
			<!-- PopBox -->
			<div class="modal modal-onload fade" id="popup-onload-<?php echo $selectedPopup->post->ID; ?>" tabindex="-1" role="dialog" aria-labelledby="popup-<?php echo $selectedPopup->post->ID; ?>-label">			
				<div class="modal-content">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						<?php echo $close; ?> <i class="fa fa-close"></i></span>
					</button>
					<div class="modal-body">
						<?php
							$elementor  = get_post_meta( $selectedPopup->post->ID, '_elementor_edit_mode', true );							
							if ( $elementor ) {
								$frontend = new Frontend;
								echo $frontend->get_builder_content( $selectedPopup->post->ID, true );
							} else {
								the_content();
							}
						?>
					</div>
					<?php if ( $dismiss ) { ?>
					<div class="modal-footer">
						<span class="nothanks" data-dismiss="modal" aria-hidden="true"><?php echo $dismiss_text; ?></span>
					</div>
					<?php } ?>
				</div>
			</div>
			<script type="text/javascript">
				(function($) {
					$(document).ready(function() {
						// If no cookie with our chosen name (e.g. no_thanks)...
						if ($.cookie("no_thanks_popup_<?php echo $selectedPopup->post->ID; ?>") == null) {
							// Show the modal, with delay func.
							function show_modal(){
								$('#popup-onload-<?php echo $selectedPopup->post->ID; ?>').modal();
							}
							// Set delay func. time in milliseconds
							if(!isMobile.any() ) {
								window.setTimeout(show_modal, <?php echo $delay; ?>);
							}
						}
						// On click of specified class (e.g. 'nothanks'), trigger cookie, with expiration in year 9999
						$(".nothanks").click(function() {
							document.cookie = "no_thanks_popup_<?php echo $selectedPopup->post->ID; ?>=true; expires=Fri, 31 Dec 9999 23:59:59 UTC";
						});
					});
				})(jQuery);
			</script>
			<?php 
				if ( $has_video ) { ?>
				<script type="text/javascript">
					(function($) {
						$('#popup-onload-<?php echo $selectedPopup->post->ID; ?>').on('hide.bs.modal', function(e) {    
							var $if = $(e.delegateTarget).find('iframe');
							var src = $if.attr("src");
							$if.attr("src", '/empty.html');
							$if.attr("src", src);
						});
					})(jQuery);
				</script>			
			<!-- PopBox -->
			<?php }
			wp_reset_postdata();
			
		}
	}
	protected function _content_template() {}

}