<div class="front_page_section front_page_section_contacts<?php
	$planty_scheme = planty_get_theme_option( 'front_page_contacts_scheme' );
	if ( ! empty( $planty_scheme ) && ! planty_is_inherit( $planty_scheme ) ) {
		echo ' scheme_' . esc_attr( $planty_scheme );
	}
	echo ' front_page_section_paddings_' . esc_attr( planty_get_theme_option( 'front_page_contacts_paddings' ) );
	if ( planty_get_theme_option( 'front_page_contacts_stack' ) ) {
		echo ' sc_stack_section_on';
	}
?>"
		<?php
		$planty_css      = '';
		$planty_bg_image = planty_get_theme_option( 'front_page_contacts_bg_image' );
		if ( ! empty( $planty_bg_image ) ) {
			$planty_css .= 'background-image: url(' . esc_url( planty_get_attachment_url( $planty_bg_image ) ) . ');';
		}
		if ( ! empty( $planty_css ) ) {
			echo ' style="' . esc_attr( $planty_css ) . '"';
		}
		?>
>
<?php
	// Add anchor
	$planty_anchor_icon = planty_get_theme_option( 'front_page_contacts_anchor_icon' );
	$planty_anchor_text = planty_get_theme_option( 'front_page_contacts_anchor_text' );
if ( ( ! empty( $planty_anchor_icon ) || ! empty( $planty_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_contacts"'
									. ( ! empty( $planty_anchor_icon ) ? ' icon="' . esc_attr( $planty_anchor_icon ) . '"' : '' )
									. ( ! empty( $planty_anchor_text ) ? ' title="' . esc_attr( $planty_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_contacts_inner
	<?php
	if ( planty_get_theme_option( 'front_page_contacts_fullheight' ) ) {
		echo ' planty-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$planty_css      = '';
			$planty_bg_mask  = planty_get_theme_option( 'front_page_contacts_bg_mask' );
			$planty_bg_color_type = planty_get_theme_option( 'front_page_contacts_bg_color_type' );
			if ( 'custom' == $planty_bg_color_type ) {
				$planty_bg_color = planty_get_theme_option( 'front_page_contacts_bg_color' );
			} elseif ( 'scheme_bg_color' == $planty_bg_color_type ) {
				$planty_bg_color = planty_get_scheme_color( 'bg_color', $planty_scheme );
			} else {
				$planty_bg_color = '';
			}
			if ( ! empty( $planty_bg_color ) && $planty_bg_mask > 0 ) {
				$planty_css .= 'background-color: ' . esc_attr(
					1 == $planty_bg_mask ? $planty_bg_color : planty_hex2rgba( $planty_bg_color, $planty_bg_mask )
				) . ';';
			}
			if ( ! empty( $planty_css ) ) {
				echo ' style="' . esc_attr( $planty_css ) . '"';
			}
			?>
	>
		<div class="front_page_section_content_wrap front_page_section_contacts_content_wrap content_wrap">
			<?php

			// Title and description
			$planty_caption     = planty_get_theme_option( 'front_page_contacts_caption' );
			$planty_description = planty_get_theme_option( 'front_page_contacts_description' );
			if ( ! empty( $planty_caption ) || ! empty( $planty_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				// Caption
				if ( ! empty( $planty_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					?>
					<h2 class="front_page_section_caption front_page_section_contacts_caption front_page_block_<?php echo ! empty( $planty_caption ) ? 'filled' : 'empty'; ?>">
					<?php
						echo wp_kses( $planty_caption, 'planty_kses_content' );
					?>
					</h2>
					<?php
				}

				// Description
				if ( ! empty( $planty_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					?>
					<div class="front_page_section_description front_page_section_contacts_description front_page_block_<?php echo ! empty( $planty_description ) ? 'filled' : 'empty'; ?>">
					<?php
						echo wp_kses( wpautop( $planty_description ), 'planty_kses_content' );
					?>
					</div>
					<?php
				}
			}

			// Content (text)
			$planty_content = planty_get_theme_option( 'front_page_contacts_content' );
			$planty_layout  = planty_get_theme_option( 'front_page_contacts_layout' );
			if ( 'columns' == $planty_layout && ( ! empty( $planty_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) ) {
				?>
				<div class="front_page_section_columns front_page_section_contacts_columns columns_wrap">
					<div class="column-1_3">
				<?php
			}

			if ( ( ! empty( $planty_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) ) {
				?>
				<div class="front_page_section_content front_page_section_contacts_content front_page_block_<?php echo ! empty( $planty_content ) ? 'filled' : 'empty'; ?>">
					<?php
					echo wp_kses( $planty_content, 'planty_kses_content' );
					?>
				</div>
				<?php
			}

			if ( 'columns' == $planty_layout && ( ! empty( $planty_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) ) {
				?>
				</div><div class="column-2_3">
				<?php
			}

			// Shortcode output
			$planty_sc = planty_get_theme_option( 'front_page_contacts_shortcode' );
			if ( ! empty( $planty_sc ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_output front_page_section_contacts_output front_page_block_<?php echo ! empty( $planty_sc ) ? 'filled' : 'empty'; ?>">
					<?php
					planty_show_layout( do_shortcode( $planty_sc ) );
					?>
				</div>
				<?php
			}

			if ( 'columns' == $planty_layout && ( ! empty( $planty_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) ) {
				?>
				</div></div>
				<?php
			}
			?>

		</div>
	</div>
</div>
