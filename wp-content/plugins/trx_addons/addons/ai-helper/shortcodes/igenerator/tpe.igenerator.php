<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v2.20.2
 */

use TrxAddons\AiHelper\Lists;
use TrxAddons\AiHelper\Utils;
use TrxAddons\AiHelper\StableDiffusion;

extract( get_query_var( 'trx_addons_args_sc_igenerator' ) );

$decorated = apply_filters( 'trx_addons_filter_sc_igenerator_decorate_upload', true );
?><#
settings = trx_addons_elm_prepare_global_params( settings );

var id = settings._element_id ? settings._element_id + '_sc' : 'sc_igenerator_' + ( '' + Math.random() ).replace( '.', '' );

var link_class = "<?php echo apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_igenerator_item_link sc_button sc_button_size_small', 'sc_igenerator'); ?>";
var link_class_over = "<?php echo apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_igenerator_item_link sc_igenerator_item_link_over', 'sc_igenerator'); ?>";

var models = JSON.parse( '<?php echo addslashes( json_encode( Lists::get_list_ai_image_models() ) ); ?>' );
var upscalers = JSON.parse( '<?php echo addslashes( json_encode( Lists::get_list_ai_image_upscalers() ) ); ?>' );
var styles = JSON.parse( '<?php echo addslashes( json_encode( Lists::get_list_stability_ai_styles() ) ); ?>' );
var styles_openai = JSON.parse( '<?php echo addslashes( json_encode( Lists::get_list_openai_styles() ) ); ?>' );
var sizes  = JSON.parse( '<?php echo addslashes( json_encode( Lists::get_list_ai_image_sizes() ) ); ?>' );
var openai_sizes  = JSON.parse( '<?php echo addslashes( json_encode( Lists::get_list_ai_image_sizes( 'openai' ) ) ); ?>' );

if ( typeof models == 'object' ) {

	#><div id="{{ id }}" class="<# print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 'sc_igenerator sc_igenerator_' + settings.type, settings ) ); #>">

		<?php $element->sc_show_titles( 'sc_igenerator' ); ?>

		<div class="sc_igenerator_content sc_item_content"><#

			// Layout 'Default' -------------------------------------------------------
			if ( settings.type == 'default' ) {
				#><div class="sc_igenerator_form sc_igenerator_form_preview <#
					print( trx_addons_get_responsive_classes( 'sc_igenerator_form_align_', settings, 'align', '' ).replace( /flex-start|flex-end/g, function( match ) {
						return match == 'flex-start' ? 'left' : 'right';
					} ) );
				#>">
					<div class="sc_igenerator_form_inner">
						<div class="sc_igenerator_form_field sc_igenerator_form_field_prompt<#
							if ( settings.show_settings ) {
								print( ' sc_igenerator_form_field_prompt_with_settings' );
							}
						#>">
							<div class="sc_igenerator_form_field_inner">
								<input type="text" value="{{ settings.prompt }}" class="sc_igenerator_form_field_prompt_text" placeholder="{{{ settings.placeholder_text || '<?php esc_attr_e('Describe what you want or hit a tag below', 'trx_addons'); ?>' }}}">
								<a href="#" class="sc_igenerator_form_field_prompt_button<# if ( ! settings.prompt ) print( ' sc_igenerator_form_field_prompt_button_disabled' ); #>">{{{ settings.button_text || '<?php esc_html_e('Generate', 'trx_addons'); ?>' }}}</a>
							</div><#
							if ( settings.show_settings ) {
								var settings_mode = settings.show_settings_size ? 'full' : 'light';
								#>
								<a href="#" class="sc_igenerator_form_settings_button trx_addons_icon-sliders"></a>
								<div class="sc_igenerator_form_settings sc_igenerator_form_settings_{{ settings_mode }}"><#
									// Settings mode 'full' - visitors can change settings 'size', 'width' and 'height'
									if ( settings_mode == 'full' ) {
										// Model
										#><div class="sc_igenerator_form_settings_field">
											<label for="sc_igenerator_form_settings_field_model"><?php esc_html_e('Model:', 'trx_addons'); ?></label>
											<select name="sc_igenerator_form_settings_field_model" id="sc_igenerator_form_settings_field_model"><#
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
											#></select>
										</div><#
										// Style
										#><div class="sc_igenerator_form_settings_field<# if ( ! settings.model || settings.model.indexOf( 'stability-ai/' ) < 0 ) print( ' trx_addons_hidden' ); #>">
											<label for="sc_igenerator_form_settings_field_style"><?php esc_html_e('Style:', 'trx_addons'); ?></label>
											<select name="sc_igenerator_form_settings_field_style" id="sc_igenerator_form_settings_field_style"><#
												for ( var style in styles ) {
													#><option value="{{ style }}"<# if ( settings.style == style ) print( ' selected="selected"' ); #>>{{ styles[style] }}</option><#
												}
											#></select>
										</div><#
										// Style for OpenAI DALL-E-3 model
										#><div class="sc_igenerator_form_settings_field<# if ( ! settings.model || settings.model.indexOf( 'openai/dall-e-3' ) < 0 ) print( ' trx_addons_hidden' ); #>">
											<label for="sc_igenerator_form_settings_field_style_openai"><?php esc_html_e('Style:', 'trx_addons'); ?></label>
											<select name="sc_igenerator_form_settings_field_style_openai" id="sc_igenerator_form_settings_field_style_openai"><#
												for ( var style in styles_openai ) {
													#><option value="{{ style }}"<# if ( settings.style_openai == style ) print( ' selected="selected"' ); #>>{{ styles_openai[style] }}</option><#
												}
											#></select>
										</div><#
										// Size
										#><div class="sc_igenerator_form_settings_field">
											<label for="sc_igenerator_form_settings_field_size"><?php esc_html_e('Size (px):', 'trx_addons'); ?></label>
											<select name="sc_igenerator_form_settings_field_size" id="sc_igenerator_form_settings_field_size"><#
												for ( var size in sizes ) {
													#><option value="{{ size }}"<#
														if ( settings.size == size ) print( ' selected="selected"' );
														if ( settings.model && settings.model.indexOf( 'openai/' ) >= 0 && ! openai_sizes[size] ) print( ' class="trx_addons_hidden"' );
													#>>{{ sizes[size] }}</option><#
												}
											#></select>
										</div><#
										// Width (numeric field)
										#><div class="sc_igenerator_form_settings_field<# if ( settings.size != 'custom' ) print( ' trx_addons_hidden' ); #>">
											<label for="sc_igenerator_form_settings_field_width"><?php esc_html_e('Width (px):', 'trx_addons'); ?></label>
											<div class="sc_igenerator_form_settings_field_numeric_wrap">
												<input
													type="number"
													name="sc_igenerator_form_settings_field_width"
													id="sc_igenerator_form_settings_field_width"
													min="0"
													max="<?php echo esc_attr( Utils::get_max_image_width() ); ?>"
													step="8"
													value="{{ settings.width }}"
												>
												<div class="sc_igenerator_form_settings_field_numeric_wrap_buttons">
													<a href="#" class="sc_igenerator_form_settings_field_numeric_wrap_button sc_igenerator_form_settings_field_numeric_wrap_button_inc"></a>
													<a href="#" class="sc_igenerator_form_settings_field_numeric_wrap_button sc_igenerator_form_settings_field_numeric_wrap_button_dec"></a>
												</div>
											</div>
										</div><#
										// Height (numeric field)
										#><div class="sc_igenerator_form_settings_field<# if ( settings.size != 'custom' ) print( ' trx_addons_hidden' ); #>">
											<label for="sc_igenerator_form_settings_field_height"><?php esc_html_e('Height (px):', 'trx_addons'); ?></label>
											<div class="sc_igenerator_form_settings_field_numeric_wrap">
												<input
													type="number"
													name="sc_igenerator_form_settings_field_height"
													id="sc_igenerator_form_settings_field_height"
													min="0"
													max="<?php echo esc_attr( Utils::get_max_image_height() ); ?>"
													step="8"
													value="{{ settings.height }}"
												>
												<div class="sc_igenerator_form_settings_field_numeric_wrap_buttons">
													<a href="#" class="sc_igenerator_form_settings_field_numeric_wrap_button sc_igenerator_form_settings_field_numeric_wrap_button_inc"></a>
													<a href="#" class="sc_igenerator_form_settings_field_numeric_wrap_button sc_igenerator_form_settings_field_numeric_wrap_button_dec"></a>
												</div>
											</div>
										</div><#

									// Free mode settings
									} else {

										for ( var model in models ) {
											if ( model.slice( -2 ) == '/-' || models[model].slice( 0, 2 ) == '\\-' ) {
												#><div class="sc_igenerator_form_settings_field sc_igenerator_form_settings_field_group_title">{{{ models[model].slice( 2 ) }}}</div><#
											} else {
												var id = 'sc_igenerator_form_settings_field_model_' + settings.model.replace( '/', '-' );
												#><div class="sc_igenerator_form_settings_field">
													<input type="radio" id="{{ id }}" name="sc_igenerator_form_settings_field_model" value="{{ model }}"<# if ( settings.model == model ) print( ' checked="checked"' ); #>><label for="{{ id }}">{{ models[model] }}</label>
												</div><#
											}
										}
									}
								#></div><#
							}
						#></div><#
						if ( settings.show_negative_prompt ) {
							#><div class="sc_igenerator_form_field sc_igenerator_form_field_negative_prompt<#
								if ( settings.model && settings.model.indexOf( 'openai/' ) >= 0 ) {
									print( ' trx_addons_hidden' );
								}
							#>">
								<div class="sc_igenerator_form_field_inner">
									<label for="sc_igenerator_form_field_negative_prompt_text"><?php esc_html_e('Negative prompt (optional):', 'trx_addons'); ?></label>
									<input type="text" value="{{ settings.negative_prompt }}" id="sc_igenerator_form_field_negative_prompt_text" class="sc_igenerator_form_field_negative_prompt_text" placeholder="{{{ settings.negative_placeholder_text || '<?php esc_attr_e( "Items you don't want in the image", 'trx_addons' ); ?>' }}}">
								</div>
							</div><#
						}
						if ( settings.show_upload ) {
							#><div class="sc_igenerator_form_field sc_igenerator_form_field_upload_image">
								<div class="sc_igenerator_form_field_inner">
									<label for="sc_igenerator_form_field_upload_image_field"><?php esc_html_e('Upload image to make variations (optional):', 'trx_addons'); ?></label>
									<?php if ( $decorated ) { ?>
										<div class="sc_igenerator_form_field_upload_image_decorator theme_form_field_text">
											<span class="sc_igenerator_form_field_upload_image_text theme_form_field_placeholder"><?php esc_html_e( "Image is not selected", 'trx_addons' ); ?></span>
											<span class="sc_igenerator_form_field_upload_image_button trx_addons_icon-upload"><?php esc_html_e( "Browse", 'trx_addons' ); ?></span>
									<?php } ?>
									<input type="file" id="sc_igenerator_form_field_upload_image_field" class="sc_igenerator_form_field_upload_image_field" placeholder="<?php esc_attr_e( "Select an image to make variations", 'trx_addons' ); ?>">
									<?php if ( $decorated ) { ?>
										</div>
									<?php } ?>
								</div>
							</div><#
						}
						if ( settings.tags && settings.tags.length && settings.tags[0].title ) {
							#><div class="sc_igenerator_form_field sc_igenerator_form_field_tags"><#
								if ( settings.tags_label ) {
									#><span class="sc_igenerator_form_field_tags_label">{{ settings.tags_label }}</span><#
								}
								#><span class="sc_igenerator_form_field_tags_list"><#
									_.each( settings.tags, function( tag ) {
										#><a href="#" class="sc_igenerator_form_field_tags_item" data-tag-prompt="{{ tag.prompt }}">{{ tag.title }}</a><#
									} );
								#></span><#
							#></div><#
						}
					#></div><#
					if ( settings.show_limits ) {
						#><div class="sc_igenerator_limits">
							<span class="sc_igenerator_limits_label"><?php
								esc_html_e( 'Limits per hour (day/week/month/year): XX images.', 'trx_addons' );
							?></span>
							<span class="sc_igenerator_limits_value"><?php
								esc_html_e( 'Used: YY images.', 'trx_addons' );
							?></span>
						</div><#
					}
				#></div><#

			// Layout 'Extended' ---------------------------------------------------------
			} else if ( settings.type == 'extended' ) {
				#><div class="sc_igenerator_form sc_igenerator_form_preview">
					<div class="sc_igenerator_form_inner">
						<div class="sc_igenerator_form_actions">
							<ul class="sc_igenerator_form_actions_list">
								<li class="sc_igenerator_form_actions_item sc_igenerator_form_actions_item_generation sc_igenerator_form_actions_item_active"><a href="#" data-action="generation"><?php esc_html_e('Generation', 'trx_addons'); ?></a></li>
								<li class="sc_igenerator_form_actions_item sc_igenerator_form_actions_item_variations"><a href="#" data-action="variations"><?php esc_html_e('Variations', 'trx_addons'); ?></a></li>
								<li class="sc_igenerator_form_actions_item sc_igenerator_form_actions_item_upscale"><a href="#" data-action="upscale"><?php esc_html_e('Upscale', 'trx_addons'); ?></a></li>
								<li class="sc_igenerator_form_actions_slider"></li>
							</ul>
						</div>
						<div class="sc_igenerator_form_fields"><#

							// Left block of fields
							#><div class="sc_igenerator_form_fields_left"><#

								// Upload image
								#><div class="sc_igenerator_form_field sc_igenerator_form_field_upload_image trx_addons_hidden" data-actions="variations,upscale">
									<div class="sc_igenerator_form_field_inner">
										<label for="sc_igenerator_form_field_upload_image_field"><?php esc_html_e( 'Upload image', 'trx_addons' ); ?></label>
										<?php if ( $decorated ) { ?>
											<div class="sc_igenerator_form_field_upload_image_decorator theme_form_field_text">
												<span class="sc_igenerator_form_field_upload_image_text theme_form_field_placeholder"><?php esc_html_e( "Image is not selected", 'trx_addons' ); ?></span>
												<span class="sc_igenerator_form_field_upload_image_button trx_addons_icon-upload"><?php esc_html_e( "Browse", 'trx_addons' ); ?></span>
										<?php } ?>
											<input type="file" id="sc_igenerator_form_field_upload_image_field" class="sc_igenerator_form_field_upload_image_field" placeholder="<?php esc_attr_e( "Select an image to make variations or upscale", 'trx_addons' ); ?>">
										<?php if ( $decorated ) { ?>
											</div>
										<?php } ?>
									</div>
								</div><#
								
								// Prompt
								#><div class="sc_igenerator_form_field sc_igenerator_form_field_prompt" data-actions="generation,variations">
									<div class="sc_igenerator_form_field_inner">
										<label for="sc_igenerator_form_field_prompt_text"><?php esc_html_e( 'Prompt', 'trx_addons' ); ?></label>
										<input type="text" value="{{ settings.prompt }}" class="sc_igenerator_form_field_prompt_text" placeholder="{{{ settings.placeholder_text || '<?php esc_attr_e( 'Describe what you want or hit a tag below', 'trx_addons' ); ?>' }}}">
									</div>
								</div><#

								// Negative prompt
								if ( settings.show_negative_prompt ) {
									#><div class="sc_igenerator_form_field sc_igenerator_form_field_negative_prompt<#
										if ( settings.model && settings.model.indexOf( 'openai/' ) >= 0 ) {
											print( ' trx_addons_hidden' );
										}
									#>" data-actions="generation,variations">
										<div class="sc_igenerator_form_field_inner">
											<label for="sc_igenerator_form_field_negative_prompt_text"><?php esc_html_e('Negative prompt (optional):', 'trx_addons'); ?></label>
											<input type="text" value="{{ settings.negative_prompt }}" id="sc_igenerator_form_field_negative_prompt_text" class="sc_igenerator_form_field_negative_prompt_text" placeholder="{{{ settings.negative_placeholder_text || '<?php esc_attr_e( "Items you don't want in the image", 'trx_addons' ); ?>' }}}">
										</div>
									</div><#
								}

								// Tags
								#><div class="sc_igenerator_form_field sc_igenerator_form_field_tags" data-actions="generation"><#
									if ( settings.tags_label ) {
										#><span class="sc_igenerator_form_field_tags_label">{{ settings.tags_label }}</span><#
									}
									if ( settings.tags && settings.tags.length ) {
										#><span class="sc_igenerator_form_field_tags_list"><#
											_.each( settings.tags, function( tag ) {
												#><a href="#" class="sc_igenerator_form_field_tags_item" data-tag-prompt="{{ tag.prompt }}">{{ tag.title }}</a><#
											} );
										#></span><#
									}
								#></div>
							</div><#
			
							// Right block of fields
							#><div class="sc_igenerator_form_fields_right"><#
									
								// Model
								#><div class="sc_igenerator_form_field sc_igenerator_form_field_model" data-actions="generation,variations">
									<div class="sc_igenerator_form_field_inner">
										<label for="sc_igenerator_form_field_model"><?php esc_html_e( 'Model', 'trx_addons' ); ?></label>
										<div class="sc_igenerator_form_field_model_wrap<#
											if ( settings.show_settings ) {
												print( ' sc_igenerator_form_field_model_wrap_with_settings' );
											}
										#>">
											<select name="sc_igenerator_form_field_model" id="sc_igenerator_form_field_model"><#
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

											// Button "Settings"
											if ( settings.show_settings ) {
												#><a href="#" class="sc_igenerator_form_settings_button trx_addons_icon-sliders"></a><#

												// Popup with settings
												#><div class="sc_igenerator_form_settings"><#

													// Stable Diffusion: Guidance scale
													#><div class="sc_igenerator_form_settings_field sc_igenerator_form_settings_field_guidance_scale"
														data-actions="generation,variations,upscale"
														data-models="stable-diffusion"
													>
														<label for="sc_igenerator_form_settings_field_guidance_scale"><?php esc_html_e('Guidance scale:', 'trx_addons'); ?></label>
														<div class="sc_igenerator_form_settings_field_numeric_wrap">
															<input
																type="number"
																name="sc_igenerator_form_settings_field_guidance_scale"
																id="sc_igenerator_form_settings_field_guidance_scale"
																min="1"
																max="20"
																step="0.1"
																value="<?php echo esc_attr( trx_addons_get_option( 'ai_helper_guidance_scale_stable_diffusion' ) ); ?>"
															>
															<div class="sc_igenerator_form_settings_field_numeric_wrap_buttons">
																<a href="#" class="sc_igenerator_form_settings_field_numeric_wrap_button sc_igenerator_form_settings_field_numeric_wrap_button_inc"></a>
																<a href="#" class="sc_igenerator_form_settings_field_numeric_wrap_button sc_igenerator_form_settings_field_numeric_wrap_button_dec"></a>
															</div>
														</div>
														<div class="sc_igenerator_form_settings_field_description"><?php
															esc_html_e( 'Scale for classifier-free guidance (min: 1; max: 20)', 'trx_addons' );
														?></div>
													</div><#

													// Stable Diffusion: Inference steps
													#><div class="sc_igenerator_form_settings_field sc_igenerator_form_settings_field_inference_steps"
														data-actions="generation,variations,upscale"
														data-models="stable-diffusion"
													>
														<label for="sc_igenerator_form_settings_field_inference_steps"><?php esc_html_e('Inference steps:', 'trx_addons'); ?></label>
														<div class="sc_igenerator_form_settings_field_numeric_wrap">
															<input
																type="number"
																name="sc_igenerator_form_settings_field_inference_steps"
																id="sc_igenerator_form_settings_field_inference_steps"
																min="21"
																max="51"
																step="10"
																value="<?php echo esc_attr( trx_addons_get_option( 'ai_helper_inference_steps_stable_diffusion' ) ); ?>"
															>
															<div class="sc_igenerator_form_settings_field_numeric_wrap_buttons">
																<a href="#" class="sc_igenerator_form_settings_field_numeric_wrap_button sc_igenerator_form_settings_field_numeric_wrap_button_inc"></a>
																<a href="#" class="sc_igenerator_form_settings_field_numeric_wrap_button sc_igenerator_form_settings_field_numeric_wrap_button_dec"></a>
															</div>
														</div>
														<div class="sc_igenerator_form_settings_field_description"><?php
															esc_html_e( 'Number of denoising steps. The value accepts 21,31,41 and 51.', 'trx_addons' );
														?></div>
													</div><#

													// Stability AI: Cfg scale
													#><div class="sc_igenerator_form_settings_field sc_igenerator_form_settings_field_cfg_scale"
														data-actions="generation,variations,upscale"
														data-models="stability-ai"
													>
														<label for="sc_igenerator_form_settings_field_cfg_scale"><?php esc_html_e('Cfg scale:', 'trx_addons'); ?></label>
														<div class="sc_igenerator_form_settings_field_numeric_wrap">
															<input
																type="number"
																name="sc_igenerator_form_settings_field_cfg_scale"
																id="sc_igenerator_form_settings_field_cfg_scale"
																min="0"
																max="35"
																step="0.1"
																value="<?php echo esc_attr( trx_addons_get_option( 'ai_helper_cfg_scale_stability_ai' ) ); ?>"
															>
															<div class="sc_igenerator_form_settings_field_numeric_wrap_buttons">
																<a href="#" class="sc_igenerator_form_settings_field_numeric_wrap_button sc_igenerator_form_settings_field_numeric_wrap_button_inc"></a>
																<a href="#" class="sc_igenerator_form_settings_field_numeric_wrap_button sc_igenerator_form_settings_field_numeric_wrap_button_dec"></a>
															</div>
														</div>
														<div class="sc_igenerator_form_settings_field_description"><?php
															esc_html_e( 'How strictly the diffusion process adheres to the prompt text (higher values keep your image closer to your prompt)', 'trx_addons' );
														?></div>
													</div><#

													// Stability AI: Diffusion steps
													#><div class="sc_igenerator_form_settings_field sc_igenerator_form_settings_field_diffusion_steps"
														data-actions="generation,variations,upscale"
														data-models="stability-ai"
													>
														<label for="sc_igenerator_form_settings_field_diffusion_steps"><?php esc_html_e('Diffusion steps:', 'trx_addons'); ?></label>
														<div class="sc_igenerator_form_settings_field_numeric_wrap">
															<input
																type="number"
																name="sc_igenerator_form_settings_field_diffusion_steps"
																id="sc_igenerator_form_settings_field_diffusion_steps"
																min="10"
																max="150"
																step="1"
																value="<?php echo esc_attr( trx_addons_get_option( 'ai_helper_diffusion_steps_stability_ai' ) ); ?>"
															>
															<div class="sc_igenerator_form_settings_field_numeric_wrap_buttons">
																<a href="#" class="sc_igenerator_form_settings_field_numeric_wrap_button sc_igenerator_form_settings_field_numeric_wrap_button_inc"></a>
																<a href="#" class="sc_igenerator_form_settings_field_numeric_wrap_button sc_igenerator_form_settings_field_numeric_wrap_button_dec"></a>
															</div>
														</div>
														<div class="sc_igenerator_form_settings_field_description"><?php
															esc_html_e( 'Number of diffusion steps to run.', 'trx_addons' );
														?></div>
													</div><#

													// Stable Diffusion & Stability AI: Seed
													#><div class="sc_igenerator_form_settings_field sc_igenerator_form_settings_field_seed"
														data-actions="generation,variations,upscale"
														data-models="stable-diffusion,stability-ai"
													>
														<label for="sc_igenerator_form_settings_field_seed"><?php esc_html_e('Seed:', 'trx_addons'); ?></label>
														<div class="sc_igenerator_form_settings_field_numeric_wrap">
															<input
																type="number"
																name="sc_igenerator_form_settings_field_seed"
																id="sc_igenerator_form_settings_field_seed"
																min="0"
																max="4294967295"
																step="1"
																value="0"
															>
															<div class="sc_igenerator_form_settings_field_numeric_wrap_buttons">
																<a href="#" class="sc_igenerator_form_settings_field_numeric_wrap_button sc_igenerator_form_settings_field_numeric_wrap_button_inc"></a>
																<a href="#" class="sc_igenerator_form_settings_field_numeric_wrap_button sc_igenerator_form_settings_field_numeric_wrap_button_dec"></a>
															</div>
														</div>
														<div class="sc_igenerator_form_settings_field_description"><?php
															esc_html_e( 'Seed is used to reproduce results, same seed will give you same image in return again. Pass 0 for a random number.', 'trx_addons' );
														?></div>
													</div>
												</div><#
											}
										#></div>
									</div>
								</div><#

								// Style
								#><div class="sc_igenerator_form_field sc_igenerator_form_field_style<#
									if ( ! settings.model || settings.model.indexOf( 'stability-ai/' ) < 0 ) {
										print( ' trx_addons_hidden' );
									}
									#>"
									data-actions="generation,variations,upscale"
								>
									<div class="sc_igenerator_form_field_inner">
										<label for="sc_igenerator_form_field_style"><?php esc_html_e( 'Style', 'trx_addons' ); ?></label>
										<select name="sc_igenerator_form_field_style" id="sc_igenerator_form_field_style"><#
											for ( var style in styles ) {
												#><option value="{{ style }}"<# if ( settings.style == style ) print( ' selected="selected"' ); #>>{{ styles[style] }}</option><#
											}
										#></select>
									</div>
								</div><#

								// Style for Open AI DALL-E-3 model
								#><div class="sc_igenerator_form_field sc_igenerator_form_field_style_openai<#
									if ( ! settings.model || settings.model.indexOf( 'openai/dall-e-3' ) < 0 ) {
										print( ' trx_addons_hidden' );
									}
									#>"
									data-actions="generation,variations,upscale"
								>
									<div class="sc_igenerator_form_field_inner">
										<label for="sc_igenerator_form_field_style_openai"><?php esc_html_e( 'Style', 'trx_addons' ); ?></label>
										<select name="sc_igenerator_form_field_style_openai" id="sc_igenerator_form_field_style_openai"><#
											for ( var style in styles_openai ) {
												#><option value="{{ style }}"<# if ( settings.style == style ) print( ' selected="selected"' ); #>>{{ styles_openai[style] }}</option><#
											}
										#></select>
									</div>
								</div><#

								// LoRA models
								#><div class="sc_igenerator_form_field sc_igenerator_form_field_lora_model<#
									if ( ! settings.model || settings.model.indexOf( 'stable-diffusion/' ) < 0 || settings.model === 'stable-diffusion/default' ) {
										print( ' trx_addons_hidden' );
									}
									#>"
									data-actions="generation,variations"
								>
									<div class="sc_igenerator_form_field_inner">
										<label for="sc_igenerator_form_field_lora_model"><?php esc_html_e( 'LoRA model[s] (optional)', 'trx_addons' ); ?></label>
										<input type="text" name="sc_igenerator_form_field_lora_model" id="sc_igenerator_form_field_lora_model" value="{{ settings.lora_model }}" placeholder="<?php esc_attr_e( 'model_id[=strength][,model_id[=strength],...]', 'trx_addons' ); ?>">
										<div class="sc_igenerator_form_field_description"><?php
											echo wp_kses( sprintf(
														__( 'You can see a list of available models here: %s', 'trx_addons' ),
														'<a href="' . esc_url( StableDiffusion::instance()->get_url( 'models/section/lora' ) ) . '" target="_blank">' . esc_html__( 'LoRA models', 'trx_addons' ) . '</a>'
											), 'trx_addons_kses_content' );
										?></div>
									</div>
								</div><#

								// Upscaler
								#><div class="sc_igenerator_form_field sc_igenerator_form_field_upscaler" data-actions="upscale">
									<div class="sc_igenerator_form_field_inner">
										<label for="sc_igenerator_form_field_upscaler"><?php esc_html_e( 'Model', 'trx_addons' ); ?></label>
										<select name="sc_igenerator_form_field_upscaler" id="sc_igenerator_form_field_upscaler"><#
											var i = 0;
											for ( var upscaler in upscalers ) {
												i++;
												#><option value="{{ upscaler }}"<# if ( i == 1 ) print( ' selected="selected"' ); #>>{{ upscalers[upscaler] }}</option><#
											}
										#></select>
									</div>
								</div><#

								// Size & Width & Height
								#><div class="sc_igenerator_form_field sc_igenerator_form_field_size" data-actions="generation,variations,upscale"><#

									// Size
									#><div class="sc_igenerator_form_field_size_wrap">
										<div class="sc_igenerator_form_field_inner">
											<label for="sc_igenerator_form_field_size"><?php esc_html_e( 'Size (px)', 'trx_addons' ); ?></label>
											<select name="sc_igenerator_form_field_size" id="sc_igenerator_form_field_size"><#
												for ( var size in sizes ) {
													#><option value="{{ size }}"<#
														if ( settings.size == size ) print( ' selected="selected"' );
														if ( settings.model && settings.model.indexOf( 'openai/' ) >= 0 && ! openai_sizes[size] ) print( ' class="trx_addons_hidden"' );
													#>>{{ sizes[size] }}</option><#
												}
											#></select>
										</div>
									</div><#

									// Scale
									#><div class="sc_igenerator_form_field_scale_wrap trx_addons_hidden">
										<div class="sc_igenerator_form_field_inner">
											<label for="sc_igenerator_form_field_scale"><?php esc_html_e( 'Scale factor', 'trx_addons' ); ?></label>
											<div class="sc_igenerator_form_field_numeric_wrap">
												<input
													type="number"
													name="sc_igenerator_form_field_scale"
													id="sc_igenerator_form_field_scale"
													min="2"
													max="4"
													value="2"
												>
												<div class="sc_igenerator_form_field_numeric_wrap_buttons">
													<a href="#" class="sc_igenerator_form_field_numeric_wrap_button sc_igenerator_form_field_numeric_wrap_button_inc"></a>
													<a href="#" class="sc_igenerator_form_field_numeric_wrap_button sc_igenerator_form_field_numeric_wrap_button_dec"></a>
												</div>
											</div>
										</div>
									</div><#

									// Dimensions (width & height)
									#><div class="sc_igenerator_form_field_dimensions_wrap<# if ( settings.size != 'custom' ) print( ' trx_addons_hidden' ); #>"><#

										// Width (numeric field)
										#><div class="sc_igenerator_form_field_width_wrap">
											<div class="sc_igenerator_form_field_inner">
												<label for="sc_igenerator_form_field_width"><?php esc_html_e( 'Width', 'trx_addons' ); ?></label>
												<div class="sc_igenerator_form_field_numeric_wrap">
													<input
														type="number"
														name="sc_igenerator_form_field_width"
														id="sc_igenerator_form_field_width"
														min="0"
														max="<?php echo esc_attr( Utils::get_max_image_width() ); ?>"
														step="8"
														value="{{ settings.width }}"
													>
													<div class="sc_igenerator_form_field_numeric_wrap_buttons">
														<a href="#" class="sc_igenerator_form_field_numeric_wrap_button sc_igenerator_form_field_numeric_wrap_button_inc"></a>
														<a href="#" class="sc_igenerator_form_field_numeric_wrap_button sc_igenerator_form_field_numeric_wrap_button_dec"></a>
													</div>
												</div>
											</div>
										</div><#

										// Height (numeric field)
										#><div class="sc_igenerator_form_field_height_wrap">
											<div class="sc_igenerator_form_field_inner">
												<label for="sc_igenerator_form_field_height"><?php esc_html_e( 'Height', 'trx_addons' ); ?></label>
												<div class="sc_igenerator_form_field_numeric_wrap">
													<input
														type="number"
														name="sc_igenerator_form_field_height"
														id="sc_igenerator_form_field_height"
														min="0"
														max="<?php echo esc_attr( Utils::get_max_image_height() ); ?>"
														step="8"
														value="{{ settings.height }}"
													>
													<div class="sc_igenerator_form_field_numeric_wrap_buttons">
														<a href="#" class="sc_igenerator_form_field_numeric_wrap_button sc_igenerator_form_field_numeric_wrap_button_inc"></a>
														<a href="#" class="sc_igenerator_form_field_numeric_wrap_button sc_igenerator_form_field_numeric_wrap_button_dec"></a>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div><#

								// Button "Generate"
								#><div class="sc_igenerator_form_field sc_igenerator_form_field_generate" data-actions="generation,variations,upscale"><#
									var link_class = "<?php echo apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_igenerator_form_field_generate_button sc_button sc_button_size_small', 'sc_igenerator'); ?>";
									#><a href="#" class="{{ link_class }}"><#
										#><span class="sc_button_icon"><span class="trx_addons_icon-magic"></span></span><#
										#><span class="sc_button_text"><# print( settings.button_text ? settings.button_text : "<?php esc_html_e( 'Generate', 'trx_addons' ); ?>" ); #></span><#
									#></a><#
								#></div>
							</div>
						</div>
					</div><#
					if ( settings.show_limits ) {
						#><div class="sc_igenerator_limits">
							<span class="sc_igenerator_limits_label"><?php
								esc_html_e( 'Limits per hour (day/week/month/year): XX images.', 'trx_addons' );
							?></span>
							<span class="sc_igenerator_limits_value"><?php
								esc_html_e( 'Used: YY images.', 'trx_addons' );
							?></span>
						</div><#
					}
				#></div><#

			// Custom layout from the theme ----------------------------------------------
			} else {
				trx_addons_do_action( 'trx_addons_action_sc_igenerator_show_layout', settings );
			}
		#></div>

		<?php $element->sc_show_links('sc_igenerator'); ?>

	</div><#

	settings = trx_addons_elm_restore_global_params( settings );

} else {

	#><div class="sc_igenerator_error"><?php
		esc_html_e( 'Image Generator: No models available', 'trx_addons' );
	?></div><#

}
#>