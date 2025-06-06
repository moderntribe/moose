<?php declare( strict_types=1 );

/**
 * Base environment configuration, loaded for all environments (including automated tests)
 */

function tribe_isSSL(): bool {
	return ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off';
}

/**
 * @param string $name
 * @param mixed $default
 *
 * @return array|int|mixed|string|null
 */
function tribe_getenv( string $name, $default = null ) {
	$env = getenv( $name );
	if ( $env === false ) {
		return $default;
	}

	$env_str = strtolower( trim( $env ) );
	if ( $env_str === 'false' || $env_str === 'true' ) {
		return filter_var( $env_str, FILTER_VALIDATE_BOOLEAN );
	}

	if ( is_numeric( $env ) ) {
		return $env - 0;
	}

	return $env;
}

if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

if ( file_exists( __DIR__ . '/.env' ) ) {
	$dotenv = Dotenv\Dotenv::createUnsafeImmutable( __DIR__ );
	$dotenv->load();
}

if ( file_exists( __DIR__ . '/local-config.php' ) ) {
	include __DIR__ . '/local-config.php';
}

// Support a DATABASE_URL env var. Many PaaS like Heroku often provide credentials in this manner.
if ( ! defined( 'DB_NAME' ) && tribe_getenv( 'DATABASE_URL', false ) ) {
	$DSN = parse_url( tribe_getenv( 'DATABASE_URL' ) );
	// ** MySQL settings - You can get this info from your web host ** //
	/** The name of the database for WordPress */
	define( 'DB_NAME', substr( $DSN['path'], 1 ) );
	/** MySQL database username */
	define( 'DB_USER', $DSN['user'] );
	/** MySQL database password */
	define( 'DB_PASSWORD', $DSN['pass'] );
	/** MySQL hostname */
	define( 'DB_HOST', $DSN['host'] );
}

// ==============================================================
// Assign default constant values
// ==============================================================

// Provide fallback if ENVIRONMENT is already present.
if ( defined( 'ENVIRONMENT' ) && ! defined( 'WP_ENVIRONMENT_TYPE' ) ) {
	define( 'WP_ENVIRONMENT_TYPE', strtolower( ENVIRONMENT ) );
}

if ( ! defined( 'WP_ENVIRONMENT_TYPE' ) ) {
	define( 'WP_ENVIRONMENT_TYPE', 'development' );
}

if ( ! isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) ) {
	$_SERVER['HTTP_X_FORWARDED_PROTO'] = '';
}

if ( ! isset( $_SERVER['HTTP_HOST'] ) ) {
	$_SERVER['HTTP_HOST'] = 'local-cli';
}

if ( $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
	$_SERVER['HTTPS']       = 'on';
	$_SERVER['SERVER_PORT'] = 443;
}

// ==============================================================
// If a Load Balancer or Proxy is used, X-Forwarded-For HTTP Header to get the users real IP address
// ==============================================================

if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
	$http_x_headers = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );

	$_SERVER['REMOTE_ADDR'] = $http_x_headers[0];
}

$config_defaults = [

	// Multisite
	'WP_ALLOW_MULTISITE'             => tribe_getenv( 'WP_ALLOW_MULTISITE', false ),
	'MULTISITE'                      => tribe_getenv( 'WP_MULTISITE', false ),
	'SUBDOMAIN_INSTALL'              => tribe_getenv( 'SUBDOMAIN_INSTALL', false ),
	'DOMAIN_CURRENT_SITE'            => tribe_getenv( 'DOMAIN_CURRENT_SITE', '%%PRIMARY_DOMAIN%%' ),
	'PATH_CURRENT_SITE'              => tribe_getenv( 'PATH_CURRENT_SITE', '/' ),
	'SITE_ID_CURRENT_SITE'           => tribe_getenv( 'SITE_ID_CURRENT_SITE', 1 ),
	'BLOG_ID_CURRENT_SITE'           => tribe_getenv( 'BLOG_ID_CURRENT_SITE', 1 ),
	//'SUNRISE'                 => true,

	// DB settings
	'DB_CHARSET'                     => 'utf8',
	'DB_COLLATE'                     => '',

	// Language
	'WPLANG'                         => tribe_getenv( 'WPLANG', '' ),

	// Security Hashes (grab from: https://api.wordpress.org/secret-key/1.1/salt)
	'AUTH_KEY'                       => '%%AUTH_KEY%%',
	'SECURE_AUTH_KEY'                => '%%SECURE_AUTH_KEY%%',
	'LOGGED_IN_KEY'                  => '%%LOGGED_IN_KEY%%',
	'NONCE_KEY'                      => '%%NONCE_KEY%%',
	'AUTH_SALT'                      => '%%AUTH_SALT%%',
	'SECURE_AUTH_SALT'               => '%%SECURE_AUTH_SALT%%',
	'LOGGED_IN_SALT'                 => '%%LOGGED_IN_SALT%%',
	'NONCE_SALT'                     => '%%NONCE_SALT%%',

	// Security Directives
	'DISALLOW_FILE_EDIT'             => tribe_getenv( 'DISALLOW_FILE_EDIT', true ),
	'DISALLOW_FILE_MODS'             => tribe_getenv( 'DISALLOW_FILE_MODS', true ),
	'FORCE_SSL_LOGIN'                => tribe_getenv( 'FORCE_SSL_LOGIN', true ),
	'FORCE_SSL_ADMIN'                => tribe_getenv( 'FORCE_SSL_ADMIN', true ),

	// Performance
	'WP_CACHE'                       => tribe_getenv( 'WP_CACHE', false ),
	'DISABLE_WP_CRON'                => tribe_getenv( 'DISABLE_WP_CRON', true ),

	// We always disable cron on large installs
	'WP_MEMORY_LIMIT'                => tribe_getenv( 'WP_MEMORY_LIMIT', '96M' ),
	'WP_MAX_MEMORY_LIMIT'            => tribe_getenv( 'WP_MAX_MEMORY_LIMIT', '256M' ),
	'EMPTY_TRASH_DAYS'               => tribe_getenv( 'EMPTY_TRASH_DAYS', 7 ),
	'WP_APC_KEY_SALT'                => tribe_getenv( 'WP_APC_KEY_SALT', 'tribe' ),
	'WP_MEMCACHED_KEY_SALT'          => tribe_getenv( 'WP_MEMCACHED_KEY_SALT', 'tribe' ),

	// Debug
	'WP_DEBUG'                       => tribe_getenv( 'WP_DEBUG', true ),
	'WP_DEBUG_LOG'                   => tribe_getenv( 'WP_DEBUG_LOG', true ),
	'WP_DEBUG_DISPLAY'               => tribe_getenv( 'WP_DEBUG_DISPLAY', true ),
	'SAVEQUERIES'                    => tribe_getenv( 'SAVEQUERIES', true ),
	'SCRIPT_DEBUG'                   => tribe_getenv( 'SCRIPT_DEBUG', true ),
	'CONCATENATE_SCRIPTS'            => tribe_getenv( 'CONCATENATE_SCRIPTS', false ),
	'COMPRESS_SCRIPTS'               => tribe_getenv( 'COMPRESS_SCRIPTS', false ),
	'COMPRESS_CSS'                   => tribe_getenv( 'COMPRESS_CSS', false ),
	'WP_DISABLE_FATAL_ERROR_HANDLER' => tribe_getenv( 'WP_DISABLE_FATAL_ERROR_HANDLER', true ),

	// Miscellaneous
	'WP_POST_REVISIONS'              => tribe_getenv( 'WP_POST_REVISIONS', true ),
	'WP_DEFAULT_THEME'               => tribe_getenv( 'WP_DEFAULT_THEME', 'core' ),

	// S3
	'S3_UPLOADS_BUCKET'              => tribe_getenv( 'S3_UPLOADS_BUCKET', '' ),
	'S3_UPLOADS_KEY'                 => tribe_getenv( 'S3_UPLOADS_KEY', '' ),
	'S3_UPLOADS_SECRET'              => tribe_getenv( 'S3_UPLOADS_SECRET', '' ),
	'S3_UPLOADS_REGION'              => tribe_getenv( 'S3_UPLOADS_REGION', '' ),

	// Glomar
	'TRIBE_GLOMAR'                   => tribe_getenv( 'TRIBE_GLOMAR', '' ),
];

// ==============================================================
// Assign default constant value overrides for production
// ==============================================================

if ( defined( 'WP_ENVIRONMENT_TYPE' ) && WP_ENVIRONMENT_TYPE === 'production' ) {
	$config_defaults['WP_CACHE']            = true;
	$config_defaults['WP_DEBUG']            = false;
	$config_defaults['WP_DEBUG_LOG']        = false;
	$config_defaults['WP_DEBUG_DISPLAY']    = false;
	$config_defaults['SAVEQUERIES']         = false;
	$config_defaults['SCRIPT_DEBUG']        = false;
	$config_defaults['CONCATENATE_SCRIPTS'] = true;
	$config_defaults['COMPRESS_SCRIPTS']    = true;
	$config_defaults['COMPRESS_CSS']        = true;
}

// ==============================================================
// Use defaults array to define constants where applicable
// ==============================================================

foreach ( $config_defaults as $config_default_key => $config_default_value ) {
	if ( defined( $config_default_key ) ) {
		continue;
	}

	define( $config_default_key, $config_default_value );
}

// ==============================================================
// Table prefix
// Change this if you have multiple installs in the same database
// ==============================================================

if ( empty( $GLOBALS['table_prefix'] ) ) {
	// phpcs:ignore
	$table_prefix = $GLOBALS['table_prefix'] = tribe_getenv( 'DB_TABLE_PREFIX', 'tribe_' );
}

if ( empty( $GLOBALS['memcached_servers'] ) ) {
	$GLOBALS['memcached_servers'] = [
		[
			tribe_getenv( 'MEMCACHED_HOST', 'memcached' ),
			tribe_getenv( 'MEMCACHED_PORT', '11211' )
		]
	];
}

// ==============================================================
// Manually back up the WP_DEBUG_DISPLAY directive
// ==============================================================

if ( ! defined( 'WP_DEBUG_DISPLAY' ) || ! WP_DEBUG_DISPLAY ) {
	ini_set( 'display_errors', '0' );
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	\WP_CLI::add_wp_hook(
		'enable_wp_debug_mode_checks',
		static function ( $ret ) {
			if ( WP_DEBUG_LOG && is_string( WP_DEBUG_LOG ) ) {
				ini_set( 'error_log', WP_DEBUG_LOG );
			}

			return $ret;
		},
		11
	);
}
