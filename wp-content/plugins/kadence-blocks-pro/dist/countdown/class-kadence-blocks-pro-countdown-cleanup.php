<?php
/**
 * Manage Evergreen Campaigns.
 *
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cleanup Evergreen Campaigns.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Countdown_Cleanup {

	/**
	 * Instance of this class
	 *
	 * @var null
	 */
	private static $instance = null;
	/**
	 * Instance Control
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Class Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'on_init' ) );
	}
	/**
	 * Schedule Cleanup
	 */
	public function on_init() {
		add_action( 'kadence_countdown_daily_cleanup', array( $this, 'daily_cleanup' ) );
		if ( ! wp_next_scheduled( 'kadence_countdown_daily_cleanup' ) ) {
			wp_schedule_event( time(), 'daily', 'kadence_countdown_daily_cleanup' );
		}
	}
	/**
	 * Run Daily Cleanup
	 */
	public function daily_cleanup() {
		global $wpdb;
		$now = current_time( 'mysql' );
		$sql = $wpdb->prepare(
			"DELETE FROM {$wpdb->prefix}kbp_countdown_entry
			WHERE remove_date < %s",
			$now
		);
		$wpdb->query( $sql );
	}
}
Kadence_Blocks_Pro_Countdown_Cleanup::get_instance();
