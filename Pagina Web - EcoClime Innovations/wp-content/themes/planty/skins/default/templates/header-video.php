<?php
/**
 * The template to display the background video in the header
 *
 * @package PLANTY
 * @since PLANTY 1.0.14
 */
$planty_header_video = planty_get_header_video();
$planty_embed_video  = '';
if ( ! empty( $planty_header_video ) && ! planty_is_from_uploads( $planty_header_video ) ) {
	if ( planty_is_youtube_url( $planty_header_video ) && preg_match( '/[=\/]([^=\/]*)$/', $planty_header_video, $matches ) && ! empty( $matches[1] ) ) {
		?><div id="background_video" data-youtube-code="<?php echo esc_attr( $matches[1] ); ?>"></div>
		<?php
	} else {
		?>
		<div id="background_video"><?php planty_show_layout( planty_get_embed_video( $planty_header_video ) ); ?></div>
		<?php
	}
}
