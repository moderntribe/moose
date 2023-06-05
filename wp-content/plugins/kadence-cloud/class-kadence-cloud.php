<?php
/**
 * Kadence Cloud API Class
 *
 * @package Kadence Cloud
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Kadence Cloud API Class
 */
class Kadence_Cloud {

	const SLUG = 'kadence_cloud';
	/**
	 * Action on init.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_api_endpoints' ) );
		add_action( 'init', array( $this, 'register_post_type' ), 1 );
		add_filter( 'user_has_cap', array( $this, 'filter_post_type_user_caps' ) );
		add_filter( 'kadence_post_layout', array( $this, 'cloud_item_single_layout' ), 99 );
		add_action( 'wp_enqueue_scripts', array( $this, 'action_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'action_enqueue_admin_scripts' ) );
		if ( is_admin() ) {
			// Ajax Calls.
			add_action( 'wp_ajax_kadence_cloud_regenerate_featured_image', array( $this, 'regenerate_image_ajax_callback' ) );
		}
		$slug = self::SLUG;
		add_filter(
			"manage_{$slug}_posts_columns",
			function( array $columns ) : array {
				return $this->filter_post_type_columns( $columns );
			}
		);
		add_action(
			"manage_{$slug}_posts_custom_column",
			function( string $column_name, int $post_id ) {
				$this->render_post_type_column( $column_name, $post_id );
			},
			10,
			2
		);
	}
	/**
	 * Main AJAX callback function for:
	 * 1). rebuild a featured image
	 * 2). Save it to the uploads
	 * 3). Set it as the featured image.
	 */
	public function regenerate_image_ajax_callback() {
		// Verify if the AJAX call is valid (checks nonce and current_user_can).
		check_ajax_referer( 'kadence-cloud-ajax-verification', 'security' );
		$post_id = empty( $_POST['post_id'] ) ? '' : sanitize_text_field( wp_unslash( $_POST['post_id'] ) );
		// Do you have the data?
		if ( empty( $post_id ) ) {
			wp_send_json_error( __( 'Error: please reload your page and try again.', 'kadence-cloud' ) );
		}
		$settings = json_decode( get_option( 'kadence_cloud' ), true );
		$flash_api = false;
		if ( isset( $settings ) && is_array( $settings ) && isset( $settings['flash_api'] ) && ! empty( $settings['flash_api'] ) ) {
			$flash_api = $settings['flash_api'];
		}
		if ( ! $flash_api ) {
			wp_send_json_error( __( 'Error: API Flash key needed', 'kadence-cloud' ) );
		}
		$get_image = $this->build_featured_image( $post_id, $flash_api );
		if ( ! $get_image ) {
			// Send JSON Error response to the AJAX call.
			wp_send_json_error( __( 'Image could not be generated', 'kadence-cloud' ) );
		} else {
			wp_send_json( $get_image );
		}
		die;
	}
	/**
	 * Register endpoints
	 */
	public function register_api_endpoints() {
		$rest_controller = new Kadence_Cloud_Rest_Controller();
		$rest_controller->register_routes();
	}
	/**
	 * Registers the block areas post type.
	 *
	 * @since 0.1.0
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => __( 'Cloud Library', 'kadence-cloud' ),
			'singular_name'         => __( 'Library Items', 'kadence-cloud' ),
			'menu_name'             => _x( 'Cloud Library', 'Admin Menu text', 'kadence-cloud' ),
			'add_new'               => _x( 'Add New', 'Cloud Library', 'kadence-cloud' ),
			'add_new_item'          => __( 'Add New Cloud Library', 'kadence-cloud' ),
			'new_item'              => __( 'New Cloud Library', 'kadence-cloud' ),
			'edit_item'             => __( 'Edit Cloud Library', 'kadence-cloud' ),
			'view_item'             => __( 'View Cloud Library', 'kadence-cloud' ),
			'all_items'             => __( 'All Cloud Libraries', 'kadence-cloud' ),
			'search_items'          => __( 'Search Cloud Library', 'kadence-cloud' ),
			'parent_item_colon'     => __( 'Parent Cloud Library:', 'kadence-cloud' ),
			'not_found'             => __( 'No Cloud Libraries found.', 'kadence-cloud' ),
			'not_found_in_trash'    => __( 'No Cloud Libraries found in Trash.', 'kadence-cloud' ),
			'archives'              => __( 'Cloud Library archives', 'kadence-cloud' ),
			'insert_into_item'      => __( 'Insert into Cloud Library', 'kadence-cloud' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Cloud Library', 'kadence-cloud' ),
			'filter_items_list'     => __( 'Filter Cloud Libraries list', 'kadence-cloud' ),
			'items_list_navigation' => __( 'Cloud Libraries list navigation', 'kadence-cloud' ),
			'items_list'            => __( 'Cloud Libraries list', 'kadence-cloud' ),
		);

		$rewrite = apply_filters( 'kadence_cloud_post_type_url_rewrite', array( 'slug' => 'cloud' ) );
		$args    = array(
			'labels'              => $labels,
			'description'         => __( 'Library items to include in your cloud library.', 'kadence-cloud' ),
			'public'              => true,
			'publicly_queryable'  => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-cloud',
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'can_export'          => true,
			'show_in_rest'        => true,
			'rewrite'             => $rewrite,
			'rest_base'           => 'cloud_library',
			'capability_type'     => array( 'kadence_cloud', 'kadence_clouds' ),
			'map_meta_cap'        => true,
			'supports'            => array(
				'title',
				'editor',
				'thumbnail',
				'excerpt',
				'custom-fields',
				'revisions',
			),
		);

		register_post_type( self::SLUG, $args );
		$collections_rewrite = apply_filters(
			'kadence_cloud_collections_url_rewrite',
			array(
				'slug'       => 'cloud-collections',
				'with_front' => true,
			)
		);
		// Register Collections Tax.
		$collections_labels = array(
			'name' => _x( 'Collections', 'taxonomy general name', 'kadence-cloud' ),
			'singular_name' => _x( 'Collection', 'taxonomy singular name', 'kadence-cloud' ),
			'search_items' => __( 'Search Collections', 'kadence-cloud' ),
			'popular_items' => __( 'Popular Collections', 'kadence-cloud' ),
			'all_items' => __( 'All Collections', 'kadence-cloud' ),
			'edit_item' => __( 'Edit Collection', 'kadence-cloud' ),
			'update_item' => __( 'Update Collection', 'kadence-cloud' ),
			'add_new_item' => __( 'Add New Collection', 'kadence-cloud' ),
			'new_item_name' => __( 'New Collection', 'kadence-cloud' ),
			'separate_items_with_commas' => __( 'Separate collections with commas', 'kadence-cloud' ),
			'add_or_remove_items' => __( 'Add or remove collections', 'kadence-cloud' ),
			'choose_from_most_used' => __( 'Choose from the most popular collections', 'kadence-cloud' )
		);
		register_taxonomy(
			'kadence-cloud-collections',
			array( self::SLUG ),
			array(
				'hierarchical'      => true, 
				'labels'            => $collections_labels,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => $collections_rewrite,
			)
		);
		$cat_rewrite = apply_filters(
			'kadence_cloud_category_url_rewrite',
			array(
				'slug'       => 'cloud-categories',
				'with_front' => true,
			)
		);
		// Register Category Tax.
		$categories_labels = array(
			'name' => _x( 'Categories', 'taxonomy general name', 'kadence-cloud' ),
			'singular_name' => _x( 'Category', 'taxonomy singular name', 'kadence-cloud' ),
			'search_items' => __( 'Search Categories', 'kadence-cloud' ),
			'popular_items' => __( 'Popular Categories', 'kadence-cloud' ),
			'all_items' => __( 'All Categories', 'kadence-cloud' ),
			'edit_item' => __( 'Edit Category', 'kadence-cloud' ),
			'update_item' => __( 'Update Category', 'kadence-cloud' ),
			'add_new_item' => __( 'Add New Category', 'kadence-cloud' ),
			'new_item_name' => __( 'New Category', 'kadence-cloud' ),
			'separate_items_with_commas' => __( 'Separate categories with commas', 'kadence-cloud' ),
			'add_or_remove_items' => __( 'Add or remove categories', 'kadence-cloud' ),
			'choose_from_most_used' => __( 'Choose from the most popular categories', 'kadence-cloud' )
		);
		register_taxonomy(
			'kadence-cloud-categories',
			array( self::SLUG ),
			array(
				'hierarchical'      => true, 
				'labels'            => $categories_labels,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => $cat_rewrite,
			)
		);
		$keyword_rewrite = apply_filters(
			'kadence_cloud_keyword_url_rewrite',
			array(
				'slug'       => 'cloud-keywords',
				'with_front' => true,
			)
		);
		// Register Keywords Tax.
		$keywords_labels = array(
			'name' => _x( 'Keywords', 'taxonomy general name', 'kadence-cloud' ),
			'singular_name' => _x( 'Keyword', 'taxonomy singular name', 'kadence-cloud' ),
			'search_items' => __( 'Search Keywords', 'kadence-cloud' ),
			'popular_items' => __( 'Popular Keywords', 'kadence-cloud' ),
			'all_items' => __( 'All Keywords', 'kadence-cloud' ),
			'edit_item' => __( 'Edit Keyword', 'kadence-cloud' ),
			'update_item' => __( 'Update Keyword', 'kadence-cloud' ),
			'add_new_item' => __( 'Add New Keyword', 'kadence-cloud' ),
			'new_item_name' => __( 'New Keyword', 'kadence-cloud' ),
			'separate_items_with_commas' => __( 'Separate keywords with commas', 'kadence-cloud' ),
			'add_or_remove_items' => __( 'Add or remove keywords', 'kadence-cloud' ),
			'choose_from_most_used' => __( 'Choose from the most popular keywords', 'kadence-cloud' )
		);
		register_taxonomy(
			'kadence-cloud-keywords',
			array( self::SLUG ),
			array(
				'hierarchical'      => false,
				'labels'            => $keywords_labels,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => $keyword_rewrite,
			)
		);
	}
	/**
	 * Renders the element single template on the front end.
	 *
	 * @param array $layout the layout array.
	 */
	public function cloud_item_single_layout( $layout ) {
		global $post;
		if ( is_singular( self::SLUG ) || ( is_admin() && self::SLUG === $post->post_type ) ) {
			$layout = wp_parse_args(
				array(
					'layout'           => 'fullwidth',
					'boxed'            => 'unboxed',
					'feature'          => 'hide',
					'feature_position' => 'above',
					'comments'         => 'hide',
					'navigation'       => 'hide',
					'title'            => 'hide',
					'transparent'      => 'disable',
					'sidebar'          => 'disable',
					'vpadding'         => 'hide',
					'footer'           => 'disable',
					'header'           => 'disable',
					'content'          => 'enable',
				),
				$layout
			);
		}

		return $layout;
	}
	/**
	 * Filters the capabilities of a user to conditionally grant them capabilities for managing Cloud Items.
	 *
	 * Any user who can 'edit_theme_options' will have access to manage Cloud Items.
	 *
	 * @param array $allcaps A user's capabilities.
	 * @return array Filtered $allcaps.
	 */
	public function filter_post_type_user_caps( $allcaps ) {
		if ( isset( $allcaps['edit_theme_options'] ) ) {
			$allcaps['edit_kadence_clouds']             = $allcaps['edit_theme_options'];
			$allcaps['edit_others_kadence_clouds']      = $allcaps['edit_theme_options'];
			$allcaps['edit_published_kadence_clouds']   = $allcaps['edit_theme_options'];
			$allcaps['edit_private_kadence_clouds']     = $allcaps['edit_theme_options'];
			$allcaps['delete_kadence_clouds']           = $allcaps['edit_theme_options'];
			$allcaps['delete_others_kadence_clouds']    = $allcaps['edit_theme_options'];
			$allcaps['delete_published_kadence_clouds'] = $allcaps['edit_theme_options'];
			$allcaps['delete_private_kadence_clouds']   = $allcaps['edit_theme_options'];
			$allcaps['publish_kadence_clouds']          = $allcaps['edit_theme_options'];
			$allcaps['read_private_kadence_clouds']     = $allcaps['edit_theme_options'];
		}

		return $allcaps;
	}
	/**
	 * Helper function: Generate screenshot.
	 * Taken from the core media_sideload_image function and
	 * modified to return an array of data instead of html.
	 *
	 * @since 1.1.1.
	 * @param string $file The image file path.
	 * @return array An array of image data.
	 */
	public function build_featured_image( $post_id, $api_key ) {
		$url_base = apply_filters( 'kadence_cloud_image_api_args', 'https://api.apiflash.com/v1/urltoimage' );
		$url = add_query_arg( array( 'screenshot' => true ), get_permalink( $post_id ) );
		$args = array(
			'access_key' => $api_key,
			'fresh' => true,
			'url' => $url,
			'full_page' => true,
			'scroll_page'=> true,
			'format' => 'jpeg',
			'width' => 1600,
			'height' => 400,
			'delay' => 2,
		);
		$args = apply_filters( 'kadence_cloud_image_api_args', $args );
		$image_url = esc_url_raw( add_query_arg( $args, $url_base ) );
		$image     = $this->sideload_image( $image_url, $post_id );
		return $image;
	}
	/**
	 * Helper function: Generate screenshot.
	 * Taken from the core media_sideload_image function and
	 * modified to return an array of data instead of html.
	 *
	 * @since 1.1.1.
	 * @param string $file The image file path.
	 * @return array An array of image data.
	 */
	public function sideload_image( $url, $post_id ) {
		$data = new \stdClass();

		if ( ! function_exists( 'media_handle_sideload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
		}
		if ( ! empty( $url ) ) {
			$image_data = wp_remote_retrieve_body(
				wp_safe_remote_get(
					$url,
					array(
						'timeout'   => '60',
						'sslverify' => false,
					)
				)
			);
			// Empty file content?
			if ( empty( $image_data ) ) {
				return false;
			}
			$upload_dir         = wp_upload_dir();
			$unique_file_name   = wp_unique_filename( $upload_dir['path'], sanitize_file_name( get_the_title( $post_id ) ) . '.jpeg' ); // Generate unique name.
			$filename           = basename( $unique_file_name ); // Create image file name.

			$upload = wp_upload_bits( $filename, null, $image_data );

			$post = array(
				'post_title' => $filename,
				'guid'       => $upload['url'],
			);
			if ( $info ) {
				$post['post_mime_type'] = $info['type'];
			} else {
				$post['post_mime_type'] = 'image/jpeg';
			}
			$id   = wp_insert_attachment( $post, $upload['file'] );
			$meta = wp_generate_attachment_metadata( $id, $upload['file'] );
			wp_update_attachment_metadata(
				$id,
				$meta
			);

			$data->attachment_id = $id;
			$data->url           = wp_get_attachment_url( $id );
			$data->thumbnail_url = wp_get_attachment_thumb_url( $id );
			$data->height        = ( ! empty( $meta ) && is_array( $meta ) && isset( $meta['height'] ) ? $meta['height'] : '' );
			$data->width         = ( ! empty( $meta ) && is_array( $meta ) && isset( $meta['width'] ) ? $meta['width'] : '' );
			set_post_thumbnail( $post_id, $id );
		}

		return $data;
	}
	/**
	 * Filters the block area post type columns in the admin list table.
	 *
	 * @param array $columns Columns to display.
	 * @return array Filtered $columns.
	 */
	private function filter_post_type_columns( array $columns ) : array {

		$add = array(
			'image' => esc_html__( 'Image', 'kadence-cloud' ),
		);

		$new_columns = array();
		foreach ( $columns as $key => $label ) {
			$new_columns[ $key ] = $label;
			if ( 'taxonomy-kadence-cloud-categories' == $key ) {
				$new_columns = array_merge( $new_columns, $add );
			}
		}

		return $new_columns;
	}
	/**
	 * Renders column content for the block area post type list table.
	 *
	 * @param string $column_name Column name to render.
	 * @param int    $post_id     Post ID.
	 */
	private function render_post_type_column( string $column_name, int $post_id ) {
		if ( 'image' !== $column_name ) {
			return;
		}
		echo get_the_post_thumbnail( $post_id, 'medium-large', array( 'style' => 'max-width:200px; border: 2px solid #eee; height:auto' ) );
		echo '<div style="height:1px"></div>';
		$settings = json_decode( get_option( 'kadence_cloud' ), true );
		if ( isset( $settings ) && is_array( $settings ) && isset( $settings['enable_flash'] ) && $settings['enable_flash'] ) {
			echo '<button class="kadence-cloud-rebuild-thumbnail" data-post-id="' . esc_attr( $post_id ) . '" style="
			background: transparent;
			border: 0;
			display: inline-block;
			text-decoration: underline;
			color: #2271b1;
			cursor: pointer;
			line-height: 26px;"
		>' . ( has_post_thumbnail( $post_id ) ? esc_html__( 'Rebuild thumbnail' ) : esc_html__( 'Generate thumbnail' ) ) . '<span class="spinner"></span></button>';
		}
	}
	/**
	 * Enqueues a script that adds sticky for single products
	 */
	public function action_enqueue_admin_scripts() {
		$current_page = get_current_screen();
		if ( 'edit-' . self::SLUG === $current_page->id ) {
			// Enqueue the post styles.
			wp_enqueue_script( 'kadence-cloud-admin', KADENCE_CLOUD_URL . 'assets/cloud-post-admin.js', array( 'jquery' ), KADENCE_CLOUD_VERSION, true );
			wp_localize_script(
				'kadence-cloud-admin',
				'kb_admin_cloud_params',
				array(
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
					'ajax_nonce' => wp_create_nonce( 'kadence-cloud-ajax-verification' ),
				)
			);
		}
	}
	/**
	 * Enqueues a script that adds sticky for single products
	 */
	public function action_enqueue_scripts() {

		if ( is_singular( self::SLUG ) ) {
			// Enqueue the post styles.
			if ( isset( $_GET['screenshot'] ) ) {
				wp_enqueue_style( 'kadence-cloud-post-styles', KADENCE_CLOUD_URL . 'assets/cloud-post-styles.css', array(), KADENCE_CLOUD_VERSION );
			}
		}
	}
	/**
	 * Get the filesystem.
	 *
	 * @access protected
	 * @return WP_Filesystem
	 */
	protected function get_filesystem() {
		global $wp_filesystem;

		// If the filesystem has not been instantiated yet, do it here.
		if ( ! $wp_filesystem ) {
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once wp_normalize_path( ABSPATH . '/wp-admin/includes/file.php' );
			}
			WP_Filesystem();
		}
		return $wp_filesystem;
	}
}

new Kadence_Cloud();
