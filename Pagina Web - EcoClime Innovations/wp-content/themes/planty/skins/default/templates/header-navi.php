<?php
/**
 * The template to display the main menu
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */
?>
<div class="top_panel_navi sc_layouts_row sc_layouts_row_type_compact sc_layouts_row_fixed sc_layouts_row_fixed_always sc_layouts_row_delimiter
	<?php
	if ( planty_is_on( planty_get_theme_option( 'header_mobile_enabled' ) ) ) {
		?>
		sc_layouts_hide_on_mobile
		<?php
	}
	?>
">
	<div class="content_wrap">
		<div class="columns_wrap columns_fluid">
			<div class="sc_layouts_column sc_layouts_column_align_left sc_layouts_column_icons_position_left sc_layouts_column_fluid column-1_4">
				<div class="sc_layouts_item">
					<?php
					// Logo
					get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/header-logo' ) );
					?>
				</div>
			</div><div class="sc_layouts_column sc_layouts_column_align_right sc_layouts_column_icons_position_left sc_layouts_column_fluid column-3_4">
				<div class="sc_layouts_item">
					<?php
					// Main menu
					$planty_menu_main = planty_get_nav_menu( 'menu_main' );
					// Show any menu if no menu selected in the location 'menu_main'
					if ( planty_get_theme_setting( 'autoselect_menu' ) && empty( $planty_menu_main ) ) {
						$planty_menu_main = planty_get_nav_menu();
					}
					planty_show_layout(
						$planty_menu_main,
						'<nav class="menu_main_nav_area sc_layouts_menu sc_layouts_menu_default sc_layouts_hide_on_mobile"'
							. ' itemscope="itemscope" itemtype="' . esc_attr( planty_get_protocol( true ) ) . '//schema.org/SiteNavigationElement"'
							. '>',
						'</nav>'
					);
					// Mobile menu button
					?>
					<div class="sc_layouts_iconed_text sc_layouts_menu_mobile_button">
						<a class="sc_layouts_item_link sc_layouts_iconed_text_link" href="#">
							<span class="sc_layouts_item_icon sc_layouts_iconed_text_icon trx_addons_icon-menu"></span>
						</a>
					</div>
				</div><?php
				if ( planty_exists_trx_addons() ) {
					// Display cart button
					ob_start();
					do_action( 'planty_action_cart' );
					$planty_action_output = ob_get_contents();
					ob_end_clean();
					if ( ! empty( $planty_action_output ) ) {
						?><div class="sc_layouts_item">
				<?php
							planty_show_layout( $planty_action_output );
					?>
							</div><?php
					}					
					?><div class="sc_layouts_item">
						<?php
						// Display search field
						do_action(
							'planty_action_search',
							array(
								'style' => 'fullscreen',
								'class' => 'header_search',
								'ajax'  => false
							)
						);
						?>
					</div><?php
				}
				?>
			</div>
		</div><!-- /.columns_wrap -->
	</div><!-- /.content_wrap -->
</div><!-- /.top_panel_navi -->
