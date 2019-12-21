<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'rajavillabali' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'mysql' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '6%sF8y(N:*:D!KNw>lVqEIu?>f<VcmNf;Mw/BY)wmS Wy~7GB!Y}T0c1r2m:eM_l' );
define( 'SECURE_AUTH_KEY',  ':t%(>nw6&m^$ P5v7?;tZ*Ro_pGHDVe?uVs;g^##y4b^IgSuuf(j<74I#v_HP+/Y' );
define( 'LOGGED_IN_KEY',    '2fuX.Cyz@Jv9R]&l:.TDr7&g+mN/IvFr3ZW(P$q4Mc7ed7Gkr*-f_IppC7edu1@K' );
define( 'NONCE_KEY',        ';9~)<rqCQ0Rkt]#%31Lu ZK@*|6NB29(o@a#j3M*,$TAX})G|.![}U9e~axT@%xH' );
define( 'AUTH_SALT',        ' 1g3(.d4rv;MVri*(]&cin%-dEkL+o?;%GDc{)boYo[;a,U1a6}~>&-Su6=<@8cw' );
define( 'SECURE_AUTH_SALT', 'qL58HNY$ea>C>yp[0m ?whg|Bj(%H9B9zu9|dihPn)1Ug<ZTEbq0i)}}Ff9Nldql' );
define( 'LOGGED_IN_SALT',   'WAt{[:JYQieT{:9.Jy(!=b:HqSq2kmz^F/.e@sS;UzZ-$b+&&&j)%&UkzUb{0+ws' );
define( 'NONCE_SALT',       ':&)eHih<u0m$X5]xv3vdUt,LS9H}4FD.9&OO,_187eA2?DtxC&wuV&1e}*1iZ7-g' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'rvb_';

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
