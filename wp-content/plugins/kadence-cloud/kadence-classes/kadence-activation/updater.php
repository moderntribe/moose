<?php
/**
 * Class file to check for active license
 *
 * @package Kadence Plugins
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load activation API.
require_once KADENCE_CLOUD_PATH . 'kadence-classes/kadence-activation/class-kadence-plugin-api-manager.php';
if ( class_exists( 'Kadence_Plugin_API_Manager' ) ) {
	$kt_plugin_api = Kadence_Plugin_API_Manager::get_instance();
	$kt_plugin_api->add_product( 'kadence_cloud_activation', 'kt_api_manager_kadence_cloud_data', 'kadence_cloud', 'Kadence Cloud', KADENCE_CLOUD_VERSION );
	if ( is_multisite() ) {
		$show_local_activation = apply_filters( 'kadence_activation_individual_multisites', true );
		if ( $show_local_activation ) {
			if ( 'Activated' === get_option( 'kadence_cloud_activation' ) ) {
				$kadence_cloud_updater = Kadence_Update_Checker::buildUpdateChecker(
					'https://kernl.us/api/v1/updates/6014439fade89c2bf8d33385/',
					KADENCE_CLOUD_PATH . 'kadence-cloud.php',
					'kadence-cloud'
				);
			}
		} else {
			if ( 'Activated' === get_site_option( 'kadence_cloud_activation' ) ) {
				$kadence_cloud_updater = Kadence_Update_Checker::buildUpdateChecker(
					'https://kernl.us/api/v1/updates/6014439fade89c2bf8d33385/',
					KADENCE_CLOUD_PATH . 'kadence-cloud.php',
					'kadence-cloud'
				);
			}
		}
	} elseif ( 'Activated' === get_option( 'kadence_cloud_activation' ) ) {
		$kadence_cloud_updater = Kadence_Update_Checker::buildUpdateChecker(
			'https://kernl.us/api/v1/updates/6014439fade89c2bf8d33385/',
			KADENCE_CLOUD_PATH . 'kadence-cloud.php',
			'kadence-cloud'
		);
	}
}
