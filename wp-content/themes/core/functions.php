<?php declare(strict_types=1);

/**
 * Core theme functions
 */

/**
 * TEMPORARY ENQUEUE
 *
 * @TODO remove this in favor of abstracts
 */

function tribe_theme_init(): void {
	$block_name = 'core/button';
	$args       = [
		'handle' => 'theme-core-button',
		'src'    => get_theme_file_uri( 'dist/blocks/core/button/style-index.css' ),
		'path'   => get_theme_file_path( 'dist/blocks/core/button/style-index.css' ),
	];

	wp_enqueue_block_style( $block_name, $args );
}

add_action( 'after_setup_theme', 'tribe_theme_init' );
