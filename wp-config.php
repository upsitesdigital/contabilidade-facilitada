<?php




/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

require_once dirname(__FILE__) . '/../etc/php/lib/CloudezSettings.php';
define ('WPLANG', 'pt_BR');
define('FS_METHOD', 'direct');

define('WPCF7_AUTOP', false);

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', CEZ_DBNAME);

/** MySQL database username */
define('DB_USER', CEZ_DBUSER);

/** MySQL database password */
define('DB_PASSWORD', CEZ_DBPASS);

/** MySQL hostname */
define('DB_HOST', CEZ_DBHOST);

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', CEZ_DBCHARSET);

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('WP_MEMORY_LIMIT', ini_get('memory_limit'));


/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'kqfaQU1Z(vQ8@L.s|@^(tCm|/uaG<W^eQ:Qfy_r^E0,#JdY`BKdC<751fu7M');
define('SECURE_AUTH_KEY',  '{juerEK;pXHvw`nM j_9k5UdsAXrC#LmEzHw6cbrU]XTfk5G:Xr^A`55w%9G');
define('LOGGED_IN_KEY',    'u58PW.WLZpr@vj%,9]ep:T#>Y>vArs9v;p2qLL^ |u$QyLk.qsBE@{dAbKeX');
define('NONCE_KEY',        '`|%>Kd6}ydL$}jTsH40/HqT|Ue4Kdn%2P%%1>4AVJAdcM/(xz0hRd98}||vd');
define('AUTH_SALT',        'PDh;V0RC`esQ*bFxH9ceNN{sdpazN3%CV,tuZP.6Z+dNkQeZ,H2n!K[vg@U}');
define('SECURE_AUTH_SALT', '>+:Ewc2q.R*9#zat@+jbA[VST1^U@B||dJQN,(<t$k@xM@Yy*x_`A`$n5467');
define('LOGGED_IN_SALT',   'W`fkU[0mFUTef#38G9[g%)S+@R#tc><xtak/ap4Se*Qb}zfs|;Ub`Eh+5he8');
define('NONCE_SALT',       '+vbANP7,fWFmW>[Wue$d,PeDgW[mg{|8(Cn)8aHxeCufU*M ]0%h[[UP;7ET');

define('WP_SITEURL', isset($_SERVER['HTTP_HOST']) ? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? 'https://'.$_SERVER['HTTP_HOST'] : 'http://'.$_SERVER['HTTP_HOST'] : 'http://contabilidade.siteup.dev');
define('WP_HOME', isset($_SERVER['HTTP_HOST']) ? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? 'https://'.$_SERVER['HTTP_HOST'] : 'http://'.$_SERVER['HTTP_HOST'] : 'http://contabilidade.siteup.dev');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'mode_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/**
 * security
 */
define('DISALLOW_FILE_EDIT', true);
define('CONCATENATE_SCRIPTS', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
