<?php
/**
 * Check for free plugin class
 */
class Kadence_Blocks_Pro_Plugin_Check {
	private static $active_plugins;

	public static function init() {

	self::$active_plugins = (array) get_option( 'active_plugins', array() );

	if ( is_multisite() )
		self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}

	public static function active_check_kadence_blocks() {

		if ( ! self::$active_plugins ) {
			self::init();
		}
		return in_array( 'kadence-blocks/kadence-blocks.php', self::$active_plugins ) || array_key_exists( 'kadence-blocks/kadence-blocks.php', self::$active_plugins );
	}

}
