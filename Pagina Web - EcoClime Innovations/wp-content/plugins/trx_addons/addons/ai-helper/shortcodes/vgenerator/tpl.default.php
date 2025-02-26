<?php
/**
 * The style "default" of the IGenerator
 *
 * @package ThemeREX Addons
 * @since v2.20.2
 */

use TrxAddons\AiHelper\Lists;
use TrxAddons\AiHelper\Utils;

$args = get_query_var('trx_addons_args_sc_vgenerator');

$models = Lists::get_list_ai_video_models();

if ( count( $models ) > 0 ) {

	$aspect_ratios = Lists::get_list_ai_video_ar();
	$resolutions = Lists::get_list_ai_video_resolutions();
	$durations = Lists::get_list_ai_video_durations();

	?><div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> 
		class="sc_vgenerator sc_vgenerator_<?php
			echo esc_attr( $args['type'] );
			if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] );
			?>"<?php
		if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
		trx_addons_sc_show_attributes( 'sc_vgenerator', $args, 'sc_wrapper' );
		?>><?php

		trx_addons_sc_show_titles('sc_vgenerator', $args);

		?><div class="sc_vgenerator_content sc_item_content"<?php trx_addons_sc_show_attributes( 'sc_vgenerator', $args, 'sc_items_wrapper' ); ?>>
			<div class="sc_vgenerator_form <?php
				echo esc_attr( str_replace( array( 'flex-start', 'flex-end' ), array( 'left', 'right' ), trx_addons_get_responsive_classes( 'sc_vgenerator_form_align_', $args, 'align', '' ) ) );
				?>"
				data-vgenerator-default-model="<?php echo esc_attr( $args['model'] ); ?>"
				data-vgenerator-demo-video="<?php echo ! empty( $args['demo_video'] ) && ! empty( $args['demo_video']['url'] ) ? '1' : ''; ?>"
				data-vgenerator-limit-exceed="<?php echo esc_attr( trx_addons_get_option( "ai_helper_sc_vgenerator_limit_alert" . ( ! empty( $args['premium'] ) ? '_premium' : '' ) ) ); ?>"
				data-vgenerator-settings="<?php
					echo esc_attr( trx_addons_encode_settings( array(
						'model' => $args['model'],
						'premium' => ! empty( $args['premium'] ) ? 1 : 0,
						'demo_video' => $args['demo_video'],
						'show_download' => ! empty( $args['show_download'] ) ? 1 : 0,
						'show_prompt_translated' => ! empty( $args['show_prompt_translated'] ) ? 1 : 0,
                        'system_prompt' => trim( $args['system_prompt'] ),
						'aspect_ratio' => $args['aspect_ratio'],
						// 'keyframes_frame0' => ( empty( $args['show_upload_frame0'] ) && ! empty( $args['keyframes_frame0'] ) ) ? $args['keyframes_frame0']['url'] : '',
						// 'keyframes_frame1' => ( empty( $args['show_upload_frame1'] ) && ! empty( $args['keyframes_frame1'] ) ) ? $args['keyframes_frame1']['url'] : '',
						'resolution' => $args['resolution'],
						'duration' => $args['duration'],
						'demo_video' => $args['demo_video'],
						'allow_loop' => $args['allow_loop'],
					) ) );
			?>">
				<div class="sc_vgenerator_form_inner"<?php
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
					<div class="sc_vgenerator_form_field sc_vgenerator_form_field_prompt<?php
						if ( ! empty( $args['show_settings'] ) && (int) $args['show_settings'] > 0 ) {
							echo ' sc_vgenerator_form_field_prompt_with_settings';
						}
					?>">
						<div class="sc_vgenerator_form_field_inner">
							<input type="text"
								class="sc_vgenerator_form_field_prompt_text"
								value="<?php echo esc_attr( $args['prompt'] ); ?>"
								placeholder="<?php
									if ( ! empty( $args['placeholder_text'] ) ) {
										echo esc_attr( $args['placeholder_text'] );
									} else {
										esc_attr_e('Describe what you want or hit a tag below', 'trx_addons');
									}
								?>"
							>
							<a href="#" class="sc_vgenerator_form_field_prompt_button<?php if ( empty( $args['prompt'] ) ) echo ' sc_vgenerator_form_field_prompt_button_disabled'; ?>"><?php
								if ( ! empty( $args['button_text'] ) ) {
									echo esc_html( $args['button_text'] );
								} else {
									esc_html_e('Generate', 'trx_addons');
								}
							?></a>
						</div><?php
						if ( ! empty( $args['show_settings'] ) && (int) $args['show_settings'] > 0 ) {
							?>
							<a href="#" class="sc_vgenerator_form_settings_button trx_addons_icon-sliders"></a>
							<div class="sc_vgenerator_form_settings">
								<?php
								// Model
								if ( is_array( $models ) ) {
									?><div class="sc_vgenerator_form_settings_field sc_vgenerator_form_settings_field_model">
										<label for="sc_vgenerator_form_settings_field_model"><?php esc_html_e('Model:', 'trx_addons'); ?></label>
										<select name="sc_vgenerator_form_settings_field_model" id="sc_vgenerator_form_settings_field_model"><?php
											$group = false;
											foreach ( $models as $model => $title ) {
												if ( substr( $model, -2 ) == '/-' || substr( $title, 0, 2 ) == '\\-' ) {
													if ( $group ) {
														?></optgroup><?php
													}
													$group = true;
													$title = substr( $title, 2 );
													?><optgroup label="<?php echo esc_attr( $title ); ?>"><?php
												} else {
													?><option value="<?php echo esc_attr( $model ); ?>"<?php
														if ( ! empty( $args['model'] ) && $args['model'] == $model ) {
															echo ' selected="selected"';
														}
													?>><?php
														echo esc_html( $title );
													?></option><?php
												}
											}
											if ( $group ) {
												?></optgroup><?php
											}
										?></select>
									</div><?php
								}

								//Aspect Ratio
								if ( is_array( $aspect_ratios ) ) {
									?><div class="sc_vgenerator_form_settings_field sc_vgenerator_form_settings_field_aspect_ratio">
										<label for="sc_vgenerator_form_settings_field_aspect_ratio"><?php esc_html_e( 'Aspect ratio:', 'trx_addons' ); ?></label>
										<select name="sc_vgenerator_form_settings_field_aspect_ratio" id="sc_vgenerator_form_settings_field_aspect_ratio"><?php
											foreach ( $aspect_ratios as $aspect_ratio => $title ) {
												?><option value="<?php echo esc_attr( $aspect_ratio ); ?>"<?php
													if ( ! empty( $args['aspect_ratio'] ) && $args['aspect_ratio'] == $aspect_ratio ) {
														echo ' selected="selected"';
													}
												?>><?php
													esc_html_e( $title );
												?></option><?php
											}
										?></select>
									</div><?php
								}

								// Resolution
								if ( is_array( $resolutions ) ) {
                                	?><div class="sc_vgenerator_form_settings_field sc_vgenerator_form_settings_field_resolution<?php echo ( ! empty( $args['model'] ) && ! in_array( $args['model'], Lists::get_list_models_for_access_ai_video_resolution() ) ) ? ' trx_addons_hidden' : ''; ?>">
										<label for="sc_vgenerator_form_settings_field_resolution"><?php esc_html_e( 'Resolution:', 'trx_addons' ); ?></label>
										<select name="sc_vgenerator_form_settings_field_resolution" id="sc_vgenerator_form_settings_field_resolution"><?php
											foreach ( $resolutions as $resolution => $title ) {
												?><option value="<?php echo esc_attr( $resolution ); ?>"<?php
													if ( ! empty( $args['resolution'] ) && $args['resolution'] == $resolution ) {
														echo ' selected="selected"';
													}
												?>><?php
													esc_html_e( $title );
												?></option><?php
											}
										?></select>
									</div><?php
								}

								// Duration
								if ( is_array( $durations ) ) {
									?><div class="sc_vgenerator_form_settings_field sc_vgenerator_form_settings_field_duration<?php echo ( ! empty( $args['model'] ) && ! in_array( $args['model'], Lists::get_list_models_for_access_ai_video_duration() ) ) ? ' trx_addons_hidden' : ''; ?>">
										<label for="sc_vgenerator_form_settings_field_duration"><?php esc_html_e( 'Duration:', 'trx_addons' ); ?></label>
										<select name="sc_vgenerator_form_settings_field_duration" id="sc_vgenerator_form_settings_field_duration"><?php
											foreach ( $durations as $duration => $title ) {
												?><option value="<?php echo esc_attr( $duration ); ?>"<?php
													if ( ! empty( $args['duration'] ) && $args['duration'] == $duration ) {
														echo ' selected="selected"';
													}
												?>><?php
													esc_html_e( $title );
												?></option><?php
											}
										?></select>
									</div><?php
								}

                            ?></div><?php
						}
					?></div><?php
					if ( empty( $args['allow_loop'] ) && ( ! empty( $args['show_upload_frame0'] ) || ! empty( $args['show_upload_frame1'] ) ) ) {
						?><div class="sc_vgenerator_form_field sc_vgenerator_form_field_upload_keyframe_wrap<?php echo ( ! empty( $args['model'] ) && ! in_array( $args['model'], Lists::get_list_models_for_access_ai_video_keyframes() ) ) ? ' trx_addons_hidden' : ''; ?>"><?php
							if ( ! empty( $args['show_upload_frame0'] ) ) {
								$decorated = apply_filters( 'trx_addons_filter_sc_vgenerator_decorate_upload', true );
								?><div class="sc_vgenerator_form_field sc_vgenerator_form_field_upload_start_keyframe<?php echo ( ! empty( $args['model'] ) && ! in_array( $args['model'], Lists::get_list_models_for_access_ai_video_keyframes() ) ) ? ' trx_addons_hidden' : ''; ?>">
									<div class="sc_vgenerator_form_field_inner">
										<label for="sc_vgenerator_form_field_upload_keyframe_field"><?php esc_html_e('Upload start keyframe (optional):', 'trx_addons'); ?></label><?php
										if ( $decorated ) {
											?>
											<div class="sc_vgenerator_form_field_upload_keyframe_decorator theme_form_field_text">
												<span class="sc_vgenerator_form_field_upload_keyframe_text theme_form_field_placeholder"><?php esc_html_e( "Image is not selected", 'trx_addons' ); ?></span>
												<span class="sc_vgenerator_form_field_upload_keyframe_button trx_addons_icon-upload"><?php esc_html_e( "Browse", 'trx_addons' ); ?></span>
											<?php
										}
										?><input type="file"
												id="sc_vgenerator_form_field_upload_start_keyframe_field"
												class="sc_vgenerator_form_field_upload_start_keyframe_field sc_vgenerator_form_field_upload_keyframe_field"
												data-text-placeholder="<?php esc_html_e( "Image is not selected", 'trx_addons' ); ?>"
												placeholder="<?php esc_attr_e( "Select an image to make variations", 'trx_addons' ); ?>"
											><?php
										if ( $decorated ) {
											?></div><?php
										}
									?></div>
								</div><?php
							}
							if ( ! empty( $args['show_upload_frame1'] ) ) {
								$decorated = apply_filters( 'trx_addons_filter_sc_vgenerator_decorate_upload', true );
								?><div class="sc_vgenerator_form_field sc_vgenerator_form_field_upload_end_keyframe<?php echo ( ! empty( $args['model'] ) && ! in_array( $args['model'], Lists::get_list_models_for_access_ai_video_keyframes() ) ) ? ' trx_addons_hidden' : ''; ?>">
									<div class="sc_vgenerator_form_field_inner">
										<label for="sc_vgenerator_form_field_upload_keyframe_field"><?php esc_html_e('Upload end keyframe (optional):', 'trx_addons'); ?></label><?php
										if ( $decorated ) {
											?>
											<div class="sc_vgenerator_form_field_upload_keyframe_decorator theme_form_field_text">
												<span class="sc_vgenerator_form_field_upload_keyframe_text theme_form_field_placeholder"><?php esc_html_e( "Image is not selected", 'trx_addons' ); ?></span>
												<span class="sc_vgenerator_form_field_upload_keyframe_button trx_addons_icon-upload"><?php esc_html_e( "Browse", 'trx_addons' ); ?></span>
											<?php
										}
										?><input type="file"
												id="sc_vgenerator_form_field_upload_end_keyframe_field"
												class="sc_vgenerator_form_field_upload_end_keyframe_field sc_vgenerator_form_field_upload_keyframe_field"
												data-text-placeholder="<?php esc_html_e( "Image is not selected", 'trx_addons' ); ?>"
												placeholder="<?php esc_attr_e( "Select an image to make variations", 'trx_addons' ); ?>"
											><?php
										if ( $decorated ) {
											?></div><?php
										}
									?></div>
								</div><?php
							}
						?></div><?php
					}
					if ( ! empty( $args['tags'] ) && is_array( $args['tags'] ) && count( $args['tags'] ) > 0 && ! empty( $args['tags'][0]['title'] ) ) {
						?><div class="sc_vgenerator_form_field sc_vgenerator_form_field_tags"><?php
							if ( ! empty( $args['tags_label'] ) ) {
								?><span class="sc_vgenerator_form_field_tags_label"><?php echo esc_html( $args['tags_label'] ); ?></span><?php
							}
							?><span class="sc_vgenerator_form_field_tags_list"><?php
								foreach ( $args['tags'] as $tag ) {
									?><a href="#" class="sc_vgenerator_form_field_tags_item" data-tag-prompt="<?php echo esc_attr( $tag['prompt'] ); ?>"><?php echo esc_html( $tag['title'] ); ?></a><?php
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
					$limits = (int)trx_addons_get_option( "ai_helper_sc_vgenerator_limits{$suffix}" ) > 0;
					if ( $limits ) {
						$generated = 0;
						if ( $premium ) {
							$user_id = get_current_user_id();
							$user_level = apply_filters( 'trx_addons_filter_sc_vgenerator_user_level', $user_id > 0 ? 'default' : '', $user_id );
							if ( ! empty( $user_level ) ) {
								$levels = trx_addons_get_option( "ai_helper_sc_vgenerator_levels_premium" );
								$level_idx = trx_addons_array_search( $levels, 'level', $user_level );
								$user_limit = $level_idx !== false ? $levels[ $level_idx ] : false;
								if ( isset( $user_limit['limit'] ) && trim( $user_limit['limit'] ) !== '' ) {
									$generated = trx_addons_sc_vgenerator_get_total_generated( $user_limit['per'], $suffix, $user_id );
								}
							}
						}
						if ( ! $premium || empty( $user_level ) || ! isset( $user_limit['limit'] ) || trim( $user_limit['limit'] ) === '' ) {
							$generated = trx_addons_sc_vgenerator_get_total_generated( 'hour', $suffix );
							$user_limit = array(
								'limit' => (int)trx_addons_get_option( "ai_helper_sc_vgenerator_limit_per_hour{$suffix}" ),
								'requests' => (int)trx_addons_get_option( "ai_helper_sc_vgenerator_limit_per_visitor{$suffix}" ),
								'per' => 'hour'
							);
						}
						if ( isset( $user_limit['limit'] ) && trim( $user_limit['limit'] ) !== '' ) {
							?><div class="sc_vgenerator_limits"<?php
								// If a shortcode is called not from Elementor, we need to add the width of the prompt field and alignment
								if ( empty( $args['prompt_width_extra'] ) ) {
									if ( ! empty( $args['prompt_width'] ) && (int)$args['prompt_width'] < 100 ) {
										echo ' style="max-width:' . esc_attr( $args['prompt_width'] ) . '%"';
									}
								}
							?>>
								<span class="sc_vgenerator_limits_total"><?php
									$periods = Lists::get_list_periods();
									echo wp_kses( sprintf(
														__( 'Limits%s: %s%s.', 'trx_addons' ),
														! empty( $periods[ $user_limit['per'] ] ) ? ' ' . sprintf( __( 'per %s', 'trx_addons' ), strtolower( $periods[ $user_limit['per'] ] ) ) : '',
														sprintf( __( '%s videos', 'trx_addons' ), '<span class="sc_vgenerator_limits_total_value">' . (int)$user_limit['limit'] . '</span>' ),
														! empty( $user_limit['requests'] ) ? ' ' . sprintf( __( ' for all visitors and up to %s requests from a single visitor', 'trx_addons' ), '<span class="sc_vgenerator_limits_total_requests">' . (int)$user_limit['requests'] . '</span>' ) : '',
													),
													'trx_addons_kses_content'
												);
								?></span>
								<span class="sc_vgenerator_limits_used"><?php
									echo wp_kses( sprintf(
														__( 'Used: %s videos%s.', 'trx_addons' ),
														'<span class="sc_vgenerator_limits_used_value">' . min( $generated, (int)$user_limit['limit'] )  . '</span>',
														! empty( $user_limit['requests'] ) ? sprintf( __( ', %s requests', 'trx_addons' ), '<span class="sc_vgenerator_limits_used_requests">' . (int)trx_addons_get_value_gpc( 'trx_addons_ai_helper_vgenerator_count' ) . '</span>' ) : '',
													),
													'trx_addons_kses_content'
												);
								?></span>
							</div><?php
						}
					}
				}
				?><div class="sc_vgenerator_message"<?php
					// If a shortcode is called not from Elementor, we need to add the width of the prompt field and alignment
					if ( empty( $args['prompt_width_extra'] ) ) {
						if ( ! empty( $args['prompt_width'] ) && (int)$args['prompt_width'] < 100 ) {
							echo ' style="max-width:' . esc_attr( $args['prompt_width'] ) . '%"';
						}
					}
				?>>
					<div class="sc_vgenerator_message_inner"></div>
					<a href="#" class="sc_vgenerator_message_close trx_addons_button_close" title="<?php esc_html_e( 'Close', 'trx_addons' ); ?>"><span class="trx_addons_button_close_icon"></span></a>
				</div>
			</div>
			<div class="sc_vgenerator_videos"></div>
		</div>

		<?php trx_addons_sc_show_links('sc_vgenerator', $args); ?>

	</div><?php

} else if ( true || trx_addons_is_preview() ) {

	?><div class="sc_vgenerator_error"><?php
		esc_html_e( 'Video Generator: No models available', 'trx_addons' );
	?></div><?php

}