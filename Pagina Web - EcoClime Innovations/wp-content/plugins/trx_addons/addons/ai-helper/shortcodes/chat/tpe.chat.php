<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v2.22.0
 */

extract( get_query_var( 'trx_addons_args_sc_chat' ) );
?><#

if ( false && settings.type == 'popup' ) {

	#><?php
	// $element->sc_show_placeholder( array(
	// 			'title_field' => 'type',
	// 		) );
	?><#

} else {

	settings = trx_addons_elm_prepare_global_params( settings );

	var id = settings._element_id ? settings._element_id + '_sc' : 'sc_chat_' + ( '' + Math.random() ).replace( '.', '' );
	var image_type = '';

	#><div id="{{ id }}" class="<#
		print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 'sc_chat sc_chat_' + settings.type + ( settings.allow_attachments > 0 ? ' sc_chat_with_attachments' : '' ), settings ) );
	#>"><#

		if ( settings.type != 'popup' ) {
			#><?php $element->sc_show_titles( 'sc_chat' ); ?><#
		}

		#><div class="sc_chat_content sc_item_content">
			<div class="sc_chat_form"
				data-chat-style="<#
					print( JSON.stringify( {
						'assistant_icon': settings.assistant_icon ? settings.assistant_icon : '',
						'assistant_image': settings.assistant_image.url ? settings.assistant_image.url : '',
						'assistant_name': settings.assistant_name ? settings.assistant_name : '<?php esc_attr_e( 'Assistant', 'trx_addons' ); ?>',
						'user_icon': settings.user_icon ? settings.user_icon : '',
						'user_image': settings.user_image.url ? settings.user_image.url : '',
						'user_name': settings.user_name ? settings.user_name : '<?php esc_attr_e( 'User', 'trx_addons' ); ?>',
					} ).replace(/"/g, '&quot;') );
				#>"
			>
				<div class="sc_chat_form_inner">
					<?php
					$trx_addons_ai_helper_prompt_id = 'sc_chat_form_field_prompt_' . mt_rand();

					// Chat Title
					?>
					<label for="<?php echo esc_attr( $trx_addons_ai_helper_prompt_id ); ?>" class="sc_chat_form_field_prompt_label">
						<span class="sc_chat_form_title"><#
							if ( settings.title_image.url ) {
								image_type = trx_addons_get_file_ext( settings.title_image.url );
								if ( image_type == 'svg' ) {
									#><span class="sc_chat_form_title_svg"><#
										print( trx_addons_get_inline_svg( settings.title_image.url, {
											render: function( html ) {
												if ( html ) {
													elementor.$previewContents.find( '#' + id + ' .sc_chat_form_title_svg' ).html( html );
												}
											}
										} ) );
									#></span><#
								} else {
									#><img src="{{ settings.title_image.url }}" alt="{{ settings.title_image.alt }}" class="sc_chat_form_title_image"><#
								}
							} else if ( settings.title_icon && ! trx_addons_is_off( settings.title_icon ) ) {
								#><span class="sc_chat_form_title_icon {{ settings.title_icon }}"></span><#
							}
							if ( settings.title_text !== '#' ) {
								#><span class="sc_chat_form_title_text">{{{ settings.title_text || '<?php esc_html_e( 'How can I help you?', 'trx_addons' ); ?>' }}}</span><#
							}
						#></span>
						<a href="#" class="sc_chat_form_start_new trx_addons_hidden"><#
							if ( settings.new_chat_text !== '#' ) {
								#><span class="sc_chat_form_start_new_text">{{{ settings.new_chat_text || '<?php esc_html_e( 'New chat', 'trx_addons' ); ?>' }}}</span><#
							}
							if ( settings.new_chat_image.url ) {
								image_type = trx_addons_get_file_ext( settings.new_chat_image.url );
								if ( image_type == 'svg' ) {
									#><span class="sc_chat_form_start_new_svg"><#
										print( trx_addons_get_inline_svg( settings.new_chat_image.url, {
											render: function( html ) {
												if ( html ) {
													elementor.$previewContents.find( '#' + id + ' .sc_chat_form_start_new_svg' ).html( html );
												}
											}
										} ) );
									#></span><#
								} else {
									#><img src="{{ settings.new_chat_image.url }}" alt="{{ settings.new_chat_image.alt }}" class="sc_chat_form_start_new_image"><#
								}
							} else if ( settings.new_chat_icon && ! trx_addons_is_off( settings.new_chat_icon ) ) {
								#><span class="sc_chat_form_start_new_icon {{ settings.new_chat_icon }}"></span><#
							}
						#></a>
					</label><#

					// Chat Messages
					#><div class="sc_chat_result">
						<ul class="sc_chat_list"></ul>
					</div><#

					// Tags (before the prompt field)
					var tags = '';
					if ( settings.tags && settings.tags.length && settings.tags[0].title && settings.tags[0].prompt && settings.tags_position != 'none' ) {
						tags = '<div class="sc_chat_form_field sc_chat_form_field_tags sc_chat_form_field_tags_' + settings.tags_position + '">'
									+ ( settings.tags_label ? '<span class="sc_chat_form_field_tags_label">' + settings.tags_label + '</span>' : '' )
									+ '<span class="sc_chat_form_field_tags_list">'
										+ _.map( settings.tags, function( tag ) {
												return '<a href="#" class="sc_chat_form_field_tags_item" data-tag-prompt="' + tag.prompt + '">' + tag.title + '</a>';
											} ).join('')
									+ '</span>'
								+ '</div>';
						if ( settings.tags_position == 'before' ) {
							print( tags );
							tags = '';
						}
					}

					// Prompt field
					#><div class="sc_chat_form_field sc_chat_form_field_prompt">
						<div class="sc_chat_form_field_inner">
							<input type="text" value="{{ settings.prompt }}" class="sc_chat_form_field_prompt_text"<#
								if ( settings.placeholder_text !== '#' ) {
									#> placeholder="{{{ settings.placeholder_text || '<?php esc_attr_e( 'Type your message ...', 'trx_addons' ); ?>' }}}"<#
								}
							#>>
							<a href="#" class="sc_chat_form_field_prompt_button<#
							if ( ! settings.prompt ) print( ' sc_chat_form_field_prompt_button_disabled' );
							print( settings.button_image.url || ( settings.button_icon && ! trx_addons_is_off( settings.button_icon ) )
									? ' sc_chat_form_field_prompt_button_with_icon'
									: ' sc_chat_form_field_prompt_button_without_icon'
							);
							#>"><#
								if ( settings.button_image.url ) {
									image_type = trx_addons_get_file_ext( settings.button_image.url );
									if ( image_type == 'svg' ) {
										#><span class="sc_chat_form_field_prompt_button_svg"><#
											print( trx_addons_get_inline_svg( settings.button_image.url, {
												render: function( html ) {
													if ( html ) {
														elementor.$previewContents.find( '#' + id + ' .sc_chat_form_field_prompt_button_svg' ).html( html );
													}
												}
											} ) );
										#></span><#
									} else {
										#><img src="{{ settings.button_image.url }}" alt="{{ settings.button_image.alt }}" class="sc_chat_form_field_prompt_button_image"><#
									}
								} else if ( settings.button_icon && ! trx_addons_is_off( settings.button_icon ) ) {
									#><span class="sc_chat_form_field_prompt_button_icon {{ settings.button_icon }}"></span><#
								}
								if ( settings.button_text !== '#' ) {
									#><span class="sc_chat_form_field_prompt_button_text">{{{ settings.button_text || '<?php esc_html_e('Send', 'trx_addons'); ?>' }}}</span><#
								}
							#></a><#
							// Upload attachments
							if ( settings.allow_attachments ) {
								var add_icon = settings.attachments_add_icon && settings.attachments_add_icon != 'none' ? settings.attachments_add_icon : 'trx_addons_icon-attach';
								#>
								<input type="file" id="sc_chat_form_field_upload_attachments_field" class="sc_chat_form_field_upload_attachments_field"
									accept="<?php echo esc_attr( '.' . join( ',.', trx_addons_sc_chat_get_allowed_attachments() ) ); ?>,audio/*,image/*"
									multiple
								/>
								<label for="sc_chat_form_field_upload_attachments_field" class="sc_chat_form_field_upload_attachments_label {{ add_icon }}"
									title="<?php esc_html_e( 'Select files (documents, pictures or audio) to be attached to the message', 'trx_addons' ); ?>"></label>
								<#
							}
							#>
						</div><#
						// Upload attachments
						if ( settings.allow_attachments ) {
							#>
							<div class="sc_chat_form_field_upload_attachments_list" data-clear-icon="{{ settings.attachments_clear_icon }}"></div>
							<#
						}
					#></div><#

					// Tags (after the prompt field)
					if ( tags ) {
						print( tags );
					}

					// Limits
					if ( settings.show_limits ) {
						#><div class="sc_chat_limits">
							<span class="sc_chat_limits_label"><?php
								esc_html_e( 'Limits per hour (day/week/month/year): XX requests.', 'trx_addons' );
							?></span>
							<span class="sc_chat_limits_value"><?php
								esc_html_e( 'Used: YY requests.', 'trx_addons' );
							?></span>
						</div><#
					}
				#></div>
			</div>
		</div><#

		if ( settings.type != 'popup' ) {
			#><?php $element->sc_show_links('sc_chat'); ?><#
		}

		// Show the button to open a popup
		if ( settings.type == 'popup' ) {
			#><a href="#" class="sc_chat_popup_button"
				data-chat-image="{{{ _.escape( settings.popup_button_image.url ) }}}"
				data-chat-icon="{{{ settings.popup_button_icon && ! trx_addons_is_off( settings.popup_button_icon ) ? settings.popup_button_icon : '' }}}"
				data-chat-opened-image="{{{ settings.popup_button_image.url && settings.popup_button_image_opened.url && settings.popup_button_image.url != settings.popup_button_image_opened.url ? _.escape( settings.popup_button_image_opened.url ) : '' }}}"
				data-chat-opened-icon="{{{ settings.popup_button_icon && ! trx_addons_is_off( settings.popup_button_icon ) && settings.popup_button_icon_opened && ! trx_addons_is_off( settings.popup_button_icon_opened ) && settings.popup_button_icon != settings.popup_button_icon_opened ? settings.popup_button_icon_opened : '' }}}"
			><#
				if ( settings.popup_button_image.url ) {
					image_type = trx_addons_get_file_ext( settings.popup_button_image.url );
					if ( image_type == 'svg' ) {
						#><span class="sc_chat_popup_button_svg"><#
							print( trx_addons_get_inline_svg( settings.popup_button_image.url, {
								render: function( html ) {
									if ( html ) {
										elementor.$previewContents.find( '#' + id + ' .sc_chat_popup_button_svg' ).html( html );
									}
								}
							} ) );
						#></span><#
					} else {
						#><img src="{{ settings.popup_button_image.url }}" alt="{{ settings.popup_button_image.alt }}" class="sc_chat_popup_button_image"><#
					}
				} else {
					#><span class="sc_chat_popup_button_icon {{{ settings.popup_button_icon && ! trx_addons_is_off( settings.popup_button_icon ) ? settings.popup_button_icon : 'trx_addons_icon-chat-empty' }}}"></span><#
				}
			#></a><#
		}

	#></div><#

	settings = trx_addons_elm_restore_global_params( settings );
}
#>