<?php
/**
 * The template to display Admin notices
 *
 * @package PLANTY
 * @since PLANTY 1.0.1
 */

$planty_theme_slug = get_option( 'template' );
$planty_theme_obj  = wp_get_theme( $planty_theme_slug );
?>
<div class="planty_admin_notice planty_welcome_notice notice notice-info is-dismissible" data-notice="admin">
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
		<?php
		echo esc_html(
			sprintf(
				// Translators: Add theme name and version to the 'Welcome' message
				__( 'Welcome to %1$s v.%2$s', 'planty' ),
				$planty_theme_obj->get( 'Name' ) . ( PLANTY_THEME_FREE ? ' ' . __( 'Free', 'planty' ) : '' ),
				$planty_theme_obj->get( 'Version' )
			)
		);
		?>
	</h3>
	<?php

	// Description
	?>
	<div class="planty_notice_text">
		<p class="planty_notice_text_description">
			<?php
			echo str_replace( '. ', '.<br>', wp_kses_data( $planty_theme_obj->description ) );
			?>
		</p>
		<p class="planty_notice_text_info">
			<?php
			echo wp_kses_data( __( 'Attention! Plugin "ThemeREX Addons" is required! Please, install and activate it!', 'planty' ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="planty_notice_buttons">
		<?php
		// Link to the page 'About Theme'
		?>
		<a href="<?php echo esc_url( admin_url() . 'themes.php?page=planty_about' ); ?>" class="button button-primary"><i class="dashicons dashicons-nametag"></i> 
			<?php
			echo esc_html__( 'Install plugin "ThemeREX Addons"', 'planty' );
			?>
		</a>
	</div>
</div>
