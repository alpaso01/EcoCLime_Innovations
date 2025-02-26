/*
global: elementor, jQuery;
*/

jQuery(document).ready( function() {
	"use strict";

	// Generate Elementor-specific event when field is changed:
	// input	for @ui.input, @ui.textarea
	// change	for @ui.checkbox, @ui.radio, @ui.select
	// click	for @ui.responsiveSwitchers
	jQuery( '#elementor-panel' ).on( 'change', '.trx_addons_control_gradient_field', function( e ) {
		jQuery( this ).trigger( 'input' );
	} );
} );

// Init control
window.addEventListener( 'elementor/init', function() {
	var TrxAddonsGradientItemView = elementor.modules.controls.BaseData.extend( {
		onReady() {
			var self = this;
			var value = this.getControlValue();	//this.ui.input[0].value || ''
			var options = _.extend( {
					transparency : true,
					open_on_focus: true,
					allow_empty  : true,
					wrap_width   : '100%',
					preview_style: {
						input_padding: 30,
						side         : 'left',
						width        : 24,
					},
					fallback_colors: [
						value.slice( 0, 1 ) == '#' || value.slice( 0, 3 ) == 'rgb' ? value : '#ffffff',
						value.indexOf( 'gradient' ) > 0 ? value : 'linear-gradient(90deg, rgba(255, 255, 255, .4), #000)'
					],
					on_change: function( value, fld ) {
						// jQuery( fld ).trigger( 'change' );
						self.setValue( value );
					}
				}, this.model.get( 'picker_options' ) );
				new lc_color_picker( this.ui.input[0], options );
		},
		// saveValue() {
		// 	this.setValue( this.ui.input[0].value);
		// },
		// onBeforeDestroy() {
		// 	this.saveValue();
		// 	this.ui.input[0].emojioneArea.off();
		// }
	});

	elementor.addControlView( 'trx_gradient', TrxAddonsGradientItemView );

} );
