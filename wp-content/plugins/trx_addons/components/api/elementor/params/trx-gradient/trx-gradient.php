<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ThemeREX Addons Gradient control.
 *
 * A base control for creating a plugin-specific gradient control.
 * Displays a field with a popup to select a solid color and/or a linear or a radial gradients.
 *
 * Creating new control in the editor (inside `Widget_Base::register_controls()` method):
 *
 *    $this->add_control(
 *    	'gradient',
 *    	[
 *    		'label' => __( 'Gradient', 'plugin-domain' ),
 *    		'type' => 'trx_gradient',
 *   		'default' => '',
 *          'modes' => ['solid', 'linear-gradient', 'radial-gradient'],
 *    	]
 *    );
 *
 * PHP usage (inside `Widget_Base::render()` method):
 *
 *    echo '<div style="background: ' . esc_attr( $this->get_settings( 'gradient' ) ) . '">...</div>';
 *
 * JS usage (inside `Widget_Base::content_template()` method):
 *
 *    <div style="background: {{ settings.gradient }}">...</div>
 *
 * @since 2.30.3
 *
 * @param string $label       Optional. The label that appears above of the
 *                            field. Default is empty.
 * @param string $description Optional. The description that appears below the
 *                            field. Default is empty.
 * @param string $default     Optional. Default icon name. Default is empty.
 * @param array  $modes       Optional. A list of available modes for the gradient.
 * 						      Available values are ['solid', 'linear-gradient', 'radial-gradient'].
 * 						      Default is ['linear-gradient', 'radial-gradient'].
 * @param string $separator   Optional. Set the position of the control separator.
 *                            Available values are 'default', 'before', 'after'
 *                            and 'none'. 'default' will position the separator
 *                            depending on the control type. 'before' / 'after'
 *                            will position the separator before/after the
 *                            control. 'none' will hide the separator. Default
 *                            is 'default'.
 * @param bool   $show_label  Optional. Whether to display the label. Default is
 *                            true.
 * @param bool   $label_block Optional. Whether to display the label in a
 *                            separate line. Default is false.
 * @param string $selectors   Optional. The CSS rules for the control.
 */
class Trx_Addons_Elementor_Control_Trx_Gradient extends \Elementor\Base_Data_Control {

	/**
	 * Retrieve gradient control type.
	 *
	 * @since 2.30.3
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'trx_gradient';
	}

	/**
	 * Retrieve control's default settings.
	 *
	 * Get the default settings of the control, used while initializing the control.
	 *
	 * @since 2.30.3
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'label_block' => true,
			'picker_options' => array(
				'modes' => array('linear-gradient', 'radial-gradient'),
			)
		];
	}

	
	/**
	 * Enqueue control required scripts and styles.
	 *
	 * Used to register and enqueue custom scripts and styles used by this control.
	 *
	 * @since 2.30.3
	 * @access public
	 */
	public function enqueue() {
		wp_enqueue_script( 'lc-color-picker', trx_addons_get_file_url( 'js/color-picker/lc_color_picker/lc_color_picker.min.js' ), array( 'jquery' ), null, true );
		wp_enqueue_script( 'trx_addons-elementor-control-trx-gradient', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_API . 'elementor/params/trx-gradient/trx-gradient-control.js' ), array( 'jquery' ), null, true );
	}

	/**
	 * Render gradient control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 2.30.3
	 * @access public
	 *
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<# var icon = trx_addons_get_settings_icon( data.controlValue ); #>
		<div class="elementor-control-field">
			<label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<input type="text" id="<?php echo esc_attr( $control_uid ); ?>"
						data-setting="{{ data.name }}"
						class="trx_addons_control_gradient_field {{ data.name }}"
						value="{{ data.controlValue }}"
				/>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{ data.description }}</div>
		<# } #>
		<?php
	}
}
