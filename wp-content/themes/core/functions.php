<?php declare(strict_types=1);

$tribe_autoloaders = [
	// WP sub folder
	trailingslashit( ABSPATH ) . '../vendor/autoload.php',
	// WP standard
	trailingslashit( ABSPATH ) . 'vendor/autoload.php',
	// In the core plugin
	trailingslashit( __DIR__ ) . 'vendor/autoload.php',
];

foreach ( $tribe_autoloaders as $autoloader ) {
	if ( file_exists( $autoloader ) ) {
		require_once $autoloader;
		break;
	}
}

/**
 * @TODO: Decide on an approach for this.
 */
if ( ! function_exists( 'get_component_part' ) ) {
	function get_component_part( string $slug, array $args = [] ): void {
		get_template_part( 'components/' . $slug, '', $args );
	}
}
