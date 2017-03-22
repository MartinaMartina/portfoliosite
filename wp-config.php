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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress_class');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '-zkM_Xp8-20g(k7Ej#tPza 1)&B^r:e-Qml[LL9<7G7%Tw:fxI*yz,-*}5H=G?({');
define('SECURE_AUTH_KEY',  '1&1)DVGwXhe8R.AANDb1.Qn^FQ3xfp>!382|@IT9FMC/8ME40b|(T$UvpxHc)tvF');
define('LOGGED_IN_KEY',    '%.DJM*b(d+iXQ;vki7PG/CLb&}7O6VsO~ 8?>=%i<ku+i=pQmP/5_ac2>q#EC]yh');
define('NONCE_KEY',        'R~0S+=v2j:tqe8@6cu>sS~~>}9ONL.{>c-Gl(be/>T<4L[b ?L>y 5 c]5V .*r5');
define('AUTH_SALT',        'N`i)zOU==+ZvzH5sw~{rjBz)l6)0~,S/QI<ZMU9v@]n)OY0PA&FD0ARKg:87+!dS');
define('SECURE_AUTH_SALT', 'cliUY$qR)VeV.>A6+)`YCp5TKH66E5!2; T,N8bd_(0=J7fmo05m#=-HF)> ?GA|');
define('LOGGED_IN_SALT',   'dlk%Gs^5E;2VJ?M.&V(]{7I9PJ@q=YVh5Cr:j^zM:c&+3!w]o6J}$@rZm@+^Gl,U');
define('NONCE_SALT',       '~+iOuJO%N/7xx>OQMv#-*ontqfyQslEwd8 6jYx,3|hX2:H^39+S&]>gO{6WYeum');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
