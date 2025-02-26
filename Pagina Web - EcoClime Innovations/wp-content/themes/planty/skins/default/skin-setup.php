<?php
/**
 * Skin Setup
 *
 * @package PLANTY
 * @since PLANTY 1.76.0
 */


//--------------------------------------------
// SKIN DEFAULTS
//--------------------------------------------

// Return theme's (skin's) default value for the specified parameter
if ( ! function_exists( 'planty_theme_defaults' ) ) {
	function planty_theme_defaults( $name='', $value='' ) {
		$defaults = array(
			'page_width'          => 1290,
			'page_boxed_extra'  => 60,
			'page_fullwide_max' => 1920,
			'page_fullwide_extra' => 130,
			'sidebar_width'       => 410,
			'sidebar_gap'       => 40,
			'grid_gap'          => 30,
			'rad'               => 0,
		);
		if ( empty( $name ) ) {
			return $defaults;
		} else {
			if ( empty( $value ) && isset( $defaults[ $name ] ) ) {
				$value = $defaults[ $name ];
			}
			return $value;
		}
	}
}


// Theme init priorities:
// Action 'after_setup_theme'
// 1 - register filters to add/remove lists items in the Theme Options
// 2 - create Theme Options
// 3 - add/remove Theme Options elements
// 5 - load Theme Options. Attention! After this step you can use only basic options (not overriden)
// 9 - register other filters (for installer, etc.)
//10 - standard Theme init procedures (not ordered)
// Action 'wp_loaded'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc.)


//--------------------------------------------
// SKIN SETTINGS
//--------------------------------------------
if ( ! function_exists( 'planty_skin_setup' ) ) {
	add_action( 'after_setup_theme', 'planty_skin_setup', 1 );
	function planty_skin_setup() {

		$GLOBALS['PLANTY_STORAGE'] = array_merge( $GLOBALS['PLANTY_STORAGE'], array(

			// Key validator: market[env|loc]-vendor[axiom|ancora|themerex]
			'theme_pro_key'       => 'env-themerex',

			'theme_doc_url'       => '//planty.themerex.net/doc',

			'theme_demofiles_url' => '//demofiles.themerex.net/planty/',

			'theme_rate_url'      => '//themeforest.net/download',

			'theme_custom_url'    => '//themerex.net/offers/?utm_source=offers&utm_medium=click&utm_campaign=themeinstall',

			'theme_support_url'   => '//themerex.net/support/',

            'theme_download_url'  => '//themeforest.net/user/themerex/portfolio',            // ThemeREX

            'theme_video_url'     => '//www.youtube.com/channel/UCnFisBimrK2aIE-hnY70kCA',   // ThemeREX

            'theme_privacy_url'   => '//themerex.net/privacy-policy/',                       // ThemeREX

            'portfolio_url'       => '//themeforest.net/user/themerex/portfolio',            // ThemeREX

			// Comma separated slugs of theme-specific categories (for get relevant news in the dashboard widget)
			// (i.e. 'children,kindergarten')
			'theme_categories'    => '',
		) );
	}
}


// Add/remove/change Theme Settings
if ( ! function_exists( 'planty_skin_setup_settings' ) ) {
	add_action( 'after_setup_theme', 'planty_skin_setup_settings', 1 );
	function planty_skin_setup_settings() {
		// Example: enable (true) / disable (false) thumbs in the prev/next navigation
		planty_storage_set_array( 'settings', 'thumbs_in_navigation', false );
		planty_storage_set_array2( 'required_plugins', 'revslider', 'install', true );
		planty_storage_set_array2( 'required_plugins', 'woocommerce', 'install', true );
		planty_storage_set_array2( 'required_plugins', 'elegro-payment', 'install', true );
		planty_storage_set_array2( 'required_plugins', 'ti-woocommerce-wishlist', 'install', true );
		planty_storage_set_array2( 'required_plugins', 'instagram-feed', 'install', false );
	}
}



//--------------------------------------------
// SKIN FONTS
//--------------------------------------------
if ( ! function_exists( 'planty_skin_setup_fonts' ) ) {
	add_action( 'after_setup_theme', 'planty_skin_setup_fonts', 1 );
	function planty_skin_setup_fonts() {
		// Fonts to load when theme start
		// It can be:
		// - Google fonts (specify name, family and styles)
		// - Adobe fonts (specify name, family and link URL)
		// - uploaded fonts (specify name, family), placed in the folder css/font-face/font-name inside the skin folder
		// Attention! Font's folder must have name equal to the font's name, with spaces replaced on the dash '-'
		// example: font name 'TeX Gyre Termes', folder 'TeX-Gyre-Termes'
		planty_storage_set(
			'load_fonts', array(
				array(
					'name'   => 'stolzl',
					'family' => 'sans-serif',
					'link'   => 'https://use.typekit.net/pjg1ebb.css',
					'styles' => ''
				),
				// Google font
                array(
                    'name'   => 'DM Sans',
                    'family' => 'sans-serif',
                    'link'   => '',
                    'styles' => 'ital,wght@0,400;0,500;0,700;1,400;1,500;1,700',     // Parameter 'style' used only for the Google fonts
                )
			)
		);

		// Characters subset for the Google fonts. Available values are: latin,latin-ext,cyrillic,cyrillic-ext,greek,greek-ext,vietnamese
		planty_storage_set( 'load_fonts_subset', 'latin,latin-ext' );

		// Settings of the main tags.
		// Default value of 'font-family' may be specified as reference to the array $load_fonts (see above)
		// or as comma-separated string.
		// In the second case (if 'font-family' is specified manually as comma-separated string):
		//    1) Font name with spaces in the parameter 'font-family' will be enclosed in the quotes and no spaces after comma!
		//    2) If font-family inherit a value from the 'Main text' - specify 'inherit' as a value
		// example:
		// Correct:   'font-family' => basekit_get_load_fonts_family_string( $load_fonts[0] )
		// Correct:   'font-family' => 'Roboto,sans-serif'
		// Correct:   'font-family' => '"PT Serif",sans-serif'
		// Incorrect: 'font-family' => 'Roboto, sans-serif'
		// Incorrect: 'font-family' => 'PT Serif,sans-serif'

		$font_description = esc_html__( 'Font settings for the %s of the site. To ensure that the elements scale properly on mobile devices, please use only the following units: "rem", "em" or "ex"', 'planty' );

		planty_storage_set(
			'theme_fonts', array(
				'p'       => array(
					'title'           => esc_html__( 'Main text', 'planty' ),
					'description'     => sprintf( $font_description, esc_html__( 'main text', 'planty' ) ),
					'font-family'     => '"DM Sans",sans-serif',
					'font-size'       => '1rem',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.647em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '0em',
					'margin-bottom'   => '1.57em',
				),
				'post'    => array(
					'title'           => esc_html__( 'Article text', 'planty' ),
					'description'     => sprintf( $font_description, esc_html__( 'article text', 'planty' ) ),
					'font-family'     => '',			// Example: '"PR Serif",serif',
					'font-size'       => '',			// Example: '1.286rem',
					'font-weight'     => '',			// Example: '400',
					'font-style'      => '',			// Example: 'normal',
					'line-height'     => '',			// Example: '1.75em',
					'text-decoration' => '',			// Example: 'none',
					'text-transform'  => '',			// Example: 'none',
					'letter-spacing'  => '',			// Example: '',
					'margin-top'      => '',			// Example: '0em',
					'margin-bottom'   => '',			// Example: '1.4em',
				),
				'h1'      => array(
					'title'           => esc_html__( 'Heading 1', 'planty' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H1', 'planty' ) ),
					'font-family'     => 'stolzl,sans-serif',
					'font-size'       => '3.353em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.053em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '-1.8px',
					'margin-top'      => '1.04em',
					'margin-bottom'   => '0.46em',
				),
				'h2'      => array(
					'title'           => esc_html__( 'Heading 2', 'planty' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H2', 'planty' ) ),
					'font-family'     => 'stolzl,sans-serif',
					'font-size'       => '2.765em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.021em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '0.67em',
					'margin-bottom'   => '0.56em',
				),
				'h3'      => array(
					'title'           => esc_html__( 'Heading 3', 'planty' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H3', 'planty' ) ),
					'font-family'     => 'stolzl,sans-serif',
					'font-size'       => '2.059em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.086em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '0.94em',
					'margin-bottom'   => '0.72em',
				),
				'h4'      => array(
					'title'           => esc_html__( 'Heading 4', 'planty' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H4', 'planty' ) ),
					'font-family'     => 'stolzl,sans-serif',
					'font-size'       => '1.647em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.214em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '1.15em',
					'margin-bottom'   => '0.83em',
				),
				'h5'      => array(
					'title'           => esc_html__( 'Heading 5', 'planty' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H5', 'planty' ) ),
					'font-family'     => 'stolzl,sans-serif',
					'font-size'       => '1.412em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.208em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '1.3em',
					'margin-bottom'   => '0.84em',
				),
				'h6'      => array(
					'title'           => esc_html__( 'Heading 6', 'planty' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H6', 'planty' ) ),
					'font-family'     => 'stolzl,sans-serif',
					'font-size'       => '1.118em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.474em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '1.75em',
					'margin-bottom'   => '1.1em',
				),
				'logo'    => array(
					'title'           => esc_html__( 'Logo text', 'planty' ),
					'description'     => sprintf( $font_description, esc_html__( 'text of the logo', 'planty' ) ),
					'font-family'     => 'stolzl,sans-serif',
					'font-size'       => '1.7em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.25em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
				),
				'button'  => array(
					'title'           => esc_html__( 'Buttons', 'planty' ),
					'description'     => sprintf( $font_description, esc_html__( 'buttons', 'planty' ) ),
					'font-family'     => 'stolzl,sans-serif',
					'font-size'       => '14px',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '21px',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
				),
				'input'   => array(
					'title'           => esc_html__( 'Input fields', 'planty' ),
					'description'     => sprintf( $font_description, esc_html__( 'input fields, dropdowns and textareas', 'planty' ) ),
					'font-family'     => 'inherit',
					'font-size'       => '16px',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',     // Attention! Firefox don't allow line-height less then 1.5em in the select
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0.1px',
				),
				'info'    => array(
					'title'           => esc_html__( 'Post meta', 'planty' ),
					'description'     => sprintf( $font_description, esc_html__( 'post meta (author, categories, publish date, counters, share, etc.)', 'planty' ) ),
					'font-family'     => 'inherit',
					'font-size'       => '14px',  // Old value '13px' don't allow using 'font zoom' in the custom blog items
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '0.4em',
					'margin-bottom'   => '',
				),
				'menu'    => array(
					'title'           => esc_html__( 'Main menu', 'planty' ),
					'description'     => sprintf( $font_description, esc_html__( 'main menu items', 'planty' ) ),
					'font-family'     => 'stolzl,sans-serif',
					'font-size'       => '14px',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
				),
				'submenu' => array(
					'title'           => esc_html__( 'Dropdown menu', 'planty' ),
					'description'     => sprintf( $font_description, esc_html__( 'dropdown menu items', 'planty' ) ),
					'font-family'     => '"DM Sans",sans-serif',
					'font-size'       => '15px',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
				),
				'other' => array(
					'title'           => esc_html__( 'Other', 'planty' ),
					'description'     => sprintf( $font_description, esc_html__( 'specific elements', 'planty' ) ),
					'font-family'     => '"DM Sans",sans-serif',
				),
			)
		);

		// Font presets
		planty_storage_set(
			'font_presets', array(
				'karla' => array(
								'title'  => esc_html__( 'Karla', 'planty' ),
								'load_fonts' => array(
													// Google font
													array(
														'name'   => 'Dancing Script',
														'family' => 'fantasy',
														'link'   => '',
														'styles' => '300,400,700',
													),
													// Google font
													array(
														'name'   => 'Sansita Swashed',
														'family' => 'fantasy',
														'link'   => '',
														'styles' => '300,400,700',
													),
												),
								'theme_fonts' => array(
													'p'       => array(
														'font-family'     => '"Dancing Script",fantasy',
														'font-size'       => '1.25rem',
													),
													'post'    => array(
														'font-family'     => '',
													),
													'h1'      => array(
														'font-family'     => '"Sansita Swashed",fantasy',
														'font-size'       => '4em',
													),
													'h2'      => array(
														'font-family'     => '"Sansita Swashed",fantasy',
													),
													'h3'      => array(
														'font-family'     => '"Sansita Swashed",fantasy',
													),
													'h4'      => array(
														'font-family'     => '"Sansita Swashed",fantasy',
													),
													'h5'      => array(
														'font-family'     => '"Sansita Swashed",fantasy',
													),
													'h6'      => array(
														'font-family'     => '"Sansita Swashed",fantasy',
													),
													'logo'    => array(
														'font-family'     => '"Sansita Swashed",fantasy',
													),
													'button'  => array(
														'font-family'     => '"Sansita Swashed",fantasy',
													),
													'input'   => array(
														'font-family'     => 'inherit',
													),
													'info'    => array(
														'font-family'     => 'inherit',
													),
													'menu'    => array(
														'font-family'     => '"Sansita Swashed",fantasy',
													),
													'submenu' => array(
														'font-family'     => '"Sansita Swashed",fantasy',
													),
												),
							),
				'roboto' => array(
								'title'  => esc_html__( 'Roboto', 'planty' ),
								'load_fonts' => array(
													// Google font
													array(
														'name'   => 'Noto Sans JP',
														'family' => 'serif',
														'link'   => '',
														'styles' => '300,300italic,400,400italic,700,700italic',
													),
													// Google font
													array(
														'name'   => 'Merriweather',
														'family' => 'sans-serif',
														'link'   => '',
														'styles' => '300,300italic,400,400italic,700,700italic',
													),
												),
								'theme_fonts' => array(
													'p'       => array(
														'font-family'     => '"Noto Sans JP",serif',
													),
													'post'    => array(
														'font-family'     => '',
													),
													'h1'      => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
													'h2'      => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
													'h3'      => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
													'h4'      => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
													'h5'      => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
													'h6'      => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
													'logo'    => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
													'button'  => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
													'input'   => array(
														'font-family'     => 'inherit',
													),
													'info'    => array(
														'font-family'     => 'inherit',
													),
													'menu'    => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
													'submenu' => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
												),
							),
				'garamond' => array(
								'title'  => esc_html__( 'Garamond', 'planty' ),
								'load_fonts' => array(
													// Adobe font
													array(
														'name'   => 'Europe',
														'family' => 'sans-serif',
														'link'   => 'https://use.typekit.net/qmj1tmx.css',
														'styles' => '',
													),
													// Adobe font
													array(
														'name'   => 'Sofia Pro',
														'family' => 'sans-serif',
														'link'   => 'https://use.typekit.net/qmj1tmx.css',
														'styles' => '',
													),
												),
								'theme_fonts' => array(
													'p'       => array(
														'font-family'     => '"Sofia Pro",sans-serif',
													),
													'post'    => array(
														'font-family'     => '',
													),
													'h1'      => array(
														'font-family'     => 'Europe,sans-serif',
													),
													'h2'      => array(
														'font-family'     => 'Europe,sans-serif',
													),
													'h3'      => array(
														'font-family'     => 'Europe,sans-serif',
													),
													'h4'      => array(
														'font-family'     => 'Europe,sans-serif',
													),
													'h5'      => array(
														'font-family'     => 'Europe,sans-serif',
													),
													'h6'      => array(
														'font-family'     => 'Europe,sans-serif',
													),
													'logo'    => array(
														'font-family'     => 'Europe,sans-serif',
													),
													'button'  => array(
														'font-family'     => 'Europe,sans-serif',
													),
													'input'   => array(
														'font-family'     => 'inherit',
													),
													'info'    => array(
														'font-family'     => 'inherit',
													),
													'menu'    => array(
														'font-family'     => 'Europe,sans-serif',
													),
													'submenu' => array(
														'font-family'     => 'Europe,sans-serif',
													),
												),
							),
			)
		);
	}
}


//--------------------------------------------
// COLOR SCHEMES
//--------------------------------------------
if ( ! function_exists( 'planty_skin_setup_schemes' ) ) {
	add_action( 'after_setup_theme', 'planty_skin_setup_schemes', 1 );
	function planty_skin_setup_schemes() {

		// Theme colors for customizer
		// Attention! Inner scheme must be last in the array below
		planty_storage_set(
			'scheme_color_groups', array(
				'main'    => array(
					'title'       => esc_html__( 'Main', 'planty' ),
					'description' => esc_html__( 'Colors of the main content area', 'planty' ),
				),
				'alter'   => array(
					'title'       => esc_html__( 'Alter', 'planty' ),
					'description' => esc_html__( 'Colors of the alternative blocks (sidebars, etc.)', 'planty' ),
				),
				'extra'   => array(
					'title'       => esc_html__( 'Extra', 'planty' ),
					'description' => esc_html__( 'Colors of the extra blocks (dropdowns, price blocks, table headers, etc.)', 'planty' ),
				),
				'inverse' => array(
					'title'       => esc_html__( 'Inverse', 'planty' ),
					'description' => esc_html__( 'Colors of the inverse blocks - when link color used as background of the block (dropdowns, blockquotes, etc.)', 'planty' ),
				),
				'input'   => array(
					'title'       => esc_html__( 'Input', 'planty' ),
					'description' => esc_html__( 'Colors of the form fields (text field, textarea, select, etc.)', 'planty' ),
				),
			)
		);

		planty_storage_set(
			'scheme_color_names', array(
				'bg_color'    => array(
					'title'       => esc_html__( 'Background color', 'planty' ),
					'description' => esc_html__( 'Background color of this block in the normal state', 'planty' ),
				),
				'bg_hover'    => array(
					'title'       => esc_html__( 'Background hover', 'planty' ),
					'description' => esc_html__( 'Background color of this block in the hovered state', 'planty' ),
				),
				'bd_color'    => array(
					'title'       => esc_html__( 'Border color', 'planty' ),
					'description' => esc_html__( 'Border color of this block in the normal state', 'planty' ),
				),
				'bd_hover'    => array(
					'title'       => esc_html__( 'Border hover', 'planty' ),
					'description' => esc_html__( 'Border color of this block in the hovered state', 'planty' ),
				),
				'text'        => array(
					'title'       => esc_html__( 'Text', 'planty' ),
					'description' => esc_html__( 'Color of the text inside this block', 'planty' ),
				),
				'text_dark'   => array(
					'title'       => esc_html__( 'Text dark', 'planty' ),
					'description' => esc_html__( 'Color of the dark text (bold, header, etc.) inside this block', 'planty' ),
				),
				'text_light'  => array(
					'title'       => esc_html__( 'Text light', 'planty' ),
					'description' => esc_html__( 'Color of the light text (post meta, etc.) inside this block', 'planty' ),
				),
				'text_link'   => array(
					'title'       => esc_html__( 'Link', 'planty' ),
					'description' => esc_html__( 'Color of the links inside this block', 'planty' ),
				),
				'text_hover'  => array(
					'title'       => esc_html__( 'Link hover', 'planty' ),
					'description' => esc_html__( 'Color of the hovered state of links inside this block', 'planty' ),
				),
				'text_link2'  => array(
					'title'       => esc_html__( 'Accent 2', 'planty' ),
					'description' => esc_html__( 'Color of the accented texts (areas) inside this block', 'planty' ),
				),
				'text_hover2' => array(
					'title'       => esc_html__( 'Accent 2 hover', 'planty' ),
					'description' => esc_html__( 'Color of the hovered state of accented texts (areas) inside this block', 'planty' ),
				),
				'text_link3'  => array(
					'title'       => esc_html__( 'Accent 3', 'planty' ),
					'description' => esc_html__( 'Color of the other accented texts (buttons) inside this block', 'planty' ),
				),
				'text_hover3' => array(
					'title'       => esc_html__( 'Accent 3 hover', 'planty' ),
					'description' => esc_html__( 'Color of the hovered state of other accented texts (buttons) inside this block', 'planty' ),
				),
			)
		);

		// Default values for each color scheme
		$schemes = array(

			// Color scheme: 'default'
			'default' => array(
				'title'    => esc_html__( 'Default', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#F6F6F6', //ok
					'bd_color'         => '#E7E7E7', //ok old #e5e5e5

					// Text and links colors
					'text'             => '#5E5E5E', //ok
					'text_light'       => '#9C9C9C', //ok
					'text_dark'        => '#1A1919', //ok
					'text_link'        => '#DD042B', //ok
					'text_hover'       => '#FF002E', //ok
					'text_link2'       => '#473BEF', //ok
					'text_hover2'      => '#222BB7', //ok
					'text_link3'       => '#161515', //ok
					'text_hover3'      => '#232323', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#ffffff', //ok
					'alter_bg_hover'   => '#F6F6F6', //ok
					'alter_bd_color'   => '#E7E7E7', //ok old #e5e5e5
					'alter_bd_hover'   => '#E5E5E5', //ok
					'alter_text'       => '#797C7F', //ok
					'alter_light'      => '#9C9C9C', //ok
					'alter_dark'       => '#1A1919', //ok
					'alter_link'       => '#DD042B', //ok
					'alter_hover'      => '#FF002E', //ok
					'alter_link2'      => '#473BEF', //ok
					'alter_hover2'     => '#222BB7', //ok
					'alter_link3'      => '#161515', //ok
					'alter_hover3'     => '#232323', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#1A1919', //ok
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#313131',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#A8AAAB', //ok
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff', // ok
					'extra_link'       => '#DD042B', //ok
					'extra_hover'      => '#ffffff', //ok
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#E7E7E7', //ok old #e5e5e5
					'input_bd_hover'   => '#1A1919', //ok
					'input_text'       => '#9C9C9C', //ok
					'input_light'      => '#9C9C9C', //ok
					'input_dark'       => '#1A1919', //ok

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#67bcc1',
					'inverse_bd_hover' => '#5aa4a9',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#1A1919', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#ffffff', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'dark'
			'dark'    => array(
				'title'    => esc_html__( 'Dark', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#161515', //ok
					'bd_color'         => '#302F2F', //ok #3C3F47

					// Text and links colors
					'text'             => '#D3D5D5', //ok
					'text_light'       => '#A8AAAB', //ok
					'text_dark'        => '#ffffff', //ok + #F6F6F6
					'text_link'        => '#DD042B', //ok
					'text_hover'       => '#FF002E', //ok
					'text_link2'       => '#473BEF', //ok
					'text_hover2'      => '#222BB7', //ok
					'text_link3'       => '#161515', //ok
					'text_hover3'      => '#232323', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#202020', //ok
					'alter_bg_hover'   => '#161515', //ok
					'alter_bd_color'   => '#302F2F', //ok #323641
					'alter_bd_hover'   => '#53535C', //ok
					'alter_text'       => '#D3D5D5', //ok +
					'alter_light'      => '#A8AAAB', //ok
					'alter_dark'       => '#F6F6F6', //ok +
					'alter_link'       => '#DD042B', //ok
					'alter_hover'      => '#FF002E', //ok
					'alter_link2'      => '#473BEF', //ok
					'alter_hover2'     => '#222BB7', //ok
					'alter_link3'      => '#161515', //ok
					'alter_hover3'     => '#232323', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#1A1919',
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#313131',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#A8AAAB',
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff',
					'extra_link'       => '#DD042B',
					'extra_hover'      => '#ffffff',
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#302F2F', //ok - #3C3F47
					'input_bd_hover'   => '#302F2F', //ok - #3C3F47
					'input_text'       => '#D3D5D5', //ok
					'input_light'      => '#D3D5D5', //ok
					'input_dark'       => '#ffffff',

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#e36650',
					'inverse_bd_hover' => '#cb5b47',
					'inverse_text'     => '#F6F6F6', //ok
					'inverse_light'    => '#6f6f6f',
					'inverse_dark'     => '#1A1919', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#1A1919', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'light'
			'light' => array(
				'title'    => esc_html__( 'Light', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#ffffff', //ok - GO
					'bd_color'         => '#E7E7E7', //ok old #e5e5e5

					// Text and links colors
					'text'             => '#5E5E5E', //ok
					'text_light'       => '#9C9C9C', //ok
					'text_dark'        => '#1A1919', //ok
					'text_link'        => '#DD042B', //ok
					'text_hover'       => '#FF002E', //ok
					'text_link2'       => '#473BEF', //ok
					'text_hover2'      => '#222BB7', //ok
					'text_link3'       => '#161515', //ok
					'text_hover3'      => '#232323', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#F6F6F6', //ok - GO
					'alter_bg_hover'   => '#ffffff', //ok - GO
					'alter_bd_color'   => '#E7E7E7', //ok old #e5e5e5
					'alter_bd_hover'   => '#E5E5E5', //ok
					'alter_text'       => '#797C7F', //ok
					'alter_light'      => '#9C9C9C', //ok
					'alter_dark'       => '#1A1919', //ok
					'alter_link'       => '#DD042B', //ok
					'alter_hover'      => '#FF002E', //ok
					'alter_link2'      => '#473BEF', //ok
					'alter_hover2'     => '#222BB7', //ok
					'alter_link3'      => '#161515', //ok
					'alter_hover3'     => '#232323', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#1A1919', //ok
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#313131',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#A8AAAB', //ok
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff', // ok
					'extra_link'       => '#DD042B', //ok
					'extra_hover'      => '#ffffff', //ok
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#E7E7E7', //ok old #e5e5e5
					'input_bd_hover'   => '#1A1919', //ok
					'input_text'       => '#9C9C9C', //ok
					'input_light'      => '#9C9C9C', //ok
					'input_dark'       => '#1A1919', //ok

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#67bcc1',
					'inverse_bd_hover' => '#5aa4a9',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#1A1919', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#ffffff', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Second - MetalWorking
			// Color scheme: 'mw-default'
			'mw-default' => array(
				'title'    => esc_html__( 'MW Default', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#F6F6F6', //ok
					'bd_color'         => '#E7E7E7', //ok old #e5e5e5

					// Text and links colors
					'text'             => '#5E5E5E', //ok
					'text_light'       => '#9C9C9C', //ok
					'text_dark'        => '#1A1919', //ok
					'text_link'        => '#FE5A0E', //ok
					'text_hover'       => '#F14E03', //ok
					'text_link2'       => '#473BEF', //ok
					'text_hover2'      => '#222BB7', //ok
					'text_link3'       => '#161515', //ok
					'text_hover3'      => '#232323', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#ffffff', //ok
					'alter_bg_hover'   => '#F6F6F6', //ok
					'alter_bd_color'   => '#E7E7E7', //ok old #e5e5e5
					'alter_bd_hover'   => '#E5E5E5', //ok
					'alter_text'       => '#797C7F', //ok
					'alter_light'      => '#9C9C9C', //ok
					'alter_dark'       => '#1A1919', //ok
					'alter_link'       => '#FE5A0E', //ok
					'alter_hover'      => '#F14E03', //ok
					'alter_link2'      => '#473BEF', //ok
					'alter_hover2'     => '#222BB7', //ok
					'alter_link3'      => '#161515', //ok
					'alter_hover3'     => '#232323', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#1A1919', //ok
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#313131',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#A8AAAB', //ok
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff', // ok
					'extra_link'       => '#FE5A0E', //ok
					'extra_hover'      => '#ffffff', //ok
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#E7E7E7', //ok old #e5e5e5
					'input_bd_hover'   => '#1A1919', //ok
					'input_text'       => '#9C9C9C', //ok
					'input_light'      => '#9C9C9C', //ok
					'input_dark'       => '#1A1919', //ok

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#67bcc1',
					'inverse_bd_hover' => '#5aa4a9',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#1A1919', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#ffffff', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'mw-dark'
			'mw-dark'    => array(
				'title'    => esc_html__( 'MW Dark', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#161515', //ok
					'bd_color'         => '#302F2F', //ok #3C3F47

					// Text and links colors
					'text'             => '#D3D5D5', //ok
					'text_light'       => '#A8AAAB', //ok
					'text_dark'        => '#ffffff', //ok + #F6F6F6
					'text_link'        => '#FE5A0E', //ok
					'text_hover'       => '#F14E03', //ok
					'text_link2'       => '#473BEF', //ok
					'text_hover2'      => '#222BB7', //ok
					'text_link3'       => '#161515', //ok
					'text_hover3'      => '#232323', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#202020', //ok
					'alter_bg_hover'   => '#161515', //ok
					'alter_bd_color'   => '#302F2F', //ok #323641
					'alter_bd_hover'   => '#53535C', //ok
					'alter_text'       => '#D3D5D5', //ok +
					'alter_light'      => '#A8AAAB', //ok
					'alter_dark'       => '#F6F6F6', //ok +
					'alter_link'       => '#FE5A0E', //ok
					'alter_hover'      => '#F14E03', //ok
					'alter_link2'      => '#473BEF', //ok
					'alter_hover2'     => '#222BB7', //ok
					'alter_link3'      => '#161515', //ok
					'alter_hover3'     => '#232323', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#1A1919',
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#313131',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#A8AAAB',
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff',
					'extra_link'       => '#FE5A0E',
					'extra_hover'      => '#ffffff',
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#302F2F', //ok - #3C3F47
					'input_bd_hover'   => '#302F2F', //ok - #3C3F47
					'input_text'       => '#D3D5D5', //ok
					'input_light'      => '#D3D5D5', //ok
					'input_dark'       => '#ffffff',

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#e36650',
					'inverse_bd_hover' => '#cb5b47',
					'inverse_text'     => '#F6F6F6', //ok
					'inverse_light'    => '#6f6f6f',
					'inverse_dark'     => '#1A1919', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#1A1919', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'mw-light'
			'mw-light' => array(
				'title'    => esc_html__( 'MW Light', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#ffffff', //ok - GO
					'bd_color'         => '#E7E7E7', //ok old #e5e5e5

					// Text and links colors
					'text'             => '#5E5E5E', //ok
					'text_light'       => '#9C9C9C', //ok
					'text_dark'        => '#1A1919', //ok
					'text_link'        => '#FE5A0E', //ok
					'text_hover'       => '#F14E03', //ok
					'text_link2'       => '#473BEF', //ok
					'text_hover2'      => '#222BB7', //ok
					'text_link3'       => '#161515', //ok
					'text_hover3'      => '#232323', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#F6F6F6', //ok - GO
					'alter_bg_hover'   => '#ffffff', //ok - GO
					'alter_bd_color'   => '#E7E7E7', //ok old #e5e5e5
					'alter_bd_hover'   => '#E5E5E5', //ok
					'alter_text'       => '#797C7F', //ok
					'alter_light'      => '#9C9C9C', //ok
					'alter_dark'       => '#1A1919', //ok
					'alter_link'       => '#FE5A0E', //ok
					'alter_hover'      => '#F14E03', //ok
					'alter_link2'      => '#473BEF', //ok
					'alter_hover2'     => '#222BB7', //ok
					'alter_link3'      => '#161515', //ok
					'alter_hover3'     => '#232323', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#1A1919', //ok
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#313131',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#A8AAAB', //ok
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff', // ok
					'extra_link'       => '#FE5A0E', //ok
					'extra_hover'      => '#ffffff', //ok
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#E7E7E7', //ok old #e5e5e5
					'input_bd_hover'   => '#1A1919', //ok
					'input_text'       => '#9C9C9C', //ok
					'input_light'      => '#9C9C9C', //ok
					'input_dark'       => '#1A1919', //ok

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#67bcc1',
					'inverse_bd_hover' => '#5aa4a9',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#1A1919', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#ffffff', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Third - Construction
			// Color scheme: 'co-default'
			'co-default' => array(
				'title'    => esc_html__( 'CO Default', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#F6F6F6', //ok
					'bd_color'         => '#E7E7E7', //ok old #e5e5e5

					// Text and links colors
					'text'             => '#5E5E5E', //ok
					'text_light'       => '#9C9C9C', //ok
					'text_dark'        => '#1A1919', //ok
					'text_link'        => '#FCAF17', //ok
					'text_hover'       => '#F6A400', //ok
					'text_link2'       => '#473BEF', //ok
					'text_hover2'      => '#222BB7', //ok
					'text_link3'       => '#161515', //ok
					'text_hover3'      => '#232323', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#ffffff', //ok
					'alter_bg_hover'   => '#F6F6F6', //ok
					'alter_bd_color'   => '#E7E7E7', //ok old #e5e5e5
					'alter_bd_hover'   => '#E5E5E5', //ok
					'alter_text'       => '#797C7F', //ok
					'alter_light'      => '#9C9C9C', //ok
					'alter_dark'       => '#1A1919', //ok
					'alter_link'       => '#FCAF17', //ok
					'alter_hover'      => '#F6A400', //ok
					'alter_link2'      => '#473BEF', //ok
					'alter_hover2'     => '#222BB7', //ok
					'alter_link3'      => '#161515', //ok
					'alter_hover3'     => '#232323', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#1A1919', //ok
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#313131',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#A8AAAB', //ok
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff', // ok
					'extra_link'       => '#FCAF17', //ok
					'extra_hover'      => '#ffffff', //ok
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#E7E7E7', //ok old #e5e5e5
					'input_bd_hover'   => '#1A1919', //ok
					'input_text'       => '#9C9C9C', //ok
					'input_light'      => '#9C9C9C', //ok
					'input_dark'       => '#1A1919', //ok

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#67bcc1',
					'inverse_bd_hover' => '#5aa4a9',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#1A1919', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#ffffff', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'co-dark'
			'co-dark'    => array(
				'title'    => esc_html__( 'CO Dark', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#161515', //ok
					'bd_color'         => '#302F2F', //ok #3C3F47

					// Text and links colors
					'text'             => '#D3D5D5', //ok
					'text_light'       => '#A8AAAB', //ok
					'text_dark'        => '#ffffff', //ok + #F6F6F6
					'text_link'        => '#FCAF17', //ok
					'text_hover'       => '#F6A400', //ok
					'text_link2'       => '#473BEF', //ok
					'text_hover2'      => '#222BB7', //ok
					'text_link3'       => '#161515', //ok
					'text_hover3'      => '#232323', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#202020', //ok
					'alter_bg_hover'   => '#161515', //ok
					'alter_bd_color'   => '#302F2F', //ok #323641
					'alter_bd_hover'   => '#53535C', //ok
					'alter_text'       => '#D3D5D5', //ok +
					'alter_light'      => '#A8AAAB', //ok
					'alter_dark'       => '#F6F6F6', //ok +
					'alter_link'       => '#FCAF17', //ok
					'alter_hover'      => '#F6A400', //ok
					'alter_link2'      => '#473BEF', //ok
					'alter_hover2'     => '#222BB7', //ok
					'alter_link3'      => '#161515', //ok
					'alter_hover3'     => '#232323', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#1A1919',
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#313131',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#A8AAAB',
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff',
					'extra_link'       => '#FCAF17',
					'extra_hover'      => '#ffffff',
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#302F2F', //ok - #3C3F47
					'input_bd_hover'   => '#302F2F', //ok - #3C3F47
					'input_text'       => '#D3D5D5', //ok
					'input_light'      => '#D3D5D5', //ok
					'input_dark'       => '#ffffff',

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#e36650',
					'inverse_bd_hover' => '#cb5b47',
					'inverse_text'     => '#F6F6F6', //ok
					'inverse_light'    => '#6f6f6f',
					'inverse_dark'     => '#1A1919', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#1A1919', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'co-light'
			'co-light' => array(
				'title'    => esc_html__( 'CO Light', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#ffffff', //ok - GO
					'bd_color'         => '#E7E7E7', //ok old #e5e5e5

					// Text and links colors
					'text'             => '#5E5E5E', //ok
					'text_light'       => '#9C9C9C', //ok
					'text_dark'        => '#1A1919', //ok
					'text_link'        => '#FCAF17', //ok
					'text_hover'       => '#F6A400', //ok
					'text_link2'       => '#473BEF', //ok
					'text_hover2'      => '#222BB7', //ok
					'text_link3'       => '#161515', //ok
					'text_hover3'      => '#232323', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#F6F6F6', //ok - GO
					'alter_bg_hover'   => '#ffffff', //ok - GO
					'alter_bd_color'   => '#E7E7E7', //ok old #e5e5e5
					'alter_bd_hover'   => '#E5E5E5', //ok
					'alter_text'       => '#797C7F', //ok
					'alter_light'      => '#9C9C9C', //ok
					'alter_dark'       => '#1A1919', //ok
					'alter_link'       => '#FCAF17', //ok
					'alter_hover'      => '#F6A400', //ok
					'alter_link2'      => '#473BEF', //ok
					'alter_hover2'     => '#222BB7', //ok
					'alter_link3'      => '#161515', //ok
					'alter_hover3'     => '#232323', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#1A1919', //ok
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#313131',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#A8AAAB', //ok
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff', // ok
					'extra_link'       => '#FCAF17', //ok
					'extra_hover'      => '#ffffff', //ok
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#E7E7E7', //ok old #e5e5e5
					'input_bd_hover'   => '#1A1919', //ok
					'input_text'       => '#9C9C9C', //ok
					'input_light'      => '#9C9C9C', //ok
					'input_dark'       => '#1A1919', //ok

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#67bcc1',
					'inverse_bd_hover' => '#5aa4a9',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#1A1919', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#ffffff', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Fourth - Planty Interior
			// Color scheme: 'pi-default'
			'pi-default' => array(
				'title'    => esc_html__( 'PI Default', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#FBF3E6', //ok
					'bd_color'         => '#CFC8BE', //ok old #e5e5e5

					// Text and links colors
					'text'             => '#7E7972', //ok
					'text_light'       => '#9D988F', //ok
					'text_dark'        => '#111915', //ok
					'text_link'        => '#F26C3B', //ok
					'text_hover'       => '#DE5C2C', //ok
					'text_link2'       => '#D19F58', //ok
					'text_hover2'      => '#B58541', //ok
					'text_link3'       => '#9D9932', //ok
					'text_hover3'      => '#807C25', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#ffffff', //ok
					'alter_bg_hover'   => '#FBF3E6', //ok
					'alter_bd_color'   => '#CFC8BE', //ok old #e5e5e5
					'alter_bd_hover'   => '#111915', //ok
					'alter_text'       => '#797C7F', //ok
					'alter_light'      => '#9D988F', //ok
					'alter_dark'       => '#111915', //ok
					'alter_link'       => '#F26C3B', //ok
					'alter_hover'      => '#DE5C2C', //ok
					'alter_link2'      => '#D19F58', //ok
					'alter_hover2'     => '#B58541', //ok
					'alter_link3'      => '#9D9932', //ok
					'alter_hover3'     => '#807C25', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#24362E', //ok
					'extra_bg_hover'   => '#1C2923',
					'extra_bd_color'   => '#66736D',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#65716B', //ok
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff', // ok
					'extra_link'       => '#F26C3B', //ok
					'extra_hover'      => '#ffffff', //ok
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#CFC8BE', //ok old #e5e5e5
					'input_bd_hover'   => '#111915', //ok
					'input_text'       => '#9D988F', //ok
					'input_light'      => '#9D988F', //ok
					'input_dark'       => '#111915', //ok

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#67bcc1',
					'inverse_bd_hover' => '#5aa4a9',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#111915', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#ffffff', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'pi-dark'
			'pi-dark'    => array(
				'title'    => esc_html__( 'PI Dark', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#24362E', //ok
					'bd_color'         => '#66736D', //ok #3C3F47

					// Text and links colors
					'text'             => '#BDC3C0', //ok
					'text_light'       => '#65716B', //ok
					'text_dark'        => '#ffffff', //ok + #FBF3E6
					'text_link'        => '#F26C3B', //ok
					'text_hover'       => '#DE5C2C', //ok
					'text_link2'       => '#D19F58', //ok
					'text_hover2'      => '#B58541', //ok
					'text_link3'       => '#9D9932', //ok
					'text_hover3'      => '#807C25', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#1C2923', //ok
					'alter_bg_hover'   => '#24382F', //ok
					'alter_bd_color'   => '#66736D', //ok #323641
					'alter_bd_hover'   => '#FFFFFF', //ok
					'alter_text'       => '#BDC3C0', //ok +
					'alter_light'      => '#65716B', //ok
					'alter_dark'       => '#FBF3E6', //ok +
					'alter_link'       => '#F26C3B', //ok
					'alter_hover'      => '#DE5C2C', //ok
					'alter_link2'      => '#D19F58', //ok
					'alter_hover2'     => '#B58541', //ok
					'alter_link3'      => '#9D9932', //ok
					'alter_hover3'     => '#807C25', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#111915',
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#313131',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#65716B',
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff',
					'extra_link'       => '#F26C3B',
					'extra_hover'      => '#ffffff',
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#66736D', //ok - #3C3F47
					'input_bd_hover'   => '#302F2F', //ok - #3C3F47
					'input_text'       => '#BDC3C0', //ok
					'input_light'      => '#BDC3C0', //ok
					'input_dark'       => '#ffffff',

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#e36650',
					'inverse_bd_hover' => '#cb5b47',
					'inverse_text'     => '#FBF3E6', //ok
					'inverse_light'    => '#6f6f6f',
					'inverse_dark'     => '#111915', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#111915', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'pi-light'
			'pi-light' => array(
				'title'    => esc_html__( 'PI Light', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#ffffff', //ok - GO
					'bd_color'         => '#CFC8BE', //ok old #e5e5e5

					// Text and links colors
					'text'             => '#7E7972', //ok
					'text_light'       => '#9D988F', //ok
					'text_dark'        => '#111915', //ok
					'text_link'        => '#F26C3B', //ok
					'text_hover'       => '#DE5C2C', //ok
					'text_link2'       => '#D19F58', //ok
					'text_hover2'      => '#B58541', //ok
					'text_link3'       => '#9D9932', //ok
					'text_hover3'      => '#807C25', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#FBF3E6', //ok - GO
					'alter_bg_hover'   => '#ffffff', //ok - GO
					'alter_bd_color'   => '#CFC8BE', //ok old #e5e5e5
					'alter_bd_hover'   => '#111915', //ok
					'alter_text'       => '#797C7F', //ok
					'alter_light'      => '#9D988F', //ok
					'alter_dark'       => '#111915', //ok
					'alter_link'       => '#F26C3B', //ok
					'alter_hover'      => '#DE5C2C', //ok
					'alter_link2'      => '#D19F58', //ok
					'alter_hover2'     => '#B58541', //ok
					'alter_link3'      => '#9D9932', //ok
					'alter_hover3'     => '#807C25', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#111915', //ok
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#313131',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#65716B', //ok
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff', // ok
					'extra_link'       => '#F26C3B', //ok
					'extra_hover'      => '#ffffff', //ok
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#CFC8BE', //ok old #e5e5e5
					'input_bd_hover'   => '#111915', //ok
					'input_text'       => '#9D988F', //ok
					'input_light'      => '#9D988F', //ok
					'input_dark'       => '#111915', //ok

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#67bcc1',
					'inverse_bd_hover' => '#5aa4a9',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#111915', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#ffffff', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Fifth - Textile
			// Color scheme: 'te-default'
			'te-default' => array(
				'title'    => esc_html__( 'TE Default', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#FBF3E6', //ok
					'bd_color'         => '#CFC8BE', //ok old #e5e5e5

					// Text and links colors
					'text'             => '#7E7972', //ok
					'text_light'       => '#9D988F', //ok
					'text_dark'        => '#111915', //ok
					'text_link'        => '#6464BC', //ok
					'text_hover'       => '#525298', //ok
					'text_link2'       => '#BC8556', //ok
					'text_hover2'      => '#A2734B', //ok
					'text_link3'       => '#9D9932', //ok
					'text_hover3'      => '#807C25', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#ffffff', //ok
					'alter_bg_hover'   => '#FBF3E6', //ok
					'alter_bd_color'   => '#CFC8BE', //ok old #e5e5e5
					'alter_bd_hover'   => '#111915', //ok
					'alter_text'       => '#797C7F', //ok
					'alter_light'      => '#9D988F', //ok
					'alter_dark'       => '#111915', //ok
					'alter_link'       => '#6464BC', //ok
					'alter_hover'      => '#525298', //ok
					'alter_link2'      => '#BC8556', //ok
					'alter_hover2'     => '#A2734B', //ok
					'alter_link3'      => '#9D9932', //ok
					'alter_hover3'     => '#807C25', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#191A1C', //ok
					'extra_bg_hover'   => '#1C2923',
					'extra_bd_color'   => '#4B4B4B',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#646464', //ok
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff', // ok
					'extra_link'       => '#6464BC', //ok
					'extra_hover'      => '#ffffff', //ok
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#CFC8BE', //ok old #e5e5e5
					'input_bd_hover'   => '#111915', //ok
					'input_text'       => '#9D988F', //ok
					'input_light'      => '#9D988F', //ok
					'input_dark'       => '#111915', //ok

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#67bcc1',
					'inverse_bd_hover' => '#5aa4a9',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#111915', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#ffffff', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'te-dark'
			'te-dark'    => array(
				'title'    => esc_html__( 'TE Dark', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#191A1C', //ok
					'bd_color'         => '#4B4B4B', //ok #3C3F47

					// Text and links colors
					'text'             => '#AAAAAA', //ok
					'text_light'       => '#646464', //ok
					'text_dark'        => '#ffffff', //ok + #FBF3E6
					'text_link'        => '#6464BC', //ok
					'text_hover'       => '#525298', //ok
					'text_link2'       => '#BC8556', //ok
					'text_hover2'      => '#A2734B', //ok
					'text_link3'       => '#9D9932', //ok
					'text_hover3'      => '#807C25', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#1C2923', //ok
					'alter_bg_hover'   => '#24382F', //ok
					'alter_bd_color'   => '#4B4B4B', //ok #323641
					'alter_bd_hover'   => '#FFFFFF', //ok
					'alter_text'       => '#AAAAAA', //ok +
					'alter_light'      => '#646464', //ok
					'alter_dark'       => '#FBF3E6', //ok +
					'alter_link'       => '#6464BC', //ok
					'alter_hover'      => '#525298', //ok
					'alter_link2'      => '#BC8556', //ok
					'alter_hover2'     => '#A2734B', //ok
					'alter_link3'      => '#9D9932', //ok
					'alter_hover3'     => '#807C25', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#111915',
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#313131',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#646464',
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff',
					'extra_link'       => '#6464BC',
					'extra_hover'      => '#ffffff',
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#4B4B4B', //ok - #3C3F47
					'input_bd_hover'   => '#302F2F', //ok - #3C3F47
					'input_text'       => '#AAAAAA', //ok
					'input_light'      => '#AAAAAA', //ok
					'input_dark'       => '#ffffff',

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#e36650',
					'inverse_bd_hover' => '#cb5b47',
					'inverse_text'     => '#FBF3E6', //ok
					'inverse_light'    => '#6f6f6f',
					'inverse_dark'     => '#111915', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#111915', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'te-light'
			'te-light' => array(
				'title'    => esc_html__( 'TE Light', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#ffffff', //ok - GO
					'bd_color'         => '#CFC8BE', //ok old #e5e5e5

					// Text and links colors
					'text'             => '#7E7972', //ok
					'text_light'       => '#9D988F', //ok
					'text_dark'        => '#111915', //ok
					'text_link'        => '#6464BC', //ok
					'text_hover'       => '#525298', //ok
					'text_link2'       => '#BC8556', //ok
					'text_hover2'      => '#A2734B', //ok
					'text_link3'       => '#9D9932', //ok
					'text_hover3'      => '#807C25', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#FBF3E6', //ok - GO
					'alter_bg_hover'   => '#ffffff', //ok - GO
					'alter_bd_color'   => '#CFC8BE', //ok old #e5e5e5
					'alter_bd_hover'   => '#111915', //ok
					'alter_text'       => '#797C7F', //ok
					'alter_light'      => '#9D988F', //ok
					'alter_dark'       => '#111915', //ok
					'alter_link'       => '#6464BC', //ok
					'alter_hover'      => '#525298', //ok
					'alter_link2'      => '#BC8556', //ok
					'alter_hover2'     => '#A2734B', //ok
					'alter_link3'      => '#9D9932', //ok
					'alter_hover3'     => '#807C25', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#111915', //ok
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#313131',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#646464', //ok
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff', // ok
					'extra_link'       => '#6464BC', //ok
					'extra_hover'      => '#ffffff', //ok
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#CFC8BE', //ok old #e5e5e5
					'input_bd_hover'   => '#111915', //ok
					'input_text'       => '#9D988F', //ok
					'input_light'      => '#9D988F', //ok
					'input_dark'       => '#111915', //ok

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#67bcc1',
					'inverse_bd_hover' => '#5aa4a9',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#111915', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#ffffff', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Sixth - Energy
			// Color scheme: 'en-default'
			'en-default' => array(
				'title'    => esc_html__( 'EN Default', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#F4F8FA', //ok
					'bd_color'         => '#D6DADD', //ok old #e5e5e5

					// Text and links colors
					'text'             => '#5F5F65', //ok
					'text_light'       => '#898A8E', //ok
					'text_dark'        => '#181D4E', //ok
					'text_link'        => '#096BF0', //ok
					'text_hover'       => '#065FD8', //ok
					'text_link2'       => '#F3940D', //ok
					'text_hover2'      => '#DB8408', //ok
					'text_link3'       => '#14AA59', //ok
					'text_hover3'      => '#0D8E48', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#ffffff', //ok
					'alter_bg_hover'   => '#F4F8FA', //ok
					'alter_bd_color'   => '#D6DADD', //ok old #e5e5e5
					'alter_bd_hover'   => '#181D4E', //ok
					'alter_text'       => '#797C7F', //ok
					'alter_light'      => '#898A8E', //ok
					'alter_dark'       => '#181D4E', //ok
					'alter_link'       => '#096BF0', //ok
					'alter_hover'      => '#065FD8', //ok
					'alter_link2'      => '#F3940D', //ok
					'alter_hover2'     => '#DB8408', //ok
					'alter_link3'      => '#14AA59', //ok
					'alter_hover3'     => '#0D8E48', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#0B0D26', //ok
					'extra_bg_hover'   => '#181C38',
					'extra_bd_color'   => '#242A40',
					'extra_bd_hover'   => '#454B62',
					'extra_text'       => '#A3A7AD', //ok
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff', // ok
					'extra_link'       => '#096BF0', //ok
					'extra_hover'      => '#ffffff', //ok
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#D6DADD', //ok old #e5e5e5
					'input_bd_hover'   => '#181D4E', //ok
					'input_text'       => '#898A8E', //ok
					'input_light'      => '#898A8E', //ok
					'input_dark'       => '#181D4E', //ok

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#67bcc1',
					'inverse_bd_hover' => '#5aa4a9',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#181D4E', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#ffffff', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'en-dark'
			'en-dark'    => array(
				'title'    => esc_html__( 'EN Dark', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#0B0D26', //ok
					'bd_color'         => '#242A40', //ok #3C3F47

					// Text and links colors
					'text'             => '#D2D8E2', //ok
					'text_light'       => '#A3A7AD', //ok
					'text_dark'        => '#F4F8FA', //ok + #F4F8FA
					'text_link'        => '#096BF0', //ok
					'text_hover'       => '#065FD8', //ok
					'text_link2'       => '#F3940D', //ok
					'text_hover2'      => '#DB8408', //ok
					'text_link3'       => '#14AA59', //ok
					'text_hover3'      => '#0D8E48', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#181C38', //ok
					'alter_bg_hover'   => '#24382F', //ok
					'alter_bd_color'   => '#242A40', //ok #323641
					'alter_bd_hover'   => '#FFFFFF', //ok
					'alter_text'       => '#D2D8E2', //ok +
					'alter_light'      => '#A3A7AD', //ok
					'alter_dark'       => '#FCFCFC', //ok +
					'alter_link'       => '#096BF0', //ok
					'alter_hover'      => '#065FD8', //ok
					'alter_link2'      => '#F3940D', //ok
					'alter_hover2'     => '#DB8408', //ok
					'alter_link3'      => '#14AA59', //ok
					'alter_hover3'     => '#0D8E48', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#181D4E',
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#313131',
					'extra_bd_hover'   => '#454B62',
					'extra_text'       => '#A3A7AD',
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff',
					'extra_link'       => '#096BF0',
					'extra_hover'      => '#ffffff',
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#242A40', //ok - #3C3F47
					'input_bd_hover'   => '#302F2F', //ok - #3C3F47
					'input_text'       => '#D2D8E2', //ok
					'input_light'      => '#D2D8E2', //ok
					'input_dark'       => '#ffffff',

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#e36650',
					'inverse_bd_hover' => '#cb5b47',
					'inverse_text'     => '#F4F8FA', //ok
					'inverse_light'    => '#6f6f6f',
					'inverse_dark'     => '#181D4E', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#181D4E', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'en-light'
			'en-light' => array(
				'title'    => esc_html__( 'EN Light', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#ffffff', //ok - GO
					'bd_color'         => '#D6DADD', //ok old #e5e5e5

					// Text and links colors
					'text'             => '#5F5F65', //ok
					'text_light'       => '#898A8E', //ok
					'text_dark'        => '#181D4E', //ok
					'text_link'        => '#096BF0', //ok
					'text_hover'       => '#065FD8', //ok
					'text_link2'       => '#F3940D', //ok
					'text_hover2'      => '#DB8408', //ok
					'text_link3'       => '#14AA59', //ok
					'text_hover3'      => '#0D8E48', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#F4F8FA', //ok - GO
					'alter_bg_hover'   => '#ffffff', //ok - GO
					'alter_bd_color'   => '#D6DADD', //ok old #e5e5e5
					'alter_bd_hover'   => '#181D4E', //ok
					'alter_text'       => '#797C7F', //ok
					'alter_light'      => '#898A8E', //ok
					'alter_dark'       => '#181D4E', //ok
					'alter_link'       => '#096BF0', //ok
					'alter_hover'      => '#065FD8', //ok
					'alter_link2'      => '#F3940D', //ok
					'alter_hover2'     => '#DB8408', //ok
					'alter_link3'      => '#14AA59', //ok
					'alter_hover3'     => '#0D8E48', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#181D4E', //ok
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#313131',
					'extra_bd_hover'   => '#454B62',
					'extra_text'       => '#A3A7AD', //ok
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff', // ok
					'extra_link'       => '#096BF0', //ok
					'extra_hover'      => '#ffffff', //ok
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#D6DADD', //ok old #e5e5e5
					'input_bd_hover'   => '#181D4E', //ok
					'input_text'       => '#898A8E', //ok
					'input_light'      => '#898A8E', //ok
					'input_dark'       => '#181D4E', //ok

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#67bcc1',
					'inverse_bd_hover' => '#5aa4a9',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#181D4E', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#ffffff', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Seventh - Oil
			// Color scheme: 'oi-default'
			'oi-default' => array(
				'title'    => esc_html__( 'Oil Default', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#F7F7F7', //ok
					'bd_color'         => '#D9D9D9', //ok old #e5e5e5

					// Text and links colors
					'text'             => '#5F5F65', //ok
					'text_light'       => '#898A8E', //ok
					'text_dark'        => '#292929', //ok
					'text_link'        => '#F3410B', //ok
					'text_hover'       => '#C93204', //ok
					'text_link2'       => '#086FD7', //ok
					'text_hover2'      => '#055FBA', //ok
					'text_link3'       => '#DD9F2A', //ok
					'text_hover3'      => '#C68B1C', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#ffffff', //ok
					'alter_bg_hover'   => '#F7F7F7', //ok
					'alter_bd_color'   => '#D9D9D9', //ok old #e5e5e5
					'alter_bd_hover'   => '#292929', //ok
					'alter_text'       => '#797C7F', //ok
					'alter_light'      => '#898A8E', //ok
					'alter_dark'       => '#292929', //ok
					'alter_link'       => '#F3410B', //ok
					'alter_hover'      => '#C93204', //ok
					'alter_link2'      => '#086FD7', //ok
					'alter_hover2'     => '#055FBA', //ok
					'alter_link3'      => '#DD9F2A', //ok
					'alter_hover3'     => '#C68B1C', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#171A21', //ok
					'extra_bg_hover'   => '#10152F',
					'extra_bd_color'   => '#272A31',
					'extra_bd_hover'   => '#454B62',
					'extra_text'       => '#A3A7AD', //ok
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff', // ok
					'extra_link'       => '#F3410B', //ok
					'extra_hover'      => '#ffffff', //ok
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#D9D9D9', //ok old #e5e5e5
					'input_bd_hover'   => '#292929', //ok
					'input_text'       => '#898A8E', //ok
					'input_light'      => '#898A8E', //ok
					'input_dark'       => '#292929', //ok

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#67bcc1',
					'inverse_bd_hover' => '#5aa4a9',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#292929', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#ffffff', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'oi-dark'
			'oi-dark'    => array(
				'title'    => esc_html__( 'Oil Dark', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#171A21', //ok
					'bd_color'         => '#272A31', //ok #3C3F47

					// Text and links colors
					'text'             => '#D5D5D5', //ok
					'text_light'       => '#A3A7AD', //ok
					'text_dark'        => '#F7F7F7', //ok + #F7F7F7
					'text_link'        => '#F3410B', //ok
					'text_hover'       => '#C93204', //ok
					'text_link2'       => '#086FD7', //ok
					'text_hover2'      => '#055FBA', //ok
					'text_link3'       => '#DD9F2A', //ok
					'text_hover3'      => '#C68B1C', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#10152F', //ok
					'alter_bg_hover'   => '#181C38', //ok
					'alter_bd_color'   => '#272A31', //ok #323641
					'alter_bd_hover'   => '#37393F', //ok
					'alter_text'       => '#D5D5D5', //ok +
					'alter_light'      => '#A3A7AD', //ok
					'alter_dark'       => '#FCFCFC', //ok +
					'alter_link'       => '#F3410B', //ok
					'alter_hover'      => '#C93204', //ok
					'alter_link2'      => '#086FD7', //ok
					'alter_hover2'     => '#055FBA', //ok
					'alter_link3'      => '#DD9F2A', //ok
					'alter_hover3'     => '#C68B1C', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#292929',
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#313131',
					'extra_bd_hover'   => '#454B62',
					'extra_text'       => '#A3A7AD',
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff',
					'extra_link'       => '#F3410B',
					'extra_hover'      => '#ffffff',
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#272A31', //ok - #3C3F47
					'input_bd_hover'   => '#37393F', //ok - #3C3F47
					'input_text'       => '#D5D5D5', //ok
					'input_light'      => '#D5D5D5', //ok
					'input_dark'       => '#ffffff',

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#e36650',
					'inverse_bd_hover' => '#cb5b47',
					'inverse_text'     => '#F7F7F7', //ok
					'inverse_light'    => '#6f6f6f',
					'inverse_dark'     => '#292929', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#292929', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'oi-light'
			'oi-light' => array(
				'title'    => esc_html__( 'Oil Light', 'planty' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#ffffff', //ok - GO
					'bd_color'         => '#D9D9D9', //ok old #e5e5e5

					// Text and links colors
					'text'             => '#5F5F65', //ok
					'text_light'       => '#898A8E', //ok
					'text_dark'        => '#292929', //ok
					'text_link'        => '#F3410B', //ok
					'text_hover'       => '#C93204', //ok
					'text_link2'       => '#086FD7', //ok
					'text_hover2'      => '#055FBA', //ok
					'text_link3'       => '#DD9F2A', //ok
					'text_hover3'      => '#C68B1C', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#F7F7F7', //ok - GO
					'alter_bg_hover'   => '#ffffff', //ok - GO
					'alter_bd_color'   => '#D9D9D9', //ok old #e5e5e5
					'alter_bd_hover'   => '#292929', //ok
					'alter_text'       => '#797C7F', //ok
					'alter_light'      => '#898A8E', //ok
					'alter_dark'       => '#292929', //ok
					'alter_link'       => '#F3410B', //ok
					'alter_hover'      => '#C93204', //ok
					'alter_link2'      => '#086FD7', //ok
					'alter_hover2'     => '#055FBA', //ok
					'alter_link3'      => '#DD9F2A', //ok
					'alter_hover3'     => '#C68B1C', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#292929', //ok
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#313131',
					'extra_bd_hover'   => '#454B62',
					'extra_text'       => '#A3A7AD', //ok
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff', // ok
					'extra_link'       => '#F3410B', //ok
					'extra_hover'      => '#ffffff', //ok
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent', //ok
					'input_bg_hover'   => 'transparent', //ok
					'input_bd_color'   => '#D9D9D9', //ok old #e5e5e5
					'input_bd_hover'   => '#292929', //ok
					'input_text'       => '#898A8E', //ok
					'input_light'      => '#898A8E', //ok
					'input_dark'       => '#292929', //ok

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#67bcc1',
					'inverse_bd_hover' => '#5aa4a9',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#292929', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#ffffff', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),
		);
		planty_storage_set( 'schemes', $schemes );
		planty_storage_set( 'schemes_original', $schemes );

		// Add names of additional colors
		//---> For example:
		//---> planty_storage_set_array( 'scheme_color_names', 'new_color1', array(
		//---> 	'title'       => __( 'New color 1', 'planty' ),
		//---> 	'description' => __( 'Description of the new color 1', 'planty' ),
		//---> ) );


		// Additional colors for each scheme
		// Parameters:	'color' - name of the color from the scheme that should be used as source for the transformation
		//				'alpha' - to make color transparent (0.0 - 1.0)
		//				'hue', 'saturation', 'brightness' - inc/dec value for each color's component
		planty_storage_set(
			'scheme_colors_add', array(
				'bg_color_0'        => array(
					'color' => 'bg_color',
					'alpha' => 0,
				),
				'bg_color_02'       => array(
					'color' => 'bg_color',
					'alpha' => 0.2,
				),
				'bg_color_07'       => array(
					'color' => 'bg_color',
					'alpha' => 0.7,
				),
				'bg_color_08'       => array(
					'color' => 'bg_color',
					'alpha' => 0.8,
				),
				'bg_color_09'       => array(
					'color' => 'bg_color',
					'alpha' => 0.9,
				),
				'alter_bg_color_07' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0.7,
				),
				'alter_bg_color_04' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0.4,
				),
				'alter_bg_color_00' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0,
				),
				'alter_bg_color_02' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0.2,
				),
				'alter_bd_color_02' => array(
					'color' => 'alter_bd_color',
					'alpha' => 0.2,
				),
                'alter_dark_015'     => array(
                    'color' => 'alter_dark',
                    'alpha' => 0.15,
                ),
                'alter_dark_02'     => array(
                    'color' => 'alter_dark',
                    'alpha' => 0.2,
                ),
                'alter_dark_05'     => array(
                    'color' => 'alter_dark',
                    'alpha' => 0.5,
                ),
                'alter_dark_08'     => array(
                    'color' => 'alter_dark',
                    'alpha' => 0.8,
                ),
				'alter_link_02'     => array(
					'color' => 'alter_link',
					'alpha' => 0.2,
				),
				'alter_link_07'     => array(
					'color' => 'alter_link',
					'alpha' => 0.7,
				),
				'extra_bg_color_05' => array(
					'color' => 'extra_bg_color',
					'alpha' => 0.5,
				),
				'extra_bg_color_07' => array(
					'color' => 'extra_bg_color',
					'alpha' => 0.7,
				),
				'extra_link_02'     => array(
					'color' => 'extra_link',
					'alpha' => 0.2,
				),
				'extra_link_07'     => array(
					'color' => 'extra_link',
					'alpha' => 0.7,
				),
                'text_dark_003'      => array(
                    'color' => 'text_dark',
                    'alpha' => 0.03,
                ),
                'text_dark_005'      => array(
                    'color' => 'text_dark',
                    'alpha' => 0.05,
                ),
                'text_dark_008'      => array(
                    'color' => 'text_dark',
                    'alpha' => 0.08,
                ),
				'text_dark_015'      => array(
					'color' => 'text_dark',
					'alpha' => 0.15,
				),
				'text_dark_02'      => array(
					'color' => 'text_dark',
					'alpha' => 0.2,
				),
                'text_dark_03'      => array(
                    'color' => 'text_dark',
                    'alpha' => 0.3,
                ),
                'text_dark_05'      => array(
                    'color' => 'text_dark',
                    'alpha' => 0.5,
                ),
				'text_dark_07'      => array(
					'color' => 'text_dark',
					'alpha' => 0.7,
				),
                'text_dark_08'      => array(
                    'color' => 'text_dark',
                    'alpha' => 0.8,
                ),
                'text_link_007'      => array(
                    'color' => 'text_link',
                    'alpha' => 0.07,
                ),
				'text_link_02'      => array(
					'color' => 'text_link',
					'alpha' => 0.2,
				),
                'text_link_03'      => array(
                    'color' => 'text_link',
                    'alpha' => 0.3,
                ),
				'text_link_04'      => array(
					'color' => 'text_link',
					'alpha' => 0.4,
				),
				'text_link_07'      => array(
					'color' => 'text_link',
					'alpha' => 0.7,
				),
                'text_link2_007'      => array(
                    'color' => 'text_link2',
                    'alpha' => 0.07,
                ),
				'text_link2_02'      => array(
					'color' => 'text_link2',
					'alpha' => 0.2,
				),
                'text_link2_085'      => array(
                    'color' => 'text_link2',
                    'alpha' => 0.85,
                ),
                'text_link2_000'      => array(
                    'color' => 'text_link2',
                    'alpha' => 0,
                ),
                'text_link2_03'      => array(
                    'color' => 'text_link2',
                    'alpha' => 0.3,
                ),
				'text_link2_05'      => array(
					'color' => 'text_link2',
					'alpha' => 0.5,
				),
                'text_link3_007'      => array(
                    'color' => 'text_link3',
                    'alpha' => 0.07,
                ),
				'text_link3_02'      => array(
					'color' => 'text_link3',
					'alpha' => 0.2,
				),
                'text_link3_03'      => array(
                    'color' => 'text_link3',
                    'alpha' => 0.3,
                ),
                'inverse_text_03'      => array(
                    'color' => 'inverse_text',
                    'alpha' => 0.3,
                ),
                'inverse_link_08'      => array(
                    'color' => 'inverse_link',
                    'alpha' => 0.8,
                ),
                'inverse_hover_08'      => array(
                    'color' => 'inverse_hover',
                    'alpha' => 0.8,
                ),
				'text_dark_blend'   => array(
					'color'      => 'text_dark',
					'hue'        => 2,
					'saturation' => -5,
					'brightness' => 5,
				),
				'text_link_blend'   => array(
					'color'      => 'text_link',
					'hue'        => 2,
					'saturation' => -5,
					'brightness' => 5,
				),
				'alter_link_blend'  => array(
					'color'      => 'alter_link',
					'hue'        => 2,
					'saturation' => -5,
					'brightness' => 5,
				),
			)
		);

		// Simple scheme editor: lists the colors to edit in the "Simple" mode.
		// For each color you can set the array of 'slave' colors and brightness factors that are used to generate new values,
		// when 'main' color is changed
		// Leave 'slave' arrays empty if your scheme does not have a color dependency
		planty_storage_set(
			'schemes_simple', array(
				'text_link'        => array(),
				'text_hover'       => array(),
				'text_link2'       => array(),
				'text_hover2'      => array(),
				'text_link3'       => array(),
				'text_hover3'      => array(),
				'alter_link'       => array(),
				'alter_hover'      => array(),
				'alter_link2'      => array(),
				'alter_hover2'     => array(),
				'alter_link3'      => array(),
				'alter_hover3'     => array(),
				'extra_link'       => array(),
				'extra_hover'      => array(),
				'extra_link2'      => array(),
				'extra_hover2'     => array(),
				'extra_link3'      => array(),
				'extra_hover3'     => array(),
			)
		);

		// Parameters to set order of schemes in the css
		planty_storage_set(
			'schemes_sorted', array(
				'color_scheme',
				'header_scheme',
				'menu_scheme',
				'sidebar_scheme',
				'footer_scheme',
			)
		);

		// Color presets
		planty_storage_set(
			'color_presets', array(
				'autumn' => array(
								'title'  => esc_html__( 'Autumn', 'planty' ),
								'colors' => array(
												'default' => array(
																	'text_link'  => '#d83938',
																	'text_hover' => '#f2b232',
																	),
												'dark' => array(
																	'text_link'  => '#d83938',
																	'text_hover' => '#f2b232',
																	)
												)
							),
				'green' => array(
								'title'  => esc_html__( 'Natural Green', 'planty' ),
								'colors' => array(
												'default' => array(
																	'text_link'  => '#75ac78',
																	'text_hover' => '#378e6d',
																	),
												'dark' => array(
																	'text_link'  => '#75ac78',
																	'text_hover' => '#378e6d',
																	)
												)
							),
			)
		);
	}
}

// Activation methods
if ( ! function_exists( 'planty_skin_filter_activation_methods2' ) ) {
    add_filter( 'trx_addons_filter_activation_methods', 'planty_skin_filter_activation_methods2', 11, 1 );
    function planty_skin_filter_activation_methods2( $args ) {
        $args['elements_key'] = true;
        return $args;
    }
}

// Enqueue clone specific style
if ( ! function_exists( 'planty_clone_frontend_scripts' ) ) {
    add_action( 'wp_enqueue_scripts', 'planty_clone_frontend_scripts', 2040 );
    function planty_clone_frontend_scripts() {
        $planty_url = planty_get_file_url( planty_skins_get_current_skin_dir() . 'extra-style.css' );
        if ( '' != $planty_url ) {
            wp_enqueue_style( 'planty-extra-skin-' . esc_attr( planty_skins_get_current_skin_name() ), $planty_url, array(), null );
        }
    }
}

// Extra background image
if ( ! function_exists( 'planty_clone_extra_background_image' ) ) {
    add_action( 'after_setup_theme', 'planty_clone_extra_background_image', 3 );
    function planty_clone_extra_background_image() {
        planty_storage_set_array_after(
            'options', 'remove_margins', 'extra_background_image', array(
                'title'    => esc_html__( 'Extra background image', 'planty' ),
                'desc'     => wp_kses_data( __( 'Select background image', 'planty' ) ),
                'override' => array(
                    'mode'    => 'page',
                    'section' => esc_html__( 'Content', 'planty' ),
                ),
                'refresh'  => false,
                'std'      => 0,
                'pro_only'  => PLANTY_THEME_FREE,
                'type'     => 'switch',
            )
        );
    }
}


// Add div with fixed background
if ( ! function_exists( 'planty_clone_setup_body_wrap_class' ) ) {
    add_action('planty_filter_body_wrap_class', 'planty_clone_setup_body_wrap_class');
    function planty_clone_setup_body_wrap_class($class)  {

        $extra_bg_image = planty_is_on(planty_get_theme_option('extra_background_image'));
        $fixed_bg_class = ($extra_bg_image) ? 'fixed_bg' : '';

        if  ( $extra_bg_image ) {
            $class =  $class .' '. $fixed_bg_class;
        }

        return $class;
    }
}
