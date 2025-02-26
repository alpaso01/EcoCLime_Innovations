<?php
/**
 * The style "default" of the AGenerator
 *
 * @package ThemeREX Addons
 * @since v2.31.0
 */

use TrxAddons\AiHelper\Lists;
use TrxAddons\AiHelper\Utils;

$args = get_query_var('trx_addons_args_sc_agenerator');

$models = array(
	'tts' => Lists::get_list_ai_audio_models( 'tts'),
	'transcription' => Lists::get_list_ai_audio_models( 'transcription'),
	'translation' => Lists::get_list_ai_audio_models( 'translation'),
	'voice-cover' => Lists::get_list_ai_audio_models( 'voice-cover'),
);
$voices = Lists::get_list_openai_voices();
$languages = Lists::get_list_modelslab_languages();
$emotions = Lists::get_list_modelslab_emotions();

if ( is_array( $models ) && is_array( $models['tts'] ) && count( $models['tts'] ) > 0 ) {

	?><div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> 
		class="sc_agenerator sc_agenerator_<?php
			echo esc_attr( $args['type'] );
			if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] );
			?>"<?php
		if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
		trx_addons_sc_show_attributes( 'sc_agenerator', $args, 'sc_wrapper' );
		?>><?php

		trx_addons_sc_show_titles('sc_agenerator', $args);

		?><div class="sc_agenerator_content sc_item_content"<?php trx_addons_sc_show_attributes( 'sc_agenerator', $args, 'sc_items_wrapper' ); ?>>
			<div class="sc_agenerator_form"
				data-agenerator-default-model="<?php echo esc_attr( $args['model'] ); ?>"
				data-agenerator-demo-audio="<?php echo ! empty( $args['demo_audio'] ) && ! empty( $args['demo_audio'][0]['audio']['url'] ) ? '1' : ''; ?>"
				data-agenerator-limit-exceed="<?php echo esc_attr( trx_addons_get_option( "ai_helper_sc_agenerator_limit_alert" . ( ! empty( $args['premium'] ) ? '_premium' : '' ) ) ); ?>"
				data-agenerator-settings="<?php
					echo esc_attr( trx_addons_encode_settings( array(
						'model' => $args['model'],
						'premium' => ! empty( $args['premium'] ) ? 1 : 0,
						'show_download' => ! empty( $args['show_download'] ) ? 1 : 0,
						'demo_audio' => $args['demo_audio'],
						'base64' => ! empty( $args['base64'] ) ? 1 : 0,
						) ) );
			?>">
				<div class="sc_agenerator_form_inner">
					<div class="sc_agenerator_form_actions">
						<ul class="sc_agenerator_form_actions_list">
							<li class="sc_agenerator_form_actions_item sc_agenerator_form_actions_item_tts sc_agenerator_form_actions_item_active"><a href="#" data-action="tts"><?php esc_html_e( 'Text To Speech', 'trx_addons' ); ?></a></li>
							<li class="sc_agenerator_form_actions_item sc_agenerator_form_actions_item_transcription"><a href="#" data-action="transcription"><?php esc_html_e( 'Speech To Text', 'trx_addons' ); ?></a></li>
							<li class="sc_agenerator_form_actions_item sc_agenerator_form_actions_item_translation"><a href="#" data-action="translation"><?php esc_html_e( 'Translation', 'trx_addons'); ?></a></li>
							<li class="sc_agenerator_form_actions_item sc_agenerator_form_actions_item_voice_cover"><a href="#" data-action="voice-cover"><?php esc_html_e( 'Voice Modification', 'trx_addons'); ?></a></li>
							<li class="sc_agenerator_form_actions_slider"></li>
						</ul>
					</div>
					<div class="sc_agenerator_form_fields"><?php

						// Left block of fields
						?><div class="sc_agenerator_form_fields_left"><?php
							
							// Prompt
							?><div class="sc_agenerator_form_field sc_agenerator_form_field_prompt" data-actions="tts">
								<div class="sc_agenerator_form_field_inner">
									<label for="sc_agenerator_form_field_prompt_text"><?php esc_html_e( 'Text', 'trx_addons' ); ?></label>
									<input type="text"
										class="sc_agenerator_form_field_prompt_text"
										value="<?php echo esc_attr( $args['prompt'] ); ?>"
										placeholder="<?php
											if ( ! empty( $args['placeholder_text'] ) ) {
												echo esc_attr( $args['placeholder_text'] );
											} else {
												esc_attr_e( 'Text to generate an audio file', 'trx_addons' );
											}
										?>"
									>
								</div>
							</div><?php

							// Upload audio
							$decorated = apply_filters( 'trx_addons_filter_sc_agenerator_decorate_upload', true );
							?><div class="sc_agenerator_form_field sc_agenerator_form_field_upload_audio trx_addons_hidden"
								data-actions="transcription,translation,voice-cover"
							>
								<div class="sc_agenerator_form_field_inner">
									<label for="sc_agenerator_form_field_upload_audio_field"><?php esc_html_e( 'Upload Audio', 'trx_addons' ); ?></label><?php
									if ( $decorated ) {
										?>
										<div class="sc_agenerator_form_field_upload_audio_decorator theme_form_field_text">
											<span class="sc_agenerator_form_field_upload_audio_text theme_form_field_placeholder"><?php esc_html_e( 'Audio is not selected', 'trx_addons' ); ?></span>
											<span class="sc_agenerator_form_field_upload_audio_button trx_addons_icon-upload"><?php esc_html_e( 'Browse', 'trx_addons' ); ?></span>
										<?php
									}
									?><input type="file" id="sc_agenerator_form_field_upload_audio_field" class="sc_agenerator_form_field_upload_audio_field" placeholder="<?php esc_attr_e( "Select an audio file", 'trx_addons' ); ?>"><?php
									if ( $decorated ) {
										?></div><?php
									}
								?></div>
							</div><?php

							// ModelsLab: Language
							?><div class="sc_agenerator_form_field sc_agenerator_form_field_language"
								data-actions="tts,transcription,voice-cover"
								data-models="modelslab"
							>
								<div class="sc_agenerator_form_field_inner">
									<label for="sc_agenerator_form_field_language"><?php esc_html_e( 'Language', 'trx_addons' ); ?></label>
									<select name="sc_agenerator_form_field_language" id="sc_agenerator_form_field_language"><?php
										$i = 0;
										foreach ( $languages as $lang => $title ) {
											$i++;
											?><option value="<?php echo esc_attr( $lang ); ?>"<?php
												if ( $i == 1 ) {
													echo ' selected="selected"';
												}
											?>><?php
												echo esc_html( $title );
											?></option><?php
										}
									?></select>
								</div>
							</div><?php

							// ModelsLab: Emotion
							?><div class="sc_agenerator_form_field sc_agenerator_form_field_emotion"
								data-actions="tts,voice-cover"
								data-models="modelslab"
							>
								<div class="sc_agenerator_form_field_inner">
									<label for="sc_agenerator_form_field_emotion"><?php esc_html_e( 'Emotion', 'trx_addons' ); ?></label>
									<select name="sc_agenerator_form_field_emotion" id="sc_agenerator_form_field_emotion"><?php
										$i = 0;
										foreach ( $emotions as $emo => $title ) {
											$i++;
											?><option value="<?php echo esc_attr( $emo ); ?>"<?php
												if ( $i == 1 ) {
													echo ' selected="selected"';
												}
											?>><?php
												echo esc_html( $title );
											?></option><?php
										}
									?></select>
								</div>
							</div>

						</div><?php
		
						// Right block of fields
						?><div class="sc_agenerator_form_fields_right"><?php
								
							// Model
							?><div class="sc_agenerator_form_field sc_agenerator_form_field_model">
								<div class="sc_agenerator_form_field_inner">
									<label for="sc_agenerator_form_field_model"><?php esc_html_e( 'Model', 'trx_addons' ); ?></label>
									<div class="sc_agenerator_form_field_model_wrap<?php
										if ( ! empty( $args['show_settings'] ) && (int) $args['show_settings'] > 0 ) {
											echo ' sc_agenerator_form_field_model_wrap_with_settings';
										}
									?>">
										<select name="sc_agenerator_form_field_model" id="sc_agenerator_form_field_model" data-models="<?php
											echo esc_attr( json_encode( $models ) );
										?>"><?php
											$group = false;
											foreach ( $models['tts'] as $model => $title ) {
												if ( substr( $model, -2 ) == '/-' || substr( $title, 0, 2 ) == '\\-' ) {
													if ( $group ) {
														?></optgroup><?php
													}
													$group = true;
													$title = substr( $title, 2 );
													?><optgroup label="<?php echo esc_attr( $title ); ?>"><?php
												} else {
													?><option value="<?php echo esc_attr( $model ); ?>"><?php echo esc_html( $title ); ?></option><?php
												}
											}
											if ( $group ) {
												?></optgroup><?php
											}
										?></select><?php

										if ( ! empty( $args['show_settings'] ) && (int) $args['show_settings'] > 0 ) {

											// Button "Settings"
											?><a href="#" class="sc_agenerator_form_settings_button trx_addons_icon-sliders"></a><?php

											// Popup with settings
											?><div class="sc_agenerator_form_settings"><?php

												// Open AI: Speed
												?><div class="sc_agenerator_form_settings_field sc_agenerator_form_settings_field_speed"
													data-actions="tts"
													data-models="openai"
												>
													<label for="sc_agenerator_form_settings_field_speed"><?php esc_html_e( 'Speed:', 'trx_addons' ); ?></label>
													<div class="sc_agenerator_form_settings_field_numeric_wrap">
														<input
															type="number"
															name="sc_agenerator_form_settings_field_speed"
															id="sc_agenerator_form_settings_field_speed"
															min="0.25"
															max="4"
															step="0.05"
															value="1"
														>
														<div class="sc_agenerator_form_settings_field_numeric_wrap_buttons">
															<a href="#" class="sc_agenerator_form_settings_field_numeric_wrap_button sc_agenerator_form_settings_field_numeric_wrap_button_inc"></a>
															<a href="#" class="sc_agenerator_form_settings_field_numeric_wrap_button sc_agenerator_form_settings_field_numeric_wrap_button_dec"></a>
														</div>
													</div>
													<div class="sc_agenerator_form_settings_field_description"><?php
														esc_html_e( 'The speed of the generated audio. Select a value from 0.25 to 4.0. Default is 1.0', 'trx_addons' );
													?></div>
												</div><?php

												// Open AI: Temperature
												?><div class="sc_agenerator_form_settings_field sc_agenerator_form_settings_field_temperature"
													data-actions="transcription,translation"
													data-models="openai"
												>
													<label for="sc_agenerator_form_settings_field_temperature"><?php esc_html_e('Temperature:', 'trx_addons'); ?></label>
													<div class="sc_agenerator_form_settings_field_numeric_wrap">
														<input
															type="number"
															name="sc_agenerator_form_settings_field_temperature"
															id="sc_agenerator_form_settings_field_temperature"
															min="0"
															max="1"
															step="0.1"
															value="0"
														>
														<div class="sc_agenerator_form_settings_field_numeric_wrap_buttons">
															<a href="#" class="sc_agenerator_form_settings_field_numeric_wrap_button sc_agenerator_form_settings_field_numeric_wrap_button_inc"></a>
															<a href="#" class="sc_agenerator_form_settings_field_numeric_wrap_button sc_agenerator_form_settings_field_numeric_wrap_button_dec"></a>
														</div>
													</div>
													<div class="sc_agenerator_form_settings_field_description"><?php
														esc_html_e( 'The sampling temperature, between 0 and 1. Default is 0. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.', 'trx_addons' );
													?></div>
												</div><?php

												// ModelsLab: Rate
												?><div class="sc_agenerator_form_settings_field sc_agenerator_form_settings_field_rate"
													data-actions="voice-cover"
													data-models="modelslab"
												>
													<label for="sc_agenerator_form_settings_field_rate"><?php esc_html_e('Rate:', 'trx_addons'); ?></label>
													<div class="sc_agenerator_form_settings_field_numeric_wrap">
														<input
															type="number"
															name="sc_agenerator_form_settings_field_rate"
															id="sc_agenerator_form_settings_field_rate"
															min="0"
															max="1"
															step="0.1"
															value="0.5"
														>
														<div class="sc_agenerator_form_settings_field_numeric_wrap_buttons">
															<a href="#" class="sc_agenerator_form_settings_field_numeric_wrap_button sc_agenerator_form_settings_field_numeric_wrap_button_inc"></a>
															<a href="#" class="sc_agenerator_form_settings_field_numeric_wrap_button sc_agenerator_form_settings_field_numeric_wrap_button_dec"></a>
														</div>
													</div>
													<div class="sc_agenerator_form_settings_field_description"><?php
														esc_html_e( 'Rate of control for generated voice leakage (between 0 and 1). Higher values bias model towards training data. Default is 0.5', 'trx_addons' );
													?></div>
												</div><?php

												// ModelasLab: Radius
												?><div class="sc_agenerator_form_settings_field sc_agenerator_form_settings_field_radius"
													data-actions="voice-cover"
													data-models="modelslab"
												>
													<label for="sc_agenerator_form_settings_field_radius"><?php esc_html_e('Radius:', 'trx_addons'); ?></label>
													<div class="sc_agenerator_form_settings_field_numeric_wrap">
														<input
															type="number"
															name="sc_agenerator_form_settings_field_radius"
															id="sc_agenerator_form_settings_field_radius"
															min="0"
															max="3"
															step="0.1"
															value="3"
														>
														<div class="sc_agenerator_form_settings_field_numeric_wrap_buttons">
															<a href="#" class="sc_agenerator_form_settings_field_numeric_wrap_button sc_agenerator_form_settings_field_numeric_wrap_button_inc"></a>
															<a href="#" class="sc_agenerator_form_settings_field_numeric_wrap_button sc_agenerator_form_settings_field_numeric_wrap_button_dec"></a>
														</div>
													</div>
													<div class="sc_agenerator_form_settings_field_description"><?php
														esc_html_e( 'Median filtering length to reduce voice artifacts (floating point between 0 and 3). Default is 3.', 'trx_addons' );
													?></div>
												</div><?php

												// ModelaLab: Originality
												?><div class="sc_agenerator_form_settings_field sc_agenerator_form_settings_field_originality"
													data-actions="voice-cover"
													data-models="modelslab"
												>
													<label for="sc_agenerator_form_settings_field_originality"><?php esc_html_e('Originality:', 'trx_addons'); ?></label>
													<div class="sc_agenerator_form_settings_field_numeric_wrap">
														<input
															type="number"
															name="sc_agenerator_form_settings_field_originality"
															id="sc_agenerator_form_settings_field_originality"
															min="0"
															max="1"
															step="0.01"
															value="0.33"
														>
														<div class="sc_agenerator_form_settings_field_numeric_wrap_buttons">
															<a href="#" class="sc_agenerator_form_settings_field_numeric_wrap_button sc_agenerator_form_settings_field_numeric_wrap_button_inc"></a>
															<a href="#" class="sc_agenerator_form_settings_field_numeric_wrap_button sc_agenerator_form_settings_field_numeric_wrap_button_dec"></a>
														</div>
													</div>
													<div class="sc_agenerator_form_settings_field_description"><?php
														esc_html_e( "Controls similarity to original vocals' voiceless constants (floating point between 0 and 1). Default is 0.33.", 'trx_addons' );
													?></div>
												</div><?php
											?></div><?php
										}
									?></div>
								</div>
							</div><?php

							// Open AI: Voice
							?><div class="sc_agenerator_form_field sc_agenerator_form_field_voice_openai<?php
								if ( empty( $args['model'] ) || ! Utils::is_openai_model( $args['model'] ) ) {
									echo ' trx_addons_hidden';
								}
								?>"
								data-models="openai"
								data-actions="tts"
							>
								<div class="sc_agenerator_form_field_inner">
									<label for="sc_agenerator_form_field_voice_openai"><?php esc_html_e( 'Voice', 'trx_addons' ); ?></label>
									<select name="sc_agenerator_form_field_voice_openai" id="sc_agenerator_form_field_voice_openai"><?php
										foreach ( $voices as $voice => $title ) {
											?><option value="<?php echo esc_attr( $voice ); ?>"<?php
												if ( ! empty( $args['voice'] ) && $args['voice'] == $voice ) {
													echo ' selected="selected"';
												}
											?>><?php
												echo esc_html( $title );
											?></option><?php
										}
									?></select>
								</div>
							</div><?php

							// ModelsLab: Voice for TTS
							?><div class="sc_agenerator_form_field sc_agenerator_form_field_voice_modelslab<?php
								if ( empty( $args['model'] ) || ! Utils::is_modelslab_model( $args['model'] ) ) {
									echo ' trx_addons_hidden';
								}
								?>"
								data-actions="tts"
								data-models="modelslab"
							>
								<div class="sc_agenerator_form_field_inner">
									<label for="sc_agenerator_form_field_voice_modelslab"><?php esc_html_e( 'Voice', 'trx_addons' ); ?></label>
									<input type="text" name="sc_agenerator_form_field_voice_modelslab" id="sc_agenerator_form_field_voice_modelslab" placeholder="<?php esc_attr_e( "Specify a voice slug", 'trx_addons' ); ?>">
									<div class="sc_agenerator_form_field_description"><?php
										echo sprintf( esc_html__( "List of voices: %s", 'trx_addons' ), '<a href="https://modelslab.com/voice-lists" target="_blank">modelslab.com</a>' );
									?></div>
								</div>
							</div><?php

							// ModelsLab: Voice for Cloning
							?><div class="sc_agenerator_form_field sc_agenerator_form_field_voice_cloning_modelslab<?php
								if ( empty( $args['model'] ) || ! Utils::is_modelslab_model( $args['model'] ) ) {
									echo ' trx_addons_hidden';
								}
								?>"
								data-actions="voice-cover"
								data-models="modelslab"
							>
								<div class="sc_agenerator_form_field_inner">
									<label for="sc_agenerator_form_field_voice_cloning_modelslab"><?php esc_html_e( 'Voice Cloning Model', 'trx_addons' ); ?></label>
									<input type="text" name="sc_agenerator_form_field_voice_cloning_modelslab" id="sc_agenerator_form_field_voice_cloning_modelslab" placeholder="<?php esc_attr_e( "Specify a model slug (ID) for voice covering", 'trx_addons' ); ?>">
									<div class="sc_agenerator_form_field_description"><?php
										echo sprintf( esc_html__( "List of models: %s", 'trx_addons' ), '<a href="https://modelslab.com/models/category/voice-cloning" target="_blank">modelslab.com</a>' );
									?></div>
								</div>
							</div><?php

							// ModelsLab: Upload a file with a custom voice
							?><div class="sc_agenerator_form_field sc_agenerator_form_field_upload_voice_modelslab<?php
								if ( empty( $args['model'] ) || ! Utils::is_modelslab_model( $args['model'] ) ) {
									echo ' trx_addons_hidden';
								}
								?>"
								data-actions="tts,voice-cover"
								data-models="modelslab"
							>
								<div class="sc_agenerator_form_field_inner">
									<label for="sc_agenerator_form_field_upload_voice_modelslab_field"><?php esc_html_e( 'Upload Voice (optional)', 'trx_addons' ); ?></label>
									<?php if ( $decorated ) { ?>
										<div class="sc_agenerator_form_field_upload_voice_modelslab_decorator theme_form_field_text">
											<span class="sc_agenerator_form_field_upload_voice_modelslab_text theme_form_field_placeholder"><?php esc_html_e( "Voice is not selected", 'trx_addons' ); ?></span>
											<span class="sc_agenerator_form_field_upload_voice_modelslab_button trx_addons_icon-upload"><?php esc_html_e( "Browse", 'trx_addons' ); ?></span>
									<?php } ?>
										<input type="file" id="sc_agenerator_form_field_upload_voice_modelslab_field" class="sc_agenerator_form_field_upload_voice_modelslab_field" placeholder="<?php esc_attr_e( "Select a file with a voice example", 'trx_addons' ); ?>">
									<?php if ( $decorated ) { ?>
										</div>
									<?php } ?>
								</div>
							</div><?php

							// Button "Generate"
							?><div class="sc_agenerator_form_field sc_agenerator_form_field_generate"><?php
								trx_addons_show_layout( trx_addons_sc_button( apply_filters( 'trx_addons_filter_sc_agenerator_button_generate_args', array( 'buttons' => array( array(
									"type" => "default",
									"size" => "small",
									"text_align" => "none",
									"icon" => "trx_addons_icon-magic",
									"icon_position" => "left",
									"title" => ! empty( $args['button_text'] ) ? $args['button_text'] : esc_html__( 'Process', 'trx_addons' ),
									"link" => '#',
									'class' => 'sc_agenerator_form_field_generate_button',
								) ) ) ) ) );
							?></div>
						</div>

					</div><?php

					// Loading placeholder
					?><div class="trx_addons_loading"></div><?php

					if ( ! empty( $args['show_limits'] ) ) {
						$premium = ! empty( $args['premium'] ) && (int)$args['premium'] == 1;
						$suffix = $premium ? '_premium' : '';
						$limits = (int)trx_addons_get_option( "ai_helper_sc_agenerator_limits{$suffix}" ) > 0;
						if ( $limits ) {
							$generated = 0;
							if ( $premium ) {
								$user_id = get_current_user_id();
								$user_level = apply_filters( 'trx_addons_filter_sc_agenerator_user_level', $user_id > 0 ? 'default' : '', $user_id );
								if ( ! empty( $user_level ) ) {
									$levels = trx_addons_get_option( "ai_helper_sc_agenerator_levels_premium" );
									$level_idx = trx_addons_array_search( $levels, 'level', $user_level );
									$user_limit = $level_idx !== false ? $levels[ $level_idx ] : false;
									if ( isset( $user_limit['limit'] ) && trim( $user_limit['limit'] ) !== '' ) {
										$generated = trx_addons_sc_agenerator_get_total_generated( $user_limit['per'], $suffix, $user_id );
									}
								}
							}
							if ( ! $premium || empty( $user_level ) || ! isset( $user_limit['limit'] ) || trim( $user_limit['limit'] ) === '' ) {
								$generated = trx_addons_sc_agenerator_get_total_generated( 'hour', $suffix );
								$user_limit = array(
									'limit' => (int)trx_addons_get_option( "ai_helper_sc_agenerator_limit_per_hour{$suffix}" ),
									'requests' => (int)trx_addons_get_option( "ai_helper_sc_agenerator_limit_per_visitor{$suffix}" ),
									'per' => 'hour'
								);
							}
							if ( isset( $user_limit['limit'] ) && trim( $user_limit['limit'] ) !== '' ) {
								?><div class="sc_agenerator_limits"<?php
									// If a shortcode is called not from Elementor, we need to add the width of the prompt field and alignment
									if ( empty( $args['prompt_width_extra'] ) ) {
										if ( ! empty( $args['prompt_width'] ) && (int)$args['prompt_width'] < 100 ) {
											echo ' style="max-width:' . esc_attr( $args['prompt_width'] ) . '%"';
										}
									}
								?>>
									<span class="sc_agenerator_limits_total"><?php
										$periods = Lists::get_list_periods();
										echo wp_kses( sprintf(
															__( 'Limits%s: %s%s.', 'trx_addons' ),
															! empty( $periods[ $user_limit['per'] ] ) ? ' ' . sprintf( __( 'per %s', 'trx_addons' ), strtolower( $periods[ $user_limit['per'] ] ) ) : '',
															sprintf( __( '%s audios', 'trx_addons' ), '<span class="sc_agenerator_limits_total_value">' . (int)$user_limit['limit'] . '</span>' ),
															! empty( $user_limit['requests'] ) ? ' ' . sprintf( __( ' for all visitors and up to %s requests from a single visitor', 'trx_addons' ), '<span class="sc_agenerator_limits_total_requests">' . (int)$user_limit['requests'] . '</span>' ) : '',
														),
														'trx_addons_kses_content'
													);
									?></span>
									<span class="sc_agenerator_limits_used"><?php
										echo wp_kses( sprintf(
															__( 'Used: %s audios%s.', 'trx_addons' ),
															'<span class="sc_agenerator_limits_used_value">' . min( $generated, (int)$user_limit['limit'] )  . '</span>',
															! empty( $user_limit['requests'] ) ? sprintf( __( ', %s requests', 'trx_addons' ), '<span class="sc_agenerator_limits_used_requests">' . (int)trx_addons_get_value_gpc( 'trx_addons_ai_helper_agenerator_count' ) . '</span>' ) : '',
														),
														'trx_addons_kses_content'
													);
									?></span>
								</div><?php
							}
						}
					}

					?><div class="sc_agenerator_message"<?php
						// If a shortcode is called not from Elementor, we need to add the width of the prompt field and alignment
						if ( empty( $args['prompt_width_extra'] ) ) {
							if ( ! empty( $args['prompt_width'] ) && (int)$args['prompt_width'] < 100 ) {
								echo ' style="max-width:' . esc_attr( $args['prompt_width'] ) . '%"';
							}
						}
					?>>
						<div class="sc_agenerator_message_inner"></div>
						<a href="#" class="sc_agenerator_message_close trx_addons_button_close" title="<?php esc_html_e( 'Close', 'trx_addons' ); ?>"><span class="trx_addons_button_close_icon"></span></a>
					</div><?php

				?></div>

			</div><?php

			// Audio preview area
			?><div class="sc_agenerator_audio"></div><?php

		?></div>

		<?php trx_addons_sc_show_links('sc_agenerator', $args); ?>

	</div><?php

} else if ( true || trx_addons_is_preview() ) {

	?><div class="sc_agenerator_error"><?php
		esc_html_e( 'Audio Generator: No models available', 'trx_addons' );
	?></div><?php

}