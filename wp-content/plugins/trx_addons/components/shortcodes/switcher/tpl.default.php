<?php
/**
 * The style "default" of the Switcher
 *
 * @package ThemeREX Addons
 * @since v2.6.0
 */

$args = get_query_var('trx_addons_args_sc_switcher');
if ( empty( $args['effect'] ) ) $args['effect'] = 'swap';
?>
<div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> 
	class="sc_switcher sc_switcher_<?php
		echo esc_attr( $args['type'] )
			. ' sc_switcher_effect_' . esc_attr( $args['effect'] )
			. ( trx_addons_is_on( $args['autowidth'] ) ? ' sc_switcher_autowidth' : '' )
			. ' sc_switcher_toggle_on';
		if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] );
		?>"<?php
	if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
	trx_addons_sc_show_attributes('sc_switcher', $args, 'sc_wrapper');
?>><?php

	trx_addons_sc_show_titles('sc_switcher', $args);

	?><div class="sc_switcher_content sc_item_content"<?php
		trx_addons_sc_show_attributes('sc_switcher', $args, 'sc_content');
	?>>
		<div class="sc_switcher_controls">
			<div class="sc_switcher_controls_section1">
				<<?php echo esc_html( $args['title_html_tag'] ); ?> class="sc_switcher_controls_section_title"><?php
					echo esc_html( $args['slide1_title'] );
				?></<?php echo esc_html( $args['title_html_tag'] ); ?>>
			</div>
			<div class="sc_switcher_controls_toggle sc_switcher_controls_toggle_on">
				<span class="sc_switcher_controls_toggle_button"></span>
			</div>
			<div class="sc_switcher_controls_section2">
				<<?php echo esc_html( $args['title_html_tag'] ); ?> class="sc_switcher_controls_section_title"><?php
					echo esc_html( $args['slide2_title'] );
				?></<?php echo esc_html( $args['title_html_tag'] ); ?>>
			</div>
		</div>
		<div class="sc_switcher_sections"<?php trx_addons_sc_show_attributes('sc_switcher', $args, 'sc_items'); ?>>
			<div class="sc_switcher_slider sc_switcher_slider_2">
				<?php
				for ( $i=1; $i <= 2; $i++ ) {
					?>
					<div class="sc_switcher_section sc_switcher_section_<?php
						echo (int)$i;
						if ( $i == 1 ) {
							echo ' sc_switcher_section_active';
						}
					?>"<?php
						if ( $args["slide{$i}_type"] == 'section' && ! empty( $args["slide{$i}_section"] ) ) {
							?> data-section="<?php echo esc_attr( $args["slide{$i}_section"] ); ?>"<?php
						}
					?>>
						<?php
						// Prepare layout
						if ( $args["slide{$i}_type"] == 'layout' && ! empty( $args["slide{$i}_layout"] ) ) {
							$args["slide{$i}_content"] = trx_addons_cpt_layouts_show_layout( $args["slide{$i}_layout"], 0, false );

						// Prepare template
						} else if ( $args["slide{$i}_type"] == 'template' && ! empty( $args["slide{$i}_template"] ) ) {
							$args["slide{$i}_content"] = trx_addons_cpt_layouts_show_layout( $args["slide{$i}_template"], 0, false );

						// Put the section id to the catch-list
						} else if ( $args["slide{$i}_type"] == 'section' && ! empty( $args["slide{$i}_section"] ) ) {
							if ( ! isset( $GLOBALS['TRX_ADDONS_STORAGE']['catch_output']['sc_switcher'] ) ) {
								$GLOBALS['TRX_ADDONS_STORAGE']['catch_output']['sc_switcher'] = array();
							}
							$GLOBALS['TRX_ADDONS_STORAGE']['catch_output']['sc_switcher'][ trim( $args["slide{$i}_section"] ) ] = 1;

						// Content
						} else if ( $args["slide{$i}_type"] == 'content' && ! empty( $args["slide{$i}_content"] ) ) {
							$args["slide{$i}_content"] = do_shortcode( str_replace( array( '<p>[', ']</p>' ), array( '[', ']' ), $args["slide{$i}_content"] ) );
						}
						// Output the content
						if ( ! empty( $args["slide{$i}_content"] ) ) {
							trx_addons_show_layout( $args["slide{$i}_content"] );
						}
						?>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</div><?php
	
	trx_addons_sc_show_links('sc_switcher', $args);

?></div>