<?php

/**
 * The installer.
 */
function kbp_installer( $network_wide = false ) {
	if ( $network_wide ) {
		add_action( 'shutdown', 'kbp_create_tables' );
	} else {
		kbp_create_tables();
	}
	update_option( 'kbp_is_installed', '1' );
}

/**
 * Check if KBP is installed and if not, run installation
 *
 * @return void
 */
function kbp_check_if_installed() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}

	// this is mainly for network activated installs
	if ( ! get_option( 'kbp_is_installed' ) ) {
		// Network wide?
		$network_wide = ! empty( $_GET['networkwide'] )
		? (bool) $_GET['networkwide']
		: false;
		kbp_installer( $network_wide );
	}
}
add_action( 'admin_init', 'kbp_check_if_installed' );

/**
 * Creates the Kadence Blocks Pro database tables.
 *
 * @since 2.7
 * @return void
 */
function kbp_create_tables() {
	if ( ! kadence_blocks_pro()->entries_table->exists() ) {
		kadence_blocks_pro()->entries_table->install();
	}
	if ( ! kadence_blocks_pro()->entries_meta_table->exists() ) {
		kadence_blocks_pro()->entries_meta_table->install();
	}
}
