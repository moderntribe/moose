<?php
/**
 * Plugin Name: Kadence Blocks - PRO Extension
 * Plugin URI:  https://www.kadencewp.com/product/kadence-gutenberg-blocks/
 * Description: Extends Kadence Blocks with powerful extras that make it possible to create beautiful content in the WordPress Block Editor
 * Version:     1.7.29
 * Author:      Kadence WP
 * Author URI:  https://www.kadencewp.com/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 * Text Domain: kadence-blocks-pro
 *
 * @package Kadence Blocks Pro
 */

/**
 * Class KPB_Requirements_Check
 */
final class KBP_Requirements_Check {

	/**
	 * Plugin file
	 *
	 * @var string
	 */
	private $file = '';

	/**
	 * Plugin basename
	 *
	 * @var string
	 */
	private $base = '';

	/**
	 * Requirements array
	 *
	 * @var array
	 */
	private $requirements = array(

		// PHP.
		'php' => array(
			'minimum' => '5.6.0',
			'name'    => 'PHP',
			'exists'  => true,
			'current' => false,
			'checked' => false,
			'met'     => false
		),

		// WordPress.
		'wp' => array(
			'minimum' => '4.4.0',
			'name'    => 'WordPress',
			'exists'  => true,
			'current' => false,
			'checked' => false,
			'met'     => false
		)
	);

	/**
	 * Setup plugin requirements
	 *
	 * @since 3.0
	 */
	public function __construct() {

		// Setup file & base
		$this->file = __FILE__;
		$this->base = plugin_basename( $this->file );

		// Always load translations
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// Load or quit
		$this->met()
			? $this->load()
			: $this->quit();
	}

	/**
	 * Quit without loading
	 *
	 * @since 3.0
	 */
	private function quit() {
		add_action( 'admin_head', array( $this, 'admin_head' ) );
	}

	/**
	 * Load normally
	 *
	 * @since 3.0
	 */
	private function load() {

		// Maybe include the bundled bootstrapper.
		if ( ! class_exists( 'Kadence_Blocks_Pro' ) ) {
			require_once dirname( $this->file ) . '/class-kadence-blocks-pro.php';
		}

		// Maybe hook-in the bootstrapper.
		if ( class_exists( 'Kadence_Blocks_Pro' ) ) {

			// Bootstrap to plugins_loaded before priority 10 to make sure
			// add-ons are loaded after us.
			add_action( 'plugins_loaded', array( $this, 'bootstrap' ), 4 );

			// Register the activation hook.
			register_activation_hook( $this->file, array( $this, 'install' ) );

		}
	}
	/**
	 * Bootstrap everything.
	 */
	public function bootstrap() {
		Kadence_Blocks_Pro::instance( $this->file );
	}
	/**
	 * Install, usually on an activation hook.
	 *
	 * @since 3.0
	 */
	public function install() {

		// Bootstrap to include all of the necessary files.
		$this->bootstrap();
		// Network wide?
		$network_wide = ! empty( $_GET['networkwide'] )
			? (bool) $_GET['networkwide']
			: false;

		// Call the installer directly during the activation hook.
		kbp_installer( $network_wide );
	}

	/**
	 * Plugin agnostic method to output unmet requirements styling
	 */
	public function admin_head() {

	}
	/**
	 * Plugin specific requirements checker
	 */
	private function check() {

		// Loop through requirements.
		foreach ( $this->requirements as $dependency => $properties ) {

			// Which dependency are we checking?
			switch ( $dependency ) {

				// PHP.
				case 'php' :
					$version = phpversion();
					break;

				// WP.
				case 'wp' :
					$version = get_bloginfo( 'version' );
					break;

				// Unknown.
				default :
					$version = false;
					break;
			}

			// Merge to original array.
			if ( ! empty( $version ) ) {
				$this->requirements[ $dependency ] = array_merge( $this->requirements[ $dependency ], array(
					'current' => $version,
					'checked' => true,
					'met'     => version_compare( $version, $properties['minimum'], '>=' )
				) );
			}
		}
	}

	/**
	 * Have all requirements been met?
	 *
	 * @return boolean
	 */
	public function met() {

		// Run the check.
		$this->check();

		// Default to true (any false below wins).
		$retval  = true;
		$to_meet = wp_list_pluck( $this->requirements, 'met' );

		// Look for unmet dependencies, and exit if so.
		foreach ( $to_meet as $met ) {
			if ( empty( $met ) ) {
				$retval = false;
				continue;
			}
		}

		// Return.
		return $retval;
	}

	/**
	 * Plugin specific text-domain loader.
	 *
	 * @return void
	 */
	public function load_textdomain() {

		// Set filter for plugin's languages directory
		$kbp_lang_dir = dirname( $this->base ) . '/languages/';
		$kbp_lang_dir = apply_filters( 'kbp_languages_directory', $kbp_lang_dir );

		// Load the default language files.
		load_plugin_textdomain( 'kadence-blocks-pro', false, $kbp_lang_dir );

	}
}

// Invoke the checker.
new KBP_Requirements_Check();


/**
 * Plugin Updates
 */
function kadence_blocks_pro_updating() {
	// Load updater class.
	require_once KBP_PATH . 'kadence-update-checker/kadence-update-checker.php';
	require_once KBP_PATH . 'kadence-classes/kadence-activation/updater.php';
}
add_action( 'after_setup_theme', 'kadence_blocks_pro_updating', 0 );
