/* global jQuery, TRX_ADDONS_STORAGE */

jQuery( document ).ready( function() {
	if ( ! wp.media ) {
		return;
	}
	var View = wp.media.View;
	var mediaFrameObject = null;
	var oldMediaFrameSelect = wp.media.view.MediaFrame.Select;
	var oldMediaFramePost = wp.media.view.MediaFrame.Post;
	var l10n = wp.media.view.l10n;

	// Add a template to the media frame
	if ( jQuery( '#tmpl-trx-addons-ai-helper-image-generator').length == 0 ) {
		jQuery( 'body' ).append(
			`<script type="text/html" id="tmpl-trx-addons-ai-helper-image-generator">
				<div id="trx-addons-ai-helper-image-generator-inner">
					<div id="trx-addons-ai-helper-image-generator-header">
						<div id="trx-addons-ai-helper-image-generator-header-title">
							<h2>{{ data.title }}</h2>
						</div>
						<# if ( data.canClose ) { #>
							<button type="button" class="close media-modal-close" aria-label="Close dialog">
								<span class="media-modal-icon"></span>
							</button>
						<# } #>
					</div>
					<div id="trx-addons-ai-helper-image-generator-body">
						<div id="trx-addons-ai-helper-image-generator-settings">
							<div id="trx-addons-ai-helper-image-generator-settings-new">
								<div class="trx-addons-ai-helper-image-generator-settings-item trx-addons-ai-helper-image-generator-settings-item-model">
									<label for="trx-addons-ai-helper-image-generator-model" class="trx-addons-ai-helper-image-generator-settings-item-title">{{ wp.i18n.__( 'Model', 'trx_addons' ) }}</label>
									<div class="trx-addons-ai-helper-image-generator-settings-item-field"><#

										// Model
										#><select id="trx-addons-ai-helper-image-generator-model"><#
											var group = false;
											for ( var i in data.models ) {
												if ( i.slice(-2) == '/-' || data.models[i].slice( 0, 2 ) == '\\\\-' ) {
													if ( group ) {
														#></optgroup><#
													}
													group = true;
													#><optgroup label="<# print( data.models[i].slice( 2 ) ); #>"><#
												} else {
													#><option value="{{ i }}"<# if ( i == data.model ) print(' selected') #>>{{ data.models[i] }}</option><#
												}
											}
											if ( group ) {
												#></optgroup><#
											}
										#></select><#

										// Settings button
										#><a href="#" class="trx-addons-ai-helper-image-generator-settings-button trx_addons_icon-sliders"></a><#

										// Popup with settings
										#><div class="trx-addons-ai-helper-image-generator-settings-popup"><#

											// Stable Diffusion: Guidance scale
											#><div class="trx-addons-ai-helper-image-generator-settings-field trx-addons-ai-helper-image-generator-settings-field-guidance-scale">
												<label for="trx-addons-ai-helper-image-generator-settings-field-guidance-scale">{{ wp.i18n.__( 'Guidance scale:', 'trx_addons' ) }}</label>
												<input type="number"
														id="trx-addons-ai-helper-image-generator-settings-field-guidance-scale"
														min="1"
														max="20"
														step="0.1"
														value="{{ data.guidance_scale }}"
												>
												<div class="trx-addons-ai-helper-image-generator-settings-field-description">{{ wp.i18n.__( 'Scale for classifier-free guidance (min: 1; max: 20)', 'trx_addons' ) }}</div>
											</div><#

											// Stable Diffusion: Inference steps
											#><div class="trx-addons-ai-helper-image-generator-settings-field trx-addons-ai-helper-image-generator-settings-field-inference-steps">
												<label for="trx-addons-ai-helper-image-generator-settings-field-inference-steps">{{ wp.i18n.__( 'Inference steps:', 'trx_addons' ) }}</label>
												<input type="number"
														id="trx-addons-ai-helper-image-generator-settings-field-inference-steps"
														min="21"
														max="51"
														step="10"
														value="{{ data.inference_steps }}"
												>
												<div class="trx-addons-ai-helper-image-generator-settings-field-description">{{ wp.i18n.__( 'Number of denoising steps. The value accepts 21,31,41 and 51.', 'trx_addons' ) }}</div>
											</div><#

											// Stability AI: Cfg scale
											#><div class="trx-addons-ai-helper-image-generator-settings-field trx-addons-ai-helper-image-generator-settings-field-cfg-scale">
												<label for="trx-addons-ai-helper-image-generator-settings-field-cfg-scale">{{ wp.i18n.__( 'Cfg scale:', 'trx_addons' ) }}</label>
												<input type="number"
														id="trx-addons-ai-helper-image-generator-settings-field-cfg-scale"
														min="0"
														max="35"
														step="0.1"
														value="{{ data.cfg_scale }}"
												>
												<div class="trx-addons-ai-helper-image-generator-settings-field-description">{{ wp.i18n.__( 'How strictly the diffusion process adheres to the prompt text (higher values keep your image closer to your prompt)', 'trx_addons' ) }}</div>
											</div><#

											// Stability AI: Diffusion steps
											#><div class="trx-addons-ai-helper-image-generator-settings-field trx-addons-ai-helper-image-generator-settings-field-diffusion-steps">
												<label for="trx-addons-ai-helper-image-generator-settings-field-diffusion-steps">{{ wp.i18n.__( 'Diffusion steps:', 'trx_addons' ) }}</label>
												<input type="number"
														id="trx-addons-ai-helper-image-generator-settings-field-diffusion-steps"
														min="10"
														max="150"
														step="1"
														value="{{ data.diffusion_steps }}"
												>
												<div class="trx-addons-ai-helper-image-generator-settings-field-description">{{ wp.i18n.__( 'Number of diffusion steps to run.', 'trx_addons' ) }}</div>
											</div><#

											// Stable Diffusion & Stability AI: Seed
											#><div class="trx-addons-ai-helper-image-generator-settings-field trx-addons-ai-helper-image-generator-settings-field-seed">
												<label for="trx-addons-ai-helper-image-generator-settings-field-seed">{{ wp.i18n.__( 'Seed:', 'trx_addons' ) }}</label>
												<input type="number"
														id="trx-addons-ai-helper-image-generator-settings-field-seed"
														min="0"
														max="4294967295"
														step="1"
														value="0"
												>
												<div class="trx-addons-ai-helper-image-generator-settings-field-description">{{ wp.i18n.__( 'Seed is used to reproduce results, same seed will give you same image in return again. Pass 0 for a random number.', 'trx_addons' ) }}</div>
											</div>
										</div>
									</div>
								</div><#

								// Prompt
								#><div class="trx-addons-ai-helper-image-generator-settings-item">
									<label for="trx-addons-ai-helper-image-generator-prompt" class="trx-addons-ai-helper-image-generator-settings-item-title">{{ wp.i18n.__( 'Prompt for AI', 'trx_addons' ) }}</label>
									<div class="trx-addons-ai-helper-image-generator-settings-item-field">
										<textarea id="trx-addons-ai-helper-image-generator-prompt" rows="1"
											placeholder="{{ wp.i18n.__( 'Your requirements for generated images', 'trx_addons' ) }}"><#
												if ( data.prompt ) {
													#>{{ data.prompt }}<#
												}
										#></textarea>
									</div>
								</div><#

								// Negative prompt
								#><div class="trx-addons-ai-helper-image-generator-settings-item">
									<label for="trx-addons-ai-helper-image-generator-negative-prompt" class="trx-addons-ai-helper-image-generator-settings-item-title">{{ wp.i18n.__( 'Negative prompt' ) }}</label>
									<div class="trx-addons-ai-helper-image-generator-settings-item-field">
										<textarea id="trx-addons-ai-helper-image-generator-negative-prompt" rows="1"
											placeholder="{{ wp.i18n.__( "Items you don't want in the image", 'trx_addons' ) }}"><#
												if ( data.negative_prompt ) {
													#>{{ data.negative_prompt }}<#
												}
										#></textarea>
									</div>
								</div><#

								// Style
								#><div class="trx-addons-ai-helper-image-generator-settings-item">
									<label for="trx-addons-ai-helper-image-generator-style" class="trx-addons-ai-helper-image-generator-settings-item-title">{{ wp.i18n.__( 'Style', 'trx_addons' ) }}</label>
									<div class="trx-addons-ai-helper-image-generator-settings-item-field">
										<select id="trx-addons-ai-helper-image-generator-style">
											<# for ( var i in data.styles ) { #>
												<option value="{{ i }}"<# if ( i == data.style ) print(' selected') #>>{{ data.styles[i] }}</option>
											<# } #>
										</select>
									</div>
								</div><#

								// Style & Quality for OpenAI DALL-E-3 model
								#><div class="trx-addons-ai-helper-image-generator-settings-item">
									<label for="trx-addons-ai-helper-image-generator-style-openai" class="trx-addons-ai-helper-image-generator-settings-item-title">{{ wp.i18n.__( 'Style', 'trx_addons' ) }}</label>
									<div class="trx-addons-ai-helper-image-generator-settings-item-field">
										<select id="trx-addons-ai-helper-image-generator-style-openai">
											<# for ( var i in data.styles_openai ) { #>
												<option value="{{ i }}"<# if ( i == data.style ) print(' selected') #>>{{ data.styles_openai[i] }}</option>
											<# } #>
										</select>
										<label class="trx-addons-ai-helper-image-generator-quality"><input id="trx-addons-ai-helper-image-generator-quality" name="trx-addons-ai-helper-image-generator-quality" type="checkbox" value="hd"<# if ( data.quality == 'hd' ) { #> checked<# } #>>
												{{ wp.i18n.__( 'HD', 'trx_addons' ) }}
										</label>
									</div>
								</div><#

								// LoRA model
								#><div class="trx-addons-ai-helper-image-generator-settings-item">
									<label for="trx-addons-ai-helper-image-generator-lora-model" class="trx-addons-ai-helper-image-generator-settings-item-title">{{ wp.i18n.__( 'LoRA model[s] (optional)', 'trx_addons'  ) }}</label>
									<div class="trx-addons-ai-helper-image-generator-settings-item-field">
										<input type-"text" id="trx-addons-ai-helper-image-generator-lora-model" value="{{ data.lora_model }}" placeholder="{{ wp.i18n.__( 'model_id[=strength][,model_id[=strength],...]', 'trx_addons' ) }}">
									</div>
								</div><#

								// Size
								#><div class="trx-addons-ai-helper-image-generator-settings-item">
									<label for="trx-addons-ai-helper-image-generator-size" class="trx-addons-ai-helper-image-generator-settings-item-title">{{ wp.i18n.__( 'Size', 'trx_addons' ) }}</label>
									<div class="trx-addons-ai-helper-image-generator-settings-item-field">
										<select id="trx-addons-ai-helper-image-generator-size">
											<# for ( var i in data.sizes ) { #>
												<option value="{{ i }}"<#
													if ( i == data.size ) print(' selected')
													if ( data.model.indexOf('openai/') >= 0 && ! data.sizes_openai[i] ) print(' class="trx_addons_hidden"' );
												#>>{{ data.sizes[i] }}</option>
											<# } #>
										</select>
									</div>
								</div><#

								// Width x Height
								#><div class="trx-addons-ai-helper-image-generator-settings-item">
									<label for="trx-addons-ai-helper-image-generator-width" class="trx-addons-ai-helper-image-generator-settings-item-title">{{ wp.i18n.__( 'Width x Height', 'trx_addons' ) }}</label>
									<div class="trx-addons-ai-helper-image-generator-settings-item-field">
										<input id="trx-addons-ai-helper-image-generator-width" type="number" min="0" max="1024" step="8" value="{{ data.width }}" />
										<span class="trx-addons-ai-helper-image-generator-settings-item-field-delimiter">x</span>
										<input id="trx-addons-ai-helper-image-generator-height" type="number" min="0" max="1024" step="8" value="{{ data.height }}" />
									</div>
								</div><#

								// Number of images & Append/Replace
								#><div class="trx-addons-ai-helper-image-generator-settings-item">
									<label for="trx-addons-ai-helper-image-generator-number" class="trx-addons-ai-helper-image-generator-settings-item-title">{{ wp.i18n.__( '# of images', 'trx_addons' ) }}</label>
									<div class="trx-addons-ai-helper-image-generator-settings-item-field">
										<select id="trx-addons-ai-helper-image-generator-number">
											<#
											for ( var n in data.numbers ) {
												#><option value="{{ n }}"<# if ( n == data.number ) print(' selected') #>>{{ n }}</option><#
											}
											#>
										</select>
										<label class="trx-addons-ai-helper-image-generator-append"><input id="trx-addons-ai-helper-image-generator-append" name="trx-addons-ai-helper-image-generator-append" type="radio" value="append"<# if ( data.append == 'append' ) { #> checked<# } #>>
												{{ wp.i18n.__( 'Append', 'trx_addons' ) }}
										</label>
										<label class="trx-addons-ai-helper-image-generator-append"><input id="trx-addons-ai-helper-image-generator-replace" name="trx-addons-ai-helper-image-generator-append" type="radio" value="replace"<# if ( data.append == 'replace' ) { #> checked<# } #>>
												{{ wp.i18n.__( 'Replace', 'trx_addons' ) }}
										</label>
									</div>
								</div><#

								// Generate button
								#><div class="trx-addons-ai-helper-image-generator-settings-item">
									<button id="trx-addons-ai-helper-image-generator-generate" type="button" class="components-button trx-addons-ai-helper-image-generator-button is-primary">
										<span class="dashicon dashicons dashicons-images-alt trx-addons-ai-helper-image-generator-button-icon"></span>
										<span class="trx-addons-ai-helper-image-generator-button-text">{{ wp.i18n.__( 'Generate images', 'trx_addons' ) }}</span>
									</button>
								</div>
							</div><#

							// Process the selected image
							#><div class="trx-addons-ai-helper-image-generator-settings-subtitle trx-addons-ai-helper-image-generator-settings-selected-subtitle trx_addons_hidden">
								<h3>{{ wp.i18n.__( 'Process the selected image', 'trx_addons' ) }}</h3>
								<a href="#" id="trx-addons-ai-helper-image-generator-settings-selected-delete" title="{{ wp.i18n.__( 'Delete the selected image from the preview area (not from the Media Library)', 'trx_addons' ) }}"><span class="dashicon dashicons-delete"></span>{{ wp.i18n.__( 'Delete', 'trx_addons' ) }}</a>
							</div>
							<div id="trx-addons-ai-helper-image-generator-settings-selected" class="trx_addons_hidden">
								<div class="trx-addons-ai-helper-image-generator-settings-actions">
									<ul class="trx-addons-ai-helper-image-generator-settings-actions-list">
										<li class="trx-addons-ai-helper-image-generator-settings-actions-item trx-addons-ai-helper-image-generator-settings-actions-item-add-to-library trx-addons-ai-helper-image-generator-settings-actions-item-active"><a href="#" data-action="add_to_library">{{ wp.i18n.__( 'Add to Library', 'trx_addons' ) }}</a></li>
										<li class="trx-addons-ai-helper-image-generator-settings-actions-item trx-addons-ai-helper-image-generator-settings-actions-item-variations"><a href="#" data-action="variations">{{ wp.i18n.__( 'Variations', 'trx_addons' ) }}</a></li>
										<# if ( ! data.upscalers.hasOwnProperty( 'length' ) && Object.keys( data.upscalers ).length ) { #>
											<li class="trx-addons-ai-helper-image-generator-settings-actions-item trx-addons-ai-helper-image-generator-settings-actions-item-upscale"><a href="#" data-action="upscale">{{ wp.i18n.__( 'Upscale', 'trx_addons' ) }}</a></li>
										<# } #>
										<li class="trx-addons-ai-helper-image-generator-settings-actions-slider"></li>
									</ul>
								</div><#

								// Add to Library
								#><div class="trx-addons-ai-helper-image-generator-settings-item" data-actions="add_to_library">
									<label for="trx-addons-ai-helper-image-generator-filename" class="trx-addons-ai-helper-image-generator-settings-item-title">{{ wp.i18n.__( 'Name', 'trx_addons' ) }}</label>
									<div class="trx-addons-ai-helper-image-generator-settings-item-field">
										<input type="text" id="trx-addons-ai-helper-image-generator-filename" placeholder="{{ wp.i18n.__( 'File name', 'trx_addons' ) }}" value="{{ data.filename }}">
									</div>
								</div>
								<div class="trx-addons-ai-helper-image-generator-settings-item" data-actions="add_to_library">
									<label for="trx-addons-ai-helper-image-generator-caption" class="trx-addons-ai-helper-image-generator-settings-item-title">{{ wp.i18n.__( 'Caption', 'trx_addons' ) }}</label>
									<div class="trx-addons-ai-helper-image-generator-settings-item-field">
										<textarea id="trx-addons-ai-helper-image-generator-caption" rows="2"
											placeholder="{{ wp.i18n.__( 'Caption of the image', 'trx_addons' ) }}"><#
												if ( data.caption ) {
													#>{{ data.caption }}<#
												}
										#></textarea>
									</div>
								</div>
								<div class="trx-addons-ai-helper-image-generator-settings-item" data-actions="add_to_library">
									<button id="trx-addons-ai-helper-image-generator-upload" type="button" class="components-button trx-addons-ai-helper-image-generator-button is-primary">
										<span class="dashicon dashicons dashicons-upload trx-addons-ai-helper-image-generator-button-icon"></span>
										<span class="trx-addons-ai-helper-image-generator-button-text">{{ wp.i18n.__( 'Add to Media Library', 'trx_addons' ) }}</span>
									</button>
								</div><#

								// Variations
								#><div class="trx-addons-ai-helper-image-generator-settings-item" data-actions="variations">
									<div class="trx-addons-ai-helper-image-generator-settings-item-field trx-addons-ai-helper-image-generator-settings-item-field-variations">
										<p>{{ wp.i18n.__( 'Fill in the fields in the image generation form above (model, prompts, size, quantity, etc.) and click the "Make variations" button', 'trx_addons' ) }}</p>
										<p>{{ wp.i18n.__( 'Note: Since the ModelsLab (ex Stable Diffusion) API requires URL access to the source image, your site must be accessible from the Internet. This means that "Make variations" and "Upscale" operations are not available on a local installation with models from this API.', 'trx_addons' ) }}</p>
									</div>
								</div>
								<div class="trx-addons-ai-helper-image-generator-settings-item" data-actions="variations">
									<button id="trx-addons-ai-helper-image-generator-make-variations" type="button" class="components-button trx-addons-ai-helper-image-generator-button is-primary">
										<span class="dashicon dashicons dashicons-format-gallery trx-addons-ai-helper-image-generator-button-icon"></span>
										<span class="trx-addons-ai-helper-image-generator-button-text">{{ wp.i18n.__( 'Make variations', 'trx_addons' ) }}</span>
									</button>
								</div><#

								// Upscale
								#><div class="trx-addons-ai-helper-image-generator-settings-item" data-actions="upscale">
									<label for="trx-addons-ai-helper-image-generator-upscaler" class="trx-addons-ai-helper-image-generator-settings-item-title">{{ wp.i18n.__( 'Upscaler', 'trx_addons' ) }}</label>
									<div class="trx-addons-ai-helper-image-generator-settings-item-field">
										<select id="trx-addons-ai-helper-image-generator-upscaler">
											<#
											var i = 0;
											for ( var n in data.upscalers ) {
												#><option value="{{ n }}"<# if ( i++ === 0 ) print(' selected') #>>{{ n }}</option><#
											}
											#>
										</select>
									</div>
								</div>
								<div class="trx-addons-ai-helper-image-generator-settings-item" data-actions="upscale">
									<label for="trx-addons-ai-helper-image-generator-upscale-factor" class="trx-addons-ai-helper-image-generator-settings-item-title">{{ wp.i18n.__( 'Upscale factor', 'trx_addons' ) }}</label>
									<div class="trx-addons-ai-helper-image-generator-settings-item-field">
										<select id="trx-addons-ai-helper-image-generator-upscale-factor">
											<#
											for ( var n = 2; n <= 4; n++ ) {
												#><option value="{{ n }}"<# if ( n == 2 ) print(' selected') #>>{{ n }}</option><#
											}
											#>
										</select>
									</div>
								</div>
								<div class="trx-addons-ai-helper-image-generator-settings-item" data-actions="upscale">
									<label for="trx-addons-ai-helper-image-generator-upscale-width" class="trx-addons-ai-helper-image-generator-settings-item-title">{{ wp.i18n.__( 'Width or Height', 'trx_addons' ) }}</label>
									<div class="trx-addons-ai-helper-image-generator-settings-item-field">
										<input id="trx-addons-ai-helper-image-generator-upscale-width" type="number" min="0" max="4096" step="8" value="2048" />
										<span class="trx-addons-ai-helper-image-generator-settings-item-field-delimiter">x</span>
										<input id="trx-addons-ai-helper-image-generator-upscale-height" type="number" min="0" max="4096" step="8" value="" />
									</div>
								</div>
								<div class="trx-addons-ai-helper-image-generator-settings-item" data-actions="upscale">
									<button id="trx-addons-ai-helper-image-generator-make-upscale" type="button" class="components-button trx-addons-ai-helper-image-generator-button is-primary">
										<span class="dashicon dashicons dashicons-format-gallery trx-addons-ai-helper-image-generator-button-icon"></span>
										<span class="trx-addons-ai-helper-image-generator-button-text">{{ wp.i18n.__( 'Upscale image', 'trx_addons' ) }}</span>
									</button>
								</div>
							</div><#

							// Busy wrapper
							#><div id="trx-addons-ai-helper-image-generator-settings-busy">
							</div>
						</div><#

						// Preview images placeholder
						#><div id="trx-addons-ai-helper-image-generator-preview"<# if ( data.images.length == 0 ) { #> class="trx_addons_hidden"<# } #>><#
							if ( data.images.length ) {
								data.images.forEach( function( img ) {
									#><a href="javascript:void(0)" class="trx-addons-ai-helper-image-generator-preview-image"><img src="{{ img }}" alt=""></a><#
								} )
							}
						#></div>
					</div>
				</div>
			</script>`
		);
	}

	// Extend the media frame with our custom view
	wp.media.view.TrxAddonsAiHelperImageGenerator = View.extend( {
		tagName:   'div',
		className: 'trx-addons-ai-helper-image-generator',
		template:  wp.template('trx-addons-ai-helper-image-generator'),
		fetch_img: '',
	
		events: {
			'click .close':													'hide',
			// Generate images
			'change #trx-addons-ai-helper-image-generator-model':			'changeModel',
			'click .trx-addons-ai-helper-image-generator-settings-button':	'showSettings',
			'change #trx-addons-ai-helper-image-generator-settings-field-guidance-scale':	'changeSettingGuidanceScale',
			'change #trx-addons-ai-helper-image-generator-settings-field-inference-steps':	'changeSettingInferenceSteps',
			'change #trx-addons-ai-helper-image-generator-settings-field-cfg-scale':		'changeSettingCfgScale',
			'change #trx-addons-ai-helper-image-generator-settings-field-diffusion-steps':	'changeSettingDiffusionSteps',
			'change #trx-addons-ai-helper-image-generator-settings-field-seed':				'changeSettingSeed',
			'change #trx-addons-ai-helper-image-generator-prompt':			'changePrompt',
			'change #trx-addons-ai-helper-image-generator-negative-prompt':	'changeNegativePrompt',
			'change #trx-addons-ai-helper-image-generator-style':			'changeStyle',
			'change #trx-addons-ai-helper-image-generator-style-openai':	'changeStyleOpenAI',
			'change #trx-addons-ai-helper-image-generator-lora-model':		'changeLoraModel',
			'change #trx-addons-ai-helper-image-generator-quality':			'changeQuality',
			'change #trx-addons-ai-helper-image-generator-size':			'changeSize',
			'change #trx-addons-ai-helper-image-generator-width':			'changeWidth',
			'change #trx-addons-ai-helper-image-generator-height':			'changeHeight',
			'change #trx-addons-ai-helper-image-generator-number':			'changeNumber',
			'change #trx-addons-ai-helper-image-generator-append':			'changeAppend',
			'change #trx-addons-ai-helper-image-generator-replace':			'changeReplace',
			'click #trx-addons-ai-helper-image-generator-generate':			'generateImages',
			// Process the selected image
			'click #trx-addons-ai-helper-image-generator-settings-selected-delete':	'deleteImage',
			'click .trx-addons-ai-helper-image-generator-settings-actions a':	'changeAction',
			// Add to Library
			'change #trx-addons-ai-helper-image-generator-filename':		'changeFilename',
			'change #trx-addons-ai-helper-image-generator-caption':			'changeCaption',
			'click #trx-addons-ai-helper-image-generator-upload':			'addToUploads',
			// Variations
			'click #trx-addons-ai-helper-image-generator-make-variations':	'makeVariations',
			// Upscale
			'change #trx-addons-ai-helper-image-generator-upscaler':		'changeUpscaler',
			'change #trx-addons-ai-helper-image-generator-upscale-factor':	'changeUpscaleFactor',
			'change #trx-addons-ai-helper-image-generator-upscale-width':	'changeUpscaleWidth',
			'change #trx-addons-ai-helper-image-generator-upscale-height':	'changeUpscaleHeight',
			'click #trx-addons-ai-helper-image-generator-make-upscale':		'makeUpscale',
			// Preview images
			'click .trx-addons-ai-helper-image-generator-preview-image':	'clickImage',
			'keydown .trx-addons-ai-helper-image-generator-preview-image':	'keydownImage'
		},
	
		initialize: function() {
			_.defaults( this.options, {
				title: '',
				status:  true,
				canClose: false,
				models: TRX_ADDONS_STORAGE['ai_helper_generate_image_models'],
				styles: TRX_ADDONS_STORAGE['ai_helper_generate_image_styles'],
				styles_openai: TRX_ADDONS_STORAGE['ai_helper_generate_image_styles_openai'],
				sizes: TRX_ADDONS_STORAGE['ai_helper_generate_image_sizes'],
				sizes_openai: TRX_ADDONS_STORAGE['ai_helper_generate_image_sizes_openai'],
				numbers: TRX_ADDONS_STORAGE['ai_helper_generate_image_numbers'],
				upscalers: TRX_ADDONS_STORAGE['ai_helper_generate_image_upscalers'],
			} );

			var model = trx_addons_get_cookie( 'trx_addons_ai_helper_generate_image_model', 'openai/default' );
			if ( ! this.options.models.hasOwnProperty( model ) ) {
				model = trx_addons_array_first_key( this.options.models );
			}

			var upscaler = trx_addons_get_cookie( 'trx_addons_ai_helper_generate_image_upscaler', 'stable-diffusion/upscale-sd-default' );

			if ( ! this.controller.state().get('model' ) )              this.controller.state().set( 'model', model );
			if ( ! this.controller.state().get('guidance_scale' ) )     this.controller.state().set( 'guidance_scale', TRX_ADDONS_STORAGE['ai_helper_generate_image_guidance_scale'] );
			if ( ! this.controller.state().get('inference_steps' ) )    this.controller.state().set( 'inference_steps', TRX_ADDONS_STORAGE['ai_helper_generate_image_inference_steps'] );
			if ( ! this.controller.state().get('cfg_scale' ) )          this.controller.state().set( 'cfg_scale', TRX_ADDONS_STORAGE['ai_helper_generate_image_cfg_scale'] );
			if ( ! this.controller.state().get('diffusion_steps' ) )    this.controller.state().set( 'diffusion_steps', TRX_ADDONS_STORAGE['ai_helper_generate_image_diffusion_steps'] );
			if ( ! this.controller.state().get('seed' ) )    			this.controller.state().set( 'seed', 0 );
			if ( ! this.controller.state().get('prompt' ) )             this.controller.state().set( 'prompt', '' );
			if ( ! this.controller.state().get('negative_prompt' ) )    this.controller.state().set( 'negative_prompt', '' );
			if ( ! this.controller.state().get('style' ) )              this.controller.state().set( 'style', '' );
			if ( ! this.controller.state().get('lora_model' ) )         this.controller.state().set( 'lora_model', '' );
			if ( ! this.controller.state().get('quality' ) )            this.controller.state().set( 'quality', '' );
			if ( ! this.controller.state().get('size' ) )               this.controller.state().set( 'size', '1024x1024' );
			if ( ! this.controller.state().get('width' ) )              this.controller.state().set( 'width', 1024 );
			if ( ! this.controller.state().get('height' ) )             this.controller.state().set( 'height', 1024 );
			if ( ! this.controller.state().get('number' ) )             this.controller.state().set( 'number', 3 );
			if ( ! this.controller.state().get('append' ) )             this.controller.state().set( 'append', 'append' );
			if ( ! this.controller.state().get('filename' ) )           this.controller.state().set( 'filename', '' );
			if ( ! this.controller.state().get('caption' ) )            this.controller.state().set( 'caption', '' );
			if ( ! this.controller.state().get('upscaler' ) )           this.controller.state().set( 'upscaler', upscaler );
			if ( ! this.controller.state().get('upscale_factor' ) )     this.controller.state().set( 'upscale_factor', 2 );
			if ( ! this.controller.state().get('upscale_width' ) )      this.controller.state().set( 'upscale_width', 2048 );
			if ( ! this.controller.state().get('upscale_height' ) )     this.controller.state().set( 'upscale_height', '' );
			if ( ! this.controller.state().get('images' ) )             this.controller.state().set( 'images', [] );

			this.controller.state().frame.on( 'library:selection:variations', this.attachmentMakeVariations, this );
		},

		attachmentMakeVariations: function( url ) {
			// Add an image to the preview
			this.addImageToPreview( url, {
				select: true,
				action: 'variations'
			} );
			// Add an image to the state
			var images_from_state = this.controller.state().get('images');
			images_from_state.push( url );
			this.controller.state().set('images', images_from_state);
		},

		/**
		 * Restore data from the state
		 * 
		 * @return object with data
		 */
		prepare: function() {
			var data = {
				// Options
				title:     this.options.title,
				canClose:  this.options.canClose,
				models:    this.options.models,
				styles:    this.options.styles,
				styles_openai: this.options.styles_openai,
				sizes:     this.options.sizes,
				sizes_openai: this.options.sizes_openai,
				numbers:   this.options.numbers,
				upscalers: this.options.upscalers,
				// Settings
				guidance_scale:     this.controller.state().get('guidance_scale'),
				inference_steps:    this.controller.state().get('inference_steps'),
				cfg_scale:          this.controller.state().get('cfg_scale'),
				diffusion_steps:    this.controller.state().get('diffusion_steps'),
				seed:               this.controller.state().get('seed'),
				// States
				prompt:     this.controller.state().get('prompt'),
				negative_prompt: this.controller.state().get('negative_prompt'),
				model:      this.controller.state().get('model'),
				style:      this.controller.state().get('style'),
				lora_model: this.controller.state().get('lora_model'),
				quality:    this.controller.state().get('quality'),
				size:       this.controller.state().get('size'),
				width:      this.controller.state().get('width'),
				height:     this.controller.state().get('height'),
				number:     this.controller.state().get('number'),
				append:     this.controller.state().get('append'),
				filename:   this.controller.state().get('filename'),
				caption:    this.controller.state().get('caption'),
				upscaler:   this.controller.state().get('upscaler'),
				upscale_factor: this.controller.state().get('upscale_factor'),
				upscale_width:  this.controller.state().get('upscale_width'),
				upscale_height: this.controller.state().get('upscale_height'),
				images:     this.controller.state().get('images')
			};
			return data;
		},

		/**
		 * @return {wp.media.view.TrxAddonsAiHelperImageGenerator} Returns itself to allow chaining.
		 */
		dispose: function() {
			if ( this.disposing ) {
				/**
				 * call 'dispose' directly on the parent class
				 */
				return View.prototype.dispose.apply( this, arguments );
			}
	
			/*
			* Run remove on `dispose`, so we can be sure to refresh the
			* uploader with a view-less DOM. Track whether we're disposing
			* so we don't trigger an infinite loop.
			*/
			this.disposing = true;
			return this.remove();
		},
		remove: function() {
			/**
			 * call 'remove' directly on the parent class
			 */
			var result = View.prototype.remove.apply( this, arguments );
	
			_.defer( _.bind( this.refresh, this ) );
			return result;
		},
		refresh: function() {
		},
		ready: function() {
			this.refresh();
			this.checkVisibility();
			return this;
		},
		show: function() {
			this.$el.removeClass( 'hidden' );
		},
		hide: function() {
			this.$el.addClass( 'hidden' );
		},


		/**
		 * Check visibility of fields 'size', 'width' and 'height'
		 */
		checkVisibility: function() {
			var model = this.controller.state().get('model'),
				size = this.controller.state().get('size'),
				sizes_openai = this.options.sizes_openai;
			// Show/hide field 'style'
			jQuery( '#trx-addons-ai-helper-image-generator-style' ).parents('.trx-addons-ai-helper-image-generator-settings-item').toggleClass( 'trx_addons_hidden', model.indexOf( 'stability-ai/' ) < 0 );
			// Show/hide field 'style-openai'
			jQuery( '#trx-addons-ai-helper-image-generator-style-openai' ).parents('.trx-addons-ai-helper-image-generator-settings-item').toggleClass( 'trx_addons_hidden', model.indexOf( 'openai/dall-e-3' ) < 0 );
			// Show/hide field 'lora-model'
			jQuery( '#trx-addons-ai-helper-image-generator-lora-model' ).parents('.trx-addons-ai-helper-image-generator-settings-item').toggleClass( 'trx_addons_hidden', model.indexOf( 'stable-diffusion/' ) < 0 || model == 'stable-diffusion/default' );
			// Show/hide field 'negative_prompt'
			jQuery( '#trx-addons-ai-helper-image-generator-negative-prompt' ).parents('.trx-addons-ai-helper-image-generator-settings-item').toggleClass( 'trx_addons_hidden', model.indexOf( 'stable-diffusion/' ) < 0 && model.indexOf( 'stability-ai/' ) < 0 );
			// Show/hide fields options in the field 'size' if model is 'OpenAI'
			jQuery( '#trx-addons-ai-helper-image-generator-size option' ).each( function() {
				jQuery(this).toggleClass( 'trx_addons_hidden', model.indexOf( 'openai/' ) >= 0 && ! sizes_openai[ jQuery(this).val() ] );
			} );
			if ( model.indexOf( 'openai/' ) >= 0 ) {
				if ( ! sizes_openai[ this.controller.state().get('size') ] ) {
					this.controller.state().set('size', '1024x1024');
					jQuery( '#trx-addons-ai-helper-image-generator-size' ).val( '1024x1024' ).trigger( 'change' );
				}
			}
			// Show/hide fields 'width' and 'height'
			jQuery( '#trx-addons-ai-helper-image-generator-width' ).parents('.trx-addons-ai-helper-image-generator-settings-item').toggleClass( 'trx_addons_hidden', ( model.indexOf( 'stable-diffusion/' ) < 0 && model.indexOf( 'stability-ai/' ) < 0 ) || size != 'custom' );
			// Hide options greater then 4 in the field 'number' if the model is 'stable-diffusion'
			jQuery( '#trx-addons-ai-helper-image-generator-number option' ).each( function() {
				jQuery(this).toggleClass( 'trx_addons_hidden', model.indexOf( 'stable-diffusion/' ) >= 0 && parseInt( jQuery(this).val() ) > 4 );
			} );
			if ( model.indexOf( 'stable-diffusion/' ) >= 0 ) {
				if ( this.controller.state().get('number') > 4 ) {
					this.controller.state().set('number', 4);
					jQuery( '#trx-addons-ai-helper-image-generator-number' ).val( 4 ).trigger( 'change' ); 
				}
			}
			// Show/hide the button "Settings"
			jQuery( '.trx-addons-ai-helper-image-generator-settings-button' ).toggleClass( 'trx_addons_hidden', model.indexOf( 'stable-diffusion/' ) < 0 && model.indexOf( 'stability-ai/' ) < 0 );
			// Show/hide the settings field 'Guidance Scale'
			jQuery( '#trx-addons-ai-helper-image-generator-settings-field-guidance-scale' ).parents('.trx-addons-ai-helper-image-generator-settings-field').toggleClass( 'trx_addons_hidden', model.indexOf( 'stable-diffusion/' ) < 0 );
			// Show/hide the settings field 'Inference Steps'
			jQuery( '#trx-addons-ai-helper-image-generator-settings-field-inference-steps' ).parents('.trx-addons-ai-helper-image-generator-settings-field').toggleClass( 'trx_addons_hidden', model.indexOf( 'stable-diffusion/' ) < 0 );
			// Show/hide the settings field 'Cfg Scale'
			jQuery( '#trx-addons-ai-helper-image-generator-settings-field-cfg-scale' ).parents('.trx-addons-ai-helper-image-generator-settings-field').toggleClass( 'trx_addons_hidden', model.indexOf( 'stability-ai/' ) < 0 );
			// Show/hide the settings field 'Diffusion Steps'
			jQuery( '#trx-addons-ai-helper-image-generator-settings-field-diffusion-steps' ).parents('.trx-addons-ai-helper-image-generator-settings-field').toggleClass( 'trx_addons_hidden', model.indexOf( 'stability-ai/' ) < 0 );

			// Process the selected image
			var action = jQuery( '.trx-addons-ai-helper-image-generator-settings-actions-item-active a' ).data( 'action' ),
				upscaler = this.controller.state().get('upscaler');
			jQuery( '.trx-addons-ai-helper-image-generator-settings-item[data-actions]' ).each( function() {
				var $self = jQuery(this),
					hide = $self.data( 'actions' ) != action;
				if ( $self.find( '#trx-addons-ai-helper-image-generator-upscale-factor' ).length > 0 ) {
					hide ||= upscaler.indexOf( 'stable-diffusion/' ) < 0;
				} else if ( $self.find( '#trx-addons-ai-helper-image-generator-upscale-width' ).length > 0 ) {
					hide ||= upscaler.indexOf( 'stability-ai/' ) < 0;
				}
				$self.toggleClass( 'trx_addons_hidden', hide );
			} );
		},

		/**
		 * Change a generation model in the state
		 */
		changeModel: function(e) {
			this.controller.state().set( 'model', jQuery( e.target ).val() );
			this.checkVisibility();
		},

		/**
		 * Change the setting 'Guidance scale' in the state
		 */
		changeSettingGuidanceScale: function(e) {
			this.controller.state().set( 'guidance_scale', jQuery( e.target ).val() );
		},

		/**
		 * Change the setting 'Inference Steps' in the state
		 */
		changeSettingInferenceSteps: function(e) {
			this.controller.state().set( 'inference_steps', jQuery( e.target ).val() );
		},

		/**
		 * Change the setting 'Cfg scale' in the state
		 */
		changeSettingCfgScale: function(e) {
			this.controller.state().set( 'cfg_scale', jQuery( e.target ).val() );
		},

		/**
		 * Change the setting 'Duffusion Steps' in the state
		 */
		changeSettingDuffusionSteps: function(e) {
			this.controller.state().set( 'duffusion_steps', jQuery( e.target ).val() );
		},

		/**
		 * Change the setting 'Seed' in the state
		 */
		changeSettingSeed: function(e) {
			this.controller.state().set( 'seed', jQuery( e.target ).val() );
		},

		/**
		 * Change a prompt in the state
		 */
		changePrompt: function(e) {
			this.controller.state().set( 'prompt', jQuery( e.target ).val() );
		},

		/**
		 * Change a negative prompt in the state
		 */
		changeNegativePrompt: function(e) {
			this.controller.state().set( 'negative_prompt', jQuery( e.target ).val() );
		},

		showSettings: function(e) {
			e.preventDefault();
			jQuery( e.target ).next().toggleClass( 'show' );
			return false;
		},

		/**
		 * Change a style of the image in the state
		 */
		changeStyle: function(e) {
			this.controller.state().set( 'style', jQuery( e.target ).val() );
		},

		/**
		 * Change a style of the image in the state
		 */
		changeStyleOpenAI: function(e) {
			this.controller.state().set( 'style', jQuery( e.target ).val() );
		},

		/**
		 * Change a LoRA model in the state
		 */
		changeLoraModel: function(e) {
			this.controller.state().set( 'lora_model', jQuery( e.target ).val() );
		},

		/**
		 * Change a quality in the state
		 */
		changeQuality: function(e) {
			var checkbox = jQuery( e.target ),
				value = checkbox.prop('checked') ? 'hd' : '';
			this.controller.state().set( 'quality', value );
		},

		/**
		 * Change a size of the image in the state
		 */
		changeSize: function(e) {
			this.controller.state().set( 'size', jQuery( e.target ).val() );
			this.checkVisibility();
		},

		/**
		 * Change a width of image in the state
		 */
		changeWidth: function(e) {
			this.controller.state().set( 'width', jQuery( e.target ).val() );
		},

		/**
		 * Change a height of image in the state
		 */
		changeHeight: function(e) {
			this.controller.state().set( 'height', jQuery( e.target ).val() );
		},

		/**
		 * Change a number of images in the state
		 */
		changeNumber: function(e) {
			this.controller.state().set( 'number', jQuery( e.target ).val() );
		},

		/**
		 * Change an append mode in the state
		 */
		changeAppend: function(e) {
			var checkbox = jQuery( e.target ),
				value = checkbox.prop('checked') ? 'append' : 'replace';
			this.controller.state().set( 'append', value );
		},

		/**
		 * Change an append mode in the state
		 */
		changeReplace: function(e) {
			var checkbox = jQuery( e.target ),
				value = checkbox.prop('checked') ? 'replace' : 'append';
			this.controller.state().set( 'append', value );
		},

		/**
		 * Select an action for the selected image
		 */
		changeAction: function(e) {
			jQuery( '.trx-addons-ai-helper-image-generator-settings-actions-item-active' ).removeClass( 'trx-addons-ai-helper-image-generator-settings-actions-item-active' );
			jQuery( e.target ).parent().addClass( 'trx-addons-ai-helper-image-generator-settings-actions-item-active' );
			this.moveActionSliderToActiveItem();
			this.checkVisibility();
		},

		moveActionSliderToActiveItem: function() {
			var $actions = jQuery( '.trx-addons-ai-helper-image-generator-settings-actions-list' ),
				$slider = $actions.find( '.trx-addons-ai-helper-image-generator-settings-actions-slider' ),
				$active = $actions.find( '.trx-addons-ai-helper-image-generator-settings-actions-item-active a' );
			if ( $active.length ) {
				$slider.css( {
					left: $active.offset().left - $actions.offset().left,
					width: $active.outerWidth()
				} );
			}
		},

		/**
		 * Change a name of file in the state
		 */
		changeFilename: function(e) {
			this.controller.state().set( 'filename', jQuery( e.target ).val() );
		},

		/**
		 * Change a caption of the image in the state
		 */
		changeCaption: function(e) {
			this.controller.state().set( 'caption', jQuery( e.target ).val() );
		},

		/**
		 * Change an upscaler in the state
		 */
		changeUpscaler: function(e) {
			this.controller.state().set( 'upscaler', jQuery( e.target ).val() );
			this.checkVisibility();
		},

		/**
		 * Change an upscale factor in the state
		 */
		changeUpscaleFactor: function(e) {
			this.controller.state().set( 'upscale_factor', jQuery( e.target ).val() );
		},

		/**
		 * Change an upscale width in the state
		 */
		changeUpscaleWidth: function(e) {
			var value = jQuery( e.target ).val();
			this.controller.state().set( 'upscale_width', value );
			if ( value ) {
				this.controller.state().set( 'upscale_height', '' );
				jQuery( '#trx-addons-ai-helper-image-generator-upscale-height' ).val( '' ).trigger( 'change' );
			}
		},

		/**
		 * Change an upscale height in the state
		 */
		changeUpscaleHeight: function(e) {
			var value = jQuery( e.target ).val();
			this.controller.state().set( 'upscale_height', value );
			if ( value ) {
				this.controller.state().set( 'upscale_width', '' );
				jQuery( '#trx-addons-ai-helper-image-generator-upscale-width' ).val( '' ).trigger( 'change' );
			}
		},

		/**
		 * Click on the image - select it for make variations or add to uploads
		 */
		clickImage: function(e) {
			var $image = jQuery( e.target );
			if ( ! $image.hasClass( 'trx-addons-ai-helper-image-generator-preview-image' ) ) {
				$image = $image.parents( '.trx-addons-ai-helper-image-generator-preview-image' );
			}
			if ( ! $image.hasClass( 'trx-addons-ai-helper-image-generator-preview-image-selected' ) ) {
				var url = $image.find( 'img' ).attr( 'src' ).split('?')[0],
					parts = url.split('/'),
					filename = parts[parts.length-1];
				$image.parent().find( '.trx-addons-ai-helper-image-generator-preview-image-selected' ).removeClass( 'trx-addons-ai-helper-image-generator-preview-image-selected' );
				$image.addClass( 'trx-addons-ai-helper-image-generator-preview-image-selected' );
				jQuery( '#trx-addons-ai-helper-image-generator-filename').val( filename ).trigger( 'change' );
				jQuery( '#trx-addons-ai-helper-image-generator-caption').val( '' ).trigger( 'change' );
			}
			// Display settings for the selected image
			jQuery( '#trx-addons-ai-helper-image-generator-settings-selected, .trx-addons-ai-helper-image-generator-settings-selected-subtitle' ).removeClass( 'trx_addons_hidden' );
			this.moveActionSliderToActiveItem();
		},

		/**
		 * Move selecton to the next/prev image with keyboard arrows
		 */
		keydownImage: function(e) {
			var $image = jQuery( e.target ),
				$images = $image.parent().find( '.trx-addons-ai-helper-image-generator-preview-image' ),
				idx = $image.index(),
				handled = false;
			// If 'Enter' or 'Space' is pressed - switch state of the image
//				if ( [ 13, 32 ].indexOf( e.which ) >= 0 ) {		// Enter, Space
//					$image.trigger( 'click' );
//					handled = true;
//				} else
			if ( 37 == e.which ) {					// Left
				$images
					.removeClass( 'trx-addons-ai-helper-image-generator-preview-image-selected' )
					.eq( Math.max( 0, idx - 1 ) ).focus().addClass( 'trx-addons-ai-helper-image-generator-preview-image-selected' );
				handled = true;
			} else if ( 38 == e.which ) {			// Up
				$images
					.removeClass( 'trx-addons-ai-helper-image-generator-preview-image-selected' )
					.eq( Math.max( 0, idx - 3 ) ).focus().addClass( 'trx-addons-ai-helper-image-generator-preview-image-selected' );
				handled = true;
			} else if ( 39 == e.which ) {			// Right
				$images
					.removeClass( 'trx-addons-ai-helper-image-generator-preview-image-selected' )
					.eq( Math.min( $images.length - 1, idx + 1 ) ).focus().addClass( 'trx-addons-ai-helper-image-generator-preview-image-selected' );
				handled = true;
			} else if ( 40 == e.which ) {			// Down
				$images
					.removeClass( 'trx-addons-ai-helper-image-generator-preview-image-selected' )
					.eq( Math.min( $images.length - 1, idx + 3 ) ).focus().addClass( 'trx-addons-ai-helper-image-generator-preview-image-selected' );
				handled = true;
			}
//				if ( handled ) {
//					e.preventDefault();
//					return false;
//				}
			return true;
		},

		/**
		 * Click on the button 'Delete' - delete the selected image from the preview area and from the state
		 */
		deleteImage: function(e) {
			e.preventDefault();
			if ( confirm( TRX_ADDONS_STORAGE['msg_ai_helper_delete_image'] ) ) {
				var $preview   = jQuery( '#trx-addons-ai-helper-image-generator-preview' ),
					$selected  = $preview.find( '.trx-addons-ai-helper-image-generator-preview-image-selected' ),
					$next	   = $selected.next(),
					idx        = $selected.index(),
					images_from_state = this.controller.state().get('images');
				if ( $next.length == 0 ) {
					$next = $selected.prev();
				}
				$selected.remove();
				images_from_state.splice( idx, 1 );
				this.controller.state().set('images', images_from_state);
				if ( images_from_state.length == 0 ) {
					jQuery( '#trx-addons-ai-helper-image-generator-settings-selected, .trx-addons-ai-helper-image-generator-settings-selected-subtitle' ).addClass( 'trx_addons_hidden' );
				} else {
					$next.trigger( 'click' );
				}
			}
			return false;		
		},

		/**
		 * Add an image to preview area
		 * 
		 * @param url string   URL of the image
		 * @param options object  Object with fetch ID and message
		 */
		addImageToPreview: function( url, options ) {
			var fetch_id = options && options.fetch_id ? options.fetch_id : '',
				fetch_msg = options && options.fetch_msg ? options.fetch_msg : '',
				$preview = jQuery( '#trx-addons-ai-helper-image-generator-preview' ),
				html = '<a href="javascript:void(0)" class="trx-addons-ai-helper-image-generator-preview-image'
							+ ( fetch_id ? ' trx-addons-ai-helper-image-generator-preview-image-fetch' : '' )
						+ '">'
							+ '<img src="' + url + '" alt=""' + ( fetch_id ? ' id="fetch-' + fetch_id + '"' : '' ) + '>'
							+ ( fetch_id
								? '<span class="trx-addons-ai-helper-image-generator-preview-image-fetch-info">'
										+ '<span class="trx-addons-ai-helper-image-generator-preview-image-fetch-msg">' + fetch_msg + '</span>'
										+ '<span class="trx-addons-ai-helper-image-generator-preview-image-fetch-progress">'
											+ '<span class="trx-addons-ai-helper-image-generator-preview-image-fetch-progressbar"></span>'
										+ '</span>'
									+ '</span>'
								: '' )
						+ '</a>';
			$preview.removeClass( 'trx_addons_hidden' );
			if ( options && options.after ) {
				jQuery( options.after ).after( html );
			} else {
				$preview.append( html );
			}
			if ( options ) {
				if ( options.select ) {
					$images = $preview.find( '.trx-addons-ai-helper-image-generator-preview-image' );
					$images
						.removeClass( 'trx-addons-ai-helper-image-generator-preview-image-selected' )
						.filter( ':last-child' ).trigger( 'click' );
				}
				if ( options.action ) {
					jQuery( '.trx-addons-ai-helper-image-generator-settings-actions-item a[data-action="' + options.action + '"]' ).trigger( 'click' );
				}
			}
		},

		/**
		 * Fetch images from the server
		 */
		fetchImages: function(data) {
			var self = this;
			jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
				nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
				action: 'trx_addons_ai_helper_fetch_images',
				fetch_id: data.fetch_id,
				fetch_model: data.fetch_model
			}, function( response ) {
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
				if ( ! rez.error ) {
					if ( rez.data && rez.data.images && rez.data.images.length > 0 ) {
						var images_from_state = self.controller.state().get('images');
						var images = rez.data.images,
							$preview = jQuery( '#trx-addons-ai-helper-image-generator-preview' ),
							$fetch = $preview.find( 'img#fetch-' + data.fetch_id );
						for ( var i = 0; i < images.length; i++ ) {
							// Replace image in the preview
							$fetch.eq( i )
								.attr( 'src', images[i].url )
								.parents( '.trx-addons-ai-helper-image-generator-preview-image-fetch' )
									.removeClass( 'trx-addons-ai-helper-image-generator-preview-image-fetch' )
									.find( '.trx-addons-ai-helper-image-generator-preview-image-fetch-info')
										.remove();
							// Replace image in the state
							for ( var j = 0; j < images_from_state.length; j++ ) {
								if ( images_from_state[j] == self.fetch_img ) {
									images_from_state[j] = images[i].url;
									break;
								}
							}
						}
						// Update images in the state
						self.controller.state().set('images', images_from_state);
					} else {
						setTimeout( function() {
							self.fetchImages( data );
						}, data.fetch_time ? data.fetch_time : 2000 );
					}
				} else {
					$preview.find( 'img#fetch-' + data.fetch_id ).remove();
					alert( rez.error );
				}
			} );
		},

		/**
		 * Generate images
		 */
		generateImages: function() {
			var self      = this,
				$button   = jQuery( '#trx-addons-ai-helper-image-generator-generate' ),
				$preview  = jQuery( '#trx-addons-ai-helper-image-generator-preview' ),
				$busy     = jQuery( '#trx-addons-ai-helper-image-generator-settings-busy' ),
				model     = self.controller.state().get('model'),
				size      = self.controller.state().get('size'),
				width     = self.controller.state().get('width'),
				height    = self.controller.state().get('height'),
				number    = self.controller.state().get('number'),
				append    = self.controller.state().get('append'),
				prompt    = self.controller.state().get('prompt'),
				negative_prompt = self.controller.state().get('negative_prompt');

			if ( number < 1 || prompt == '' ) {
				alert( TRX_ADDONS_STORAGE['msg_ai_helper_prompt_error'] );
				return;
			}

			// Save a current model to use it as a default for the next generation
			trx_addons_set_cookie( 'trx_addons_ai_helper_generate_image_model', model, 365 * 24 * 60 * 60 * 1000 );	// 1 year

			// Set to busy state
			$busy.addClass( 'is-busy' );

			// Disable button
			$button
				.prop( 'disabled', true )
				.addClass( 'is-busy' );

			// Send request via AJAX (REST API is not used, because a current user can't be detected)
			var data = {
				nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
				action: 'trx_addons_ai_helper_generate_images',
				model: model,
				size: size,
				number: number,
				prompt: prompt
			};
			if ( model.indexOf( 'openai/dall-e-3' ) >= 0 ) {
				data.style = self.controller.state().get('style');
				data.quality = self.controller.state().get('quality');
			}
			if ( model.indexOf( 'stable-diffusion/' ) >= 0 ) {
				data.guidance_scale = self.controller.state().get('guidance_scale');
				data.inference_steps = self.controller.state().get('inference_steps');
				if ( model != 'stable-diffusion/default' ) {
					data.lora_model = self.controller.state().get('lora_model');
				}
			}
			if ( model.indexOf( 'stability-ai/' ) >= 0 ) {
				data.style = self.controller.state().get('style');
				data.cfg_scale = self.controller.state().get('cfg_scale');
				data.diffusion_steps = self.controller.state().get('diffusion_steps');
			}
			if ( model.indexOf( 'stable-diffusion/' ) >= 0 || model.indexOf( 'stability-ai/' ) >= 0 ) {
				data.seed = self.controller.state().get('seed');
				data.negative_prompt = negative_prompt;
				if ( size == 'custom' ) {
					data.width = width;
					data.height = height;
				}
			}
			jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], data, function( response ) {
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
				// Set to normal state
				$busy.removeClass( 'is-busy' );
				// Enable button
				$button
					.prop( 'disabled', false )
					.removeClass( 'is-busy' );
				// Show images
				if ( ! rez.error ) {
					var images = rez.data.images,
						images_from_state = [],
						i = 0;
					// If need to fetch images after timeout
					if ( rez.data.fetch_id ) {
						for ( i = 0; i < number; i++ ) {
							images.push( {
								url: rez.data.fetch_img
							} );
						}
						if ( ! self.fetch_img ) {
							self.fetch_img = rez.data.fetch_img;
						}
						var time = rez.data.fetch_time ? rez.data.fetch_time : 2000;
						setTimeout( function() {
							self.fetchImages( rez.data );
						}, time );
					}
					// Show images
					if ( images.length > 0 ) {
						$preview.removeClass( 'trx_addons_hidden' );
						if ( append != 'append' ) {
							$preview.empty();
						} else {
							images_from_state = self.controller.state().get('images');
						}
						for ( i = 0; i < images.length; i++ ) {
							self.addImageToPreview( images[i].url, {
								fetch_id: rez.data.fetch_id,
								fetch_msg: rez.data.fetch_msg,
								select: i === 0 && ! rez.data.fetch_id
							} );
							images_from_state.push( images[i].url );
						}
						self.controller.state().set('images', images_from_state);
					}
				} else {
					alert( rez.error );
				}
			} );
		},

		/**
		 * Upload a selected image to the media library
		 * and insert it to the tab "Media Library"
		 */
		addToUploads: function() {
			var self       = this,
				$button    = jQuery( '#trx-addons-ai-helper-image-generator-upload' ),
				$preview   = jQuery( '#trx-addons-ai-helper-image-generator-preview' ),
				$busy      = jQuery( '#trx-addons-ai-helper-image-generator-settings-busy' ),
				$selected  = $preview.find( '.trx-addons-ai-helper-image-generator-preview-image-selected' ),
				idx        = $selected.index(),
				images_from_state = self.controller.state().get('images'),
				url        = images_from_state[idx],
				filename   = self.controller.state().get('filename'),
				caption    = self.controller.state().get('caption');

			if ( ! url ) {
				return;
			}

			// Set to busy state
			$busy.addClass( 'is-busy' );

			// Disable button
			$button
				.prop( 'disabled', true )
				.addClass( 'is-busy' );

			// Send request via AJAX (REST API is not used, because a current user can't be detected)
			jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
				nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
				action: 'trx_addons_ai_helper_add_to_uploads',
				image: url,
				filename: filename,
				caption: caption
			}, function( response ) {
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
				// Set to normal state
				$busy.removeClass( 'is-busy' );
				// Enable button
				$button
					.prop( 'disabled', false )
					.removeClass( 'is-busy' );
				// Add to tab 'Media Library'
				if ( ! rez.error ) {
					var attachment = wp.media.attachment( rez.data );
					attachment.fetch();
					self.controller.state().get('library').add( attachment ? [ attachment ] : [] );
					// Switch to the tab 'Media Library' and select the uploaded image
					self.controller.setState( 'library' );
					self.controller.state().frame.content.mode('browse');
					self.controller.state().get('selection').add( attachment );
					self.controller.state().frame.trigger( 'library:selection:add' );
			
				} else {
					alert( rez.error );
				}
			} );
		},

		/**
		 * Make variations of the selected image
		 */
		makeVariations: function() {
			var self       = this,
				$button    = jQuery( '#trx-addons-ai-helper-image-generator-make-variations' ),
				$preview   = jQuery( '#trx-addons-ai-helper-image-generator-preview' ),
				$busy      = jQuery( '#trx-addons-ai-helper-image-generator-settings-busy' ),
				$selected  = $preview.find( '.trx-addons-ai-helper-image-generator-preview-image-selected' ),
				idx        = $selected.index(),
				images_from_state = self.controller.state().get('images'),
				url        = images_from_state[idx],
				model      = self.controller.state().get('model'),
				size       = self.controller.state().get('size'),
				width	   = self.controller.state().get('width'),
				height	   = self.controller.state().get('height'),
				number     = self.controller.state().get('number'),
				prompt     = self.controller.state().get('prompt'),
				negative_prompt = self.controller.state().get('negative_prompt');

			if ( number < 1 ) {
				return;
			}

			// Set to busy state
			$busy.addClass( 'is-busy' );

			// Disable button
			$button
				.prop( 'disabled', true )
				.addClass( 'is-busy' );

			// Send request via AJAX (REST API is not used, because a current user can't be detected)
			var data = {
				nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
				action: 'trx_addons_ai_helper_make_variations',
				model: model,
				size: size,
				number: number,
				prompt: prompt,
				image: url
			};
			if ( model.indexOf( 'stable-diffusion/' ) >= 0 ) {
				data.guidance_scale = self.controller.state().get('guidance_scale');
				data.inference_steps = self.controller.state().get('inference_steps');
				if ( model != 'stable-diffusion/default' ) {
					data.lora_model = self.controller.state().get('lora_model');
				}
			}
			if ( model.indexOf( 'stability-ai/' ) >= 0 ) {
				data.style = self.controller.state().get('style');
				data.cfg_scale = self.controller.state().get('cfg_scale');
				data.diffusion_steps = self.controller.state().get('diffusion_steps');
			}
			if ( model.indexOf( 'stable-diffusion/' ) >= 0 || model.indexOf( 'stability-ai/' ) >= 0 ) {
				data.seed = self.controller.state().get('seed');
				data.negative_prompt = negative_prompt;
				if ( size == 'custom' ) {
					data.width = width;
					data.height = height;
				}
			}
			jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], data, function( response ) {
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
				// Set to normal state
				$busy.removeClass( 'is-busy' );
				// Enable button
				$button
					.prop( 'disabled', false )
					.removeClass( 'is-busy' );
				// Show images
				if ( ! rez.error ) {
					var images = rez.data.images;
					// If need to fetch images after timeout
					if ( rez.data.fetch_id ) {
						for ( i = 0; i < number; i++ ) {
							images.push( {
								url: rez.data.fetch_img
							} );
						}
						if ( ! self.fetch_img ) {
							self.fetch_img = rez.data.fetch_img;
						}
						var time = rez.data.fetch_time ? rez.data.fetch_time : 2000;
						setTimeout( function() {
							self.fetchImages( rez.data );
						}, time );
					}
					if ( images.length > 0 ) {
						for ( var i = 0; i < images.length; i++ ) {
							self.addImageToPreview( images[i].url, {
								fetch_id: rez.data.fetch_id,
								fetch_msg: rez.data.fetch_msg,
								select: false,
								after: $selected
							} );
							images_from_state.splice( idx + 1, 0, images[i].url );
						}
						self.controller.state().set('images', images_from_state);
					}
				} else {
					alert( rez.error );
				}
			} );
		},

		/**
		 * Upscale of the selected image
		 */
		makeUpscale: function() {
			var self       = this,
				$button    = jQuery( '#trx-addons-ai-helper-image-generator-make-upscale' ),
				$preview   = jQuery( '#trx-addons-ai-helper-image-generator-preview' ),
				$busy      = jQuery( '#trx-addons-ai-helper-image-generator-settings-busy' ),
				$selected  = $preview.find( '.trx-addons-ai-helper-image-generator-preview-image-selected' ),
				idx        = $selected.index(),
				images_from_state = self.controller.state().get('images'),
				url        = images_from_state[idx],
				model      = self.controller.state().get('upscaler');

			// Set to busy state
			$busy.addClass( 'is-busy' );

			// Disable button
			$button
				.prop( 'disabled', true )
				.addClass( 'is-busy' );

			// Send request via AJAX (REST API is not used, because a current user can't be detected)
			var data = {
				nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
				action: 'trx_addons_ai_helper_make_upscale',
				model: model,
				image: url
			};
			if ( model.indexOf( 'stable-diffusion/' ) >= 0 ) {
				data.scale = self.controller.state().get('upscale_factor');
			}
			if ( model.indexOf( 'stability-ai/' ) >= 0 ) {
				data.width = self.controller.state().get('upscale_width');
				data.height = self.controller.state().get('upscale_height');
			}
			jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], data, function( response ) {
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
				// Set to normal state
				$busy.removeClass( 'is-busy' );
				// Enable button
				$button
					.prop( 'disabled', false )
					.removeClass( 'is-busy' );
				// Show images
				if ( ! rez.error ) {
					var images = rez.data.images;
					// If need to fetch images after timeout
					if ( rez.data.fetch_id ) {
						for ( i = 0; i < number; i++ ) {
							images.push( {
								url: rez.data.fetch_img
							} );
						}
						if ( ! self.fetch_img ) {
							self.fetch_img = rez.data.fetch_img;
						}
						var time = rez.data.fetch_time ? rez.data.fetch_time : 2000;
						setTimeout( function() {
							self.fetchImages( rez.data );
						}, time );
					}
					if ( images.length > 0 ) {
						for ( var i = 0; i < images.length; i++ ) {
							self.addImageToPreview( images[i].url, {
								fetch_id: rez.data.fetch_id,
								fetch_msg: rez.data.fetch_msg,
								select: true,
								action: 'add_to_library',
								after: $selected
							} );
							images_from_state.splice( idx + 1, 0, images[i].url );
						}
						self.controller.state().set('images', images_from_state);
					}
				} else {
					alert( rez.error );
				}
			} );
		}
	} );

	/**
	 * Extending the current media library frame to add a new tab
	 */
	var newMediaFrame = {
		
		// initialize: function() {
		// 	// Calling the initalize method from the current frame before adding new functionality
		// 	oldMediaFrame.prototype.initialize.apply( this, arguments );
		// },

		bindHandlers: function() {
			// Calling the initalize method from the current frame before adding new functionality
			this.oldMediaFrame.prototype.bindHandlers.apply( this, arguments );
			// Add a new tab
			this.on( 'router:render:browse', this.aiHelperRouter, this );
			this.on( 'content:render:trx-addons-ai-helper-image-generator', this.aiHelperContent, this );
			this.on( 'content:render:browse', this.aiHelperAddAttachmentClick, this );
		},

		// Add a new tab
		aiHelperRouter: function( routerView ) {
			routerView.set( {
				upload: {
					text:     l10n.uploadFilesTitle,
					priority: 20
				},
				'trx-addons-ai-helper-image-generator': {
					text:     wp.i18n.__( 'AI Image Generator', 'trx_addons' ),
					priority: 30
				},
				browse: {
					text:     l10n.mediaLibraryTitle,
					priority: 40
				}
			} );
		},

		// Add a new tab content
		aiHelperContent: function() {
			this.$el.removeClass( 'hide-toolbar' );
			this.content.set( new wp.media.view.TrxAddonsAiHelperImageGenerator( {
				controller: this,
				title: wp.i18n.__( 'Generate images with AI Helper', 'trx_addons' ),
				canClose: false
			} ) );
		},

		aiHelperAddAttachmentClick: function() {
			if ( ! this.$el.hasClass( 'trx-click-inited' ) ) {
				mediaFrameObject = this;
				this.$el
					.addClass( 'trx-click-inited' )
					.on( 'click', '.attachment', this.aiHelperAddAttachmentVariationsButton );
			}
		},

		aiHelperAddAttachmentVariationsButton: function() {
			var $self = jQuery( this ),
				$info = $self.parents( '.media-frame' ).find( '.attachment-info' );
			$info.find( '.edit-attachment' ).after( '<button type="button" class="button-link variations-attachment">' + wp.i18n.__( 'Variations or Upscale', 'trx_addons' ) + '</button>' );
			$info.find( '.variations-attachment' ).on( 'click', mediaFrameObject.aiHelperAttachmentVariationsButtonClick );
		},

		aiHelperAttachmentVariationsButtonClick: function() {
			// Get URL of the selected image
			var $selected = mediaFrameObject.state().get('selection').single();
			if ( ! $selected ) {
				return;
			}
			var url = $selected.attributes.url;
			// Switch to the tab 'AI Helper'
			mediaFrameObject.$el.find( '#menu-item-trx-addons-ai-helper-image-generator' ).trigger( 'click' );
			mediaFrameObject.state().frame.trigger( 'library:selection:variations', url );
		}
	
	};

	// Extending the current media library frame to add a new tab
	wp.media.view.MediaFrame.Post = oldMediaFramePost.extend( Object.assign( { oldMediaFrame: oldMediaFramePost }, newMediaFrame ) );
	wp.media.view.MediaFrame.Select = oldMediaFrameSelect.extend( Object.assign( { oldMediaFrame: oldMediaFrameSelect }, newMediaFrame ) );
} );