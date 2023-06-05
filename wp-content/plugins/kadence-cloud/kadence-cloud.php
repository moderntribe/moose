<?php
/**
 * Plugin Name: Kadence Cloud
 * Description: Create your own cloud prebuilt sections or pages
 * Version: 1.0.7
 * Author: Kadence WP
 * Author URI: http://kadencewp.com/
 * License: GPLv2 or later
 * Text Domain: kadence-cloud
 *
 * @package Kadence Cloud
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KADENCE_CLOUD_PATH', realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR );
define( 'KADENCE_CLOUD_URL', plugin_dir_url( __FILE__ ) );
define( 'KADENCE_CLOUD_VERSION', '1.0.7' );


/**
 * Load Plugin
 */
function kadence_cloud_init() {
	require_once KADENCE_CLOUD_PATH . 'kadence-cloud-rest-controller.php';
	require_once KADENCE_CLOUD_PATH . 'class-kadence-cloud.php';
	require_once KADENCE_CLOUD_PATH . 'inc/class-kadence-cloud-settings.php';
}
add_action( 'plugins_loaded', 'kadence_cloud_init' );

/**
 * Load the plugin textdomain
 */
function kadence_cloud_lang() {
	load_plugin_textdomain( 'kadence-cloud', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'kadence_cloud_lang' );

/**
 * Plugin Updates.
 */
function kadence_cloud_api_updating() {
	require_once KADENCE_CLOUD_PATH . 'kadence-update-checker/kadence-update-checker.php';
	require_once KADENCE_CLOUD_PATH . 'kadence-classes/kadence-activation/updater.php';
}
add_action( 'after_setup_theme', 'kadence_cloud_api_updating', 1 );
