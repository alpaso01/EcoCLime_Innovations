(function ($) {

	"use strict";

    window.addEventListener( 'elementor/init', () => {

		function trx_addons_ai_helper_elm_gen_text_add_modal_type () {
			DialogsManager.addWidgetType( 'trx_addons_ai_elementor_generate_text', DialogsManager.getWidgetType( 'lightbox' ).extend( 'trx_addons_ai_elementor_generate_text', {
				onReady: function() {

					DialogsManager.getWidgetType( 'lightbox' ).prototype.onReady.apply( this, arguments );

					var self = this;

					// Create modal Header
					var $header = $( '\
						<div class="trx_addons_ai_elm_gen_text_header_inner">\
							<div class="trx_addons_ai_elm_gen_text_header_left">\
								<p class="trx_addons_ai_elm_gen_text_header_logo">' + TRX_ADDONS_STORAGE['elm_ai_generate_text_btn_label'] + '</p>\
							</div>\
							<div class="trx_addons_ai_elm_gen_text_header_right">\
								<a href="javascript:void(0);" role="button" class="trx_addons_ai_elm_gen_text_close trx_addons_button_close" title="' + TRX_ADDONS_STORAGE['elm_ai_generate_text_modal_close'] + '">\
									<span class="trx_addons_button_close_icon"></span>\
								</a>\
							</div>\
						</div>' );

					$header.find( '.trx_addons_ai_elm_gen_text_close' ).on( 'click', function(e) {
						e.preventDefault();
						self.hide();
					} );

					self.getElements( 'header' ).append( $header );


					// Create modal Message
					var form_html = '<div class="trx_addons_ai_elm_gen_text_msg_inner">\
							<div class="trx_addons_ai_elm_gen_text_msg_input">\
								<label class="trx_addons_ai_elm_gen_text_msg_label" for="trx_addons_ai_elm_gen_text_msg_input_purpose">' + TRX_ADDONS_STORAGE['elm_ai_generate_text_modal_purpose_label'] + '</label>\
								<input id="trx_addons_ai_elm_gen_text_msg_input_purpose" type="text" class="trx_addons_ai_elm_gen_text_msg_input_purpose" value="' + TRX_ADDONS_STORAGE['elm_ai_generate_text_modal_purpose_pl'] + '" />\
							</div>\
							<div class="trx_addons_ai_elm_gen_text_msg_sbm">\
								<a href="javascript:void(0);" role="button" class="elementor-button e-primary trx_addons_ai_elm_gen_text_msg_sbm_btn" title="' + TRX_ADDONS_STORAGE['elm_ai_generate_text_modal_submit'] + '">\
									<span class="trx_addons_ai_elm_gen_text_msg_sbm_btn_label">' + TRX_ADDONS_STORAGE['elm_ai_generate_text_modal_submit'] + '</span>\
								</a>\
							</div>\
						</div>';

					var $form = $( form_html );

					$form.find( '.trx_addons_ai_elm_gen_text_msg_sbm_btn' ).on( 'click', function (e) {
						e.preventDefault();
						$(this).addClass( 'trx_addons_loading' );
						self.submitData();
						// Hide modal after the server response is received
						// self.hide();
						return false;
					} );

					self.getElements( 'message' ).append( $form );
				},
				submitData: () => {},
			} ) );
		}

		window.handleAIElementorGenerateText = function ( groups, view ) {

			var self = this,
				options = {};

			self.addButtonContextMenu = function () {
	
				groups.forEach( ( group ) => {
					if ( 'save' === group.name ) {
						var $new_actions = [];
						group.actions.forEach( ( action ) => {
							if ( 'save' == action.name ) {
								$new_actions.push( {
									name: 'ai-generate-texts',
									icon: '',
									title: TRX_ADDONS_STORAGE['elm_ai_generate_text_btn_label'],
									isEnabled: () => true,
									callback: () => { self.addNotice() },
								} );
							}
	
							$new_actions.push( action );
						} );
						group.actions = $new_actions;
					}
				} );
	
				return groups;

			};

			self.addNotice = function () {

				if ( typeof DialogsManager == 'undefined' ) return false;

				trx_addons_ai_helper_elm_gen_text_add_modal_type();

				var modal = elementorCommon.dialogsManager.createWidget( 'trx_addons_ai_elementor_generate_text', {
					id: 'trx-addons-ai-helper-elementor-generate-text-modal',
					className: 'trx-addons-ai-helper-elementor-generate-text-modal',
				} );

				modal.submitData = () => {
					options.purpose_title = modal.getElements( 'message' ).find( '.trx_addons_ai_elm_gen_text_msg_input_purpose' ).val() || TRX_ADDONS_STORAGE['elm_ai_generate_text_modal_purpose_pl'];
					options.modal = modal;
					self.btnCallback();
				};

				modal.show();

			};

			self.btnCallback = function () {

				var content = view.model.toJSON( { remove: ['default'] } );

				$.post( TRX_ADDONS_STORAGE['ajax_url'], {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_elementor_generate_text',
					purpose: options.purpose_title,
					// ai_helper: options.ai_helper,
					content: JSON.stringify( content ),
				}, function( response ) {
					if ( response ) {
						self.replaceTexts( response );
					}
				} );
			};

			self.fetch_answer = function ( data ) {

				var content = view.model.toJSON( { remove: ['default'] } );//after test - remove

				jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_elementor_generate_text_fetch',
					thread_id: data.thread_id,
					run_id: data.run_id,
					content: JSON.stringify( content ),//after test - remove
				}, function( response ) {
					if ( response ) {
						self.replaceTexts( response );
					}
				} );
			};

			self.replaceTexts = function ( response ) {
				// Prepare response
				var rez = {};
				if ( response == '' || response == 0 ) {
					rez = { error: TRX_ADDONS_STORAGE['msg_ai_helper_error'] };
				} else if ( typeof response == 'string' ) {
					try {
						rez = JSON.parse( response );
					} catch (e) {
						rez = { error: TRX_ADDONS_STORAGE['msg_ai_helper_error'] };
						console.log( response );
					}
				} else {
					rez = response;
				}

				// If queued - fetch answer again
				if ( rez.finish_reason == 'queued' ) {
					var time = rez.fetch_time ? rez.fetch_time : 2000;
					setTimeout( function() {
						self.fetch_answer( rez );
					}, time );
				} else {
					if ( ! rez.error ) {
						if ( rez.data && rez.data.fields ) {
							var historyId = $e.internal( 'document/history/start-log', {
								type: 'add',
								title: options.purpose_title,
							} );

							$e.run( 'document/elements/delete', {
								container: view.container
							} );

							$e.run( 'document/elements/create', {
								container: elementor.documents.currentDocument.container,
								model: rez.data.fields,
								options: {
									at: view._index,
									shouldWrap: false,
								},
							} );

							$e.internal( "document/history/end-log", {
								id: historyId
							} );

							// elementor.reloadPreview();
						}
					}

					// Hide modal after the server response is received
					options.modal.getElements( 'message' ).find( '.trx_addons_ai_elm_gen_text_msg_sbm_btn' ).removeClass( 'trx_addons_loading' );
					options.modal.hide();

					if ( rez.error ) {
						trx_addons_msgbox_warning( rez.error, '' );
					}
				}
			};

		}

		function iniAIHandlerElementor( groups, view ) {

			var instance = new handleAIElementorGenerateText( groups, view );
			return instance.addButtonContextMenu();
		}

        elementor.hooks.addFilter( 'elements/container/contextMenuGroups', iniAIHandlerElementor );
        elementor.hooks.addFilter( 'elements/section/contextMenuGroups', iniAIHandlerElementor );

    } );
}( jQuery ) );