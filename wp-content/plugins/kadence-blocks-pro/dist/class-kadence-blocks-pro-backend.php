<?php
/**
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Enqueue CSS/JS of all the blocks.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Backend {
	/**
	 * Instance of this class
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
	/**
	 * Class Constructor.
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_filter( 'kadence_blocks_enable_disable_array', array( $this, 'add_blocks_to_block_settings' ) );
		}
		add_action( 'init', array( $this, 'load_api_settings' ) );
		add_filter( 'kadence_blocks_admin_menu_options', '__return_false' );
		add_filter( 'kadence-blocks-settings-url', array( $this, 'admin_url' ) );
	}
	/**
	 * Register settings
	 */
	public function load_api_settings() {
		register_setting(
			'kadence_blocks_send_in_blue_api',
			'kadence_blocks_send_in_blue_api',
			array(
				'type'              => 'string',
				'description'       => __( 'Send in Blue V3 API Key', 'kadence-blocks-pro' ),
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
				'default'           => '',
			)
		);
		register_setting(
			'kadence_blocks_mail_chimp_api',
			'kadence_blocks_mail_chimp_api',
			array(
				'type'              => 'string',
				'description'       => __( 'Mail Chimp API Key', 'kadence-blocks-pro' ),
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
				'default'           => '',
			)
		);
		register_setting(
			'kadence_blocks_activecampaign_api_key',
			'kadence_blocks_activecampaign_api_key',
			array(
				'type'              => 'string',
				'description'       => __( 'ActiveCampaign API Key', 'kadence-blocks-pro' ),
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
				'default'           => '',
			)
		);
		register_setting(
			'kadence_blocks_activecampaign_api_base',
			'kadence_blocks_activecampaign_api_base',
			array(
				'type'              => 'string',
				'description'       => __( 'ActiveCampaign API Base', 'kadence-blocks-pro' ),
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
				'default'           => '',
			)
		);
	}
	/**
	 * Change Settings Page url.
	 *
	 * @param string $url the url for the admin page.
	 */
	public function admin_url( $url ) {
		return admin_url( 'admin.php?page=kadence-blocks' );
	}
	/**
	 * Add to settings so they can be disabled.
	 *
	 * @since 1.0.0
	 */
	public function add_blocks_to_block_settings( $blocks ) {
		$blocks['kadence/imageoverlay'] = array(
			'slug'  => 'kadence/imageoverlay',
			'name'  => __( 'Image Overlay', 'kadence-blocks-pro' ),
			'desc'  => __( 'The Image Overlay block is a beautiful way to create an image link with an overlay title and subtitle. Using the powerful options you can set placement, animations, and hover effects.', 'kadence-blocks-pro' ),
			'image' => KBP_URL . 'dist/settings/img/imageoverlay.jpg',
		);
		$blocks['kadence/splitcontent'] = array(
			'slug'  => 'kadence/splitcontent',
			'name'  => __( 'Split Content', 'kadence-blocks-pro' ),
			'desc'  => __( 'Easily create a two column row using an image or video in one column, drag to adjust the media column width and input any desirable blocks in the content column.', 'kadence-blocks-pro' ),
			'image' => KBP_URL . 'dist/settings/img/splitcontent.jpg',
		);
		$blocks['kadence/postgrid'] = array(
			'slug'  => 'kadence/postgrid',
			'name'  => __( 'Post Grid/Carousel', 'kadence-blocks-pro' ),
			'desc'  => __( 'Add a grid or carousel of posts and customize the styling to your desired look. Posts can be selected individually or by category or tag.', 'kadence-blocks-pro' ),
			'image' => KBP_URL . 'dist/settings/img/postgridcarousel.jpg',
		);
		$blocks['kadence/modal'] = array(
			'slug'  => 'kadence/modal',
			'name'  => __( 'Modal', 'kadence-blocks-pro' ),
			'desc'  => __( 'Easily create call to action buttons that open a modal box. Include any kind of content within your modal. Customize overlay, animation, size, placement etc.', 'kadence-blocks-pro' ),
			'image' => KBP_URL . 'dist/settings/img/modal.jpg',
		);
		$blocks['kadence/productcarousel'] = array(
			'slug'  => 'kadence/productcarousel',
			'name'  => __( 'Product Carousel', 'kadence-blocks-pro' ),
			'desc'  => __( 'Add a Woocommerce Product Carousel to your page or post. You can select products by category, on sale, best selling or just individually select the products you want to show.', 'kadence-blocks-pro' ),
			'image' => KBP_URL . 'dist/settings/img/productcarousel.jpg',
		);
		$blocks['kadence/videopopup'] = array(
			'slug'  => 'kadence/videopopup',
			'name'  => __( 'Video Popup', 'kadence-blocks-pro' ),
			'desc'  => __( 'Beautifully display a thumbnail with overlay and hover effect that links to a video popup on click. Works with local or external videos!', 'kadence-blocks-pro' ),
			'image' => KBP_URL . 'dist/settings/img/block-video-pop.jpg',
		);
		$blocks['kadence/portfoliogrid'] = array(
			'slug'  => 'kadence/portfoliogrid',
			'name'  => __( 'Portfolio Grid/Carousel', 'kadence-blocks-pro' ),
			'desc'  => __( 'Display a grid or carousel of portfolio styled posts. Use any post type and define colors, hover animations. You can also add a filter for sorting.', 'kadence-blocks-pro' ),
			'image' => KBP_URL . 'dist/settings/img/block-portfolio-grid.jpg',
		);
		return $blocks;
	}
}
Kadence_Blocks_Pro_Backend::get_instance();
