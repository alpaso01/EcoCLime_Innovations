<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'planty_gutenberg_get_css' ) ) {
	add_filter( 'planty_filter_get_css', 'planty_gutenberg_get_css', 10, 2 );
	function planty_gutenberg_get_css( $css, $args ) {

		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts                   = $args['fonts'];
			$fonts['p_font-family!'] = str_replace(';', ' !important;', $fonts['p_font-family']);
			$fonts['p_font-size!'] = str_replace(';', ' !important;', $fonts['p_font-size']);
			$css['fonts']           .= <<<CSS
body.edit-post-visual-editor {
	{$fonts['p_font-family!']}
	{$fonts['p_font-size']}
	{$fonts['p_font-weight']}
	{$fonts['p_font-style']}
	{$fonts['p_line-height']}
	{$fonts['p_text-decoration']}
	{$fonts['p_text-transform']}
	{$fonts['p_letter-spacing']}
}
.editor-post-title__block .editor-post-title__input {
	{$fonts['h1_font-family']}
	{$fonts['h1_font-size']}
	{$fonts['h1_font-weight']}
	{$fonts['h1_font-style']}
}
.block-editor-block-list__block {
	{$fonts['p_margin-top']}
	{$fonts['p_margin-bottom']}
}

CSS;
		}

		return $css;
	}
}

