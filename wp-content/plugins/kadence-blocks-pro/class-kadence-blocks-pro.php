<?php
/**
 * Main plugin class
 */
final class Kadence_Blocks_Pro {
	/**
	 * Instance Control
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * @var \KBP\Tables\Entries
	 */
	public $entries_table;

	/**
	 * @var \KBP\Tables\Entries_Meta
	 */
	public $entries_meta_table;

	/**
	 * Main Kadence_Blocks_Pro Instance.
	 *
	 * Insures that only one instance of Kadence_Blocks_Pro exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @static
	 * @staticvar array $instance
	 *
	 * @param string $file Main plugin file path.
	 *
	 * @return Kadence_Blocks_Pro The one true Kadence_Blocks_Pro
	 */
	public static function instance( $file = '' ) {

		// Return if already instantiated.
		if ( self::is_instantiated() ) {
			return self::$instance;
		}

		// Setup the singleton.
		self::setup_instance( $file );

		// Bootstrap.
		self::$instance->setup_constants();
		self::$instance->setup_files();
		self::$instance->setup_application();

		// Return the instance.
		return self::$instance;

	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cloning instances of the class is forbidden.', 'kadence-blocks-pro' ), '3.0' );
	}

	/**
	 * Disable un-serializing of the class.
	 *
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of the class is forbidden.', 'kadence-blocks-pro' ), '3.0' );
	}

	/**
	 * Return whether the main loading class has been instantiated or not.
	 *
	 * @access private
	 * @return boolean True if instantiated. False if not.
	 */
	private static function is_instantiated() {

		// Return true if instance is correct class.
		if ( ! empty( self::$instance ) && ( self::$instance instanceof Kadence_Blocks_Pro ) ) {
			return true;
		}

		// Return false if not instantiated correctly.
		return false;
	}

	/**
	 * Setup the singleton instance
	 *
	 * @param string $file Path to main plugin file.
	 *
	 * @access private
	 */
	private static function setup_instance( $file = '' ) {
		self::$instance       = new Kadence_Blocks_Pro();
		self::$instance->file = $file;
	}
	/**
	 * Setup plugin constants.
	 *
	 * @access private
	 * @return void
	 */
	private function setup_constants() {

		if ( ! defined( 'KBP_VERSION' ) ) {
			define( 'KBP_VERSION', '1.7.29' );
		}

		if ( ! defined( 'KBP_PLUGIN_FILE' ) ) {
			define( 'KBP_PLUGIN_FILE', $this->file );
		}

		if ( ! defined( 'KBP_PATH' ) ) {
			define( 'KBP_PATH', realpath( plugin_dir_path( KBP_PLUGIN_FILE ) ) . DIRECTORY_SEPARATOR );
		}

		if ( ! defined( 'KBP_URL' ) ) {
			define( 'KBP_URL', plugin_dir_url( KBP_PLUGIN_FILE ) );
		}

	}
	/**
	 * Include required files.
	 *
	 * @access private
	 * @return void
	 */
	private function setup_files() {
		$this->include_files();

		// Admin.
		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			$this->include_admin();
		} else {
			$this->include_frontend();
		}
	}

	/**
	 * Setup the rest of the application
	 */
	private function setup_application() {

		self::$instance->entries_table            = new \KBP\Tables\Entries();
		self::$instance->entries_meta_table       = new \KBP\Tables\Entries_Meta();
		self::$instance->entries_countdown_table  = new \KBP\Tables\Countdown_Entries();

	}
	/**
	 * On Load
	 */
	public function include_files() {
		require_once KBP_PATH . 'includes/kbp-installer.php';
		require_once KBP_PATH . 'includes/kbp-active-campaign-controller.php';

		require_once KBP_PATH . 'dist/form/admin/berlindb/base.php';
		require_once KBP_PATH . 'dist/form/admin/berlindb/table.php';
		require_once KBP_PATH . 'dist/form/admin/berlindb/query.php';
		require_once KBP_PATH . 'dist/form/admin/berlindb/column.php';
		require_once KBP_PATH . 'dist/form/admin/berlindb/row.php';
		require_once KBP_PATH . 'dist/form/admin/berlindb/schema.php';
		require_once KBP_PATH . 'dist/form/admin/berlindb/compare.php';
		require_once KBP_PATH . 'dist/form/admin/berlindb/date.php';

		require_once KBP_PATH . 'dist/form/admin/form-entries-meta-table.php';
		require_once KBP_PATH . 'dist/form/admin/form-entries-table.php';
		require_once KBP_PATH . 'dist/form/admin/form-entries-query.php';
		require_once KBP_PATH . 'dist/form/admin/form-entries-schema.php';

		require_once KBP_PATH . 'dist/countdown/countdown-entries-table.php';
		require_once KBP_PATH . 'dist/countdown/countdown-entries-query.php';
		require_once KBP_PATH . 'dist/countdown/countdown-entries-schema.php';
		require_once KBP_PATH . 'dist/countdown/countdown-entry.php';
		require_once KBP_PATH . 'dist/countdown/class-kadence-blocks-pro-countdown.php';
		require_once KBP_PATH . 'dist/countdown/class-kadence-blocks-pro-countdown-cleanup.php';

		require_once KBP_PATH . 'dist/form/admin/form-entry.php';
		require_once KBP_PATH . 'dist/form/admin/kb-form-admin-entries-table-list.php';
		require_once KBP_PATH . 'dist/form/admin/kadence-admin-form-entries.php';
		require_once KBP_PATH . 'dist/form/kbp-form-actions.php';
		// Dynamic Content.
		require_once KBP_PATH . 'dist/dynamic-content/inc/metabox.php';
		require_once KBP_PATH . 'dist/dynamic-content/inc/woo.php';
		require_once KBP_PATH . 'dist/dynamic-content/inc/acf.php';
		require_once KBP_PATH . 'dist/dynamic-content/inc/pods.php';
		require_once KBP_PATH . 'dist/dynamic-content/inc/image-format.php';
		require_once KBP_PATH . 'dist/dynamic-content/inc/gallery-format.php';
		require_once KBP_PATH . 'dist/dynamic-content/inc/background-format.php';
		require_once KBP_PATH . 'dist/dynamic-content/class-kadence-blocks-pro-dynamic-content.php';
		//require_once KBP_PATH . 'dist/dynamicblocks/kadence-animate-on-scroll.php';
		require_once KBP_PATH . 'dist/class-kadence-blocks-dynamic-content-controller.php';
		require_once KBP_PATH . 'dist/class-kadence-blocks-post-select-controller.php';
		require_once KBP_PATH . 'dist/dynamicblocks/form-mailchimp-rest-api.php';
		require_once KBP_PATH . 'dist/dynamicblocks/form-sendinblue-rest-api.php';
		require_once KBP_PATH . 'dist/dynamicblocks/form-activecampaign-rest-api.php';
		require_once KBP_PATH . 'dist/init.php';
		require_once KBP_PATH . 'dist/dynamicblocks/class-kadence-blocks-pro-post-grid.php';
		require_once KBP_PATH . 'dist/dynamicblocks/class-kadence-blocks-pro-dynamic-html-block.php';
		require_once KBP_PATH . 'dist/dynamicblocks/portfolio-grid-carousel.php';
		if ( class_exists( 'Woocommerce' ) ) {
			require_once KBP_PATH . 'dist/dynamicblocks/product-carousel.php';
		}
		require_once KBP_PATH . 'dist/dynamicblocks/user-info.php';
		require_once KBP_PATH . 'dist/dynamicblocks/dynamic-list.php';
		require_once KBP_PATH . 'dist/class-kadence-blocks-pro-css.php';
		require_once KBP_PATH . 'dist/class-kadence-blocks-pro-frontend.php';
		require_once KBP_PATH . 'dist/class-kadence-blocks-pro-backend.php';
		require_once KBP_PATH . 'dist/class-kadence-blocks-pro-custom-icons.php';
	}

	/**
	 * On Load
	 */
	public function include_admin() {
		/**
		 * Plugin check class.
		 */
		require_once KBP_PATH . 'class-kadence-blocks-pro-plugin-check.php';
		if ( ! Kadence_Blocks_Pro_Plugin_Check::active_check_kadence_blocks() ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_need_kadence_blocks' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}
	}
	/**
	 * On Load
	 */
	public function include_frontend() {
	}
	/**
	 * Admin Notice
	 */
	public function admin_notice_need_kadence_blocks() {
		if ( get_transient( 'kadence_blocks_pro_free_plugin_notice' ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$installed_plugins = get_plugins();
		if ( ! isset( $installed_plugins['kadence-blocks/kadence-blocks.php'] ) ) {
			$button_label = esc_html__( 'Install Kadence Blocks', 'kadence-blocks-pro' );
			$data_action  = 'install';
		} else {
			$button_label = esc_html__( 'Activate Kadence Blocks', 'kadence-blocks-pro' );
			$data_action  = 'activate';
		}
		$install_link    = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'install-plugin',
					'plugin' => 'kadence-blocks',
				),
				network_admin_url( 'update.php' )
			),
			'install-plugin_kadence-blocks'
		);
		$activate_nonce  = wp_create_nonce( 'activate-plugin_kadence-blocks/kadence-blocks.php' );
		$activation_link = self_admin_url( 'plugins.php?_wpnonce=' . $activate_nonce . '&action=activate&plugin=kadence-blocks%2Fkadence-blocks.php' );
		echo '<div class="notice notice-error is-dismissible kt-blocks-pro-notice-wrapper">';
		// translators: %s is a link to kadence block plugin.
		echo '<p>' . sprintf( esc_html__( 'Kadence Blocks Pro requires %s to be active for all functions to work.', 'kadence-blocks-pro' ) . '</p>', '<a target="_blank" href="https://wordpress.org/plugins/kadence-blocks/">Kadence Blocks</a>' );
		echo '<p class="submit">';
		echo '<a class="button button-primary kt-install-blocks-btn" data-redirect-url="' . esc_url( admin_url( 'options-general.php?page=kadence_blocks' ) ) . '" data-activating-label="' . esc_attr__( 'Activating...', 'kadence-blocks-pro' ) . '" data-activated-label="' . esc_attr__( 'Activated', 'kadence-blocks-pro' ) . '" data-installing-label="' . esc_attr__( 'Installing...', 'kadence-blocks-pro' ) . '" data-installed-label="' . esc_attr__( 'Installed', 'kadence-blocks-pro' ) . '" data-action="' . esc_attr( $data_action ) . '" data-install-url="' . esc_attr( $install_link ) . '" data-activate-url="' . esc_attr( $activation_link ) . '">' . esc_html( $button_label ) . '</a>';
		echo '</p>';
		echo '</div>';
		wp_enqueue_script( 'kt-blocks-install' );
	}
	/**
	 * Function to output admin scripts.
	 *
	 * @param object $hook page hook.
	 */
	public function admin_scripts( $hook ) {
		wp_register_script( 'kt-blocks-install', KBP_URL . 'dist/settings/admin-activate.js', false, KBP_VERSION );
		wp_enqueue_style( 'kt-blocks-install', KBP_URL . 'dist/settings/admin-activate.css', false, KBP_VERSION );
	}
}
/**
 * Function to get main class instance.
 */
function kadence_blocks_pro() {
	return Kadence_Blocks_Pro::instance();
}
