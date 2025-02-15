<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'paginaweb' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         ')1*/kkeFRJ^UYgmnj}#SQT`kXnh%gP>GS4Jq7Mb5~G0bI/r{0rg^S -vQ !FAHd1' );
define( 'SECURE_AUTH_KEY',  '8Ofh@`6m?dC>v}pi8B~<q#==p;JWc}1Y2iJPK}P_zTt{=K,S_l]JL1}6Qs8}AqHo' );
define( 'LOGGED_IN_KEY',    's6e#=[gbRvPM)!V#9Oz2xyE mAdGtZ17rTMA&bF1Xr?Z-&&bt@:b)Ce0Jj,7fF!$' );
define( 'NONCE_KEY',        '%CHx?C4kn`4Y&#;<n5,^:ATc#dpnM-,:(7T/[*n}Oi_(,d[&jZ}~)vN2GiW0i>^B' );
define( 'AUTH_SALT',        'O9mC)Fit>4n:CZdH`BJy2rA@{Ed}bVjenR%AMN&*E?Dz<aq{{m@6!Lr:q BNDG;U' );
define( 'SECURE_AUTH_SALT', '*FJyg3C6 |IrXxf@O~E,x^Qep,*?Ao:6Ea4}`ha|T5Pu]B&8cHz7% DJ2SZ?fzz7' );
define( 'LOGGED_IN_SALT',   'OT-S;YSUiAa}:Ce,BC4|iv@^9y0pTtM){!Bep~v&]@]fR;e%MYL=3NjgGcKwCklE' );
define( 'NONCE_SALT',       '<hpXLDZcZ5aZhyZ-NmY3A@eXP?c;NbRYU*2Ko&n~w}sX&Qc#}TIt7*=q$sb-$nj(' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */


define('WP_ALLOW_REPAIR', true);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
