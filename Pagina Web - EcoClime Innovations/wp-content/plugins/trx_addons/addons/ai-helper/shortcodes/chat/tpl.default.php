<?php
/**
 * The style "default" of the Chat
 *
 * @package ThemeREX Addons
 * @since v2.22.0
 */

use TrxAddons\AiHelper\Lists;

$args = get_query_var('trx_addons_args_sc_chat');

// if ( $args['type'] == 'popup' ) {
// 	ob_start();
// }

do_action( 'trx_addons_action_sc_chat_before', $args );

?><div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> 
	class="sc_chat sc_chat_<?php
		echo esc_attr( $args['type'] );
		if ( ! empty( $args['open_on_load'] ) ) echo ' sc_chat_open_on_load';
		if ( ! empty( $args['allow_attachments'] ) ) echo ' sc_chat_with_attachments';
		if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] );
		?>"<?php
	if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
	trx_addons_sc_show_attributes( 'sc_chat', $args, 'sc_wrapper' );
	?>><?php

	if ( $args['type'] != 'popup' ) {
		trx_addons_sc_show_titles('sc_chat', $args);
	}

	do_action( 'trx_addons_action_sc_chat_before_content', $args );

	?><div class="sc_chat_content sc_item_content"<?php trx_addons_sc_show_attributes( 'sc_chat', $args, 'sc_items_wrapper' ); ?>>
		<div class="sc_chat_form"
			data-chat-limit-exceed="<?php echo esc_attr( trx_addons_get_option( "ai_helper_sc_chat_limit_alert" . ( ! empty( $args['premium'] ) ? '_premium' : '' ) ) ); ?>"
			data-chat-save-history="<?php echo esc_attr( ! empty( $args['save_history'] ) ? 1 : 0 ); ?>"
			data-chat-settings="<?php
				echo esc_attr( trx_addons_encode_settings( array(
					'premium' => ! empty( $args['premium'] ) ? 1 : 0,
					'model' => ! empty( $args['model'] ) ? $args['model'] : '',
					'nolimits' => ! empty( $args['nolimits'] ) ? 1 : 0,
					'flowise_override' => ! empty( $args['flowise_override'] ) ? $args['flowise_override'] : '',
					'system_prompt' => ! empty( $args['system_prompt'] ) ? $args['system_prompt'] : '',
					'temperature' => ! empty( $args['temperature'] ) ? (float)$args['temperature'] : 0,
					'max_tokens' => ! empty( $args['max_tokens'] ) ? (int)$args['max_tokens'] : 0,
				) ) );
			?>"
			data-chat-style="<?php
				echo esc_attr( json_encode( array(
					'assistant_icon' => ! empty( $args['assistant_icon'] ) && ! trx_addons_is_off( $args['assistant_icon'] ) ? $args['assistant_icon'] : '',
					'assistant_image' => ! empty( $args['assistant_image'] ) ? esc_url( trx_addons_get_attachment_url( $args['assistant_image'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size( 'tiny' ), 'sc_chat_assistant' ) ) ) : '',
					'assistant_svg' => trx_addons_get_file_ext( $args['assistant_image'] ) == 'svg' ? trx_addons_get_svg_from_file( $args['assistant_image'] ) : '',
					'assistant_name' => ! empty( $args['assistant_name'] ) ? $args['assistant_name'] : __( 'Assistant', 'trx_addons' ),
					'user_icon' => ! empty( $args['user_icon'] ) && ! trx_addons_is_off( $args['user_icon'] ) ? $args['user_icon'] : '',
					'user_image' => ! empty( $args['user_image'] ) ? esc_url( trx_addons_get_attachment_url( $args['user_image'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size( 'tiny' ), 'sc_chat_user' ) ) ) : '',
					'user_svg' => trx_addons_get_file_ext( $args['user_image'] ) == 'svg' ? trx_addons_get_svg_from_file( $args['user_image'] ) : '',
					'user_name' => ! empty( $args['user_name'] ) ? $args['user_name'] : __( 'User', 'trx_addons' ),
				) ) );
			?>"
		>
			<div class="sc_chat_form_inner">
				<?php
				$trx_addons_ai_helper_prompt_id = 'sc_chat_form_field_prompt_' . mt_rand();

				// Title
				?>
				<label for="<?php echo esc_attr( $trx_addons_ai_helper_prompt_id ); ?>" class="sc_chat_form_field_prompt_label">
					<span class="sc_chat_form_title"><?php
						if ( ! empty( $args['title_image'] ) ) {
							$icon_type = trx_addons_get_file_ext( $args['title_image'] );
							if ( $icon_type == 'svg' ) {
								?><span class="sc_chat_form_title_svg"><?php
									trx_addons_show_layout( trx_addons_get_svg_from_file( $args['title_image'] ) );
								?></span><?php
							} else {
								?><img src="<?php echo esc_url( trx_addons_get_attachment_url( $args['title_image'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size( 'tiny' ), 'sc_chat_title' ) ) ); ?>"
										alt="<?php esc_attr_e( 'Chat title icon', 'trx_addons' ); ?>"
										class="sc_chat_form_title_image"><?php
							}
						} else if ( ! empty( $args['title_icon'] ) && ! trx_addons_is_off( $args['title_icon'] ) ) {
							?><span class="sc_chat_form_title_icon <?php echo esc_attr( $args['title_icon'] ); ?>"></span><?php
						}
						if ( isset( $args['title_text'] ) && $args['title_text'] != '#' ) {
							?><span class="sc_chat_form_title_text"><?php
								if ( ! empty( $args['title_text'] ) ) {
									echo esc_html( $args['title_text'] );
								} else {
									esc_html_e( 'How can I help you?', 'trx_addons' );
								}
							?></span><?php
						}
					?></span><?php
					?><a href="#" class="sc_chat_form_start_new trx_addons_hidden"><?php
						if ( isset( $args['new_chat_text'] ) && $args['new_chat_text'] != '#' ) {
							?><span class="sc_chat_form_start_new_text"><?php
								if ( ! empty( $args['new_chat_text'] ) ) {
									echo esc_html( $args['new_chat_text'] );
								} else {
									esc_html_e( 'New chat', 'trx_addons' );
								}
							?></span><?php
						}
						if ( ! empty( $args['new_chat_image'] ) ) {
							$icon_type = trx_addons_get_file_ext( $args['new_chat_image'] );
							if ( $icon_type == 'svg' ) {
								?><span class="sc_chat_form_start_new_svg" title="<?php esc_attr_e( 'New chat', 'trx_addons' ); ?>"><?php
									trx_addons_show_layout( trx_addons_get_svg_from_file( $args['new_chat_image'] ) );
								?></span><?php
							} else {
								?><img src="<?php echo esc_url( trx_addons_get_attachment_url( $args['new_chat_image'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size( 'tiny' ), 'sc_chat_start_new' ) ) ); ?>"
										alt="<?php esc_attr_e( 'New chat', 'trx_addons' ); ?>"
										title="<?php esc_attr_e( 'New chat', 'trx_addons' ); ?>"
										class="sc_chat_form_start_new_image"><?php
							}
						} else if ( ! empty( $args['new_chat_icon'] ) && ! trx_addons_is_off( $args['new_chat_icon'] ) ) {
							?><span class="sc_chat_form_start_new_icon <?php echo esc_attr( $args['new_chat_icon'] ); ?>" title="<?php esc_attr_e( 'New chat', 'trx_addons' ); ?>"></span><?php
						}
				?></a></label><?php

				// Chat messages
				?><div class="sc_chat_result">
					<ul class="sc_chat_list"></ul>
				</div><?php

				// Tags (before the prompt field)
				$tags = '';
				if ( ! empty( $args['tags'] ) && is_array( $args['tags'] ) && count( $args['tags'] ) > 0 && ! empty( $args['tags'][0]['title'] ) && ! empty( $args['tags'][0]['prompt'] ) && ! empty( 'tags_position' ) && ! trx_addons_is_off( $args['tags_position'] ) ) {
					if ( $args['tags_position'] == 'after' ) {
						ob_start();
					}
					?><div class="sc_chat_form_field sc_chat_form_field_tags sc_chat_form_field_tags_<?php echo esc_attr( $args['tags_position'] ); ?>"><?php
						if ( ! empty( $args['tags_label'] ) ) {
							?><span class="sc_chat_form_field_tags_label"><?php echo esc_html( $args['tags_label'] ); ?></span><?php
						}
						?><span class="sc_chat_form_field_tags_list"><?php
							foreach ( $args['tags'] as $tag ) {
								?><a href="#" class="sc_chat_form_field_tags_item" data-tag-prompt="<?php echo esc_attr( $tag['prompt'] ); ?>"><?php echo esc_html( $tag['title'] ); ?></a><?php
							}
						?></span><?php
					?></div><?php
					if ( $args['tags_position'] == 'after' ) {
						$tags = ob_get_contents();
						ob_end_clean();
					}
				}

				// Prompt
				?><div class="sc_chat_form_field sc_chat_form_field_prompt">
					<div class="sc_chat_form_field_inner">
						<input type="text"
							id="<?php echo esc_attr( $trx_addons_ai_helper_prompt_id ); ?>"
							class="sc_chat_form_field_prompt_text"
							value="<?php echo esc_attr( $args['prompt'] ); ?>"
							placeholder="<?php
								if ( isset( $args['placeholder_text'] ) && $args['placeholder_text'] != '#' ) {
									if ( ! empty( $args['placeholder_text'] ) ) {
										echo esc_attr( $args['placeholder_text'] );
									} else {
										esc_attr_e( 'Type your message ...', 'trx_addons' );
									}
								}
							?>"
						>
						<a href="#" class="sc_chat_form_field_prompt_button<?php
							if ( empty( $args['prompt'] ) ) echo ' sc_chat_form_field_prompt_button_disabled';
							echo ! empty( $args['button_image'] ) || ( ! empty( $args['button_icon'] ) && ! trx_addons_is_off( $args['button_icon'] ) )
									? ' sc_chat_form_field_prompt_button_with_icon'
									: ' sc_chat_form_field_prompt_button_without_icon';
						?>"><?php
							if ( ! empty( $args['button_image'] ) ) {
								$icon_type = trx_addons_get_file_ext( $args['button_image'] );
								if ( $icon_type == 'svg' ) {
									?><span class="sc_chat_form_field_prompt_button_svg"><?php
										trx_addons_show_layout( trx_addons_get_svg_from_file( $args['button_image'] ) );
									?></span><?php
								} else {
									?><img src="<?php echo esc_url( trx_addons_get_attachment_url( $args['button_image'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size( 'tiny' ), 'sc_chat_field_prompt_button' ) ) ); ?>"
											alt="<?php esc_attr_e( 'New chat icon', 'trx_addons' ); ?>"
											class="sc_chat_form_field_prompt_button_image"><?php
								}
							} else if ( ! empty( $args['button_icon'] ) && ! trx_addons_is_off( $args['button_icon'] ) ) {
								?><span class="sc_chat_form_field_prompt_button_icon <?php echo esc_attr( $args['button_icon'] ); ?>"></span><?php
							}
							if ( isset( $args['button_text'] ) && $args['button_text'] != '#' ) {
								?><span class="sc_chat_form_field_prompt_button_text"><?php
									if ( ! empty( $args['button_text'] ) ) {
										echo esc_html( $args['button_text'] );
									} else {
										esc_html_e('Send', 'trx_addons');
									}
								?></span><?php
							}
						?></a><?php
						// Upload attachments
						if ( ! empty( $args['allow_attachments'] ) ) {
							$add_icon = ! empty( $args['attachments_add_icon'] ) && ! trx_addons_is_off( $args['attachments_add_icon'] ) ? $args['attachments_add_icon'] : 'trx_addons_icon-attach';

							?>
							<input type="file" id="sc_chat_form_field_upload_attachments_field" class="sc_chat_form_field_upload_attachments_field"
							accept="<?php echo esc_attr( '.' . join( ',.', trx_addons_sc_chat_get_allowed_attachments() ) ); ?>,audio/*,image/*"
							multiple
							/>
							<label for="sc_chat_form_field_upload_attachments_field" class="sc_chat_form_field_upload_attachments_label <?php echo esc_attr( $add_icon ); ?>"
								title="<?php esc_html_e( 'Select files (documents, pictures or audio) to be attached to the message', 'trx_addons' ); ?>"></label>
							<?php
						}
						?>
					</div><?php
					// Attachments list
					if ( ! empty( $args['allow_attachments'] ) ) {
						?>
						<div class="sc_chat_form_field_upload_attachments_list"
							data-clear-icon="<?php echo ! empty( $args['attachments_clear_icon'] ) ? esc_attr( $args['attachments_clear_icon'] ) : 'trx_addons_icon-cancel-2'; ?>"></div>
						<?php
					}
				?></div><?php

				// Tags (after the prompt field)
				if ( ! empty( $tags ) ) {
					trx_addons_show_layout( $tags );
				}

				// Limits
				if ( ! empty( $args['show_limits'] ) ) {
					$premium = ! empty( $args['premium'] ) && (int)$args['premium'] == 1;
					$suffix = $premium ? '_premium' : '';
					$limits = (int)trx_addons_get_option( "ai_helper_sc_chat_limits{$suffix}" ) > 0;
					if ( $limits ) {
						$generated = 0;
						if ( $premium ) {
							$user_id = get_current_user_id();
							$user_level = apply_filters( 'trx_addons_filter_sc_chat_user_level', $user_id > 0 ? 'default' : '', $user_id );
							if ( ! empty( $user_level ) ) {
								$levels = trx_addons_get_option( "ai_helper_sc_chat_levels_premium" );
								$level_idx = trx_addons_array_search( $levels, 'level', $user_level );
								$user_limit = $level_idx !== false ? $levels[ $level_idx ] : false;
								if ( isset( $user_limit['limit'] ) && trim( $user_limit['limit'] ) !== '' ) {
									$generated = trx_addons_sc_chat_get_total_generated( $user_limit['per'], $suffix, $user_id );
								}
							}
						}
						if ( ! $premium || empty( $user_level ) || ! isset( $user_limit['limit'] ) || trim( $user_limit['limit'] ) === '' ) {
							$generated = trx_addons_sc_chat_get_total_generated( 'hour', $suffix );
							$user_limit = array(
								'limit' => (int)trx_addons_get_option( "ai_helper_sc_chat_limit_per_hour{$suffix}" ),
								'requests' => (int)trx_addons_get_option( "ai_helper_sc_chat_limit_per_visitor{$suffix}" ),
								'per' => 'hour'
							);
						}
						if ( isset( $user_limit['limit'] ) && trim( $user_limit['limit'] ) !== '' ) {
							?><div class="sc_chat_limits">
								<span class="sc_chat_limits_total"><?php
									$periods = Lists::get_list_periods();
									echo wp_kses( sprintf(
														__( 'Limits%s: %s%s.', 'trx_addons' ),
														! empty( $periods[ $user_limit['per'] ] ) ? ' ' . sprintf( __( 'per %s', 'trx_addons' ), strtolower( $periods[ $user_limit['per'] ] ) ) : '',
														sprintf( __( '%s requests', 'trx_addons' ), '<span class="sc_chat_limits_total_value">' . (int)$user_limit['limit'] . '</span>' ),
														! empty( $user_limit['requests'] ) ? ' ' . sprintf( __( ' for all visitors and up to %s requests from a single visitor', 'trx_addons' ), '<span class="sc_chat_limits_total_requests">' . (int)$user_limit['requests'] . '</span>' ) : '',
													),
													'trx_addons_kses_content'
												);
								?></span>
								<span class="sc_chat_limits_used"><?php
									echo wp_kses( sprintf(
														__( 'Used: %s requests%s.', 'trx_addons' ),
														'<span class="sc_chat_limits_used_value">' . min( $generated, (int)$user_limit['limit'] )  . '</span>',
														! empty( $user_limit['requests'] ) ? ' ' . sprintf( __( 'from all visitors and %s requests from the current user', 'trx_addons' ), '<span class="sc_chat_limits_used_requests">' . (int)trx_addons_get_value_gpc( 'trx_addons_ai_helper_chat_count' ) . '</span>' ) : '',
													),
													'trx_addons_kses_content'
												);
								?></span>
							</div><?php
						}
					}
				}
				?><div class="sc_chat_message">
					<div class="sc_chat_message_inner"></div>
					<a href="#" class="sc_chat_message_close trx_addons_button_close" title="<?php esc_html_e( 'Close', 'trx_addons' ); ?>"><span class="trx_addons_button_close_icon"></span></a>
				</div>
			</div>
		</div>
	</div>

	<?php
	do_action( 'trx_addons_action_sc_chat_after_content', $args );

	if ( $args['type'] != 'popup' ) {
		trx_addons_sc_show_links('sc_chat', $args);
	}

	// Show the button to open a popup
	if ( $args['type'] == 'popup' ) {
		?><a href="#" class="sc_chat_popup_button"
			data-chat-image="<?php echo ! empty( $args['popup_button_image'] ) ? esc_attr( trx_addons_get_attachment_url( $args['popup_button_image'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size( 'tiny' ), 'sc_chat_popup_button' ) ) ) : ''; ?>"
			data-chat-svg="<?php echo ! empty( $args['popup_button_image'] ) && trx_addons_get_file_ext( $args['popup_button_image'] ) == 'svg' ? esc_attr( trx_addons_get_svg_from_file( $args['popup_button_image'] ) ) : ''; ?>"
			data-chat-icon="<?php echo ! empty( $args['popup_button_icon'] ) && ! trx_addons_is_off( $args['popup_button_icon'] ) ? $args['popup_button_icon'] : ''; ?>"
			data-chat-opened-image="<?php echo ! empty( $args['popup_button_image'] ) && ! empty( $args['popup_button_image_opened'] ) && $args['popup_button_image'] != $args['popup_button_image_opened'] ? esc_attr( trx_addons_get_attachment_url( $args['popup_button_image_opened'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size( 'tiny' ), 'sc_chat_popup_button' ) ) ) : ''; ?>"
			data-chat-opened-svg="<?php echo ! empty( $args['popup_button_image'] ) && trx_addons_get_file_ext( $args['popup_button_image'] ) == 'svg' && ! empty( $args['popup_button_image_opened'] ) && trx_addons_get_file_ext( $args['popup_button_image_opened'] ) == 'svg' && $args['popup_button_image'] != $args['popup_button_image_opened'] ? esc_attr( trx_addons_get_svg_from_file( $args['popup_button_image_opened'] ) ) : ''; ?>"
			data-chat-opened-icon="<?php echo ! empty( $args['popup_button_icon'] ) && ! trx_addons_is_off( $args['popup_button_icon'] ) && ! empty( $args['popup_button_icon_opened'] ) && ! trx_addons_is_off( $args['popup_button_icon_opened'] ) && $args['popup_button_icon'] != $args['popup_button_icon_opened'] ? $args['popup_button_icon_opened'] : ''; ?>"
		><?php
			if ( ! empty( $args['popup_button_image'] ) ) {
				$icon_type = trx_addons_get_file_ext( $args['popup_button_image'] );
				if ( $icon_type == 'svg' ) {
					?><span class="sc_chat_popup_button_svg"><?php
						trx_addons_show_layout( trx_addons_get_svg_from_file( $args['popup_button_image'] ) );
					?></span><?php
				} else {
					?><img src="<?php echo esc_url( trx_addons_get_attachment_url( $args['popup_button_image'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size( 'tiny' ), 'sc_chat_popup_button' ) ) ); ?>"
							alt="<?php esc_attr_e( 'Open chat icon', 'trx_addons' ); ?>"
							class="sc_chat_popup_button_image"><?php
				}
			} else {
				?><span class="sc_chat_popup_button_icon <?php echo ! empty( $args['popup_button_icon'] ) && ! trx_addons_is_off( $args['popup_button_icon'] ) ? esc_attr( $args['popup_button_icon'] ) : 'trx_addons_icon-chat-empty'; ?>"></span><?php
			}
		?></a><?php
	}
	
?></div><?php

do_action( 'trx_addons_action_sc_chat_after', $args );

// if ( $args['type'] == 'popup' ) {

// 	// Get the output buffer
// 	$output = ob_get_contents();
// 	ob_end_clean();

// 	// Add the output to the inline content (it will be shown before the body close tag)
// 	trx_addons_add_inline_html( $output );
// }
