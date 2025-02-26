<?php
/**
 * The style "default" of the mgenerator
 *
 * @package ThemeREX Addons
 * @since v2.31.0
 */

use TrxAddons\AiHelper\Lists;

$args = get_query_var('trx_addons_args_sc_mgenerator');

?><div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> 
	class="sc_mgenerator<?php
		if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] );
		?>"<?php
	if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
	trx_addons_sc_show_attributes( 'sc_mgenerator', $args, 'sc_wrapper' );
?>><?php

	trx_addons_sc_show_titles('sc_mgenerator', $args);

	?><div class="sc_mgenerator_content sc_item_content"<?php trx_addons_sc_show_attributes( 'sc_mgenerator', $args, 'sc_items_wrapper' ); ?>>

		<div class="sc_mgenerator_form <?php
			echo esc_attr( str_replace( array( 'flex-start', 'flex-end' ), array( 'left', 'right' ), trx_addons_get_responsive_classes( 'sc_mgenerator_form_align_', $args, 'align', '' ) ) );
			?>"
			data-mgenerator-demo-music="<?php echo ! empty( $args['demo_music'] ) && ! empty( $args['demo_music'][0]['music']['url'] ) ? '1' : ''; ?>"
			data-mgenerator-limit-exceed="<?php echo esc_attr( trx_addons_get_option( "ai_helper_sc_mgenerator_limit_alert" . ( ! empty( $args['premium'] ) ? '_premium' : '' ) ) ); ?>"
			data-mgenerator-settings="<?php
				echo esc_attr( trx_addons_encode_settings( array(
					'sampling_rate' => $args['sampling_rate'],
					'duration' => $args['duration'],
					'demo_music' => $args['demo_music'],
					'premium' => ! empty( $args['premium'] ) ? 1 : 0,
					'show_download' => ! empty( $args['show_download'] ) ? 1 : 0,
					'show_prompt_translated' => ! empty( $args['show_prompt_translated'] ) ? 1 : 0,
					'show_upload_audio' => ! empty( $args['show_upload_audio'] ) ? 1 : 0,
					'base64' => ! empty( $args['base64'] ) ? 1 : 0,
					'system_prompt' => trim( $args['system_prompt'] ),
				) ) );
		?>">
			<div class="sc_mgenerator_form_inner"<?php
				// If a shortcode is called not from Elementor, we need to add the width of the prompt field and alignment
				if ( empty( $args['prompt_width_extra'] ) ) {
					$css = '';
					if ( ! empty( $args['prompt_width'] ) && (int)$args['prompt_width'] < 100 ) {
						$css = 'width:' . esc_attr( $args['prompt_width'] ) . '%;';
					}
					if ( ! empty( $css ) ) {
						echo ' style="' . esc_attr( $css ) . '"';
					}
				}
			?>>
				<div class="sc_mgenerator_form_field sc_mgenerator_form_field_prompt<?php
					if ( ! empty( $args['show_settings'] ) && (int)$args['show_settings'] > 0 ) {
						echo ' sc_mgenerator_form_field_prompt_with_settings';
					}
				?>">
					<div class="sc_mgenerator_form_field_inner">
						<input type="text"
							class="sc_mgenerator_form_field_prompt_text"
							value="<?php echo esc_attr( $args['prompt'] ); ?>"
							placeholder="<?php
								if ( ! empty( $args['placeholder_text'] ) ) {
									echo esc_attr( $args['placeholder_text'] );
								} else {
									esc_attr_e('Describe what you want or hit a tag below', 'trx_addons');
								}
							?>"
						>
						<a href="#" class="sc_mgenerator_form_field_prompt_button<?php if ( empty( $args['prompt'] ) ) echo ' sc_mgenerator_form_field_disabled'; ?>"><?php
							if ( ! empty( $args['button_text'] ) ) {
								echo esc_html( $args['button_text'] );
							} else {
								esc_html_e('Generate', 'trx_addons');
							}
						?></a>
					</div><?php
					if ( ! empty( $args['show_settings'] ) && (int)$args['show_settings'] > 0 ) {
						?>
						<a href="#" class="sc_mgenerator_form_settings_button trx_addons_icon-sliders"></a>
						<div class="sc_mgenerator_form_settings"><?php

							// Sample Rate (numeric field)
							?><div class="sc_mgenerator_form_settings_field sc_mgenerator_form_settings_field_sampling_rate">
								<label for="sc_mgenerator_form_settings_field_sampling_rate"><?php esc_html_e( 'Sampling Rate (Hz):', 'trx_addons' ); ?></label>
								<div class="sc_mgenerator_form_settings_field_numeric_wrap">
									<input
										type="number"
										name="sc_mgenerator_form_settings_field_sampling_rate"
										id="sc_mgenerator_form_settings_field_sampling_rate"
										min="10000"
										max="48000"
										step="1000"
										value="<?php echo esc_attr( $args['sampling_rate'] ); ?>"
									>
									<div class="sc_mgenerator_form_settings_field_numeric_wrap_buttons">
										<a href="#" class="sc_mgenerator_form_settings_field_numeric_wrap_button sc_mgenerator_form_settings_field_numeric_wrap_button_inc"></a>
										<a href="#" class="sc_mgenerator_form_settings_field_numeric_wrap_button sc_mgenerator_form_settings_field_numeric_wrap_button_dec"></a>
									</div>
								</div>
							</div><?php

							// Duration (sec) (numeric field)
							?><div class="sc_mgenerator_form_settings_field sc_mgenerator_form_settings_field_duration">
								<label for="sc_mgenerator_form_settings_field_duration"><?php esc_html_e( 'Duration (sec):', 'trx_addons'); ?></label>
								<div class="sc_mgenerator_form_settings_field_numeric_wrap">
									<input
										type="number"
										name="sc_mgenerator_form_settings_field_duration"
										id="sc_mgenerator_form_settings_field_duration"
										min="5"
										max="20"
										step="0.1"
										value="<?php echo esc_attr( $args['duration'] ); ?>"
									>
									<div class="sc_mgenerator_form_settings_field_numeric_wrap_buttons">
										<a href="#" class="sc_mgenerator_form_settings_field_numeric_wrap_button sc_mgenerator_form_settings_field_numeric_wrap_button_inc"></a>
										<a href="#" class="sc_mgenerator_form_settings_field_numeric_wrap_button sc_mgenerator_form_settings_field_numeric_wrap_button_dec"></a>
									</div>
								</div>
							</div>
						</div><?php
					}
				?></div><?php

				// Upload the conditioning melody for audio generation
				if ( ! empty( $args['show_upload_audio'] ) ) {
					$decorated = apply_filters( 'trx_addons_filter_sc_mgenerator_decorate_upload', true );
					?><div class="sc_mgenerator_form_field sc_mgenerator_form_field_upload_audio">
						<div class="sc_mgenerator_form_field_inner">
							<label for="sc_mgenerator_form_field_upload_audio_field"><?php esc_html_e( 'Upload the conditioning melody for audio generation (optional):', 'trx_addons' ); ?></label><?php
							if ( $decorated ) {
								?>
								<div class="sc_mgenerator_form_field_upload_audio_decorator theme_form_field_text">
									<span class="sc_mgenerator_form_field_upload_audio_text theme_form_field_placeholder"><?php esc_html_e( "Audio is not selected", 'trx_addons' ); ?></span>
									<span class="sc_mgenerator_form_field_upload_audio_button trx_addons_icon-upload"><?php esc_html_e( "Browse", 'trx_addons' ); ?></span>
								<?php
							}
							?><input type="file" id="sc_mgenerator_form_field_upload_audio_field" class="sc_mgenerator_form_field_upload_audio_field" placeholder="<?php esc_attr_e( "Select the conditioning melody", 'trx_addons' ); ?>"><?php
							if ( $decorated ) {
								?></div><?php
							}
						?></div>
					</div><?php
				}
				if ( ! empty( $args['tags'] ) && is_array( $args['tags'] ) && count( $args['tags'] ) > 0 && ! empty( $args['tags'][0]['title'] ) ) {
					?><div class="sc_mgenerator_form_field sc_mgenerator_form_field_tags"><?php
						if ( ! empty( $args['tags_label'] ) ) {
							?><span class="sc_mgenerator_form_field_tags_label"><?php echo esc_html( $args['tags_label'] ); ?></span><?php
						}
						?><span class="sc_mgenerator_form_field_tags_list"><?php
							foreach ( $args['tags'] as $tag ) {
								?><a href="#" class="sc_mgenerator_form_field_tags_item" data-tag-prompt="<?php echo esc_attr( $tag['prompt'] ); ?>"><?php echo esc_html( $tag['title'] ); ?></a><?php
							}
						?></span><?php
					?></div><?php
				}
			?></div>
			<div class="trx_addons_loading">
			</div><?php
			if ( ! empty( $args['show_limits'] ) ) {
				$premium = ! empty( $args['premium'] ) && (int)$args['premium'] == 1;
				$suffix = $premium ? '_premium' : '';
				$limits = (int)trx_addons_get_option( "ai_helper_sc_mgenerator_limits{$suffix}" ) > 0;
				if ( $limits ) {
					$generated = 0;
					if ( $premium ) {
						$user_id = get_current_user_id();
						$user_level = apply_filters( 'trx_addons_filter_sc_mgenerator_user_level', $user_id > 0 ? 'default' : '', $user_id );
						if ( ! empty( $user_level ) ) {
							$levels = trx_addons_get_option( "ai_helper_sc_mgenerator_levels_premium" );
							$level_idx = trx_addons_array_search( $levels, 'level', $user_level );
							$user_limit = $level_idx !== false ? $levels[ $level_idx ] : false;
							if ( isset( $user_limit['limit'] ) && trim( $user_limit['limit'] ) !== '' ) {
								$generated = trx_addons_sc_mgenerator_get_total_generated( $user_limit['per'], $suffix, $user_id );
							}
						}
					}
					if ( ! $premium || empty( $user_level ) || ! isset( $user_limit['limit'] ) || trim( $user_limit['limit'] ) === '' ) {
						$generated = trx_addons_sc_mgenerator_get_total_generated( 'hour', $suffix );
						$user_limit = array(
							'limit' => (int)trx_addons_get_option( "ai_helper_sc_mgenerator_limit_per_hour{$suffix}" ),
							'requests' => (int)trx_addons_get_option( "ai_helper_sc_mgenerator_limit_per_visitor{$suffix}" ),
							'per' => 'hour'
						);
					}
					if ( isset( $user_limit['limit'] ) && trim( $user_limit['limit'] ) !== '' ) {
						?><div class="sc_mgenerator_limits"<?php
							// If a shortcode is called not from Elementor, we need to add the width of the prompt field and alignment
							if ( empty( $args['prompt_width_extra'] ) ) {
								if ( ! empty( $args['prompt_width'] ) && (int)$args['prompt_width'] < 100 ) {
									echo ' style="max-width:' . esc_attr( $args['prompt_width'] ) . '%"';
								}
							}
						?>>
							<span class="sc_mgenerator_limits_total"><?php
								$periods = Lists::get_list_periods();
								echo wp_kses( sprintf(
													__( 'Limits%s: %s%s.', 'trx_addons' ),
													! empty( $periods[ $user_limit['per'] ] ) ? ' ' . sprintf( __( 'per %s', 'trx_addons' ), strtolower( $periods[ $user_limit['per'] ] ) ) : '',
													sprintf( __( '%s music', 'trx_addons' ), '<span class="sc_mgenerator_limits_total_value">' . (int)$user_limit['limit'] . '</span>' ),
													! empty( $user_limit['requests'] ) ? ' ' . sprintf( __( ' for all visitors and up to %s requests from a single visitor', 'trx_addons' ), '<span class="sc_mgenerator_limits_total_requests">' . (int)$user_limit['requests'] . '</span>' ) : '',
												),
												'trx_addons_kses_content'
											);
							?></span>
							<span class="sc_mgenerator_limits_used"><?php
								echo wp_kses( sprintf(
													__( 'Used: %s music%s.', 'trx_addons' ),
													'<span class="sc_mgenerator_limits_used_value">' . min( $generated, (int)$user_limit['limit'] )  . '</span>',
													! empty( $user_limit['requests'] ) ? sprintf( __( ', %s requests', 'trx_addons' ), '<span class="sc_mgenerator_limits_used_requests">' . (int)trx_addons_get_value_gpc( 'trx_addons_ai_helper_mgenerator_count' ) . '</span>' ) : '',
												),
												'trx_addons_kses_content'
											);
							?></span>
						</div><?php
					}
				}
			}
			?><div class="sc_mgenerator_message"<?php
				// If a shortcode is called not from Elementor, we need to add the width of the prompt field and alignment
				if ( empty( $args['prompt_width_extra'] ) ) {
					if ( ! empty( $args['prompt_width'] ) && (int)$args['prompt_width'] < 100 ) {
						echo ' style="max-width:' . esc_attr( $args['prompt_width'] ) . '%"';
					}
				}
			?>>
				<div class="sc_mgenerator_message_inner"></div>
				<a href="#" class="sc_mgenerator_message_close trx_addons_button_close" title="<?php esc_html_e( 'Close', 'trx_addons' ); ?>"><span class="trx_addons_button_close_icon"></span></a>
			</div>
		</div>
		<div class="sc_mgenerator_music"></div>
	</div>

	<?php trx_addons_sc_show_links('sc_mgenerator', $args); ?>

</div>
