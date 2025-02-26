<?php
$planty_woocommerce_sc = planty_get_theme_option( 'front_page_woocommerce_products' );
if ( ! empty( $planty_woocommerce_sc ) ) {
	?><div class="front_page_section front_page_section_woocommerce<?php
		$planty_scheme = planty_get_theme_option( 'front_page_woocommerce_scheme' );
		if ( ! empty( $planty_scheme ) && ! planty_is_inherit( $planty_scheme ) ) {
			echo ' scheme_' . esc_attr( $planty_scheme );
		}
		echo ' front_page_section_paddings_' . esc_attr( planty_get_theme_option( 'front_page_woocommerce_paddings' ) );
		if ( planty_get_theme_option( 'front_page_woocommerce_stack' ) ) {
			echo ' sc_stack_section_on';
		}
	?>"
			<?php
			$planty_css      = '';
			$planty_bg_image = planty_get_theme_option( 'front_page_woocommerce_bg_image' );
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
		$planty_anchor_icon = planty_get_theme_option( 'front_page_woocommerce_anchor_icon' );
		$planty_anchor_text = planty_get_theme_option( 'front_page_woocommerce_anchor_text' );
		if ( ( ! empty( $planty_anchor_icon ) || ! empty( $planty_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
			echo do_shortcode(
				'[trx_sc_anchor id="front_page_section_woocommerce"'
											. ( ! empty( $planty_anchor_icon ) ? ' icon="' . esc_attr( $planty_anchor_icon ) . '"' : '' )
											. ( ! empty( $planty_anchor_text ) ? ' title="' . esc_attr( $planty_anchor_text ) . '"' : '' )
											. ']'
			);
		}
	?>
		<div class="front_page_section_inner front_page_section_woocommerce_inner
			<?php
			if ( planty_get_theme_option( 'front_page_woocommerce_fullheight' ) ) {
				echo ' planty-full-height sc_layouts_flex sc_layouts_columns_middle';
			}
			?>
				"
				<?php
				$planty_css      = '';
				$planty_bg_mask  = planty_get_theme_option( 'front_page_woocommerce_bg_mask' );
				$planty_bg_color_type = planty_get_theme_option( 'front_page_woocommerce_bg_color_type' );
				if ( 'custom' == $planty_bg_color_type ) {
					$planty_bg_color = planty_get_theme_option( 'front_page_woocommerce_bg_color' );
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
			<div class="front_page_section_content_wrap front_page_section_woocommerce_content_wrap content_wrap woocommerce">
				<?php
				// Content wrap with title and description
				$planty_caption     = planty_get_theme_option( 'front_page_woocommerce_caption' );
				$planty_description = planty_get_theme_option( 'front_page_woocommerce_description' );
				if ( ! empty( $planty_caption ) || ! empty( $planty_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					// Caption
					if ( ! empty( $planty_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
						?>
						<h2 class="front_page_section_caption front_page_section_woocommerce_caption front_page_block_<?php echo ! empty( $planty_caption ) ? 'filled' : 'empty'; ?>">
						<?php
							echo wp_kses( $planty_caption, 'planty_kses_content' );
						?>
						</h2>
						<?php
					}

					// Description (text)
					if ( ! empty( $planty_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
						?>
						<div class="front_page_section_description front_page_section_woocommerce_description front_page_block_<?php echo ! empty( $planty_description ) ? 'filled' : 'empty'; ?>">
						<?php
							echo wp_kses( wpautop( $planty_description ), 'planty_kses_content' );
						?>
						</div>
						<?php
					}
				}

				// Content (widgets)
				?>
				<div class="front_page_section_output front_page_section_woocommerce_output list_products shop_mode_thumbs">
					<?php
					if ( 'products' == $planty_woocommerce_sc ) {
						$planty_woocommerce_sc_ids      = planty_get_theme_option( 'front_page_woocommerce_products_per_page' );
						$planty_woocommerce_sc_per_page = count( explode( ',', $planty_woocommerce_sc_ids ) );
					} else {
						$planty_woocommerce_sc_per_page = max( 1, (int) planty_get_theme_option( 'front_page_woocommerce_products_per_page' ) );
					}
					$planty_woocommerce_sc_columns = max( 1, min( $planty_woocommerce_sc_per_page, (int) planty_get_theme_option( 'front_page_woocommerce_products_columns' ) ) );
					echo do_shortcode(
						"[{$planty_woocommerce_sc}"
										. ( 'products' == $planty_woocommerce_sc
												? ' ids="' . esc_attr( $planty_woocommerce_sc_ids ) . '"'
												: '' )
										. ( 'product_category' == $planty_woocommerce_sc
												? ' category="' . esc_attr( planty_get_theme_option( 'front_page_woocommerce_products_categories' ) ) . '"'
												: '' )
										. ( 'best_selling_products' != $planty_woocommerce_sc
												? ' orderby="' . esc_attr( planty_get_theme_option( 'front_page_woocommerce_products_orderby' ) ) . '"'
													. ' order="' . esc_attr( planty_get_theme_option( 'front_page_woocommerce_products_order' ) ) . '"'
												: '' )
										. ' per_page="' . esc_attr( $planty_woocommerce_sc_per_page ) . '"'
										. ' columns="' . esc_attr( $planty_woocommerce_sc_columns ) . '"'
						. ']'
					);
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
}
