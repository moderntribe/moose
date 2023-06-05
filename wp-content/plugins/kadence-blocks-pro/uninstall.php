<?php
/**
 * Uninstall Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

wp_clear_scheduled_hook( 'kadence_countdown_daily_cleanup' );

if ( apply_filters( 'kadence_blocks_pro_remove_data_uninstall', false ) ) {

	// Load kbp file.
	include_once 'kadence-blocks-pro.php';
	if ( class_exists( 'Kadence_Blocks_Pro' ) ) {
		// This is kind of stupid but it ensures that the capabilities file (class used below) gets loaded.
		Kadence_Blocks_Pro::instance( dirname( __FILE__ ) . '/kadence-blocks-pro.php' );
	}
	$entries_table = new \KBP\Tables\Entries();
	if ( $entries_table->exists() ) {
		$entries_table->uninstall();
	}
	$entries_meta_table = new \KBP\Tables\Entries_Meta();
	if ( $entries_meta_table->exists() ) {
		$entries_meta_table->uninstall();
	}
	$countdown_table = new \KBP\Tables\Countdown_Entries();
	if ( $$countdown_table->exists() ) {
		$countdown_table->uninstall();
	}
}
