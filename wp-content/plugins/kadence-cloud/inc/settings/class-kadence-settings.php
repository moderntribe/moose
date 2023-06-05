<?php
/**
 * Kadence Settings Class
 *
 * @package Kadence Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Kadence_Settings', false ) ) {

	/**
	 * Main Kadence_Settings class
	 */
	class Kadence_Settings {
		/**
		 * Global arguments array.
		 *
		 * @var array|mixed|void
		 */
		public $args = array();

		/**
		 * Panel sections array.
		 *
		 * @var array|mixed|void
		 */
		public $sections = array();

		/**
		 * Project opt_name
		 *
		 * @var mixed|string
		 */
		public $opt_name = '';

		/**
		 * Creates page's hook suffix.
		 *
		 * @var false|string
		 * @access private
		 */
		private $page = '';

		/**
		 * Holds Changelog.
		 *
		 * @var false|string
		 * @access private
		 */
		private $changelog = '';

		/**
		 * Holds Pro Changelog.
		 *
		 * @var false|string
		 * @access private
		 */
		private $pro_changelog = '';
		/**
		 * Cloning is forbidden.
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'kadence-cloud' ), '1.0' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'kadence-cloud' ), '1.0' );
		}

		/**
		 * Class Constructor. Defines the args for the settings class
		 *
		 * @param       array $sections Panel sections.
		 * @param       array $args     Class constructor arguments.
		 */
		public function __construct( $sections = array(), $args = array() ) {
			if ( empty( $args ) || ! isset( $args['opt_name'] ) || ( isset( $args['opt_name'] ) && empty( $args['opt_name'] ) ) ) {
				return;
			}
			$default = array(
				'opt_name'                         => '',
				'menu_icon'                        => '',
				'menu_title'                       => '',
				'page_title'                       => '',
				'page_slug'                        => '',
				'page_permissions'                 => 'manage_options',
				'menu_type'                        => 'menu',
				'page_parent'                      => 'themes.php',
				'page_priority'                    => null,
				'footer_credit'                    => '',
				'class'                            => '',
				'admin_bar'                        => true,
				'admin_bar_priority'               => 999,
				'admin_bar_icon'                   => '',
				'show_import_export'               => false, // Needs to be built.
				'sidebar'                          => array(),
				'tabs'                             => array(),
				'started'                          => array(),
				'changelog'                        => '',
				'pro_changelog'                    => '',
				'network_admin'                    => false, // Needs to be built.
				'database'                         => '',
			);

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			$default    = apply_filters( 'kadence_settings_args_defaults', $default );
			$args       = wp_parse_args( $args, $default );
			$this->args = $args;
			$this->sections = $sections;
			// Add Page.
			add_action( 'admin_menu', array( $this, 'options_page' ), 20 );
			// Add a network menu.
			if ( 'network' === $this->args['database'] && $this->args['network_admin'] ) {
				add_action( 'network_admin_menu', array( $this, 'options_page' ) );
			}
		}
		/**
		 * Options Page Function, creates main options page.
		 *
		 * @access      public
		 * @return void
		 */
		public function options_page() {
			// phpcs:ignore Generic.CodeAnalysis.EmptyStatement
			if ( 'hidden' === $this->args['menu_type'] ) {
				// No menu to add!
			} elseif ( 'submenu' === $this->args['menu_type'] ) {
				$this->submenu();
			} else {
				// Theme-Check notice is displayed for WP.org theme devs, informing them to NOT use this.
				$this->page = call_user_func(
					'add_menu_page',
					$this->args['page_title'],
					$this->args['menu_title'],
					$this->args['page_permissions'],
					$this->args['page_slug'],
					array(
						$this,
						'generate_panel',
					),
					$this->args['menu_icon'],
					$this->args['page_priority']
				);
			}
			add_action( "load-{$this->page}", array( $this, 'load_page' ) );
		}
		/**
		 * Class Add Sub Menu Function, creates options submenu in WordPress admin area.
		 *
		 * @since       3.1.9
		 * @access      private
		 * @return      void
		 */
		private function submenu() {
			global $submenu;

			$page_parent      = $this->args['page_parent'];
			$page_title       = $this->args['page_title'];
			$menu_title       = $this->args['menu_title'];
			$page_permissions = $this->args['page_permissions'];
			$page_slug        = $this->args['page_slug'];

			$test = array(
				'index.php'               => 'dashboard',
				'edit.php'                => 'posts',
				'upload.php'              => 'media',
				'link-manager.php'        => 'links',
				'edit.php?post_type=page' => 'pages',
				'edit-comments.php'       => 'comments',
				'themes.php'              => 'theme',
				'plugins.php'             => 'plugins',
				'users.php'               => 'users',
				'tools.php'               => 'management',
				'options-general.php'     => 'options',
			);

			if ( isset( $test[ $page_parent ] ) ) {
				$function   = 'add_' . $test[ $page_parent ] . '_page';
				$this->page = $function(
					$page_title,
					$menu_title,
					$page_permissions,
					$page_slug,
					array( $this, 'generate_panel' )
				);
			} else {
				// Network settings and Post type menus. These do not have
				// wrappers and need to be appened to using add_submenu_page.
				// Okay, since we've left the post type menu appending
				// as default, we need to validate it, so anything that
				// isn't post_type=<post_type> doesn't get through and mess
				// things up.
				$add_menu = false;
				if ( 'settings.php' !== $page_parent ) {
					// Establish the needle.
					$needle = '?post_type=';

					// Check if it exists in the page_parent (how I miss instr).
					$needle_pos = strrpos( $page_parent, $needle );

					// It's there, so...
					if ( $needle_pos > 0 ) {

						// Get the post type.
						$post_type = substr( $page_parent, $needle_pos + strlen( $needle ) );

						// Ensure it exists.
						if ( post_type_exists( $post_type ) ) {
							// Set flag to add the menu page.
							$add_menu = true;
						}
						// custom menu.
					} elseif ( isset( $submenu[ $this->args['page_parent'] ] ) ) {
						$add_menu = true;
					} else {
						global $menu;

						foreach ( $menu as $menupriority => $menuitem ) {
							$needle_menu_slug = isset( $menuitem ) ? $menuitem[2] : false;
							if ( false !== $needle_menu_slug ) {

								// check if the current needle menu equals page_parent.
								if ( 0 === strcasecmp( $needle_menu_slug, $page_parent ) ) {

									// found an empty parent menu.
									$add_menu = true;
								}
							}
						}
					}
				} else {
					// The page_parent was settings.php, so set menu add
					// flag to true.
					$add_menu = true;
				}
				// Add the submenu if it's permitted.
				if ( true === $add_menu ) {
					$this->page = call_user_func(
						'add_submenu_page',
						$page_parent,
						$page_title,
						$menu_title,
						$page_permissions,
						$page_slug,
						array( $this, 'generate_panel' )
					);
				}
			}
		}
		/**
		 * Output the option panel.
		 */
		public function generate_panel() {
			?>
			<div class="kadence_settings_dash_head">
				<div class="kadence_settings_dash_head_container">
					<div class="kadence_settings_dash_logo">
						<img src="<?php echo esc_url( $this->args['logo'] ); ?>">
					</div>
					<div class="kadence_settings_dash_title">
						<h1><?php echo esc_html( $this->args['page_title'] ); ?></h1>
					</div>
					<div class="kadence_settings_dash_version">
						<span>
							<?php echo esc_html( $this->args['version'] ); ?>
						</span>
					</div>
				</div>
			</div>
			<div class="wrap kadence_settings_dash">
				<div class="kadence_settings_dashboard">
					<h2 class="notices" style="display:none;"></h2>
					<?php settings_errors(); ?>
					<div class="page-grid">
						<div class="kadence_settings_dashboard_main">
						</div>
						<div class="side-panel">
							<?php do_action( 'kadence_settings_dash_side_panel', $this->args['opt_name'] ); ?>
							<?php
							if ( $this->args['sidebar'] && is_array( $this->args['sidebar'] ) ) {
								foreach ( $this->args['sidebar'] as $key => $side_item ) {
									?>
									<div class="community-section sidebar-section components-panel">
									<div class="components-panel__body is-opened">
									<h2><?php echo esc_html( $side_item['title'] ); ?></h2>
									<p><?php echo esc_html( $side_item['description'] ); ?></p>
									<a href="<?php echo esc_url( $side_item['link'] ); ?>" target="_blank" class="sidebar-link"><?php echo esc_html( $side_item['link_text'] ); ?></a>
									</div>
									</div>
									<?php
								}
							}
							?>
							<?php do_action( 'kadence_settings_dash_side_panel_after', $this->args['opt_name'] ); ?>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		/**
		 * Load Page Scripts
		 */
		public function load_page() {
			// Do admin head action for this page.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_head' ) );
		}
		/**
		 * Load Page Scripts
		 */
		public function admin_head() {
			wp_enqueue_style( 'kadence-settings', KADENCE_SETTINGS_URL . 'build/settings.css', array( 'wp-components' ), KADENCE_SETTINGS_VERSION );
			wp_enqueue_script( 'kadence-settings', KADENCE_SETTINGS_URL . 'build/settings.js', array( 'jquery', 'wp-i18n', 'wp-element', 'wp-data', 'wp-components', 'wp-api', 'wp-hooks', 'wp-edit-post', 'lodash', 'wp-block-library', 'wp-block-editor', 'wp-editor', 'wp-polyfill', 'wp-primitives' ), KADENCE_SETTINGS_VERSION, true );
			if ( 'network' === $this->args['database'] ) {
				$values = get_site_option( $this->args['opt_name'] );
			} else {
				$values = get_option( $this->args['opt_name'] );
			}
			wp_localize_script(
				'kadence-settings',
				'kadenceSettingsParams',
				array(
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
					'ajax_nonce' => wp_create_nonce( 'kadence-settings-ajax-verification' ),
					'settings'   => is_array( $values ) ? wp_json_encode( $values ) : $values,
					'sections'   => wp_json_encode( $this->sections ),
					'args'       => wp_json_encode( $this->args ),
					'tabs'       => wp_json_encode( $this->args['tabs'] ),
					'opt_name'   => $this->args['opt_name'],
					'changelog'    => $this->get_changelog( $this->args['changelog'] ),
					'proChangelog' => $this->get_changelog( $this->args['pro_changelog'] ),
					'started' => wp_json_encode( $this->args['started'] ),
					'taxonomies' => $this->get_taxonomies(),
					'themeColors' => get_theme_support( 'editor-color-palette' ),
				)
			);
			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations( 'kadence-settings', 'kadence-settings' );
			}
		}
		/**
		 * Setup the post type taxonomies for post blocks.
		 *
		 * @return array
		 */
		public function get_taxonomies() {
			$post_types = $this->filtered_get_post_types();
			$output = array();
			foreach ( $post_types as $key => $post_type ) {
				$taxonomies = get_object_taxonomies( $post_type['value'], 'objects' );
				$taxs = array();
				foreach ( $taxonomies as $term_slug => $term ) {
					if ( ! $term->public || ! $term->show_ui ) {
						continue;
					}
					$taxs[ $term_slug ] = $term;
					$terms = get_terms( $term_slug );
					$term_items = array();
					if ( ! empty( $terms ) ) {
						foreach ( $terms as $term_key => $term_item ) {
							$term_items[] = array(
								'value' => $term_item->term_id,
								'label' => $term_item->name,
							);
						}
						$output[ $post_type['value'] ]['terms'][ $term_slug ] = $term_items;
					}
				}
				$output[ $post_type['value'] ]['taxonomy'] = $taxs;
			}
			return apply_filters( 'kadence_settings_taxonomies', $output );
		}
		/**
		 * Setup the post type options for post blocks.
		 *
		 * @return array
		 */
		public function filtered_get_post_types() {
			$args = array(
				'public'       => true,
				'show_in_rest' => true,
			);
			$post_types = get_post_types( $args, 'objects' );
			$output = array();
			foreach ( $post_types as $post_type ) {
				// if ( 'product' == $post_type->name || 'attachment' == $post_type->name ) {
				// 	continue;
				// }
				if ( 'attachment' == $post_type->name ) {
					continue;
				}
				$output[] = array(
					'value' => $post_type->name,
					'label' => $post_type->label,
				);
			}
			return apply_filters( 'kadence_settings_post_types', $output );
		}
		/**
		 * Get Changelog
		 *
		 * @param $changelog_path the path to the change log.
		 */
		public function get_changelog( $changelog_path ) {
			$changelog = array();
			if ( empty( $changelog_path ) ) {
				return $changelog;
			}
			if ( ! is_file( $changelog_path ) ) {
				return $changelog;
			}
			global $wp_filesystem;
			if ( ! is_object( $wp_filesystem ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();
			}

			$changelog_string = $wp_filesystem->get_contents( $changelog_path );
			if ( is_wp_error( $changelog_string ) ) {
				return $changelog;
			}
			$changelog = explode( PHP_EOL, $changelog_string );
			$releases  = [];
			foreach ( $changelog as $changelog_line ) {
				if ( empty( $changelog_line ) ) {
					continue;
				}
				if ( substr( ltrim( $changelog_line ), 0, 2 ) === '==' ) {
					if ( isset( $release ) ) {
						$releases[] = $release;
					}
					$changelog_line = trim( str_replace( '=', '', $changelog_line ) );
					$release = array(
						'head'    => $changelog_line,
					);
				} else {
					if ( preg_match( '/[*|-]?\s?(\[fix]|\[Fix]|fix|Fix)[:]?\s?\b/', $changelog_line ) ) {
						//$changelog_line     = preg_replace( '/[*|-]?\s?(\[fix]|\[Fix]|fix|Fix)[:]?\s?\b/', '', $changelog_line );
						$changelog_line = trim( str_replace( [ '*', '-' ], '', $changelog_line ) );
						$release['fix'][] = $changelog_line;
						continue;
					}

					if ( preg_match( '/[*|-]?\s?(\[add]|\[Add]|add|Add)[:]?\s?\b/', $changelog_line ) ) {
						//$changelog_line        = preg_replace( '/[*|-]?\s?(\[add]|\[Add]|add|Add)[:]?\s?\b/', '', $changelog_line );
						$changelog_line = trim( str_replace( [ '*', '-' ], '', $changelog_line ) );
						$release['add'][] = $changelog_line;
						continue;
					}
					$changelog_line = trim( str_replace( [ '*', '-' ], '', $changelog_line ) );
					$release['update'][] = $changelog_line;
				}
			}
			return $releases;
		}
	}
}

