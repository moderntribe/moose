<?php
/**
 * Enqueue JS for Custom Icons and build admin for icons.
 *
 * @since   1.4.0
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue JS for Custom Icons and build admin for icons.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Custom_Icons {
	/**
	 * Instance of this class
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Icon post type query.
	 *
	 * @var $icon_query
	 */
	public static $icon_query = null;

	/**
	 * Icon post type query.
	 *
	 * @var $icon_array
	 */
	public static $icon_array = null;

	/**
	 * Icons in svg array.
	 *
	 * @var $icon_array
	 */
	public static $svg_icon_array = null;

	/**
	 * Icon post type query.
	 *
	 * @var $icon_names
	 */
	public static $icon_names = null;

	/**
	 * Show only one custom set.
	 *
	 * @var $only_custom
	 */
	public static $only_custom = false;

	/**
	 * Icon file contents.
	 *
	 * @var $icon_contents
	 */
	public static $icon_contents = null;

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
		add_action( 'init', array( $this, 'icon_post_type' ) );
		add_action( 'current_screen', array( $this, 'add_block_icons' ), 10 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'add_editor_block_icons' ), 1 );
	}
	/**
	 * On init
	 */
	public function on_init() {
		if ( is_admin() ) {
			add_action( 'add_meta_boxes_kb_icon', array( $this, 'add_meta_box' ) );

			add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 20 );

			add_action( 'admin_enqueue_scripts', array( $this, 'metabox_admin_scripts' ), 10, 1 );
			add_action( 'save_post_kb_icon', array( $this, 'save_post_meta' ), 10, 3 );
			// add_action( 'manage_kb_icon_posts_custom_column', array( $this, 'render_columns' ), 10, 2 );
			// add_filter( 'manage_kb_icon_posts_columns', array( $this, 'manage_columns' ), 100 );
		}

		add_filter( 'upload_mimes', array( $this, 'extra_mime_types' ) );

		add_filter( 'wp_check_filetype_and_ext', array( $this, 'files_ext_json' ), 10, 4 );

		// Filter in icons to the front end.
		add_filter( 'kadence_svg_icons', array( $this, 'add_custom_icons' ) );
	}
	/**
	 * Add Icon manager link to admin menu
	 *
	 * @param array $icons the block icons.
	 */
	public function add_custom_icons( $all_icons ) {
		$icons = $this->kadence_icon_query();
		if ( $icons ) {
			if ( is_null( self::$svg_icon_array ) ) {
				$svg_icon_array = array();
				if ( $icons ) {
					foreach ( $icons as $icon ) {
						$type      = get_post_meta( $icon->ID, '_kb_icon_type', true );
						$show_only = get_post_meta( $icon->ID, '_kb_icon_show_only', true );

						if ( $type === 'json' ) {
							$selection = get_post_meta( $icon->ID, '_kb_icon_json', true );
						} else {
							$type = 'file';
							$selection = get_post_meta( $icon->ID, '_kb_icon_selection', true );
						}

						if ( ! empty( $selection ) ) {
							$import = '';
							if ( $type === 'file' ) {
								$path = get_attached_file( $selection );
								if ( ! empty( $path ) && file_exists( $path ) ) {
									$import = json_decode( file_get_contents( $path ) );
								}
							} else {
								$import = json_decode( $selection );
							}
							if ( $import && isset( $import->IcoMoonType ) && isset( $import->preferences ) && isset( $import->preferences->imagePref ) && isset( $import->preferences->imagePref->prefix ) ) {
								$prefix = $import->preferences->imagePref->prefix;
								foreach ( $import->icons as $icon_object ) {
									$first  = '0';
									$second = '0';
									if ( isset( $icon_object->icon->width ) ) {
										if ( $icon_object->icon->width > $import->height ) {
											$diff = $icon_object->icon->width - $import->height;
											$second = - floor( $diff / 2 );
										} elseif ( $icon_object->icon->width < $import->height ) {
											$diff = $import->height - $icon_object->icon->width;
											$first = - floor( $diff / 2 );
										}
									}
									$svg_icon_array[ $prefix . $icon_object->properties->name ] = array(
										'vB' => $first . ' ' . $second . ' ' . $import->height . ' ' . ( isset( $icon_object->icon->width ) ? $icon_object->icon->width : $import->height ),
										'cD' => array(),
									);
									foreach ( $icon_object->icon->paths as $key => $value ) {
										$svg_icon_array[ $prefix . $icon_object->properties->name ]['cD'][] = array(
											'nE'  => 'path',
											'aBs' => array( 'd' => $value ),
										);
									}
								}
							}
						}
					}
				}
				self::$svg_icon_array = $svg_icon_array;
			}
			$all_icons = array_merge( self::$svg_icon_array, $all_icons );
		}
		return $all_icons;
	}
	/**
	 * Add Icon manager link to admin menu
	 */
	public function register_admin_menu() {
		$menu_title = _x( 'Custom Icons', 'Kadence Blocks', 'kadence-blocks-pro' );
		add_submenu_page(
			'kadence-blocks',
			$menu_title,
			$menu_title,
			'manage_options',
			'edit.php?post_type=kb_icon'
		);
	}
	/**
	 * Load the media uploader and our meta upload file.
	 */
	public function metabox_admin_scripts( $hook ) {
		global $typenow;
		if ( 'kb_icon' === $typenow ) {
			wp_enqueue_media();
			// Registers and enqueues the required javascript.
			wp_register_script(
				'kb-custom-icons-meta',
				KBP_URL . 'dist/kb-custom-icons-admin.js',
				array( 'jquery' ),
				KBP_VERSION,
				true
			);
			wp_localize_script(
				'kb-custom-icons-meta',
				'kb_meta_file',
				array(
					'title' => __( 'Choose or Upload File', 'kadence-blocks-pro' ),
					'button' => __( 'Use this File', 'kadence-blocks-pro' ),
				)
			);
			wp_enqueue_script( 'kb-custom-icons-meta' );
		}
	}
	/**
	 * Add Meta Box
	 */
	public function add_meta_box() {
		add_meta_box(
			'kadence-custom-icons-metabox',
			__( 'Icon Set', 'kadence-blocks-pro' ),
			array( $this, 'render_metabox' ),
			'kb_icon',
			'normal',
			'default'
		);
	}
	/**
	 * Render Meta Box
	 */
	public function render_metabox() {
		global $post;

		$icon_json = get_post_meta( $post->ID, '_kb_icon_json', true );
		if ( empty( $icon_json ) ) {
			$icon_json = '{}';
		}

		$type = get_post_meta( $post->ID, '_kb_icon_type', true );
		if ( empty( $type ) ) {
			$type = 'file';
		}

		$selection     = get_post_meta( $post->ID, '_kb_icon_selection', true );
		$show_only     = get_post_meta( $post->ID, '_kb_icon_show_only', true );
		$option_values = array( 'false', 'true' );

		if ( ! empty( $selection ) && $type === 'file' ) {
			$path = get_attached_file( $selection );
			if ( ! empty( $path ) && file_exists( $path ) ) {
				$icon_json = file_get_contents( $path );
			}
		}

		wp_enqueue_style( 'wp-components' );
		wp_register_script( 'kb-pro-icon-upload', KBP_URL . 'dist/build/icon-upload.js', array( 'lodash', 'wp-components', 'wp-dom-ready', 'wp-element', 'wp-i18n' ), '2.2', true );
		wp_localize_script(
			'kb-pro-icon-upload',
			'kadence_blocks_params',
			array(
				'ajax_url'       => admin_url( 'admin-ajax.php' ),
				'ajax_nonce'     => wp_create_nonce( 'kadence-blocks-ajax-verification' ),
				'ajax_loader'    => KBP_URL . 'dist/assets/images/ajax-loader.gif',
				'config'         => get_option( 'kt_blocks_config_blocks' ),
				'configuration'  => get_option( 'kadence_blocks_config_blocks' ),
				'settings'       => get_option( 'kadence_blocks_settings_blocks' ),
				'rest_url'       => get_rest_url(),

			)
		);

		wp_enqueue_script( 'kb-pro-icon-upload' );

		?>
		<script>
			window.kadenceDynamicParams = {};
			window.ktGbToolsData = {};

			let kbIconJson = <?php echo $icon_json; ?>;

		</script>

			<fieldset class="kt_meta_box kb_icon_show_only" style="padding: 0 0 10px; border-bottom:1px solid #e9e9e9;">

				<div class="kadence_replace_json_file">
					Loading...
				</div>

			</fieldset>
			<div class="clearfixit" style="padding: 5px 0; clear:both;"></div>
			<fieldset class="kt_meta_box kb_icon_show_only" style="padding: 10px 0 0;">
				<div style="width: 18%; padding: 0 2% 0 0; float: left;">
					<label for="_kb_icon_show_only" style="font-weight: 600;"><?php echo esc_html__( 'Show only these icons', 'kadence-reading-time' ); ?></label>
				</div>
				<div style="width: 80%;float: right;">
					<select name="_kb_icon_show_only">
					<?php
					foreach ( $option_values as $value ) {
						if ( $value == $show_only ) {
							?>
							<option value="<?php echo esc_attr( $value ); ?>" selected><?php echo esc_attr( $value ); ?></option>
							<?php
						} else {
							?>
							<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_attr( $value ); ?></option>
							<?php
						}
					}
					?>
					</select>
				</div>
				<div class="clearfixit" style="padding: 5px 0; clear:both;"></div>
		<?php
		// Security field.
		wp_nonce_field( 'kb_icons_form_metabox_nonce', 'kb_icons_form_metabox_process' );
	}
	/**
	 * Save Meta Box.
	 *
	 * @param number $post_id the post id.
	 * @param object $post the post object.
	 * @param object $update the update object.
	 */
	public function save_post_meta( $post_id, $post, $update ) {
		// If this is an autosave, our form has not been submitted,
		// so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		// Check if user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return $post_id;
		}

		// Check if not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return $post_id;
		}

		$nonce_name   = isset( $_POST['kb_icons_form_metabox_process'] ) ? $_POST['kb_icons_form_metabox_process'] : '';
		$nonce_action = 'kb_icons_form_metabox_nonce';

		// Check if nonce is set.
		if ( ! isset( $nonce_name ) ) {
			return $post_id;
		}
		// Check if nonce is valid.
		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
			return $post_id;
		}
		if ( isset( $_POST['_kb_icon_selection'] ) ) {
			update_post_meta( $post_id, '_kb_icon_selection', sanitize_text_field( wp_unslash( $_POST['_kb_icon_selection'] ) ) );
		}

		if ( isset( $_POST['_kb_icon_show_only'] ) ) {
			update_post_meta( $post_id, '_kb_icon_show_only', sanitize_text_field( wp_unslash( $_POST['_kb_icon_show_only'] ) ) );
		}

		if ( isset( $_POST['_kb_icon_json'] ) ) {
			update_post_meta( $post_id, '_kb_icon_json', $_POST['_kb_icon_json'] );

			if( !empty( $_POST['_kb_icon_json'] )) {
				update_post_meta( $post_id, '_kb_icon_type', 'json' );
			}
		}
	}
	/**
	 * Add icons to blocks.
	 */
	public function add_editor_block_icons() {
		$icons = $this->kadence_icon_query();
		if ( $icons ) {
			if ( is_null( self::$icon_array ) || is_null( self::$icon_names ) ) {
				$this->build_block_icon_array();
			}
			wp_enqueue_script( 'kadence_blocks_custom_icons', KBP_URL . 'dist/kb-custom-icons.js', array( 'wp-blocks', 'wp-i18n', 'wp-element' ), KBP_VERSION, true );
			wp_localize_script(
				'kadence_blocks_custom_icons',
				'kadence_custom_icons',
				array(
					'icons'            => self::$icon_array,
					'icon_names'       => self::$icon_names,
					'only_custom_sets' => apply_filters( 'kadence_blocks_only_custom_icon_sets', false ),
					'only_custom'      => self::$only_custom,
				)
			);
		}
	}
	/**
	 * Add icons to blocks.
	 */
	public function add_block_icons() {
		if ( ! is_admin() ) {
			return;
		}
		global $pagenow;
		if ( 'nav-menus.php' === $pagenow ) {
			$icons = $this->kadence_icon_query();
			if ( $icons ) {
				if ( is_null( self::$icon_array ) || is_null( self::$icon_names ) ) {
					$this->build_block_icon_array();
				}
				wp_enqueue_script( 'kadence_blocks_custom_icons', KBP_URL . 'dist/kb-custom-icons.js', array( 'wp-blocks', 'wp-i18n', 'wp-element' ), KBP_VERSION, true );
				wp_localize_script(
					'kadence_blocks_custom_icons',
					'kadence_custom_icons',
					array(
						'icons'            => self::$icon_array,
						'icon_names'       => self::$icon_names,
						'only_custom_sets' => apply_filters( 'kadence_blocks_only_custom_icon_sets', false ),
						'only_custom'      => self::$only_custom,
					)
				);
			}
		}

		global $typenow;
		if ( 'kb_icon' === $typenow && in_array($pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			wp_enqueue_script( 'kt-admin-icons-edit', KBP_URL . 'dist/settings/admin-activate.js', false, KBP_VERSION );
		}
	}
	/**
	 * Build Icon post type
	 */
	public function icon_post_type() {
		$icon_labels = array(
			'name'               => __( 'Custom Icons', 'kadence-blocks-pro' ),
			'singular_name'      => __( 'Custom Icon', 'kadence-blocks-pro' ),
			'add_new'            => __( 'Add New Custom Icon', 'kadence-blocks-pro' ),
			'add_new_item'       => __( 'Add New Custom Icon', 'kadence-blocks-pro' ),
			'edit_item'          => __( 'Edit Custom Icon', 'kadence-blocks-pro' ),
			'new_item'           => __( 'New Custom Icon', 'kadence-blocks-pro' ),
			'all_items'          => __( 'All Custom Icons', 'kadence-blocks-pro' ),
			'view_item'          => __( 'View Custom Icon', 'kadence-blocks-pro' ),
			'search_items'       => __( 'Search Custom Icons', 'kadence-blocks-pro' ),
			'not_found'          => __( 'No Custom Icons found', 'kadence-blocks-pro' ),
			'not_found_in_trash' => __( 'No Custom Icons found in Trash', 'kadence-blocks-pro' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Custom Icons', 'kadence-blocks-pro' ),
		);

		$icon_args = array(
			'labels'              => $icon_labels,
			'public'              => false,
			'publicly_queryable'  => false,
			'map_meta_cap'        => true,
			'show_ui'             => true,
			'exclude_from_search' => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'query_var'           => true,
			'rewrite'             => false,
			'has_archive'         => false,
			'show_in_rest'        => true,
			'hierarchical'        => false,
			'capability_type'     => 'post',
			'menu_icon'           => 'dashicons-editor-textcolor',
			'supports'            => array( 'title' ),
		);

		register_post_type( 'kb_icon', $icon_args );
	}
	/**
	 * Query Custom icons.
	 */
	public static function kadence_icon_query() {
		if ( is_null( self::$icon_query ) ) {
			$icon = new WP_Query(
				array(
					'post_type'      => 'kb_icon',
					'posts_per_page' => -1,
					'post_status'    => 'publish',
					'suppress_filters' => true,
				)
			);

			self::$icon_query = $icon->posts;
		}
		return self::$icon_query;
	}
	/**
	 * Get Icon Name.
	 *
	 * @param number $id the file id.
	 */
	public function get_icon_name( $id, $type = 'file') {
		$name = __( 'Custom Icon Set', 'kadence-blocks-pro' );
		if ( is_null( self::$icon_contents ) ) {
			$path = get_attached_file( $id );
			if ( ! empty( $path ) && file_exists( $path ) ) {
				if( $type === 'json') {
					self::$icon_contents = json_decode( $id );
				} else {
					self::$icon_contents = json_decode( file_get_contents( $path ) );
				}
			}
		}
		if ( ! is_null( self::$icon_contents ) ) {
			$import = self::$icon_contents;
			if ( isset( $import->IcoMoonType ) && isset( $import->preferences ) && isset( $import->preferences->imagePref ) && isset( $import->preferences->imagePref->name ) ) {
				$name = $import->preferences->imagePref->name;
			}
		}
		return $name;
	}
	/**
	 * Get Icon prefix.
	 *
	 * @param number $id the file id.
	 */
	public function get_icon_prefix( $id, $type = 'file' ) {
		$prefix = __( 'Not Found', 'kadence-blocks-pro' );
		if ( is_null( self::$icon_contents ) ) {
			$path = get_attached_file( $id );
			if ( ! empty( $path ) && file_exists( $path ) ) {
				if( $type === 'json') {
					self::$icon_contents = json_decode( $id );
				} else {
					self::$icon_contents = json_decode( file_get_contents( $path ) );
				}
			}
		}
		if ( ! is_null( self::$icon_contents ) ) {
			$import = self::$icon_contents;
			if ( isset( $import->IcoMoonType ) && isset( $import->preferences ) && isset( $import->preferences->imagePref ) && isset( $import->preferences->imagePref->prefix ) ) {
				$prefix = $import->preferences->imagePref->prefix;
			}
		}
		return $prefix;
	}
	/**
	 * Build custom Icons array for preview.
	 *
	 * @param number $id the file id.
	 */
	public function build_preview_icon_array( $id, $type = 'file' ) {
		$icons = array();
		if ( is_null( self::$icon_contents ) ) {
			if( $type === 'json') {
				self::$icon_contents = json_decode( $id );
			} else {
				$path = get_attached_file( $id );
				if ( ! empty( $path ) && file_exists( $path ) ) {
					self::$icon_contents = json_decode( file_get_contents( $path ) );
				}
			}
		}
		if ( ! is_null( self::$icon_contents ) ) {
			$import = self::$icon_contents;
			if ( isset( $import->IcoMoonType ) && isset( $import->icons ) ) {
				foreach ( $import->icons as $icon_object ) {
					$icons[ $icon_object->properties->name ] = array(
						'name' => $icon_object->properties->name,
					);
					$first  = '0';
					$second = '0';
					if ( isset( $icon_object->icon->width ) ) {
						if ( $icon_object->icon->width > $import->height ) {
							$diff = $icon_object->icon->width - $import->height;
							$second = - floor( $diff / 2 );
						} elseif ( $icon_object->icon->width < $import->height ) {
							$diff = $import->height - $icon_object->icon->width;
							$first = - floor( $diff / 2 );
						}
					}
					$icons[ $icon_object->properties->name ] = array(
						'vB' => $first . ' ' . $second . ' ' . $import->height . ' ' . ( isset( $icon_object->icon->width ) ? $icon_object->icon->width : $import->height ),
						'cD' => array(),
					);
					foreach ( $icon_object->icon->paths as $key => $value ) {
						$icons[ $icon_object->properties->name ]['cD'][] = array(
							'nE'  => 'path',
							'aBs' => array( 'd' => $value ),
						);
					}
				}
			}
		}
		return $icons;
	}

	/**
	 * Build custom Icons array.
	 */
	public function build_block_icon_array() {
		$icon_array      = array( 'icons' => array() );
		$icon_name_array = array( 'icon_cats' => array() );
		$icons           = $this->kadence_icon_query();

		if ( $icons ) {
			foreach ( $icons as $icon ) {
				$title     = $icon->post_title;
				$type      = get_post_meta( $icon->ID, '_kb_icon_type', true );
				$show_only = get_post_meta( $icon->ID, '_kb_icon_show_only', true );

				if ( $type === 'json' ) {
					$selection = get_post_meta( $icon->ID, '_kb_icon_json', true );
				} else {
					$type = 'file';
					$selection = get_post_meta( $icon->ID, '_kb_icon_selection', true );
				}

				if ( ! empty( $title ) && ! empty( $selection ) ) {
					$path = get_attached_file( $selection );

					if ( ( ! empty( $path ) && file_exists( $path ) ) || $type === 'json' ) {
						if ( 'true' === $show_only ) {
							self::$icon_array = null;
							self::$icon_names = null;
							self::$only_custom = true;
						}

						if( $type === 'file') {
							$import = json_decode( file_get_contents( $path ) );
						} else if( isset( $selection) ) {
							$import = json_decode( $selection );
						} else {
							continue;
						}

						if ( isset( $import->IcoMoonType ) && isset( $import->preferences ) && isset( $import->preferences->imagePref ) && isset( $import->preferences->imagePref->prefix ) ) {
							$prefix = $import->preferences->imagePref->prefix;
							$icon_name_array['icon_cats'][ $title ] = array();
							foreach ( $import->icons as $icon_object ) {
								$icon_name_array['icon_cats'][ $title ][] = $prefix . $icon_object->properties->name;
								$first  = '0';
								$second = '0';
								if ( isset( $icon_object->icon->width ) ) {
									if ( $icon_object->icon->width > $import->height ) {
										$diff = $icon_object->icon->width - $import->height;
										$second = - floor( $diff / 2 );
									} elseif ( $icon_object->icon->width < $import->height ) {
										$diff = $import->height - $icon_object->icon->width;
										$first = - floor( $diff / 2 );
									}
								}
								$icon_array['icons'][ $prefix . $icon_object->properties->name ] = array(
									'vB' => $first . ' ' . $second . ' ' . $import->height . ' ' . ( isset( $icon_object->icon->width ) ? $icon_object->icon->width : $import->height ),
									'cD' => array(),
								);
								foreach ( $icon_object->icon->paths as $key => $value ) {
									$icon_array['icons'][ $prefix . $icon_object->properties->name ]['cD'][] = array(
										'nE'  => 'path',
										'aBs' => array( 'd' => $value ),
									);
								}
							}
						}
					}
					if ( 'true' === $show_only ) {
						break;
					}
				}
			}
		}
		self::$icon_array = $icon_array;
		self::$icon_names = $icon_name_array;
	}
	/**
	 * Add mime types for json file
	 *
	 * @param array $mimes the upload mimes.
	 * @return array
	 */
	public function extra_mime_types( $mimes ) {
		$mimes['json'] = 'application/json';

		return $mimes;
	}

	/**
	 * Allow upload of json file.
	 *
	 * @param array  $data the upload data.
	 * @param string $file the file object.
	 * @param string $filename the file name.
	 * @param array  $mimes the upload mimes.
	 * @return array
	 */
	public function files_ext_json( $data = null, $file = null, $filename = null, $mimes = null ) {
		// We don't need to do anything if fileinfo has already assigned these.
		if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
			return $data;
		}
		$wp_file_type = wp_check_filetype( $filename, $mimes );
		if ( 'json' === $wp_file_type['ext'] ) {
			$data['ext']  = 'json';
			$data['type'] = 'application/json';
		}
		return $data;
	}
}
Kadence_Blocks_Pro_Custom_Icons::get_instance();
