<?php
/**
 * The style "Modern" of the Search form
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.08
 */

$args = get_query_var('trx_addons_args_sc_layouts_search');

$main_logo_args =  planty_get_logo_image();
$main_logo = trx_addons_get_attachment_url($main_logo_args['logo'], 'full');
$main_logo_retina = trx_addons_get_attachment_url($main_logo_args['logo_retina'], 'full');
$main_logo_attr = trx_addons_getimagesize( $main_logo );

$logo_search_image = trx_addons_get_attachment_url($args['logo_search']['url'], 'full');
$logo_search_retina_image = trx_addons_get_attachment_url($args['logo_search_retina']['url'], 'full');
$logo_search_attr = trx_addons_getimagesize( $logo_search_image );

?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> class="sc_layouts_search<?php
		trx_addons_cpt_layouts_sc_add_classes($args);
	?>"<?php
	if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"'; ?>><?php

	$args['class'] = ( !empty($args['class']) ? ' ' : '' ) . 'layouts_search'; ?>

    <div class="search_modern">
        <span class="search_submit"></span>
        <div class="search_wrap<?php if (!empty($args['ajax'])) echo ' search_ajax'; if (!empty($args['scheme_search'])) echo ' scheme_'. esc_attr($args['scheme_search'])?>">
            <div class="search_header_wrap"><?php
                if ( !empty( $logo_search_image ) ) {
                    ?><img class="logo_image"
                            src="<?php echo esc_url( $logo_search_image ); ?>"
                            <?php
                            if ( !empty( $logo_search_retina_image ) ) {?>
                                srcset="<?php echo esc_url( $logo_search_retina_image ); ?> 2x"<?php
                            }
                            ?>
                            alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" <?php
                            if ( !empty( $logo_search_attr[3] ) ) trx_addons_show_layout( $logo_search_attr[3] );
                    ?>><?php
                } else {
                    ?><img class="logo_image"
                           src="<?php echo esc_url( $main_logo ); ?>"
                    <?php
                    if ( !empty( $main_logo_retina ) ) {?>
                        srcset="<?php echo esc_url( $main_logo_retina ); ?> 2x"<?php
                    }
                    ?>
                        alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" <?php
                    if ( !empty( $main_logo_attr[3] ) ) trx_addons_show_layout( $main_logo_attr[3] );
                    ?>><?php
                }

                ?>
                <a class="search_close"></a>
            </div>
            <div class="search_form_wrap">
                <form role="search" method="get" class="search_form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <input type="hidden" value="<?php
                    if (!empty($args['post_types'])) {
                        echo esc_attr( is_array($args['post_types']) ? join(',', $args['post_types']) : $args['post_types'] );
                    }
                    ?>" name="post_types">
                    <input type="text" class="search_field" placeholder="<?php esc_attr_e( 'Type words and hit enter', 'planty' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
                    <button type="submit" class="search_submit"></button>
                    <?php
                    if (!empty($args['ajax'])) {
                        ?><div class="search_results widget_area"><a href="#" class="search_results_close trx_addons_icon-cancel"></a><div class="search_results_content"></div></div><?php
                    }
                    ?>
                </form>
            </div>
        </div>
        <div class="search_overlay<?php if (!empty($args['scheme_search'])) echo ' scheme_'. esc_attr($args['scheme_search'])?>"></div>
    </div>


</div><!-- /.sc_layouts_search --><?php

trx_addons_sc_layouts_showed('search', true);
