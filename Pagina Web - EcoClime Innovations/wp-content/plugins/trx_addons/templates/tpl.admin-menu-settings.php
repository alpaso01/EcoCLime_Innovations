<?php
	/**
	 * Menu Settings Popup.
	 */

	defined( 'ABSPATH' ) || exit;
?>
<div class="trx-addons-dialog trx-addons-nav-menu-item-settings">
	<div class="trx-addons-dialog-modal trx-addons-nav-menu-item-settings-modal">
		<div class="trx-addons-dialog-content">

			<div class="trx-addons-dialog-header">
				<div class="trx-addons-dialog-header-title-area">
					<div class="trx-addons-dialog-header-logo">
						<img src="<?php echo esc_url( TRX_ADDONS_PLUGIN_URL . 'trx_addons.png' ); ?>">
					</div>
					<div class="trx-addons-dialog-header-title"><?php esc_html_e( 'Menu Item Settings', 'trx_addons' ); ?></div>
				</div>
				<div class="trx-addons-dialog-header-buttons-area">
					<div class="trx-addons-dialog-header-button-close">
						<i class="dashicons dashicons-no-alt" aria-hidden="true" title="<?php echo esc_attr__( 'Close', 'trx_addons' ); ?>"></i>
					</div>
				</div>
			</div>

			<div class="trx-addons-dialog-body">
				<div class="trx-addons-dialog-fields">
					<div class="trx_addons_options">
						<?php trx_addons_options_show_fields( trx_addons_cpt_layouts_submenu_get_fields(), 'menu-settings' ); ?>
					</div>
				</div>
			</div>

			<div class="trx-addons-dialog-footer">
				<div class="trx-addons-dialog-footer-buttons">
					<button id="trx-addons-nav-menu-item-save" class="trx-addons-dialog-button" type="button">
						<span><?php esc_html_e( 'Apply', 'trx_addons' ); ?></span>
						<i class="dashicons dashicons-admin-generic loader-hidden"></i>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
