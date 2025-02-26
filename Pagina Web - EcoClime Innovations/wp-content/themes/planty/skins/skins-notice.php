<?php
/**
 * The template to display Admin notices
 *
 * @package PLANTY
 * @since PLANTY 1.0.64
 */

$planty_skins_url  = get_admin_url( null, 'admin.php?page=trx_addons_theme_panel#trx_addons_theme_panel_section_skins' );
$planty_skins_args = get_query_var( 'planty_skins_notice_args' );
?>
<div class="planty_admin_notice planty_skins_notice notice notice-info is-dismissible" data-notice="skins">
	<?php
	// Theme image
	$planty_theme_img = planty_get_file_url( 'screenshot.jpg' );
	if ( '' != $planty_theme_img ) {
		?>
		<div class="planty_notice_image"><img src="<?php echo esc_url( $planty_theme_img ); ?>" alt="<?php esc_attr_e( 'Theme screenshot', 'planty' ); ?>"></div>
		<?php
	}

	// Title
	?>
	<h3 class="planty_notice_title">
		<?php esc_html_e( 'New skins are available', 'planty' ); ?>
	</h3>
	<?php

	// Description
	$planty_total      = $planty_skins_args['update'];	// Store value to the separate variable to avoid warnings from ThemeCheck plugin!
	$planty_skins_msg  = $planty_total > 0
							// Translators: Add new skins number
							? '<strong>' . sprintf( _n( '%d new version', '%d new versions', $planty_total, 'planty' ), $planty_total ) . '</strong>'
							: '';
	$planty_total      = $planty_skins_args['free'];
	$planty_skins_msg .= $planty_total > 0
							? ( ! empty( $planty_skins_msg ) ? ' ' . esc_html__( 'and', 'planty' ) . ' ' : '' )
								// Translators: Add new skins number
								. '<strong>' . sprintf( _n( '%d free skin', '%d free skins', $planty_total, 'planty' ), $planty_total ) . '</strong>'
							: '';
	$planty_total      = $planty_skins_args['pay'];
	$planty_skins_msg .= $planty_skins_args['pay'] > 0
							? ( ! empty( $planty_skins_msg ) ? ' ' . esc_html__( 'and', 'planty' ) . ' ' : '' )
								// Translators: Add new skins number
								. '<strong>' . sprintf( _n( '%d paid skin', '%d paid skins', $planty_total, 'planty' ), $planty_total ) . '</strong>'
							: '';
	?>
	<div class="planty_notice_text">
		<p>
			<?php
			// Translators: Add new skins info
			echo wp_kses_data( sprintf( __( "We are pleased to announce that %s are available for your theme", 'planty' ), $planty_skins_msg ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="planty_notice_buttons">
		<?php
		// Link to the theme dashboard page
		?>
		<a href="<?php echo esc_url( $planty_skins_url ); ?>" class="button button-primary"><i class="dashicons dashicons-update"></i> 
			<?php
			// Translators: Add theme name
			esc_html_e( 'Go to Skins manager', 'planty' );
			?>
		</a>
	</div>
</div>
