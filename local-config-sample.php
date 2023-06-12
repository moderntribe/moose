<?php declare(strict_types=1);

/*
 * This is a sample local-config.php file
 *
 * You may include settings here that you only want
 * enabled on your local development checkouts
 *
*/

/**
 * Lando local development
 * uncomment below to use the lando settings for local development.
 */
/** This will ensure these are only loaded on Lando */
if ( getenv( 'LANDO_INFO' ) ) {
	$lando_info      = json_decode( getenv( 'LANDO_INFO' ) );
	$database_config = $lando_info->database;
	define( 'DB_NAME', $database_config->creds->database );
	define( 'DB_USER', $database_config->creds->user );
	define( 'DB_PASSWORD', $database_config->creds->password );
	define( 'DB_HOST', $database_config->internal_connection->host );

	/** URL routing (Optional, may not be necessary) */
	// define('WP_HOME','http://mysite.lndo.site');
	// define('WP_SITEURL','http://mysite.lndo.site');
}

/**
 * Set the current environment type. Accepted values:
 * - production (default)
 * - staging
 * - development
 * - local
 *
 * Note, if you are using Local, you will need to define these here.
 *
 * @link https://make.wordpress.org/core/2020/07/24/new-wp_get_environment_type-function-in-wordpress-5-5/
 */
if ( ! defined( 'WP_ENVIRONMENT_TYPE' ) ) {
	define( 'WP_ENVIRONMENT_TYPE', 'local' );
}

/*
 * Glomar
 *
 * GLOMAR is a plugin that blocks the frontend of the site from public access.
 * If you would like to disable the plugin locally, add the following to your local-config.php.
 */
define( 'TRIBE_GLOMAR', false );

/*
 * ACF Integration
 *
 * If the constant is set to true, this will hide the ACF menu item.  The ACF
 * menu item is also hidden when wp_get_environment_type() function returns
 * 'production'.
 */
define( 'HIDE_ACF_MENU', false );
