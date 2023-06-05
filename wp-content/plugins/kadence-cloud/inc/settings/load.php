<?php
/**
 * Figure out what version to load.
 *
 * Written by Chris Jean for iThemes.com, adapted for Kadence.
 *
 * @package Kadence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$kadence_registration_list_version   = '1.5.4';
$kadence_registration_list_library   = 'kadence_settings';
$kadence_registration_list_init_file = dirname( __FILE__ ) . '/init.php';

$GLOBALS['kadence_classes_registration_list'][ $kadence_registration_list_library ][ $kadence_registration_list_version ] = $kadence_registration_list_init_file;

if ( ! function_exists( 'kadence_registration_list_init' ) ) {
	/**
	 * Figure out which class to load.
	 */
	function kadence_registration_list_init() {

		$init_files = array();
		foreach ( (array) $GLOBALS['kadence_classes_registration_list'] as $library => $versions ) {
			$max_version = '-10000';
			$init_file = '';

			foreach ( (array) $versions as $version => $file ) {
				if ( version_compare( $version, $max_version, '>' ) ) {
					$max_version = $version;
					$init_file   = $file;
				}
			}

			if ( ! empty( $init_file ) ) {
				$init_files[] = $init_file;
			}
		}

		unset( $GLOBALS['kadence_classes_registration_list'] );

		foreach ( (array) $init_files as $init_file ) {
			require_once( $init_file );
		}

	}
	add_action( 'after_setup_theme', 'kadence_registration_list_init' );
}
