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
// ** Chatbot **//
define( 'MYC_KEY_FILE_CONTENT', 'c30241535e1bb4764521bc73c5d85844a6fdfce2' );
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );
/** MySQL database username */
define( 'DB_USER', 'root' );
/** MySQL database password */
define( 'DB_PASSWORD', 'root' );
/** MySQL hostname */
define( 'DB_HOST', 'localhost' );
/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );
/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );
/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'JbyuAvZxScyMpwS/QTxfmzPv1a+s5+uPvcsg+LE3RBpomdfr52JHc7buEdLfbLf5Mn9yzDq+nGPXRsqRQ/3YrQ==');
define('SECURE_AUTH_KEY',  '91JvSVpsI1RkWKptpm4YAWOcLZ0wL5U9FMi/TvwKqV0m5u43Dv8CiloD+skMowCAwZrMwJDx3uk8NAZOtuc9mA==');
define('LOGGED_IN_KEY',    'ldoqC1BKkx+A8PjqffJhRWgq2mlsfIphB3Qg9MJdLegFjQjBN/gpn3lLNazvVE37G9nWonQK866XrbfeULSVog==');
define('NONCE_KEY',        'BaeUkMJYh2+ytOKJWpiNGyZGDbj+GsPTAeIPdN+1fI2DibIsOY2JhFNRVZ4nCnIPQfkRxNow/HGcqvjerSZ1mg==');
define('AUTH_SALT',        '6JFpUKtHWYkAKhhisDMzW+ijm/JxvYS5/TyVjlLfe5votxD4GpDhpbFSyZTimExi+A2oB5UYD6jb7ek1FCLuzw==');
define('SECURE_AUTH_SALT', 'xRw9V5PG7GftYHuQ6et4UmbM+dNvKsk3cSxO9vwTBTTiTmQshL6yqRiE+wQbAjprkGK7AbZ5Tf/cQYN/9A2DYQ==');
define('LOGGED_IN_SALT',   'bn8iTGRjrqSVqUBwt09dUENGtM8eXNetFL5RPfp61Te727mzX3D7BKqsYaZx0k3MhHkS9maKS6LjZDPduun+tA==');
define('NONCE_SALT',       '4IzyDOlsWSep1He4u7S6qdigchzSEL6Yesq9xWZcUPrr6/W9kG7VgdwaCEOnYLyz+dZDCx6pQZlyfxCpmh7Akg==');
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_16srpg8aoc_';
/* That's all, stop editing! Happy publishing. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
