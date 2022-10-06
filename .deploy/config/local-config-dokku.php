<?php declare(strict_types=1);

/**
 * Local config for the dokku setup
 */

/**
 * Gets the value from the .env file
 *
 * @param string $key
 * @param string $default
 *
 * @return string|bool
 */
function fromenv( string $key, mixed $default = null ) {
	$value = getenv( $key );
	if ( $value === false ) {
		$value = $default;
	}

	return $value;
}

// S3-Uploads Plugin
define( 'S3_UPLOADS_BUCKET', fromenv( 'S3_UPLOADS_BUCKET', '' ) );
define( 'S3_UPLOADS_REGION', fromenv( 'S3_UPLOADS_REGION', 'us-west-2' ) );
define( 'S3_UPLOADS_KEY', fromenv( 'S3_UPLOADS_KEY', '' ) );
define( 'S3_UPLOADS_SECRET', fromenv( 'S3_UPLOADS_SECRET', '' ) );


/*
 * Debugging
 */
define( 'WP_DEBUG', ( bool ) fromenv( 'WP_DEBUG', false ) );
define( 'SAVEQUERIES', ( bool ) fromenv( 'SAVEQUERIES', true ) );
define( 'WP_DEBUG_DISPLAY', ( bool ) fromenv( 'WP_DEBUG_DISPLAY', false ) );
define( 'SCRIPT_DEBUG', ( bool ) fromenv( 'SCRIPT_DEBUG', false ) );
define( 'WP_CACHE', ( bool ) fromenv( 'WP_CACHE', false ) );


/*
 * Glomar
 *
 * GLOMAR is a plugin that blocks the frontend of the site from public access.
 * If you would like to disable the plugin locally, add the following to your local-config.php.
 */
define( 'TRIBE_GLOMAR', true );
