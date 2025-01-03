<?php declare(strict_types=1);
/**
 * Copies the local-config-sample.php file to local-config.php if it doesn't exist.
 * Generates a local-config.json file using Lando's own environment variables if it doesn't exist.
 */

/**
 * PHP Local Config
 */

if ( file_exists( 'local-config.php' ) ) {
	echo "local-config.php already exists. Skipping.\n";
} else {
	copy('local-config-sample.php', 'local-config.php');
}

/**
 * JSON Local Config
 */

if ( ! getenv( 'LANDO_INFO' ) ) {
	echo "To create a local-config.json file, this script must be run within a Lando container.\n";
	echo "Try `lando composer create-local-configs` if you're using Lando or manually create the file using local-config-sample.json as an example.\n";
	exit;
}

if ( file_exists( 'local-config.json' ) ) {
	echo "local-config.json already exists. Skipping.\n";
	exit;
}

$lando_info = json_decode( getenv( 'LANDO_INFO' ) );

// Get the HTTP server details, depending on the project configuration
$http_service_info = $lando_info->appserver->via === 'apache' ? $lando_info->appserver : $lando_info->appserver_nginx;

// Get the cert directory by removing the root `/lando` directory from Lando's internal cert path
$cert_directory = str_replace( '/lando', '', dirname( getenv( 'LANDO_SERVICE_CERT' ) ) );

// Create the config array
$config = [
	// Append Lando's cert directory to Lando's local config directory path
	'certPath' => getenv( 'LANDO_CONFIG_DIR' ) . $cert_directory,
	// Set the cert name to the base name of Lando's hostname without the `.internal` extension
	'certName' => basename( $http_service_info->hostnames[0], '.internal' ),
	// Set the host from Lando's service URL
	'host'     => parse_url( $http_service_info->urls[0] )['host'],
	'protocol' => 'https'
];

// Write the config values to local-config.json
file_put_contents( 'local-config.json', json_encode( $config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );

exit;
