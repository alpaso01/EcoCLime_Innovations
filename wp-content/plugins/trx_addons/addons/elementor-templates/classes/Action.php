<?php
/**
 * Add custom control for Elementor.
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorTemplates;

use TrxAddons\ElementorTemplates\Utils as TrxAddonsUtils;

use Elementor\Base_Data_Control;

/**
 * Action class.
 */
class Action extends Base_Data_Control {

	var $color_prefix = 'theme_color_';
	var $font_prefix = 'theme_font_';
	var $allowed_props = array( 'font-family', 'font-size', 'font-weight', 'text-transform', 'font-style', 'text-decoration', 'line-height', 'letter-spacing', 'word-spacing' );

	/**
	 * Get control type.
	 * Retrieve the control type.
	 *
	 * @access public
	 */
	public function get_type() {
		return 'trx_addons_elementor_extension_action';
	}

	/**
	 * Get data control value.
	 * Retrieve the value of the data control from a specific Controls_Stack settings.
	 *
	 * @param array $control  Control.
	 * @param array $settings Element settings.
	 *
	 * @access public
	 *
	 * @return bool
	 */
	public function get_value( $control, $settings ) {
		return false;
	}

	/**
	 * Get data control default value.
	 *
	 * Retrieve the default value of the data control. Used to return the default
	 * values while initializing the data control.
	 *
	 * @access public
	 * @return string Control default value.
	 */
	public function get_default_value() {
		return '';
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'trx_addons_elementor_extension_action', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'elementor-templates/js/action.js' ), array( 'jquery' ), null, false );

		$schemes = trx_addons_get_theme_color_schemes();
		if ( empty( $schemes ) || ! is_array( $schemes ) ) {
			$schemes = array();
		}
		$default_scheme = trx_addons_get_theme_option( 'color_scheme', 'default' );

		// Get "factory" scheme colors from the theme
		$schemes_factory = array();
		$scheme_storage = trx_addons_get_theme_data( 'options[scheme_storage]' );
		if ( ! empty( $scheme_storage['factory'] ) && is_string( $scheme_storage['factory'] ) && substr( $scheme_storage['factory'], 0, 2 ) == 'a:' ) {
			$scheme_storage = trx_addons_unserialize( $scheme_storage['factory'] );
			foreach( $scheme_storage as $scheme => $data ) {
				// Get a factory colors for the current scheme only
				if ( $scheme != $default_scheme ) {
					continue;
				}
				// Factory Colors
				$schemes_factory[ $scheme ] = array();
				foreach ( $data['colors'] as $key => $value ) {
					$schemes_factory[ $scheme ][] = array(
						'_id'   => $this->color_prefix . $key,
						'title' => apply_filters( 'trx_addons_filter_global_color_title', sprintf( _x( 'Theme %s', 'Global color title in Elementor', 'trx_addons' ), ucfirst( str_replace( '_', ' ', $key ) ) ), $key ),
						'color' => $value,
					);
				}
			}
		}

		// Get "factory" fonts from the theme
		$fonts_factory = array();
		$fonts   = trx_addons_get_theme_data( 'theme_fonts' );
		$options = trx_addons_get_theme_data( 'options' );
		// Get theme-specific breakpoints
		// $breakpoints = trx_addons_call_theme_function( 'get_theme_breakpoints', array(), array(
		// 	'desktop' => array(),
		// 	'tablet' => array(),
		// 	'mobile' => array(),
		// ) );
		// Get Elementor-specific breakpoints
		$breakpoints = trx_addons_elm_get_breakpoints();
		if ( is_array( $fonts ) ) {
			foreach ( $fonts as $tag => $v ) {
				$tag_settings = array(
					'_id'                   => $this->font_prefix . $tag,
					'title'                 => $v['title'],
					'typography_typography' => 'custom',
				);
				foreach ( $this->allowed_props as $css_prop ) {
					$fonts_prop = str_replace( '-', '_', $css_prop );
					foreach ( ! empty( $options["{$tag}_{$css_prop}"]['responsive'] ) ? $breakpoints : array( 'desktop' => array() ) as $bp => $bpv ) {
						$suffix = $bp == 'desktop' ? '' : '_' . $bp;
						$css_value = isset( $options[ "{$tag}_{$css_prop}" ]["factory{$suffix}"] ) ? $options[ "{$tag}_{$css_prop}" ]["factory{$suffix}"] : '';
						// Convert options with units to the Elementor format (e.g. 'font-size' => '16px' to 'font-size' => array( 'size' => 16', 'unit' => 'px' )
						if ( in_array( $css_prop, array( 'font-size', 'line-height', 'letter-spacing', 'margin-top', 'margin-bottom', 'word-spacing' ) ) ) {
							$css_value = TrxAddonsUtils::parse_css_value( $css_value );
						} else if ( in_array( $css_prop, array( 'font-family' ) ) ) {
							$parts2 = explode( ',', $css_value );
							$css_value = str_replace( array( '"', "'" ), '', $parts2[0] );
						}
						$tag_settings[ "typography_{$fonts_prop}{$suffix}" ] = $css_value;
					}
				}
				$fonts_factory[] = $tag_settings;
			}
		}

		wp_localize_script( 'trx_addons_elementor_extension_action', 'TRX_ADDONS_ELEMENTOR_EXTENSION_ACTION', array(
				'cssDir'          => \Elementor\Core\Files\Base::get_base_uploads_url() . \Elementor\Core\Files\Base::DEFAULT_FILES_DIR,
				'globalKit'       => get_option( 'elementor_active_kit' ),
				'schemes'         => $schemes,
				'schemesFactory'  => $schemes_factory,
				//'fonts'           => $fonts,
				'fontsFactory'    => $fonts_factory,
				'translate'       => array(
					'resetHeader'                  => __( 'Are you sure?', 'trx_addons' ),
					'resetGlobalColorsMessage'     => __( 'This will revert the color palette and the color labels to their defaults. You can undo this action from the revisions tab.', 'trx_addons' ),
					'resetGlobalFontsMessage'      => __( 'This will revert the global font labels & values to their defaults. You can undo this action from the revisions tab.', 'trx_addons' ),
				)
			)
		);
	}

	/**
	 * Get default control settings.
	 *
	 * @since 1.6.0
	 * @return array
	 */
	protected function get_default_settings() {
		return array(
			'button_type' => 'success',
		);
	}

	/**
	 * Control Content template.
	 *
	 * {@inheritDoc}
	 *
	 * @since 1.6.0 Added data.button_type class to button.
	 * @return void
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field">
			<label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<button
					data-action="{{ data.action }}"
					style="padding:7px 10px"
					class="elementor-button elementor-button-{{{ data.button_type }}}"
				>
				{{{ data.action_label }}}</button>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}
}
