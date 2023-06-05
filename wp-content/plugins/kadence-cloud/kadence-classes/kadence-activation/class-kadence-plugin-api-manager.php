<?php
/**
 * Class file to check for active license
 * Displays an inactive message if the API License Key has not yet been activated
 *
 * @package Kadence Plugins
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Kadence_Plugin_API_Manager' ) ) {
	/**
	 * Class to check for license
	 *
	 * @category Class
	 */
	class Kadence_Plugin_API_Manager {
		private $api_url             = 'https://www.kadencewp.com/';
		private $api_data_key        = 'kt_plugin_api_manager';
		/**
		 * This is fall back for where we make api calls.
		 *
		 * @var url
		 */
		private $fallback_api_url    = 'https://www.kadencethemes.com/';
		private $renewal_url         = 'https://www.kadencewp.com/my-account/';
		private $admin_page_id       = 'kadence_plugin_activation';
		private $admin_page_name     = 'KT Plugin Activation';
		private $admin_page_title    = 'Kadence License Activation';
		private $version             = '1.2.8';
		public static $multisite     = false;
		public static $current_theme = null;
		public static $instance_id   = null;
		public static $domain        = null;
		public static $memberkey     = null;
		public static $memberemail   = null;
		public static $memberactive  = null;
		public static $ithemesactive  = null;
		public static $products      = array();

		/**
		 * Settings Control.
		 *
		 * @var settings array
		 */
		public static $settings = array();

		/**
		 * Instance Control.
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

		public static function add_product( $product_key = '', $product_data_key = '', $product_id = '', $product_name = '', $version = '1' ) {
			// Lets make sure it's not added to the array
			if ( ! isset( self::$products[ $product_id ] ) ) {
				// add to the products array
				self::$products[ $product_id ] = array(
					'product_key'      => $product_key,
					'product_data_key' => $product_data_key,
					'product_id'       => $product_id,
					'product_name'     => $product_name,
					'version'          => $version,
				);
			}
		}
		/**
		 * Construct function.
		 */
		public function __construct() {
			if ( is_admin() ) {
				// Add notices.
				add_action( 'init', array( $this, 'on_init' ), 1 );

				// Add notices.
				add_action( 'admin_init', array( $this, 'on_admin_init' ), 20 );

				// Repeat Check license.
				add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'status_check' ) );

				// register settings.
				add_action( 'admin_init', array( $this, 'load_settings' ) );
				// Save Network.
				add_action( 'network_admin_edit_kt_activate_update_network_options', array( $this, 'update_network_options' ) );
				// deactivate Network.
				add_action( 'network_admin_edit_kt_deactivate_update_network_options', array( $this, 'deactivate_network_options' ) );

			}
		}

		/**
		 * Get Things started
		 */
		public function on_init() {
			if ( is_multisite() ) {
				$show_local_activation = apply_filters( 'kadence_activation_individual_multisites', true );
				if ( $show_local_activation ) {
					self::$multisite = false;
					add_action( 'admin_menu', array( $this, 'add_menu' ) );
				} else {
					self::$multisite = true;
					add_action( 'network_admin_menu', array( $this, 'add_network_menu' ), 10);
				}
			} else {
				add_action( 'admin_menu', array( $this, 'add_menu' ) );
			}
			self::$current_theme = wp_get_theme();
			self::$instance_id   = $this->get_setting_option( 'kt_plugin_api_manager_instance_id', 'needs_instance' ); // Instance ID (unique to each blog activation).
			if ( 'needs_instance' == self::$instance_id || '' == self::$instance_id ) {
				self::$instance_id = wp_generate_password( 12, false );
				$this->update_setting_option( 'kt_plugin_api_manager_instance_id', self::$instance_id );
			}
			self::$domain = str_ireplace( array( 'http://', 'https://' ), '', home_url() );
		}

		/**
		 * Add Notices
		 */
		public function on_admin_init() {

			add_action( 'admin_notices', array( $this, 'check_external_blocking' ) );
			add_action( 'admin_notices', array( $this, 'inactive_notice' ) );
		}

		/**
		 * Displays an inactive notice when the software is inactive.
		 */
		public function inactive_notice() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			if ( isset( $_GET['page'] ) && $this->admin_page_id == $_GET['page'] ) {
				return;
			}
			$inactive_plugins = array();
			if ( $this->is_membership_activated() ) {
				foreach ( self::$products as $key => $value ) {
					if ( $this->get_setting_option( $value['product_key'], 'Deactivated' ) != 'Activated' ) {
						$this->update_setting_option( $value['product_key'], 'Activated' );
					}
				}
			} else {
				foreach ( self::$products as $key => $value ) {
					if ( $this->get_setting_option( $value['product_key'], 'Deactivated' ) != 'Activated' ) {
						$inactive_plugins[] = $value['product_name'];
					}
				}
			}
			if ( ! empty( $inactive_plugins ) ) {
				if ( self::$multisite && is_multisite() ) {
					if ( current_user_can( 'manage_network_options' ) ) {
						echo '<div class="error">';
						echo '<p>' . __( 'The following plugins have not been activated: ', 'kadence-plugin-api-manager' ) . implode( ', ', $inactive_plugins ) . '. <a href="' . esc_url( network_admin_url( 'settings.php?page=' . esc_attr( $this->admin_page_id ) ) ) . '">' . __( 'Click here to activate.', 'kadence-plugin-api-manager' ) . '</a></p>';
						echo '</div>';
					}

				} else {
					echo '<div class="error">';
					echo '<p>' . __( 'The following plugins have not been activated: ', 'kadence-plugin-api-manager' ) . implode( ', ', $inactive_plugins ) . '. <a href="' . esc_url( admin_url( 'options-general.php?page=' . esc_attr( $this->admin_page_id ) ) ) . '">' . __( 'Click here to activate.', 'kadence-plugin-api-manager' ) . '</a></p>';
					echo '</div>';
				}
			}
		}
		/**
		 * Checks if membership activated
		 * @return bool
		 */
		public function is_membership_activated() {
			if ( is_null( self::$memberactive ) ) {
				$current_theme_name = self::$current_theme->get( 'Name' );
				$current_theme_template = self::$current_theme->get( 'Template' );
				$membership = false;
				if ( 'Pinnacle Premium' == $current_theme_name || 'pinnacle_premium' == $current_theme_template ) {
					// Check if activated
					if ( get_option( 'kt_api_manager_pinnacle_premium_activated' ) == 'Activated' ) {
						// Check if membership
						$data     = get_option( 'kt_api_manager' );
						$license  = substr( $data[ 'kt_api_key' ], 0, 3 );
						if ( 'ktm' == $license || 'ktl' == $license ) {
							$membership = true;
							self::$memberkey = $data[ 'kt_api_key' ];
							self::$memberemail = $data[ 'activation_email' ];
						}
					}
				} else if ( 'Ascend - Premium' == $current_theme_name || 'ascend_premium' == $current_theme_template ) {
					// Check if activated
					if ( get_option( 'kt_api_manager_ascend_premium_activated' ) == 'Activated' ) {
						// Check if membership
						$data     = get_option( 'kt_api_manager' );
						$license  = substr( $data[ 'kt_api_key' ], 0, 3 );
						if ( 'ktm' == $license || 'ktl' == $license ) {
							$membership = true;
							self::$memberkey = $data[ 'kt_api_key' ];
							self::$memberemail = $data[ 'activation_email' ];
						}
					}
				} else if( 'Virtue - Premium' == $current_theme_name || 'virtue_premium' == $current_theme_template ) {
					if( get_option( 'kt_api_manager_virtue_premium_activated' ) == 'Activated' ) {
						// Check if membership
						$data     = get_option( 'kt_api_manager' );
						$license  = substr( $data[ 'kt_api_key' ], 0, 3 );
						if ( 'ktm' == $license || 'ktl' == $license ) {
							$membership = true;
							self::$memberkey = $data[ 'kt_api_key' ];
							self::$memberemail = $data[ 'activation_email' ];
						}
					}
				}
				self::$memberactive = $membership;
			}
			return self::$memberactive;
		}

		/**
		 * Adds The admin Menu
		 */
		public function add_menu() {
			$page = add_options_page( $this->admin_page_name, $this->admin_page_title, 'manage_options', $this->admin_page_id, array( $this, 'config_page' ) );
			add_action( 'admin_print_styles-' . $page, array( $this, 'load_scripts' ) );
		}

		/**
		 * Adds The admin Menu
		 */
		public function add_network_menu() {
			$page = add_submenu_page( 'settings.php', $this->admin_page_name, $this->admin_page_title, 'manage_network_options', $this->admin_page_id, array( $this, 'config_page' ) );
			add_action( 'admin_print_styles-' . $page, array( $this, 'load_scripts' ) );
		}


		/**
		 * Loads Admin Scripts
		 */
		public function load_scripts() {
			wp_enqueue_style( $this->admin_page_id . '-css', plugin_dir_url(__FILE__) . '/kadence-api-manage.css', array(), $this->version, 'all' );
		}

		/**
		 * Checks if license has expired
		 */
		public function status_check( $transient_value = null ) {
			if ( ! $this->is_membership_activated() ) {
				$status = get_transient( 'kt_plugin_api_status_check' );
				if ( false === $status ) {
						foreach ( self::$products as $p_key => $p_values ) {
							if ( $this->get_setting_option( $p_values['product_key'], 'Deactivated' ) == 'Activated' ) {
								$data = $this->get_setting_option( $p_values['product_data_key'], array('api_email' => '', 'api_key' => '' ) );
								if ( isset( $data['ithemes'] ) && $data['ithemes'] ) {
									if ( empty( $data[ 'ithemes_key' ] ) ) {
										$this->uninstall( $p_key );
										$this->update_setting_option( $p_values['product_key'], 'Deactivated' );
									}
								} else {
									if ( empty( $data[ 'api_email' ] ) || empty( $data[ 'api_key' ] ) ) {
										$this->uninstall( $p_key );
										$this->update_setting_option( $p_values['product_key'], 'Deactivated' );
									}
								}
								$args = array(
									'email'         => ( isset( $data[ 'api_email' ] ) ? $data[ 'api_email' ] : '' ),
									'licence_key'   => ( isset( $data[ 'api_key' ] ) ? $data[ 'api_key' ] : '' ),
									'product_id'    => $p_values['product_id'],
									'version'       => $p_values['version'],
									'ithemes_key'   => ( isset( $data['ithemes_key'] ) ? $data['ithemes_key'] : '' ),
								);
								if ( isset( $data['ithemes'] ) && $data['ithemes'] ) {
									$status_results = json_decode( $this->status_ithemes( $args ), true );
								} else {
									$status_results = json_decode( $this->status( $args ), true );
								}
								if ( $status_results == 'failed' ) {
									// do nothing, could be timeout
								} else if ( isset( $status_results['status_check'] ) && $status_results['status_check'] == 'inactive' ) {
									$this->uninstall( $p_key );
									$this->update_setting_option( $p_values['product_key'], 'Deactivated' );
								} else if( isset( $status_results['error'] ) && isset( $status_results['code'] ) && ( '101' == $status_results['code'] || '104' == $status_results['code'] ) ) {
									$this->uninstall( $p_key );
									$this->update_setting_option( $p_values['product_key'], 'Deactivated' );
								}
							}
						}
					set_transient( 'kt_plugin_api_status_check', 1, WEEK_IN_SECONDS );
				}
			}
			return $transient_value;
		}

		/**
		 * Uninstalls a plugin activation
		 */
		public function uninstall( $p_key ) {
			global $blog_id;

			$this->license_key_deactivation( $p_key );

			// Remove options
			if ( is_multisite() ) {

				switch_to_blog( $blog_id );

				foreach ( array(
						self::$products[$p_key ]['product_data_key'],
						self::$products[$p_key ]['product_data_key'] . '_deactivation',
						) as $option) {

					delete_option( $option );

				}

				restore_current_blog();

			} else {

				foreach ( array(
					self::$products[$p_key ]['product_data_key'],
					self::$products[$p_key ]['product_data_key'] . '_deactivation',
				) as $option ) {

					$this->delete_setting_option( $option );

				}

			}

		}

		/**
		 * Deactivates the license on the API server.
		 *
		 * @return void
		 * @param string $p_key the product key.
		 */
		public function license_key_deactivation( $p_key ) {
			$data = $this->get_data_options( self::$products[ $p_key ]['product_data_key'] );
			$args = array(
				'email'       => ( isset( $data['api_email'] ) ? $data['api_email'] : '' ),
				'licence_key' => ( isset( $data['api_key'] ) ? $data['api_key'] : '' ),
				'product_id'  => self::$products[ $p_key ]['product_id'],
			);

			if ( $this->get_setting_option( self::$products[ $p_key ]['product_key'], 'Deactivated' ) == 'Activated' && $args['email'] != '' && $args['licence_key'] != '' ) {
				$this->deactivate( $args ); // reset license key activation.
			}
		}

		/**
		 * Check for external blocking contstant
		 */
		public function check_external_blocking() {
			// show notice if external requests are blocked through the WP_HTTP_BLOCK_EXTERNAL constant.
			if ( defined( 'WP_HTTP_BLOCK_EXTERNAL' ) && WP_HTTP_BLOCK_EXTERNAL === true ) {

				// check if our API endpoint is in the allowed hosts
				$host = parse_url( $this->$api_url, PHP_URL_HOST );

				if ( ! defined( 'WP_ACCESSIBLE_HOSTS' ) || stristr( WP_ACCESSIBLE_HOSTS, $host ) === false ) {
					?>
					<div class="error">
						<p><?php printf( __( '<b>Warning!</b> You\'re blocking external requests which means you won\'t be able to get certain plugin updates. Please add %s to %s.', 'kadence-plugin-api-manager' ), '<strong>' . $host . '</strong>', '<code>WP_ACCESSIBLE_HOSTS</code>' ); ?></p>
					</div>
					<?php
				}
			}
		}

		/**
		 * Get data by key
		 *
		 * @return string
		 * @param string $key the product key.
		 */
		public function get_data_options( $key ) {
			if ( ! isset( self::$settings[ $key ] ) ) {
				self::$settings[ $key ] = $this->get_setting_option( $key, array( 'api_email' => '', 'api_key' => '' ) );
			}
			return self::$settings[ $key ];
		}
		/**
		 * Build activation page.
		 */
		public function config_page() {
			$membership_active = $this->is_membership_activated();
			?>
			<div class="wrap kt_theme_license">
				<h2 class="notices"></h2>
				<div class="kt_title_area">
					<h1>
						<?php echo __( 'Kadence Plugin Activation.', 'kadence-plugin-api-manager' ); ?>
					</h1>
					<h5>
					<?php printf( __( 'Activating your license allows for plugin updates. If you need your api key you will find it by logging in to your %s Kadence WP account%s.', 'kadence-plugin-api-manager' ), '<a href="https://www.kadencewp.com/my-account/" target="_blank">', '</a>' );
					?>
					</h5>
				</div>
				<?php if ( isset( $_GET['kt_updated'] ) && 'true' === $_GET['kt_updated'] ) : ?>
					<div id="message" class="updated notice is-dismissible"><p><?php _e( 'Activated', 'kadence-plugin-api-manager' ); ?></p></div>
				<?php elseif ( isset( $_GET['kt_updated'] ) && 'false' === $_GET['kt_updated'] ) : ?>
					<div id="message" class="updated error"><p><?php _e( 'Could Not Activate', 'kadence-plugin-api-manager'); ?></p></div>
				<?php elseif ( isset( $_GET['kt_deactivate'] ) && 'true' === $_GET['kt_deactivate'] ) : ?>
					<div id="message" class="updated notice is-dismissible"><p><?php _e( 'Deactivated', 'kadence-plugin-api-manager' ); ?></p></div>
				<?php elseif ( isset( $_GET['kt_deactivate'] ) && 'false' === $_GET['kt_deactivate'] ) : ?>
					<div id="message" class="updated error"><p><?php _e( 'Could Not Deactivate, Try again.', 'kadence-plugin-api-manager'); ?></p></div>
				<?php endif; ?>

				<div class="kad-panel-contain">
					<div class="content kt-admin-clearfix">
						<div class="kt-main">
							<?php
							foreach ( self::$products as $p_key => $p_values ) {
								$data = $this->get_data_options( self::$products[$p_key]['product_data_key'] );
								$is_ithemes = ( isset( $data['ithemes'] ) && $data['ithemes'] ? true : false );
								echo '<div class="kt-product-container">';
								echo '<h4>' . esc_html( $p_values['product_name'] ) .'</h4>';
								if ( ! empty( $data['api_key'] ) || ! empty( $data['ithemes_key'] ) ) {
									echo '<h3 class="kt-primary-color">' . __( ' Status: Active', 'kadence-plugin-api-manager' ) . '</h3>';
								}
								if ( $membership_active ) {
									echo '<h2>' . __( 'Kadence Membership activated through theme', 'kadence-plugin-api-manager' ) . '</h2>';
									echo '<h3 class="kt-primary-color">' . __( ' Status: Active', 'kadence-plugin-api-manager' ) . '</h3>';
								} else {
									if ( self::$multisite && is_multisite() ) {
										echo '<form action="edit.php?action=kt_activate_update_network_options" method="post" id="kadence-activation-' . esc_attr( $p_key ) . '">';
											settings_fields( $p_values['product_data_key'] );
											do_settings_sections( $p_values['product_id'] );
											if ( empty( $data['api_key'] ) && empty( $data['ithemes_key'] ) ) {
												submit_button( __( 'Activate', 'kadence-plugin-api-manager' ), 'primary', 'btn-submit' );
											}
										echo '</form>';
									} else {
										echo '<form action="options.php" method="post" id="kadence-activation-' . esc_attr( $p_key ) . '">';
											settings_fields( $p_values['product_data_key'] );
											do_settings_sections( $p_values['product_id'] );
											if ( empty( $data['api_key'] ) && empty( $data['ithemes_key'] ) ) {
												submit_button( __( 'Activate', 'kadence-plugin-api-manager' ), 'primary', 'btn-submit' );
											}
										echo '</form>';
									}
									if ( ! empty( $data['api_key'] ) || ! empty( $data['ithemes_key'] ) ) {
										if ( self::$multisite && is_multisite() ) {
											echo '<form action="edit.php?action=kt_deactivate_update_network_options" method="post" class="kt-deactivation-form ' . ( $is_ithemes ? 'kt-deactivation-ithemes' : 'kt-deactivation-kadence' ) . '">';
												settings_fields( $p_values['product_data_key'] . '_deactivation' );
												do_settings_sections( $p_values['product_id'] . '_deactivation' );
												submit_button( __( 'Deactivate', 'kadence-plugin-api-manager' ), 'kt-deactivation-submit' );
											echo '</form>';
										} else {
											echo '<form action="options.php" method="post" class="kt-deactivation-form ' . ( $is_ithemes ? 'kt-deactivation-ithemes' : 'kt-deactivation-kadence' ) . '">';
												settings_fields( $p_values['product_data_key'] . '_deactivation' );
												do_settings_sections( $p_values['product_id'] . '_deactivation' );
												submit_button( __( 'Deactivate', 'kadence-plugin-api-manager' ), 'kt-deactivation-submit' );
											echo '</form>';
										}
									}
								}
								echo '</div>';
							}
							?>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * This function here is hooked up to a special action and necessary to process
		 * the saving of the options. This is the big difference with a normal options
		 * page.
		 */
		public function update_network_options() {
			$options_id = $_REQUEST['option_page'];

			// Make sure we are posting from our options page.
			check_admin_referer( $options_id . '-options' );

			$settings = $this->validate_options( $_POST[ $options_id ] );
			if ( isset( $settings['api_email'] ) && ! empty( $settings['api_email'] ) ) {
				$updated = 'true';
				$this->update_setting_option( $options_id, $settings );
			} else {
				$updated = 'false';
				$this->update_setting_option( $options_id, array() );
			}

			// At last we redirect back to our options page.
			wp_redirect( add_query_arg( array( 'page' => $this->admin_page_id, 'kt_updated' => $updated ), network_admin_url( 'settings.php' ) ) );
			exit;
		}
		/**
		 * This function here is hooked up to a special action and necessary to process
		 * the saving of the options. This is the big difference with a normal options
		 * page.
		 */
		public function deactivate_network_options() {

			$options_id = $_REQUEST['option_page'];

			// Make sure we are posting from our options page.
			check_admin_referer( $options_id . '-options' );

			$settings = $this->key_deactivation( $_POST[ $options_id ] );
			if ( false === $settings ) {
				$updated = 'false';
			} else {
				$updated = 'true';
				$this->update_setting_option( $options_id, array() );
			}

			// At last we redirect back to our options page.
			wp_redirect( add_query_arg( array( 'page' => $this->admin_page_id, 'kt_deactivate' => $updated ), network_admin_url( 'settings.php' ) ) );
			exit;
		}
		/**
		 * Register settings.
		 */
		public function load_settings() {
			foreach ( self::$products as $p_key => $p_values ) {
				$args = array(
					'sanitize_callback' => array( $this, 'validate_options' ),
				);
				register_setting( $p_values['product_data_key'], $p_values['product_data_key'], $args );

				// API Activation settings
				add_settings_section( $p_values['product_key'], __( 'API License Activation', 'kadence-plugin-api-manager' ), array( $this, 'api_key_text' ), $p_values['product_id'] );

				add_settings_field( 'ithemes', '', array( $this, 'api_ithemes_field' ), $p_values['product_id'], $p_values['product_key'], array( 'p_key' => $p_key ) );
				$data = $this->get_data_options( self::$products[ $p_key ]['product_data_key'] );
				$is_ithemes = ( isset( $data['ithemes'] ) && $data['ithemes'] ? true : false );
				add_settings_field( $p_values['product_key'], ( $is_ithemes ? __( 'iThemes Username', 'kadence-plugin-api-manager' ) : __( 'API License Key', 'kadence-plugin-api-manager' ) ), array( $this, 'api_key_field' ), $p_values['product_id'], $p_values['product_key'], array( 'p_key' => $p_key ) );
				if ( $is_ithemes && isset( $data['ithemes_key'] ) && ! empty( $data['ithemes_key'] ) ) {
					// test.
				} else {
					add_settings_field( 'activation_email', ( $is_ithemes ? __( 'iThemes Password', 'kadence-plugin-api-manager' ) : __( 'API License Email', 'kadence-plugin-api-manager' ) ), array( $this, 'api_email_field' ), $p_values['product_id'], $p_values['product_key'], array( 'p_key' => $p_key ) );
				}

				// Deactivation settings
				register_setting( $p_values['product_data_key'] . '_deactivation', $p_values['product_data_key'] . '_deactivation', array( $this, 'key_deactivation' ) );
				add_settings_section( 'deactivate_button', '', array( $this, 'deactivate_text' ), $p_values['product_id'] . '_deactivation' );
				add_settings_field( 'deactivate_button', ( $is_ithemes ? __( 'iThemes Password', 'kadence-plugin-api-manager' ) : __( 'Check box to deactivate API License key', 'kadence-plugin-api-manager' ) ), array( $this, 'deactivate_inputs' ), $p_values['product_id'] . '_deactivation', 'deactivate_button', array( 'p_key' => $p_key, 'class' => 'kt-plugin-deactivation' ));
			}

		}
		/**
		 * Provides text for api key section
		 */
		public function api_key_text() {

		}

		/**
		 * Outputs API License text field
		 */
		public function api_key_field( $args ) {
			$data = $this->get_data_options( self::$products[$args['p_key']]['product_data_key'] );
			$is_ithemes = ( isset( $data['ithemes'] ) && $data['ithemes'] ? true : false );
			if ( $is_ithemes ) {
				if ( isset( $data['ithemes_key'] ) && ! empty( $data['ithemes_key'] ) ) {
					$input_disabled = 'disabled';
				} else {
					$input_disabled = '';
				}
				echo '<input id="' . self::$products[$args['p_key']]['product_id'] . '_username" name="' . esc_attr( self::$products[$args['p_key']]['product_data_key'] ) . '[username]" ' . esc_attr( $input_disabled ) . ' size="25" type="text" value="'. ( isset( $data['username'] ) ? $data['username'] : '' ) . '" />';
				echo '<input id="' . self::$products[$args['p_key']]['product_id'] . '_product_id" name="' . esc_attr( self::$products[$args['p_key']]['product_data_key'] ) . '[product_id]" type="hidden" value="' . esc_attr( $args['p_key'] ) . '" />';
			} else {
				if ( isset( $data['api_key'] ) && ! empty( $data['api_key'] ) ) {
					$start = 3;
					$length = mb_strlen( $data['api_key'] ) - $start - 3;
					$mask_string = preg_replace( '/\S/', 'X', $data['api_key'] );
					$mask_string = mb_substr( $mask_string, $start, $length );
					$input_string = substr_replace( $data['api_key'], $mask_string, $start, $length );
					$input_disabled = 'disabled';
				} else {
					$input_string = '';
					$input_disabled = '';
				}

				echo '<input id="' . self::$products[$args['p_key']]['product_id'] . '_api_key" name="' . esc_attr( self::$products[$args['p_key']]['product_data_key'] ) . '[api_key]" ' . esc_attr( $input_disabled ) . ' size="25" type="text" value="' . esc_attr( $input_string ) . '" />';
				echo '<input id="' . self::$products[$args['p_key']]['product_id'] . '_product_id" name="' . esc_attr( self::$products[$args['p_key']]['product_data_key'] ) . '[product_id]" type="hidden" value="' . esc_attr( $args['p_key'] ) . '" />';

				if ( isset( $data['api_key'] ) && ! empty( $data['api_key'] ) ) {
					echo '<span class="ktap-icon-pos"><i class="dashicons dashicons-yes" style="font-size: 32px; color:green;"></i></span>';
				} else {
					echo '<span class="ktap-icon-pos"><i class="dashicons dashicons-warning" style="font-size: 32px; color:orange;"></a></span>';
				}
			}
		}

		/**
		 *  Outputs API License email text field
		 */
		public function api_email_field( $args ) {
			$data = $this->get_data_options( self::$products[$args['p_key']]['product_data_key'] );
			$is_ithemes = ( isset( $data['ithemes'] ) && $data['ithemes'] ? true : false );
			if ( $is_ithemes ) {
				if ( isset( $data['ithemes_key'] ) && ! empty( $data['ithemes_key'] ) ) {
				} else {
					echo '<input id="' . self::$products[$args['p_key']]['product_id'] . '_password" name="' . self::$products[$args['p_key']]['product_data_key'] . '[password]" size="25" type="password" value="" />';
				}
			} else {
				if ( isset( $data['api_email'] ) && ! empty( $data['api_email'] ) ) {
					$input_disabled = 'disabled';
					$input_string = $data['api_email'];
				} else {
					$input_disabled = '';
					$input_string = '';
				}
				echo '<input id="' . self::$products[$args['p_key']]['product_id'] . '_activation_email" name="' . self::$products[$args['p_key']]['product_data_key'] . '[api_email]" ' . esc_attr( $input_disabled ) . ' size="25" type="text" value="' . esc_attr( $input_string ) . '" />';
				if ( isset( $data['api_email'] ) && ! empty( $data['api_email'] ) ) {
					echo '<span class="ktap-icon-pos"><i class="dashicons dashicons-yes" style="font-size: 32px; color:green;"></i></span>';
				} else {
					echo '<span class="ktap-icon-pos"><i class="dashicons dashicons-warning" style="font-size: 32px; color:orange;"></i></a></span>';
				}
			}
		}
		/**
		 *  Outputs API License email text field
		 */
		public function api_ithemes_field( $args ) {
			$data = $this->get_data_options( self::$products[$args['p_key']]['product_data_key'] );
			$is_ithemes = ( isset( $data['ithemes'] ) && $data['ithemes'] ? true : false );
			if ( isset( $data['api_email'] ) && ! empty( $data['api_email'] ) ) {
			} else {
				if ( $args['p_key'] === 'kadence_gutenberg_pro' || $args['p_key'] === 'kadence_woo' || $args['p_key'] === 'kadence_cloud' ) {
					if ( ! empty( $data['api_key'] ) || ! empty( $data['ithemes_key'] ) ) {
						
					} else {
						echo '<div class="toggle-to-ithemes">';
						echo '<input type="checkbox" onChange="document.getElementById(\'kadence-activation-' . esc_attr( $args['p_key'] ) .'\').submit()" id="' . self::$products[$args['p_key']]['product_id'] . '_ithemes" name="' . esc_attr( self::$products[$args['p_key']]['product_data_key']) . '[ithemes]" value="on"';
						echo checked( $is_ithemes, true );
						echo '/>';
						echo '<label for="' . esc_attr( self::$products[$args['p_key']]['product_id'] ) . '_ithemes">' . __( 'Use iThemes Toolkit/Agency License', 'kadence-plugin-api-manager' ) . '</label>';
						echo '</div>';
					}
				}
			}
		}

		/**
		 *  Outputs deactivation field
		 */
		public function deactivate_inputs( $args ) {
			$data = $this->get_data_options( self::$products[$args['p_key']]['product_data_key'] );
			$is_ithemes = ( isset( $data['ithemes'] ) && $data['ithemes'] ? true : false );
			if ( $is_ithemes ) {
				if ( isset( $data['ithemes_key'] ) && ! empty( $data['ithemes_key'] ) ) {
					echo '<input id="' . self::$products[$args['p_key']]['product_id'] . '_deactivation_password" name="' . self::$products[$args['p_key']]['product_data_key'] . '_deactivation[password]" size="25" type="password" value="" />';
				}
			} else {
				echo '<input type="checkbox" id="' . self::$products[$args['p_key']]['product_id'] . '_deactivation_checkbox" name="' . esc_attr( self::$products[$args['p_key']]['product_data_key']) . '_deactivation[deactivation_checkbox]" value="on"';
				echo checked( false, 'on' );
				echo '/>';
			}
			echo '<input id="'.self::$products[$args['p_key']]['product_id'].'_product_id" name="' . esc_attr( self::$products[$args['p_key']]['product_data_key'] ). '_deactivation[product_id]" type="hidden" value="' . esc_attr( $args['p_key'] ) . '" />';
			echo '<input id="' . self::$products[$args['p_key']]['product_id'] . '_deactivation_username" name="' . esc_attr( self::$products[$args['p_key']]['product_data_key'] ) . '_deactivation[username]" size="25" type="hidden" value="'. ( isset( $data['username'] ) ? empty( $data['username'] ) : '' ) . '" />';
		}

		/**
		 * Provides text for deactivation section
		 */
		public function deactivate_text() {

		}
		/**
		 * Updates Settings.
		 *
		 * @param string $key the setting Key.
		 * @param mixed  $option the setting value.
		 */
		public function update_setting_option( $key, $option ) {
			if ( self::$multisite && is_multisite() ) {
				update_site_option( $key, $option );
			} else {
				update_option( $key, $option );
			}
		}
		/**
		 * Retrives Settings.
		 *
		 * @param string $key the setting Key.
		 * @param mixed  $default the setting default value.
		 */
		public function get_setting_option( $key, $default = null ) {
			if ( self::$multisite && is_multisite() ) {
				return get_site_option( $key, $default );
			} else {
				return get_option( $key, $default );
			}
		}
		/**
		 * Delete Settings.
		 *
		 * @param string $key the setting Key.
		 */
		public function delete_setting_option( $key ) {
			if ( self::$multisite && is_multisite() ) {
				delete_site_option( $key );
			} else {
				delete_option( $key );
			}
		}

		/**
		 *  Sanitizes and validates all input and output for Dashboard
		 *
		 * @param mixed $input the settings value.
		 */
		public function platform_validate_options( $input ) {
			//error_log( print_r( $input, true ) );
			if ( $input && isset( $input['kadence_plugin_api_manager_ithemes'] ) ) {
				if ( 'true' === $input['kadence_plugin_api_manager_ithemes'] ) {
					$this->update_setting_option( 'kadence_plugin_api_manager_ithemes', true );
				} else {
					$this->update_setting_option( 'kadence_plugin_api_manager_ithemes', false );
				}
			}
			return $input;
		}	
		/**
		 *  Sanitizes and validates all input and output for Dashboard
		 *
		 * @param mixed $input the settings value.
		 */
		public function validate_options( $input ) {
			$current_key = ( isset( $input['api_key'] ) ? trim( $input['api_key'] ) : '' );
			$current_email =  ( isset( $input['api_email'] ) ? trim( $input['api_email'] ) : '' );
			$current_product_id = ( isset( $input['product_id'] ) ? trim( $input['product_id'] ) : '' );
			$is_ithemes = ( isset( $input['ithemes'] ) ? trim( $input['ithemes'] ) : '' );
			$username = ( isset( $input['username'] ) ? trim( $input['username'] ) : '' );
			$password = ( isset( $input['password'] ) ? trim( $input['password'] ) : '' );
			$settings = array();
			if ( empty( $current_product_id ) ) {
				return false;
			}
			if ( $is_ithemes && 'on' === $is_ithemes ) {
				$settings['ithemes'] = true;
			} else {
				$settings['ithemes'] = false;
			}
			$data = $this->get_data_options( self::$products[ $current_product_id ]['product_data_key'] );
			// Should match the settings_fields() value.
			if ( $_REQUEST['option_page'] == self::$products[ $current_product_id ]['product_data_key'] ) {
				if ( ( isset( $data['ithemes'] ) && $data['ithemes'] !== $settings['ithemes'] ) || ( ! isset( $data['ithemes'] ) && $settings['ithemes'] ) ) {
					return $settings;
				}
				if ( $settings['ithemes'] ) {
					if ( empty( $username ) ) {
						add_settings_error( 'api_error_missing', 'api_username_error',  __( 'Missing Username, Please add. ', 'kadence-plugin-api-manager' ), 'error' );
						return $settings;
					}
					if ( empty( $password ) ) {
						add_settings_error( 'api_error_missing', 'api_password_error',  __( 'Missing Password, Please add. ', 'kadence-plugin-api-manager' ), 'error' );
						return $settings;
					}
				} else {
					if ( empty( $current_key ) ) {
						add_settings_error( 'api_error_missing', 'api_key_error',  __( 'Missing API Key, Please add. ', 'kadence-plugin-api-manager' ), 'error' );
						return $settings;
					}
					if ( empty( $current_email ) ) {
						add_settings_error( 'api_error_missing', 'api_email_error',  __( 'Missing API Email, Please add. ', 'kadence-plugin-api-manager' ), 'error' );
						return $settings;
					}
				}
				$args = array(
					'email'       => $current_email,
					'licence_key' => $current_key,
					'product_id'  => $current_product_id,
					'version'     => self::$products[ $current_product_id ]['version'],
					'username'    => $username,
					'password'    => $password,
				);
				if ( $settings['ithemes'] ) {
					$activate_results = json_decode( $this->activate_ithemes( $args ), true );
				} else {
					$activate_results = json_decode( $this->activate( $args ), true );
				}
				if ( isset( $activate_results['activated'] ) && $activate_results['activated'] === true ) {

					add_settings_error( 'activate_text', 'activate_msg', __( 'Plugin activated.', 'kadence-plugin-api-manager' ), 'updated' );
					$settings['api_key']    = $current_key;
					$settings['api_email']  = $current_email;
					$settings['product_id'] = $current_product_id;
					$settings['username']   = $username;
					if ( isset( $activate_results['key'] ) && ! empty( $activate_results['key'] ) ) {
						$settings['ithemes_key'] = $activate_results['key'];
					}
					$this->update_setting_option( self::$products[ $current_product_id ]['product_key'], 'Activated' );

					return $settings;
				}

				if ( $activate_results == false ) {
					add_settings_error( 'api_key_check_text', 'api_key_check_error', __( 'Connection failed to the License Key API server. Make sure your host servers php version has the curl module installed and enabled.', 'kadence-plugin-api-manager' ), 'error' );
					$this->update_setting_option( self::$products[ $current_product_id ]['product_key'], 'Deactivated' );

					return false;
				}
				if ( isset( $activate_results['code'] ) ) {

					switch ( $activate_results['code'] ) {
						case '100':
							$additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
							add_settings_error( 'api_email_text', 'api_email_error', "{$activate_results['error']}. {$additional_info}", 'error' );
						break;
						case '101':
							$additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
							add_settings_error( 'api_key_text', 'api_key_error', "{$activate_results['error']}. {$additional_info}", 'error' );
						break;
						case '102':
							$additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
							add_settings_error( 'api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error', "{$activate_results['error']}. {$additional_info}", 'error' );
						break;
						case '103':
							$additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
							add_settings_error( 'api_key_exceeded_text', 'api_key_exceeded_error', "{$activate_results['error']}. {$additional_info}", 'error' );
						break;
						case '104':
							$additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
							add_settings_error( 'api_key_not_activated_text', 'api_key_not_activated_error', "{$activate_results['error']}. {$additional_info}", 'error' );
						break;
						case '105':
							$additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
							add_settings_error( 'api_key_invalid_text', 'api_key_invalid_error', "{$activate_results['error']}. {$$additional_info}", 'error' );
						break;
						case '106':
							$additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
							add_settings_error( 'sub_not_active_text', 'sub_not_active_error', "{$activate_results['error']}. {$additional_info}", 'error' );
						break;
					}

					$this->update_setting_option( self::$products[ $current_product_id ]['product_key'], 'Deactivated' );
					return false;
				}
			}

			return $settings;
		}

		/**
		 * Deactivates the license key to allow key to be used on another blog
		 *
		 * @param array $input the input array.
		 */
		public function key_deactivation( $input ) {
			$current_product_id = trim( $input['product_id'] );
			$current_checkbox = ( isset( $input['deactivation_checkbox'] ) ? trim( $input['deactivation_checkbox'] ) : '' );
			$username = ( isset( $input['username'] ) ? trim( $input['username'] ) : '' );
			$password = ( isset( $input['password'] ) ? trim( $input['password'] ) : '' );

			// Should match the settings_fields() value
			if ( $_REQUEST['option_page'] == self::$products[$current_product_id]['product_data_key'] . '_deactivation' ) {
				$data = $this->get_data_options( self::$products[$current_product_id]['product_data_key'] );
				if ( isset( $data['ithemes'] ) && $data['ithemes'] ) {
					if ( empty( $password ) ) {
						add_settings_error( 'kadence_deactivate_needs_check', 'deactivate_msg', __( 'You must supply an iThemes membership password in order to remove licenses. ', 'kadence-plugin-api-manager' ), 'error' );
						return false;
					}
				} else if ( ! isset( $current_checkbox ) || empty( $current_checkbox ) ) {
					add_settings_error( 'kadence_deactivate_needs_check', 'deactivate_msg', __( 'Please check box to deactivate. ', 'kadence-plugin-api-manager' ), 'error' );
					return false;
				}
				$args = array(
					'email'         => ( isset( $data['api_email'] ) ? $data['api_email'] : '' ),
					'licence_key'   => ( isset( $data['api_key'] ) ? $data['api_key'] : '' ),
					'product_id'    => $current_product_id,
					'version'       => self::$products[ $current_product_id ]['version'],
					'ithemes_key'   => ( isset( $data['ithemes_key'] ) ? $data['ithemes_key'] : '' ),
					'username'      => ( isset( $data['username'] ) ? $data['username'] : $username ),
					'password'      => $password,
				);
				if ( isset( $data['ithemes'] ) && $data['ithemes'] ) {
					$activate_results = json_decode( $this->deactivate_ithemes( $args ), true );
				} else {
					$activate_results = json_decode( $this->deactivate( $args ), true );
				}

				if ( isset( $activate_results['deactivated'] ) && $activate_results['deactivated'] == true ) {

					$this->update_setting_option( self::$products[$current_product_id]['product_key'], 'Deactivated' );
					$this->update_setting_option( self::$products[$current_product_id]['product_data_key'], array('api_email' => '', 'api_key' => '' ) );
					if ( isset( self::$settings[$current_product_id]['product_data_key'] ) ) {
						self::$settings[$current_product_id]['product_data_key'] = null;
					}
					add_settings_error( 'kadence_deactivate_text', 'deactivate_msg', __( 'License deactivated.', 'kadence-plugin-api-manager' ), 'updated' );

					return array( 'product_id' => $current_product_id );
				}

				if ( $activate_results == false ) {
					add_settings_error( 'api_key_check_text', 'api_key_check_error', __( 'Connection failed to the License Key API server. Make sure your host servers php version has the curl module installed and enabled.', 'kadence-plugin-api-manager' ), 'error' );

					return array( 'product_id' => $current_product_id );
				}

				if ( isset( $activate_results['code'] ) ) {

					switch ( $activate_results['code'] ) {
						case '100':
							$additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
							add_settings_error( 'api_email_text', 'api_email_error', "{$activate_results['error']}. {$additional_info}", 'error' );
						break;
						case '101':
							$additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
							add_settings_error( 'api_key_text', 'api_key_error', "{$activate_results['error']}. {$additional_info}", 'error' );
						break;
						case '102':
							$additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
							add_settings_error( 'api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error', "{$activate_results['error']}. {$additional_info}", 'error' );
						break;
						case '103':
							$additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
							add_settings_error( 'api_key_exceeded_text', 'api_key_exceeded_error', "{$activate_results['error']}. {$additional_info}", 'error' );
						break;
						case '104':
							$additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
							add_settings_error( 'api_key_not_activated_text', 'api_key_not_activated_error', "{$activate_results['error']}. {$additional_info}", 'error' );
						break;
						case '105':
							$additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
							add_settings_error( 'api_key_invalid_text', 'api_key_invalid_error', "{$activate_results['error']}. {$additional_info}", 'error' );
						break;
						case '106':
							$additional_info = ! empty( $activate_results['additional info'] ) ? esc_attr( $activate_results['additional info'] ) : '';
							add_settings_error( 'sub_not_active_text', 'sub_not_active_error', "{$activate_results['error']}. {$additional_info}", 'error' );
						break;
					}

					$this->update_setting_option( self::$products[$current_product_id]['product_key'], 'Deactivated' );
					$this->update_setting_option( self::$products[$current_product_id]['product_data_key'], array('api_email' => '', 'api_key' => '' ) );
					if ( isset( self::$settings[$current_product_id]['product_data_key'] ) ) {
							self::$settings[$current_product_id]['product_data_key'] = null;
						}
					add_settings_error( 'kadence_deactivate_text', 'deactivate_msg', __( 'License deactivated.', 'kadence-plugin-api-manager' ), 'updated' );

					return array( 'product_id' => $current_product_id );
				}
			}

			return false;

		}

		/*
		 * API URl
		 */
		public function create_software_api_url( $args ) {

			$api_url = add_query_arg( $args, $this->api_url );

			return $api_url;
		}
		/**
		 * API Activation
		 *
		 * @param array $args the product args.
		 */
		public function activate_ithemes( $args ) {
			if ( 'kadence_gutenberg_pro' !== $args['product_id'] && 'kadence_woo' !== $args['product_id'] && 'kadence_cloud' !== $args['product_id'] ) {
				// Not setup yet.
				return false;
			}
			if ( 'kadence_cloud' === $args['product_id'] ) {
				$product = 'kadence-cloud';
			} elseif ( 'kadence_woo' === $args['product_id'] ) {
				$product = 'kadence-woo-extras';
			} else {
				$product = 'kadence-blocks-pro';
			}
			// Ithemes Add.
			if ( is_callable( 'network_home_url' ) ) {
				$site_url = network_home_url( '', 'http' );
			} else {
				$site_url = get_bloginfo( 'url' );
			}
			$site_url = preg_replace( '/^https/', 'http', $site_url );
			$site_url = preg_replace( '|/$|', '', $site_url );
			$username = $args['username'];
			$password = $args['password'];
			$query = array(
				'user' => $username,
			);
			$default_query = array(
				'wp'           => $GLOBALS['wp_version'],
				'site'         => $site_url,
				'timestamp'    => time(),
				'auth_version' => '2',
			);
			$data = array(
				'auth_token' => $this->get_password_hash( $username, $password, $site_url ),
				'packages'   => array(
					$product => array(
						'ver' => $args['version'],
						'key' => '',
						'active' => false,
					),
				),
			);
			if ( isset( $data['auth_token'] ) ) {
				$data['iterations'] = 8;
			}
			$query = array_merge( $default_query, $query );
			$post_data = array(
				'request' => json_encode( $data ),
			);
			$remote_post_args = array(
				'timeout' => 10,
				'body'    => $post_data,
			);
			$request = "/package-activate/?" . http_build_query( $query, '', '&' );
			$response = wp_remote_post( 'https://api.ithemes.com/updater' . $request, $remote_post_args );
			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) {
				return false;
			}
			$response = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( ! isset( $response['packages'] ) ) {
				return false;
			}
			if ( empty( $response['packages'] ) ) {
				return false;
			}
			if ( ! is_array( $response['packages'] ) ) {
				return false;
			}
			$success = array();
			$data_key = '';
			foreach ( $response['packages'] as $package => $data ) {
				if ( preg_match( '/ \|\|\| \d+$/', $package ) ) {
					continue;
				}
				if ( ! empty( $data['key'] ) && 'active' === $data['status'] ) {
					$success[] = 'kadence-blocks-pro';
					$data_key = $data['key'];
				}
			}
			if ( ! empty( $success ) ) {
				return '{"activated":true,"key":"' . $data_key . '"}';
			} else {
				return false;
			}
		}

		/**
		 * API Activation
		 *
		 * @param array $args the product args.
		 */
		public function deactivate_ithemes( $args ) {
			if ( 'kadence_gutenberg_pro' !== $args['product_id'] && 'kadence_woo' !== $args['product_id'] && 'kadence_cloud' !== $args['product_id'] ) {
				// Not setup yet.
				return false;
			}
			if ( 'kadence_cloud' === $args['product_id'] ) {
				$product = 'kadence-cloud';
			} elseif ( 'kadence_woo' === $args['product_id'] ) {
				$product = 'kadence-woo-extras';
			} else {
				$product = 'kadence-blocks-pro';
			}
			// Ithemes Add.
			if ( is_callable( 'network_home_url' ) ) {
				$site_url = network_home_url( '', 'http' );
			} else {
				$site_url = get_bloginfo( 'url' );
			}
			$site_url = preg_replace( '/^https/', 'http', $site_url );
			$site_url = preg_replace( '|/$|', '', $site_url );
			$username = $args['username'];
			$password = $args['password'];
			$query = array(
				'user' => $username,
			);
			$default_query = array(
				'wp'           => $GLOBALS['wp_version'],
				'site'         => $site_url,
				'timestamp'    => time(),
				'auth_version' => '2',
			);
			$data = array(
				'auth_token' => $this->get_password_hash( $username, $password, $site_url ),
				'packages'   => array(
					$product => array(
						'ver' => $args['version'],
						'key' => $args['ithemes_key'],
						'active' => true,
					),
				),
			);
			if ( isset( $data['auth_token'] ) ) {
				$data['iterations'] = 8;
			}
			$query = array_merge( $default_query, $query );
			$post_data = array(
				'request' => json_encode( $data ),
			);
			$remote_post_args = array(
				'timeout' => 10,
				'body'    => $post_data,
			);
			$request = "/package-deactivate/?" . http_build_query( $query, '', '&' );
			$response = wp_remote_post( 'https://api.ithemes.com/updater' . $request, $remote_post_args );
			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) {
				return false;
			}
			$response = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( ! isset( $response['packages'] ) ) {
				return false;
			}
			if ( empty( $response['packages'] ) ) {
				return false;
			}
			if ( ! is_array( $response['packages'] ) ) {
				return false;
			}
			$success = array();
			foreach ( $response['packages'] as $package => $data ) {
				if ( preg_match( '/ \|\|\| \d+$/', $package ) ) {
					continue;
				}
				if ( isset( $data['status'] ) && 'inactive' === $data['status'] ) {
					$success[] = 'kadence-blocks-pro';
				}
			}
			if ( ! empty( $success ) ) {
				return '{"deactivated":true}';
			} else {
				return false;
			}
		}
		/**
		 * API Activation
		 *
		 * @param array $args the product args.
		 */
		public function activate( $args ) {
			$license = substr( $args['licence_key'], 0, 3 );
			if ( 'ktm' == $license ) {
				$args['product_id'] = 'ktm';
			} elseif ( 'ktl' === $license ) {
				$args['product_id'] = 'ktl';
			}
			if ( 'kadence_gutenberg_pro' === $args['product_id'] ) {
				if ( 'vps' == $license ) {
					$args['product_id'] = 'vps';
				} else if ( 'pps' == $license ) {
					$args['product_id'] = 'pps';
				} else if ( 'aps' == $license ) {
					$args['product_id'] = 'aps';
				}
			}
			$defaults = array(
				'wc-api'           => 'am-software-api',
				'request'          => 'activation',
				'instance'         => self::$instance_id,
				'platform'         => self::$domain,
				'software_version' => $this->version,
			);
			$args = wp_parse_args( $defaults, $args );

			$target_url = esc_url_raw( $this->create_software_api_url( $args ) );

			$request = wp_safe_remote_get( $target_url, array( 'sslverify'  => false ) );

			if ( is_wp_error( $request ) ) {
				// Lets try api address for some server types.
				$new_target_url = esc_url_raw( add_query_arg( $args, $this->fallback_api_url ) );

				$request = wp_safe_remote_get( $new_target_url, array( 'sslverify'  => false ) );

				if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
					return false;
				}

			} else if ( wp_remote_retrieve_response_code( $request ) != 200 ) {
				return false;
			}

			$response = wp_remote_retrieve_body( $request );

			return $response;
		}
		public function get_password_hash( $username, $password, $site_url ) {
			$password = $this->get_pbkdf2( $password, $username );
			$salted_password = $password . $username . $site_url . $GLOBALS['wp_version'];
			$salted_password = substr( $salted_password, 0, max( strlen( $password ), 512 ) );
			$auth_token = wp_hash_password( $salted_password );
            return $auth_token;
		}
		public function get_salt( $username ) {
			return strtolower( trim( $username ) ) . 'wdHVwU&HcYcWnllo%kTUUnxpScy4%ICM29';
		}
		public function get_pbkdf2( $password, $username ) {
			
			return $this->pbkdf2(
				'sha256', 
				$password, 
				$this->get_salt( $username ), 
				131072, 
				64 / 2, 
				false
			);
			
		}
        
		//-----------------------------------------------------------------------------        
		
		/*
			* PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
			* $algorithm - The hash algorithm to use. Recommended: SHA256
			* $password - The password.
			* $salt - A salt that is unique to the password.
			* $count - Iteration count. Higher is better, but slower. Recommended: At least 1000.
			* $key_length - The length of the derived key in bytes.
			* $raw_output - If true, the key is returned in raw binary format. Hex encoded otherwise.
			* Returns: A $key_length-byte key derived from the password and salt.
			*
			* Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
			*
			* This implementation of PBKDF2 was originally created by https://defuse.ca
			* With improvements by http://www.variations-of-shadow.com
			*/
		private function pbkdf2( $algorithm, $password, $salt, $count, $key_length, $raw_output = false ) {
			
			$algorithm = strtolower($algorithm);
			
			if(!in_array($algorithm, hash_algos(), true))
				trigger_error('PBKDF2 ERROR: Invalid hash algorithm.', E_USER_ERROR);
			
			if($count <= 0 || $key_length <= 0)
				trigger_error('PBKDF2 ERROR: Invalid parameters.', E_USER_ERROR);
					
		
			$hash_length = strlen(hash($algorithm, '', true));
			$block_count = ceil($key_length / $hash_length);
		
			$output = '';
			
			for($i = 1; $i <= $block_count; $i++) 
			{
				
				// $i encoded as 4 bytes, big endian.
				$last = $salt . pack("N", $i);
				
				// first iteration
				$last = $xorsum = hash_hmac($algorithm, $last, $password, true);
				
				// perform the other $count - 1 iterations
				for ($j = 1; $j < $count; $j++) 
				{
					$xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
				}
				
				$output .= $xorsum;
				
			}
		
			if($raw_output)
				return substr($output, 0, $key_length);
			else
				return bin2hex(substr($output, 0, $key_length));
				
		}

		/**
		 * Deactivates software
		 *
		 * @param array $args the product args.
		 */
		public function deactivate( $args ) {
			$license = substr( $args['licence_key'], 0, 3 );
			if ( 'ktm' == $license ) {
				$args['product_id'] = 'ktm';
			} elseif ( 'ktl' === $license ) {
				$args['product_id'] = 'ktl';
			}
			if ( 'kadence_gutenberg_pro' === $args['product_id'] ) {
				if ( 'vps' == $license ) {
					$args['product_id'] = 'vps';
				} else if ( 'pps' == $license ) {
					$args['product_id'] = 'pps';
				} else if ( 'aps' == $license ) {
					$args['product_id'] = 'aps';
				}
			}
			$defaults = array(
				'wc-api'   => 'am-software-api',
				'request'  => 'deactivation',
				'instance' => self::$instance_id,
				'platform' => self::$domain,
			);

			$args = wp_parse_args( $defaults, $args );

			$target_url = esc_url_raw( $this->create_software_api_url( $args ) );

			$request = wp_safe_remote_get( $target_url, array( 'sslverify'  => false ) );

			if ( is_wp_error( $request ) ) {
				// Lets try api address for some server types.
				$new_target_url = esc_url_raw( add_query_arg( $args, $this->fallback_api_url ) );

				$request = wp_safe_remote_get( $new_target_url, array( 'sslverify'  => false ) );

				if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
					return false;
				}
			} else if ( wp_remote_retrieve_response_code( $request ) != 200 ) {
				return false;
			}

			$response = wp_remote_retrieve_body( $request );

			return $response;
		}

		/**
		 * Checks if the software is activated or deactivated
		 *
		 * @param array $args the product args.
		 */
		public function status_ithemes( $args ) {
			if ( 'kadence_gutenberg_pro' !== $args['product_id'] && 'kadence_woo' !== $args['product_id'] && 'kadence_cloud' !== $args['product_id'] ) {
				// Not setup yet.
				return false;
			}
			if ( 'kadence_cloud' === $args['product_id'] ) {
				$product = 'kadence-cloud';
			} elseif ( 'kadence_woo' === $args['product_id'] ) {
				$product = 'kadence-woo-extras';
			} else {
				$product = 'kadence-blocks-pro';
			}
			// Ithemes Add.
			if ( is_callable( 'network_home_url' ) ) {
				$site_url = network_home_url( '', 'http' );
			} else {
				$site_url = get_bloginfo( 'url' );
			}
			$site_url = preg_replace( '/^https/', 'http', $site_url );
			$site_url = preg_replace( '|/$|', '', $site_url );
			$query = array();
			$default_query = array(
				'wp'           => $GLOBALS['wp_version'],
				'site'         => $site_url,
				'timestamp'    => time(),
				'auth_version' => '2',
			);
			$data = array(
				'packages'   => array(
					$product => array(
						'ver' => $args['version'],
						'key' => $args['ithemes_key'],
						'active' => true,
					),
				),
			);
			$query = array_merge( $default_query, $query );
			$post_data = array(
				'request' => json_encode( $data ),
			);
			$remote_post_args = array(
				'timeout' => 10,
				'body'    => $post_data,
			);
			$request = "/package-details/?" . http_build_query( $query, '', '&' );
			$response = wp_remote_post( 'https://api.ithemes.com/updater' . $request, $remote_post_args );
			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) {
				return false;
			}
			$response = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( ! isset( $response['packages'] ) ) {
				return '{"status_check":"inactive"}';
			}
			if ( empty( $response['packages'] ) ) {
				return '{"status_check":"inactive"}';
			}
			if ( ! is_array( $response['packages'] ) ) {
				return '{"status_check":"inactive"}';
			}
			$success = array();
			foreach ( $response['packages'] as $package => $data ) {
				if ( preg_match( '/ \|\|\| \d+$/', $package ) ) {
					continue;
				}
				if ( isset( $data['status'] ) && 'active' === $data['status'] ) {
					$success[] = 'kadence-blocks-pro';
				}
			}
			if ( ! empty( $success ) ) {
				return '{"status_check":"active"}';
			} else {
				return '{"status_check":"inactive"}';
			}
		}
		/**
		 * Checks if the software is activated or deactivated
		 *
		 * @param array $args the product args.
		 */
		public function status( $args ) {
			$license = substr( $args['licence_key'], 0, 3 );
			if ( 'ktm' == $license ) {
				$args['product_id'] = 'ktm';
			} elseif ( 'ktl' === $license ) {
				$args['product_id'] = 'ktl';
			}
			if ( 'kadence_gutenberg_pro' === $args['product_id'] ) {
				if ( 'vps' == $license ) {
					$args['product_id'] = 'vps';
				} else if ( 'pps' == $license ) {
					$args['product_id'] = 'pps';
				} else if ( 'aps' == $license ) {
					$args['product_id'] = 'aps';
				}
			}
			$defaults = array(
				'wc-api'   => 'am-software-api',
				'request'  => 'status',
				'instance' => self::$instance_id,
				'platform' => self::$domain,
			);

			$args = wp_parse_args( $defaults, $args );

			$target_url = esc_url_raw( $this->create_software_api_url( $args ) );

			$request = wp_safe_remote_get( $target_url, array( 'sslverify'  => false ) );

			if ( is_wp_error( $request ) ) {
				// Lets try api address for some server types.
				$new_target_url = esc_url_raw( add_query_arg( $args, $this->fallback_api_url ) );

				$request = wp_safe_remote_get( $new_target_url, array( 'sslverify'  => false ) );

				if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
					return false;
				}
			} else if ( wp_remote_retrieve_response_code( $request ) != 200 ) {
				return false;
			}

			$response = wp_remote_retrieve_body( $request );

			return $response;
		}

	}
}
