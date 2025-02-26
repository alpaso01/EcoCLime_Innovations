<?php
/**
 * The style "extended" of the IGenerator
 *
 * @package ThemeREX Addons
 * @since v2.26.4
 */

use TrxAddons\AiHelper\Lists;
use TrxAddons\AiHelper\Utils;
use TrxAddons\AiHelper\StableDiffusion;

$args = get_query_var('trx_addons_args_sc_igenerator');

$models = Lists::get_list_ai_image_models();
$upscalers = Lists::get_list_ai_image_upscalers();
$styles = Lists::get_list_stability_ai_styles();
$styles_openai = Lists::get_list_openai_styles();
$sizes = Lists::get_list_ai_image_sizes();
$openai_sizes = Lists::get_list_ai_image_sizes( 'openai' );

if ( count( $models ) > 0 ) {

	?><div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> 
		class="sc_igenerator sc_igenerator_<?php
			echo esc_attr( $args['type'] );
			if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] );
			?>"<?php
		if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
		trx_addons_sc_show_attributes( 'sc_igenerator', $args, 'sc_wrapper' );
		?>><?php

		trx_addons_sc_show_titles('sc_igenerator', $args);

		?><div class="sc_igenerator_content sc_item_content"<?php trx_addons_sc_show_attributes( 'sc_igenerator', $args, 'sc_items_wrapper' ); ?>>
			<div class="sc_igenerator_form"
				data-igenerator-default-model="<?php echo esc_attr( $args['model'] ); ?>"
				data-igenerator-number="<?php echo esc_attr( $args['number'] ); ?>"
				data-igenerator-popup="<?php echo ! empty( $args['show_popup'] ) && (int) $args['show_popup'] > 0 ? '1' : ''; ?>"
				data-igenerator-demo-images="<?php echo ! empty( $args['demo_images'] ) && ! empty( $args['demo_images'][0]['url'] ) ? '1' : ''; ?>"
				data-igenerator-limit-exceed="<?php echo esc_attr( trx_addons_get_option( "ai_helper_sc_igenerator_limit_alert" . ( ! empty( $args['premium'] ) ? '_premium' : '' ) ) ); ?>"
				data-igenerator-settings="<?php
					echo esc_attr( trx_addons_encode_settings( array(
						'number' => $args['number'],
						'columns' => $args['columns'],
						'columns_tablet' => $args['columns_tablet'],
						'columns_mobile' => $args['columns_mobile'],
						'size' => $args['size'],
						'width' => $args['width'],
						'height' => $args['height'],
						'demo_thumb_size' => $args['demo_thumb_size'],
						'demo_images' => $args['demo_images'],
						'model' => $args['model'],
						'premium' => ! empty( $args['premium'] ) ? 1 : 0,
						'show_download' => ! empty( $args['show_download'] ) ? 1 : 0,
						'show_prompt_translated' => ! empty( $args['show_prompt_translated'] ) ? 1 : 0,
						'safety_checker' => $args['safety_checker'],
						'quality' => $args['quality'],
						'system_prompt' => trim( $args['system_prompt'] ),
						// 'upscale' => ! empty( $args['upscale'] ) ? 1 : 0,
						// 'quality' => ! empty( $args['quality'] ) ? 1 : 0,
						// 'panorama' => ! empty( $args['panorama'] ) ? 1 : 0,
					) ) );
			?>">
				<div class="sc_igenerator_form_inner">
					<div class="sc_igenerator_form_actions">
						<ul class="sc_igenerator_form_actions_list">
							<li class="sc_igenerator_form_actions_item sc_igenerator_form_actions_item_generation sc_igenerator_form_actions_item_active"><a href="#" data-action="generation"><?php esc_html_e('Generation', 'trx_addons'); ?></a></li>
							<li class="sc_igenerator_form_actions_item sc_igenerator_form_actions_item_variations"><a href="#" data-action="variations"><?php esc_html_e('Variations', 'trx_addons'); ?></a></li>
							<?php if ( count( $upscalers ) > 0 ) { ?>
								<li class="sc_igenerator_form_actions_item sc_igenerator_form_actions_item_upscale"><a href="#" data-action="upscale"><?php esc_html_e('Upscale', 'trx_addons'); ?></a></li>
							<?php } ?>
							<li class="sc_igenerator_form_actions_slider"></li>
						</ul>
					</div>
					<div class="sc_igenerator_form_fields"><?php

						// Left block of fields
						?><div class="sc_igenerator_form_fields_left"><?php

							// Upload image
							$decorated = apply_filters( 'trx_addons_filter_sc_igenerator_decorate_upload', true );
							?><div class="sc_igenerator_form_field sc_igenerator_form_field_upload_image trx_addons_hidden" data-actions="variations,upscale">
								<div class="sc_igenerator_form_field_inner">
									<label for="sc_igenerator_form_field_upload_image_field"><?php esc_html_e( 'Upload image', 'trx_addons' ); ?></label><?php
									if ( $decorated ) {
										?>
										<div class="sc_igenerator_form_field_upload_image_decorator theme_form_field_text">
											<span class="sc_igenerator_form_field_upload_image_text theme_form_field_placeholder"><?php esc_html_e( 'Image is not selected', 'trx_addons' ); ?></span>
											<span class="sc_igenerator_form_field_upload_image_button trx_addons_icon-upload"><?php esc_html_e( 'Browse', 'trx_addons' ); ?></span>
										<?php
									}
									?><input type="file" id="sc_igenerator_form_field_upload_image_field" class="sc_igenerator_form_field_upload_image_field" placeholder="<?php esc_attr_e( "Select an image to make variations or upscale", 'trx_addons' ); ?>"><?php
									if ( $decorated ) {
										?></div><?php
									}
								?></div>
							</div><?php
							
							// Prompt
							?><div class="sc_igenerator_form_field sc_igenerator_form_field_prompt" data-actions="generation,variations">
								<div class="sc_igenerator_form_field_inner">
									<label for="sc_igenerator_form_field_prompt_text"><?php esc_html_e( 'Prompt', 'trx_addons' ); ?></label>
									<input type="text"
										class="sc_igenerator_form_field_prompt_text"
										value="<?php echo esc_attr( $args['prompt'] ); ?>"
										placeholder="<?php
											if ( ! empty( $args['placeholder_text'] ) ) {
												echo esc_attr( $args['placeholder_text'] );
											} else {
												esc_attr_e( 'Describe what you want or hit a tag below', 'trx_addons' );
											}
										?>"
									>
								</div>
							</div><?php

							// Negative prompt
							if ( ! empty( $args['show_negative_prompt'] ) ) {
								?><div class="sc_igenerator_form_field sc_igenerator_form_field_negative_prompt<?php
									if ( empty( $args['model'] ) || ! Utils::is_model_support_negative_prompt( $args['model'] ) ) {
										echo ' trx_addons_hidden';
									}
								?>" data-actions="generation,variations">
									<div class="sc_igenerator_form_field_inner">
										<label for="sc_igenerator_form_field_negative_prompt_text"><?php esc_html_e('Negative prompt (optional):', 'trx_addons'); ?></label>
										<input type="text"
											value="<?php echo esc_attr( $args['negative_prompt'] ); ?>"
											class="sc_igenerator_form_field_negative_prompt_text"
											placeholder="<?php
												if ( ! empty( $args['negative_placeholder_text'] ) ) {
													echo esc_attr( $args['negative_placeholder_text'] );
												} else {
													esc_attr_e( "Items you don't want in the image", 'trx_addons' );
												}
											?>"
										>
									</div>
								</div><?php
							}

							// Tags
							?><div class="sc_igenerator_form_field sc_igenerator_form_field_tags" data-actions="generation"><?php
								if ( ! empty( $args['tags_label'] ) ) {
									?><span class="sc_igenerator_form_field_tags_label"><?php echo esc_html( $args['tags_label'] ); ?></span><?php
								}
								if ( ! empty( $args['tags'] ) && is_array( $args['tags'] ) && count( $args['tags'] ) > 0 ) {
									?><span class="sc_igenerator_form_field_tags_list"><?php
										foreach ( $args['tags'] as $tag ) {
											?><a href="#" class="sc_igenerator_form_field_tags_item" data-tag-prompt="<?php echo esc_attr( $tag['prompt'] ); ?>"><?php echo esc_html( $tag['title'] ); ?></a><?php
										}
									?></span><?php
								}
							?></div>
						</div><?php
		
						// Right block of fields
						?><div class="sc_igenerator_form_fields_right"><?php
								
							// Model
							if ( is_array( $models ) ) {
								?><div class="sc_igenerator_form_field sc_igenerator_form_field_model" data-actions="generation,variations">
									<div class="sc_igenerator_form_field_inner">
										<label for="sc_igenerator_form_field_model"><?php esc_html_e( 'Model', 'trx_addons' ); ?></label>
										<div class="sc_igenerator_form_field_model_wrap<?php
											if ( ! empty( $args['show_settings'] ) && (int) $args['show_settings'] > 0 ) {
												echo ' sc_igenerator_form_field_model_wrap_with_settings';
											}
										?>">
											<select name="sc_igenerator_form_field_model" id="sc_igenerator_form_field_model"><?php
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
											?></select><?php

											if ( ! empty( $args['show_settings'] ) && (int) $args['show_settings'] > 0 ) {

												// Button "Settings"
												?><a href="#" class="sc_igenerator_form_settings_button trx_addons_icon-sliders"></a><?php

												// Popup with settings
												?><div class="sc_igenerator_form_settings"><?php

													// Stable Diffusion: Guidance scale
													?><div class="sc_igenerator_form_settings_field sc_igenerator_form_settings_field_guidance_scale"
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
													</div><?php

													// Stable Diffusion: Inference steps
													?><div class="sc_igenerator_form_settings_field sc_igenerator_form_settings_field_inference_steps"
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
													</div><?php

													// Stability AI: Cfg scale
													?><div class="sc_igenerator_form_settings_field sc_igenerator_form_settings_field_cfg_scale"
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
													</div><?php

													// Stability AI: Diffusion steps
													?><div class="sc_igenerator_form_settings_field sc_igenerator_form_settings_field_diffusion_steps"
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
													</div><?php

													// Stable Diffusion & Stability AI: Seed
													?><div class="sc_igenerator_form_settings_field sc_igenerator_form_settings_field_seed"
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
													</div><?php
												?></div><?php
											}
										?></div>
									</div>
								</div><?php
							}

							// Style
							if ( is_array( $styles ) ) {
								?><div class="sc_igenerator_form_field sc_igenerator_form_field_style<?php if ( empty( $args['model'] ) || ! Utils::is_stability_ai_model( $args['model'] ) ) echo ' trx_addons_hidden'; ?>" data-actions="generation,variations">
									<div class="sc_igenerator_form_field_inner">
										<label for="sc_igenerator_form_field_style"><?php esc_html_e( 'Style', 'trx_addons' ); ?></label>
										<select name="sc_igenerator_form_field_style" id="sc_igenerator_form_field_style"><?php
											foreach ( $styles as $style => $title ) {
												?><option value="<?php echo esc_attr( $style ); ?>"<?php
													if ( ! empty( $args['style'] ) && $args['style'] == $style ) {
														echo ' selected="selected"';
													}
												?>><?php
													echo esc_html( $title );
												?></option><?php
											}
										?></select>
									</div>
								</div><?php
							}

							// Style for OpenAI DALL-E 3 model
							if ( is_array( $styles_openai ) ) {
								?><div class="sc_igenerator_form_field sc_igenerator_form_field_style_openai<?php if ( empty( $args['model'] ) || ! Utils::is_openai_dall_e_3_model( $args['model'] ) ) echo ' trx_addons_hidden'; ?>" data-actions="generation,variations">
									<div class="sc_igenerator_form_field_inner">
										<label for="sc_igenerator_form_field_style_openai"><?php esc_html_e( 'Style', 'trx_addons' ); ?></label>
										<select name="sc_igenerator_form_field_style_openai" id="sc_igenerator_form_field_style_openai"><?php
											foreach ( $styles_openai as $style => $title ) {
												?><option value="<?php echo esc_attr( $style ); ?>"<?php
													if ( ! empty( $args['style_openai'] ) && $args['style_openai'] == $style ) {
														echo ' selected="selected"';
													}
												?>><?php
													echo esc_html( $title );
												?></option><?php
											}
										?></select>
									</div>
								</div><?php
							}

							// LoRA models
							?><div class="sc_igenerator_form_field sc_igenerator_form_field_lora_model<?php if ( empty( $args['model'] ) || ! Utils::is_stable_diffusion_model( $args['model'] ) ) echo ' trx_addons_hidden'; ?>" data-actions="generation,variations">
								<div class="sc_igenerator_form_field_inner">
									<label for="sc_igenerator_form_field_lora_model"><?php esc_html_e( 'LoRA model[s] (optional)', 'trx_addons' ); ?></label>
									<input type="text" name="sc_igenerator_form_field_lora_model" id="sc_igenerator_form_field_lora_model" value="" placeholder="<?php esc_attr_e( 'model_id[=strength][,model_id[=strength],...]', 'trx_addons' ); ?>">
									<div class="sc_igenerator_form_field_description"><?php
										echo wp_kses( sprintf(
													__( 'You can see a list of available models here: %s', 'trx_addons' ),
													'<a href="' . esc_url( StableDiffusion::instance()->get_url( 'models/section/lora' ) ) . '" target="_blank">' . esc_html__( 'LoRA models', 'trx_addons' ) . '</a>'
										), 'trx_addons_kses_content' );
									?></div>
								</div>
							</div><?php

							// Upscaler
							if ( is_array( $upscalers ) ) {
								?><div class="sc_igenerator_form_field sc_igenerator_form_field_upscaler" data-actions="upscale">
									<div class="sc_igenerator_form_field_inner">
										<label for="sc_igenerator_form_field_upscaler"><?php esc_html_e( 'Model', 'trx_addons' ); ?></label>
										<select name="sc_igenerator_form_field_upscaler" id="sc_igenerator_form_field_upscaler"><?php
											$i = 0;
											foreach ( $upscalers as $upscaler => $title ) {
												$i++;
												?><option value="<?php echo esc_attr( $upscaler ); ?>"<?php
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
							}

							// Size & Width & Height
							if ( is_array( $sizes ) ) {
								?><div class="sc_igenerator_form_field sc_igenerator_form_field_size" data-actions="generation,variations,upscale"><?php

									// Size
									?><div class="sc_igenerator_form_field_size_wrap">
										<div class="sc_igenerator_form_field_inner">
											<label for="sc_igenerator_form_field_size"><?php esc_html_e( 'Size (px)', 'trx_addons' ); ?></label>
											<select name="sc_igenerator_form_field_size" id="sc_igenerator_form_field_size"><?php
												foreach ( $sizes as $size => $title ) {
													?><option value="<?php echo esc_attr( $size ); ?>"<?php
														if ( ! empty( $args['size'] ) && $args['size'] == $size ) {
															echo ' selected="selected"';
														}
														if ( ! empty( $args['model'] ) && strpos( $args['model'], 'openai/' ) !== false && ! isset( $openai_sizes[ $size ] ) ) {
															echo ' class="trx_addons_hidden"';
														}
													?>><?php
														echo esc_html( $title );
													?></option><?php
												}
											?></select>
										</div>
									</div><?php

									// Scale
									?><div class="sc_igenerator_form_field_scale_wrap trx_addons_hidden">
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
									</div><?php

									// Dimensions (width & height)
									?><div class="sc_igenerator_form_field_dimensions_wrap<?php if ( $args['size'] != 'custom' ) echo ' trx_addons_hidden'; ?>"><?php

										// Width (numeric field)
										?><div class="sc_igenerator_form_field_width_wrap">
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
														value="<?php echo esc_attr( $args['width'] ); ?>"
													>
													<div class="sc_igenerator_form_field_numeric_wrap_buttons">
														<a href="#" class="sc_igenerator_form_field_numeric_wrap_button sc_igenerator_form_field_numeric_wrap_button_inc"></a>
														<a href="#" class="sc_igenerator_form_field_numeric_wrap_button sc_igenerator_form_field_numeric_wrap_button_dec"></a>
													</div>
												</div>
											</div>
										</div><?php

										// Height (numeric field)
										?><div class="sc_igenerator_form_field_height_wrap">
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
														value="<?php echo esc_attr( $args['height'] ); ?>"
													>
													<div class="sc_igenerator_form_field_numeric_wrap_buttons">
														<a href="#" class="sc_igenerator_form_field_numeric_wrap_button sc_igenerator_form_field_numeric_wrap_button_inc"></a>
														<a href="#" class="sc_igenerator_form_field_numeric_wrap_button sc_igenerator_form_field_numeric_wrap_button_dec"></a>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div><?php
							}

							// Button "Generate"
							?><div class="sc_igenerator_form_field sc_igenerator_form_field_generate" data-actions="generation,variations,upscale"><?php
								trx_addons_show_layout( trx_addons_sc_button( apply_filters( 'trx_addons_filter_sc_igenerator_button_generate_args', array( 'buttons' => array( array(
									"type" => "default",
									"size" => "small",
									"text_align" => "none",
									"icon" => "trx_addons_icon-magic",
									"icon_position" => "left",
									"title" => ! empty( $args['button_text'] ) ? $args['button_text'] : esc_html__( 'Generate', 'trx_addons' ),
									"link" => '#',
									'class' => 'sc_igenerator_form_field_generate_button',
								) ) ) ) ) );
							?></div>
						</div>
					</div><?php

					// Loading placeholder
					?><div class="trx_addons_loading"></div><?php

					if ( ! empty( $args['show_limits'] ) ) {
						$premium = ! empty( $args['premium'] ) && (int)$args['premium'] == 1;
						$suffix = $premium ? '_premium' : '';
						$limits = (int)trx_addons_get_option( "ai_helper_sc_igenerator_limits{$suffix}" ) > 0;
						if ( $limits ) {
							$generated = 0;
							if ( $premium ) {
								$user_id = get_current_user_id();
								$user_level = apply_filters( 'trx_addons_filter_sc_igenerator_user_level', $user_id > 0 ? 'default' : '', $user_id );
								if ( ! empty( $user_level ) ) {
									$levels = trx_addons_get_option( "ai_helper_sc_igenerator_levels_premium" );
									$level_idx = trx_addons_array_search( $levels, 'level', $user_level );
									$user_limit = $level_idx !== false ? $levels[ $level_idx ] : false;
									if ( isset( $user_limit['limit'] ) && trim( $user_limit['limit'] ) !== '' ) {
										$generated = trx_addons_sc_igenerator_get_total_generated( $user_limit['per'], $suffix, $user_id );
									}
								}
							}
							if ( ! $premium || empty( $user_level ) || ! isset( $user_limit['limit'] ) || trim( $user_limit['limit'] ) === '' ) {
								$generated = trx_addons_sc_igenerator_get_total_generated( 'hour', $suffix );
								$user_limit = array(
									'limit' => (int)trx_addons_get_option( "ai_helper_sc_igenerator_limit_per_hour{$suffix}" ),
									'requests' => (int)trx_addons_get_option( "ai_helper_sc_igenerator_limit_per_visitor{$suffix}" ),
									'per' => 'hour'
								);
							}
							if ( isset( $user_limit['limit'] ) && trim( $user_limit['limit'] ) !== '' ) {
								?><div class="sc_igenerator_limits"<?php
									// If a shortcode is called not from Elementor, we need to add the width of the prompt field and alignment
									if ( empty( $args['prompt_width_extra'] ) ) {
										if ( ! empty( $args['prompt_width'] ) && (int)$args['prompt_width'] < 100 ) {
											echo ' style="max-width:' . esc_attr( $args['prompt_width'] ) . '%"';
										}
									}
								?>>
									<span class="sc_igenerator_limits_total"><?php
										$periods = Lists::get_list_periods();
										echo wp_kses( sprintf(
															__( 'Limits%s: %s%s.', 'trx_addons' ),
															! empty( $periods[ $user_limit['per'] ] ) ? ' ' . sprintf( __( 'per %s', 'trx_addons' ), strtolower( $periods[ $user_limit['per'] ] ) ) : '',
															sprintf( __( '%s images', 'trx_addons' ), '<span class="sc_igenerator_limits_total_value">' . (int)$user_limit['limit'] . '</span>' ),
															! empty( $user_limit['requests'] ) ? ' ' . sprintf( __( ' for all visitors and up to %s requests from a single visitor', 'trx_addons' ), '<span class="sc_igenerator_limits_total_requests">' . (int)$user_limit['requests'] . '</span>' ) : '',
														),
														'trx_addons_kses_content'
													);
									?></span>
									<span class="sc_igenerator_limits_used"><?php
										echo wp_kses( sprintf(
															__( 'Used: %s images%s.', 'trx_addons' ),
															'<span class="sc_igenerator_limits_used_value">' . min( $generated, (int)$user_limit['limit'] )  . '</span>',
															! empty( $user_limit['requests'] ) ? sprintf( __( ', %s requests', 'trx_addons' ), '<span class="sc_igenerator_limits_used_requests">' . (int)trx_addons_get_value_gpc( 'trx_addons_ai_helper_igenerator_count' ) . '</span>' ) : '',
														),
														'trx_addons_kses_content'
													);
									?></span>
								</div><?php
							}
						}
					}

					?><div class="sc_igenerator_message"<?php
						// If a shortcode is called not from Elementor, we need to add the width of the prompt field and alignment
						if ( empty( $args['prompt_width_extra'] ) ) {
							if ( ! empty( $args['prompt_width'] ) && (int)$args['prompt_width'] < 100 ) {
								echo ' style="max-width:' . esc_attr( $args['prompt_width'] ) . '%"';
							}
						}
					?>>
						<div class="sc_igenerator_message_inner"></div>
						<a href="#" class="sc_igenerator_message_close trx_addons_button_close" title="<?php esc_html_e( 'Close', 'trx_addons' ); ?>"><span class="trx_addons_button_close_icon"></span></a>
					</div><?php

				?></div>

			</div><?php

			// Images preview area
			?><div class="sc_igenerator_images sc_igenerator_images_columns_<?php echo esc_attr( $args['columns'] ); ?> sc_igenerator_images_size_<?php echo esc_attr( $args['size'] ); ?>"></div><?php

		?></div>

		<?php trx_addons_sc_show_links('sc_igenerator', $args); ?>

	</div><?php

} else if ( true || trx_addons_is_preview() ) {

	?><div class="sc_igenerator_error"><?php
		esc_html_e( 'Image Generator: No models available', 'trx_addons' );
	?></div><?php

}