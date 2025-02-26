<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v2.31.0
 */

use TrxAddons\AiHelper\Lists;

extract( get_query_var( 'trx_addons_args_sc_agenerator' ) );

$decorated = apply_filters( 'trx_addons_filter_sc_agenerator_decorate_upload', true );
?><#
settings = trx_addons_elm_prepare_global_params( settings );

var id = settings._element_id ? settings._element_id + '_sc' : 'sc_agenerator_' + ( '' + Math.random() ).replace( '.', '' );

var link_class = "<?php echo apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_agenerator_item_link sc_button sc_button_size_small', 'sc_agenerator'); ?>";
var link_class_over = "<?php echo apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_agenerator_item_link sc_agenerator_item_link_over', 'sc_agenerator'); ?>";

var models    = JSON.parse( '<?php echo addslashes( json_encode( Lists::get_list_ai_audio_models() ) ); ?>' );
var voices    = JSON.parse( '<?php echo addslashes( json_encode( Lists::get_list_openai_voices() ) ); ?>' );
var languages = JSON.parse( '<?php echo addslashes( json_encode( Lists::get_list_modelslab_languages() ) ); ?>' );
var emotions  = JSON.parse( '<?php echo addslashes( json_encode( Lists::get_list_modelslab_emotions() ) ); ?>' );

if ( typeof models == 'object' ) {

	#><div id="{{ id }}" class="<# print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 'sc_agenerator sc_agenerator_' + settings.type, settings ) ); #>">

		<?php $element->sc_show_titles( 'sc_agenerator' ); ?>

		<div class="sc_agenerator_content sc_item_content">
			<div class="sc_agenerator_form sc_agenerator_form_preview">
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
					<div class="sc_agenerator_form_fields"><#

						// Left block of fields
						#><div class="sc_agenerator_form_fields_left"><#
							
							// Prompt
							#><div class="sc_agenerator_form_field sc_agenerator_form_field_prompt" data-actions="tts">
								<div class="sc_agenerator_form_field_inner">
									<label for="sc_agenerator_form_field_prompt_text"><?php esc_html_e( 'Text', 'trx_addons' ); ?></label>
									<input type="text" value="{{ settings.prompt }}" class="sc_agenerator_form_field_prompt_text" placeholder="{{{ settings.placeholder_text || '<?php esc_attr_e( 'Text to generate an audio file', 'trx_addons' ); ?>' }}}">
								</div>
							</div><#

							// Upload audio
							#><div class="sc_agenerator_form_field sc_agenerator_form_field_upload_audio trx_addons_hidden"
								data-actions="transcription,translation,voice-cover"
							>
								<div class="sc_agenerator_form_field_inner">
									<label for="sc_agenerator_form_field_upload_audio_field"><?php esc_html_e( 'Upload Audio', 'trx_addons' ); ?></label>
									<?php if ( $decorated ) { ?>
										<div class="sc_agenerator_form_field_upload_audio_decorator theme_form_field_text">
											<span class="sc_agenerator_form_field_upload_audio_text theme_form_field_placeholder"><?php esc_html_e( "Audio is not selected", 'trx_addons' ); ?></span>
											<span class="sc_agenerator_form_field_upload_audio_button trx_addons_icon-upload"><?php esc_html_e( "Browse", 'trx_addons' ); ?></span>
									<?php } ?>
										<input type="file" id="sc_agenerator_form_field_upload_audio_field" class="sc_agenerator_form_field_upload_audio_field" placeholder="<?php esc_attr_e( "Select an audio file", 'trx_addons' ); ?>">
									<?php if ( $decorated ) { ?>
										</div>
									<?php } ?>
								</div>
							</div><#

							// ModelsLab: Language
							#><div class="sc_agenerator_form_field sc_agenerator_form_field_language"
								data-actions="tts,transcription,voice-cover"
								data-models="modelslab"
							>
								<div class="sc_agenerator_form_field_inner">
									<label for="sc_agenerator_form_field_language"><?php esc_html_e( 'Language', 'trx_addons' ); ?></label>
									<select name="sc_agenerator_form_field_language" id="sc_agenerator_form_field_language"><#
										var i = 0;
										for ( var language in languages ) {
											i++;
											#><option value="{{ language }}"<# if ( i == 1 ) print( ' selected="selected"' ); #>>{{ languages[language] }}</option><#
										}
									#></select>
								</div>
							</div><#

							// ModelsLab: Emotion
							#><div class="sc_agenerator_form_field sc_agenerator_form_field_emotion"
								data-actions="tts,voice-cover"
								data-models="modelslab"
							>
								<div class="sc_agenerator_form_field_inner">
									<label for="sc_agenerator_form_field_emotion"><?php esc_html_e( 'Emotion', 'trx_addons' ); ?></label>
									<select name="sc_agenerator_form_field_emotion" id="sc_agenerator_form_field_emotion"><#
										var i = 0;
										for ( var emotion in emotions ) {
											i++;
											#><option value="{{ emotion }}"<# if ( i == 1 ) print( ' selected="selected"' ); #>>{{ emotions[emotion] }}</option><#
										}
									#></select>
								</div>
							</div>

						</div><#
		
						// Right block of fields
						#><div class="sc_agenerator_form_fields_right"><#
								
							// Model
							#><div class="sc_agenerator_form_field sc_agenerator_form_field_model">
								<div class="sc_agenerator_form_field_inner">
									<label for="sc_agenerator_form_field_model"><?php esc_html_e( 'Model', 'trx_addons' ); ?></label>
									<div class="sc_agenerator_form_field_model_wrap<#
										if ( settings.show_settings ) {
											print( ' sc_agenerator_form_field_model_wrap_with_settings' );
										}
									#>">
										<select name="sc_agenerator_form_field_model" id="sc_agenerator_form_field_model"><#
											var group = false;
											for ( var model in models ) {
												if ( model.slice( -2 ) == '/-' || models[model].slice( 0, 2 ) == '\\-' ) {
													if ( group ) {
														#></optgroup><#
													}
													group = true;
													#><optgroup label="{{{ models[model].slice( 2 ) }}}"><#
												} else {
													#><option value="{{ model }}"<# if ( settings.model == model ) print( ' selected="selected"' ); #>>{{ models[model] }}</option><#
												}
											}
											if ( group ) {
												#></optgroup><#
											}
										#></select><#

										if ( settings.show_settings ) {

											// Button "Settings"
											#><a href="#" class="sc_agenerator_form_settings_button trx_addons_icon-sliders"></a><#

											// Popup with settings
											#><div class="sc_agenerator_form_settings"><#

												// Open AI: Speed
												#><div class="sc_agenerator_form_settings_field sc_agenerator_form_settings_field_speed"
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
												</div><#

												// Open AI: Temperature
												#><div class="sc_agenerator_form_settings_field sc_agenerator_form_settings_field_temperature"
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
												</div><#

												// ModelsLab: Rate
												#><div class="sc_agenerator_form_settings_field sc_agenerator_form_settings_field_rate"
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
												</div><#

												// ModelasLab: Radius
												#><div class="sc_agenerator_form_settings_field sc_agenerator_form_settings_field_radius"
													data-actions="voice-cover"
													data-models="modelslab"
												>
													<label for="sc_agenerator_form_settings_field_radius"><?php esc_html_e( 'Radius:', 'trx_addons' ); ?></label>
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
												</div><#

												// ModelaLab: Originality
												#><div class="sc_agenerator_form_settings_field sc_agenerator_form_settings_field_originality"
													data-actions="voice-cover"
													data-models="modelslab"
												>
													<label for="sc_agenerator_form_settings_field_originality"><?php esc_html_e( 'Originality:', 'trx_addons' ); ?></label>
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
												</div>
											</div><#
										}
									#></div>
								</div>
							</div><#

							// Open AI: Voice
							#><div class="sc_agenerator_form_field sc_agenerator_form_field_voice_openai<#
								if ( ! settings.model || settings.model.indexOf( 'openai/' ) < 0 ) {
									print( ' trx_addons_hidden' );
								}
								#>"
								data-actions="tts"
							>
								<div class="sc_agenerator_form_field_inner">
									<label for="sc_agenerator_form_field_voice_openai"><?php esc_html_e( 'Voice', 'trx_addons' ); ?></label>
									<select name="sc_agenerator_form_field_voice_openai" id="sc_agenerator_form_field_voice_openai"><#
										for ( var voice in voices ) {
											#><option value="{{ voice }}"<# if ( settings.voice == voice ) print( ' selected="selected"' ); #>>{{ voices[voice] }}</option><#
										}
									#></select>
								</div>
							</div><#

							// ModelsLab: Voice for TTS
							#><div class="sc_agenerator_form_field sc_agenerator_form_field_voice_modelslab<#
								if ( ! settings.model || settings.model.indexOf( 'modelslab/' ) < 0 ) {
									print( ' trx_addons_hidden' );
								}
								#>"
								data-actions="tts"
								data-models="modelslab"
							>
								<div class="sc_agenerator_form_field_inner">
									<label for="sc_agenerator_form_field_voice_modelslab"><?php esc_html_e( 'Voice', 'trx_addons' ); ?></label>
									<input type="text" name="sc_agenerator_form_field_voice_modelslab" id="sc_agenerator_form_field_voice_modelslab" placeholder="<?php esc_attr_e( "Specify a voice slug (ID)", 'trx_addons' ); ?>">
									<div class="sc_agenerator_form_field_description"><?php
										echo sprintf( esc_html__( "List of voices: %s", 'trx_addons' ), '<a href="https://modelslab.com/voice-lists" target="_blank">modelslab.com</a>' );
									?></div>
								</div>
							</div><#

							// ModelsLab: Voice for Cloning
							#><div class="sc_agenerator_form_field sc_agenerator_form_field_voice_cloning_modelslab<#
								if ( ! settings.model || settings.model.indexOf( 'modelslab/' ) < 0 ) {
									print( ' trx_addons_hidden' );
								}
								#>"
								data-actions="voice-cover"
								data-models="modelslab"
							>
								<div class="sc_agenerator_form_field_inner">
									<label for="sc_agenerator_form_field_voice_cloning_modelslab"><?php esc_html_e( 'Voice Cloning Model', 'trx_addons' ); ?></label>
									<input type="text" name="sc_agenerator_form_field_voice_cloning_modelslab" id="sc_agenerator_form_field_voice_cloning_modelslab" placeholder="<?php esc_attr_e( "Specify a model slug (ID) for voice covering", 'trx_addons' ); ?>">
									<div class="sc_agenerator_form_field_description"><?php
										echo sprintf( esc_html__( "List of voices: %s", 'trx_addons' ), '<a href="https://modelslab.com/models/category/voice-cloning" target="_blank">modelslab.com</a>' );
									?></div>
								</div>
							</div><#

							// ModelsLab: Upload a file with a custom voice
							#><div class="sc_agenerator_form_field sc_agenerator_form_field_upload_voice_modelslab<#
								if ( ! settings.model || settings.model.indexOf( 'modelslab/' ) < 0 ) {
									print( ' trx_addons_hidden' );
								}
								#>"
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
							</div><#

							// Button "Generate"
							#><div class="sc_agenerator_form_field sc_agenerator_form_field_generate"><#
								var link_class = "<?php echo apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_agenerator_form_field_generate_button sc_button sc_button_size_small', 'sc_agenerator'); ?>";
								#><a href="#" class="{{ link_class }}"><#
									#><span class="sc_button_icon"><span class="trx_addons_icon-magic"></span></span><#
									#><span class="sc_button_text"><# print( settings.button_text ? settings.button_text : "<?php esc_html_e( 'Process', 'trx_addons' ); ?>" ); #></span><#
								#></a><#
							#></div>
						</div>
					</div>
				</div><#
				if ( settings.show_limits ) {
					#><div class="sc_agenerator_limits">
						<span class="sc_agenerator_limits_label"><?php
							esc_html_e( 'Limits per hour (day/week/month/year): XX audio.', 'trx_addons' );
						?></span>
						<span class="sc_agenerator_limits_value"><?php
							esc_html_e( 'Used: YY audio.', 'trx_addons' );
						?></span>
					</div><#
				}
			#></div>
		</div>

		<?php $element->sc_show_links('sc_agenerator'); ?>

	</div><#

	settings = trx_addons_elm_restore_global_params( settings );

} else {

	#><div class="sc_agenerator_error"><?php
		esc_html_e( 'Audio Generator: No models available', 'trx_addons' );
	?></div><#

}
#>