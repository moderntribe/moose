<?php
/**
 * Post Block Render
 *
 * @since   1.0.5
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Dynamic_List
 *
 * @category class
 */
class Kadence_Blocks_Pro_Post_Grid {

	/**
	 * Google fonts to enqueue
	 *
	 * @var array
	 */
	public static $gfonts = array();
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
		add_action( 'init', array( $this, 'kadence_blocks_pro_post_block' ), 20 );
		// add_action( 'wp_head', array( $this, 'frontend_gfonts' ), 85 );
		add_filter( 'rest_post_collection_params', array( $this, 'kadence_blocks_rand_orderby_rest_post_collection_params' ) );
		add_filter( 'rest_post_collection_params', array( $this, 'kadence_blocks_menu_order_orderby_rest_post_collection_params' ) );
		add_action( 'rest_api_init', array( $this, 'kadence_blocks_pro_register_rest_fields' ) );
		// Building the post.
		add_action( 'kadence_blocks_post_no_posts', array( $this, 'get_no_posts' ), 15 );
		add_action( 'kadence_blocks_post_loop_header', array( $this, 'get_above_categories' ), 10 );
		add_action( 'kadence_blocks_post_loop_start', array( $this, 'get_post_image' ), 20 );
		add_action( 'kadence_blocks_post_loop_header', array( $this, 'get_post_title' ), 20 );
		add_action( 'kadence_blocks_post_loop_header', array( $this, 'get_meta_area' ), 30 );
		add_action( 'kadence_blocks_post_loop_header_meta', array( $this, 'get_meta_date' ), 10 );
		add_action( 'kadence_blocks_post_loop_header_meta', array( $this, 'get_meta_modified_date' ), 12 );
		add_action( 'kadence_blocks_post_loop_header_meta', array( $this, 'get_meta_author' ), 15 );
		add_action( 'kadence_blocks_post_loop_header_meta', array( $this, 'get_meta_category' ), 20 );
		add_action( 'kadence_blocks_post_loop_header_meta', array( $this, 'get_meta_comment' ), 25 );
		add_action( 'kadence_blocks_post_loop_content', array( $this, 'get_post_excerpt' ), 20 );
		add_action( 'kadence_blocks_post_loop_content', array( $this, 'get_post_read_more' ), 30 );
		add_action( 'kadence_blocks_post_loop_footer_start', array( $this, 'get_post_footer_date' ), 10 );
		add_action( 'kadence_blocks_post_loop_footer_start', array( $this, 'get_post_footer_categories' ), 15 );
		add_action( 'kadence_blocks_post_loop_footer_start', array( $this, 'get_post_footer_tags' ), 20 );
		add_action( 'kadence_blocks_post_loop_footer_end', array( $this, 'get_post_footer_author' ), 10 );
		add_action( 'kadence_blocks_post_loop_footer_end', array( $this, 'get_post_footer_comments' ), 15 );
	}
	/**
	 * Add `rand` as an option for orderby param in REST API.
	 * Hook to `rest_{$this->post_type}_collection_params` filter.
	 *
	 * @param array $query_params Accepted parameters.
	 * @return array
	 */
	public function kadence_blocks_rand_orderby_rest_post_collection_params( $query_params ) {
		$query_params['orderby']['enum'][] = 'rand';
		return $query_params;
	}

	/**
	 * Add `menu_order` as an option for orderby param in REST API.
	 * Hook to `rest_{$this->post_type}_collection_params` filter.
	 *
	 * @param array $query_params Accepted parameters.
	 * @return array
	 */
	public function kadence_blocks_menu_order_orderby_rest_post_collection_params( $query_params ) {
		$query_params['orderby']['enum'][] = 'menu_order';
		return $query_params;
	}
	/**
	 * Registers scripts and styles.
	 */
	public function register_scripts() {
		// If in the backend, bail out.
		if ( is_admin() ) {
			return;
		}
		// Lets register all the block styles.
		wp_register_style( 'kadence-blocks-post-grid', KBP_URL . 'dist/build/block-css/style-post-grid-styles.css', array(), KBP_VERSION );
		wp_register_script( 'kadence-blocks-isotope', KBP_URL . 'dist/vendor/isotope.pkgd.min.js', array(), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-pro-iso-init', KBP_URL . 'dist/kb-iso-init.js', array( 'kadence-blocks-isotope' ), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-pro-iso-post-init', KBP_URL . 'dist/kb-iso-post-init.js', array( 'kadence-blocks-isotope' ), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-pro-masonry-init', KBP_URL . 'dist/kt-masonry-init.js', array( 'masonry' ), KBP_VERSION, true );
		wp_register_style( 'kadence-blocks-pro-slick', KBP_URL . 'dist/vendor/kt-blocks-slick.css', array(), KBP_VERSION );
		wp_register_style( 'kad-splide', KBP_URL . 'dist/assets/css/kadence-splide.min.css', array(), KBP_VERSION );
		wp_register_script( 'kadence-slick', KBP_URL . 'dist/vendor/slick.min.js', array( 'jquery' ), KBP_VERSION, true );
		wp_register_script( 'kad-splide', KBP_URL . 'dist/assets/js/splide.min.js', array(), KBP_VERSION, true );
		wp_register_script( 'kadence-splide-auto-scroll', KBP_URL . 'dist/assets/js/splide-auto-scroll.min.js', array( 'kad-splide' ), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-pro-slick-init', KBP_URL . 'dist/kt-slick-init.js', array( 'jquery', 'kadence-slick' ), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-pro-splide-init', KBP_URL . 'dist/assets/js/kb-splide-init.min.js', array( 'kad-splide' ), KBP_VERSION, true );
	}
	/**
	 * Registers and enqueue's script.
	 *
	 * @param string  $handle the handle for the script.
	 */
	public function enqueue_script( $handle ) {
		if ( ! wp_script_is( $handle, 'registered' ) ) {
			$this->register_scripts();
		}
		wp_enqueue_script( $handle );
	}
	/**
	 * Registers and enqueue's styles.
	 *
	 * @param string  $handle the handle for the script.
	 */
	public function enqueue_style( $handle ) {
		if ( ! wp_style_is( $handle, 'registered' ) ) {
			$this->register_scripts();
		}
		wp_enqueue_style( $handle );
	}
	/**
	 * Register the dynamic block.
	 *
	 * @since 1.0.5
	 *
	 * @return void
	 */
	public function kadence_blocks_pro_post_block() {

		// Only load if Gutenberg is available.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		// Hook server side rendering into render callback.
		register_block_type(
			'kadence/postgrid',
			array(
				'attributes' => array(
					'queryType' => array(
						'type' => 'string',
						'default' => 'query',
					),
					'postIds' => array(
						'type' => 'array',
						'default' => array(),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'order' => array(
						'type' => 'string',
						'default' => 'desc',
					),
					'orderBy'  => array(
						'type' => 'string',
						'default' => 'date',
					),
					'categories' => array(
						'type' => 'array',
						'default' => array(),
						'items'   => array(
							'type' => 'object',
						),
					),
					'tags' => array(
						'type' => 'array',
						'default' => array(),
						'items'   => array(
							'type' => 'object',
						),
					),
					'postTax'=> array(
						'type' => 'boolean',
						'default' => false,
					),
					'uniqueID' => array(
						'type' => 'string',
					),
					'postsToShow' => array(
						'type' => 'number',
						'default' => 6,
					),
					'pagination'=> array(
						'type' => 'boolean',
						'default' => false,
					),
					// Layout
					'blockAlignment' => array(
						'type' => 'string',
						'default' => 'none',
					),
					'layout' => array(
						'type' => 'string',
					),
					'postColumns'=> array(
						'type' => 'array',
						'default' => array( 2, 2, 2, 2, 1, 1 ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'columnControl' => array(
						'type' => 'string',
						'default' => 'linked',
					),
					'columnGap' => array(
						'type' => 'number',
						'default' => 30,
					),
					'columnGapTablet' => array(
						'type' => 'number',
						'default' => '',
					),
					'columnGapMobile' => array(
						'type' => 'number',
						'default' => '',
					),
					'columnGapUnit' => array(
						'type' => 'string',
						'default' => 'px',
					),
					'rowGap' => array(
						'type' => 'number',
						'default' => 30,
					),
					'rowGapTablet' => array(
						'type' => 'number',
						'default' => '',
					),
					'rowGapMobile' => array(
						'type' => 'number',
						'default' => '',
					),
					'rowGapUnit' => array(
						'type' => 'string',
						'default' => 'px',
					),
					'autoPlay' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'autoSpeed' => array(
						'type' => 'number',
						'default' => 7000,
					),
					'transSpeed' => array(
						'type' => 'number',
						'default' => 400,
					),
					'slidesScroll' => array(
						'type' => 'string',
						'default' => '1',
					),
					'arrowStyle' => array(
						'type' => 'string',
						'default' => 'whiteondark',
					),
					'dotStyle' => array(
						'type' => 'string',
						'default' => 'dark',
					),
					// Container.
					'backgroundColor' => array(
						'type' => 'string',
					),
					'containerPadding'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
					),
					'containerTabletPadding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'containerMobilePadding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'containerPaddingType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					'borderColor' => array(
						'type' => 'string',
					),
					'borderOpacity' => array(
						'type' => 'number',
						'default' => 1,
					),
					'borderWidth'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'borderRadius'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					// Image.
					'displayImage' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'alignImage' => array(
						'type' => 'string',
						'default' => 'top',
					),
					'sideImageWidth' => array(
						'type' => 'number',
						'default' => 30,
					),
					'sideImageMoveAboveMobile' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'imageRatio'=> array(
						'type' => 'string',
						'default' => '75',
					),
					'imagePadding'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
					),
					'imageTabletPadding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'imageMobilePadding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'imagePaddingType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					// Header.
					'headerBG'=> array(
						'type' => 'string',
					),
					'headerBGOpacity'=> array(
						'type' => 'number',
						'default' => 1,
					),
					'headerPadding'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
					),
					'headerTabletPadding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'headerMobilePadding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'headerPaddingType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					'headerMargin'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
					),
					'headerTabletMargin' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'headerMobileMargin' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'headerMarginType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					// Above Title.
					'displayAboveCategories' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'aboveDividerSymbol' => array(
						'type' => 'string',
						'default' => 'line',
					),
					'aboveColor'=> array(
						'type' => 'string',
					),
					'aboveLinkColor' => array(
						'type' => 'string',
					),
					'aboveLinkHoverColor' => array(
						'type' => 'string',
					),
					'aboveFont'=> array(
						'type' => 'array',
						'default' => array(
							array(
								'size' => array( '', '', '' ),
								'sizeType' => 'px',
								'lineHeight' => array( '', '', '' ),
								'lineType' => 'px',
								'letterSpacing' => '',
								'textTransform' => '',
								'family' => '',
								'google' => '',
								'style' => '',
								'weight' => '',
								'variant' => '',
								'subset' => '',
								'loadGoogle' => true,
							)
						),
						'items'   => array(
							'type' => 'object',
						),
					),
					// Title.
					'displayTitle' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'titleColor'=> array(
						'type' => 'string',
					),
					'titleHoverColor'=> array(
						'type' => 'string',
					),
					'titleFont'=> array(
						'type' => 'array',
						'default' => array(
							array(
								'level' => 2,
								'size' => array( '', '', '' ),
								'sizeType' => 'px',
								'lineHeight' => array( '', '', '' ),
								'lineType' => 'px',
								'letterSpacing' => '',
								'textTransform' => '',
								'family' => '',
								'google' => '',
								'style' => '',
								'weight' => '',
								'variant' => '',
								'subset' => '',
								'loadGoogle' => true,
							)
						),
						'items'   => array(
							'type' => 'object',
						),
					),
					'titlePadding'=> array(
						'type' => 'array',
						'default' => array( 5, 0, 10, 0 ),
					),
					'titleTabletPadding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'titleMobilePadding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'titlePaddingType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					'titleMargin'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
					),
					'titleTabletMargin' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'titleMobileMargin' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'titleMarginType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					// Meta.
					'displayDate' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'datePreText' => array(
						'type'    => 'string',
						'default' => __( 'Posted on', 'kadence-blocks-pro' ),
					),
					'displayModifiedDate' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'modifiedDatePreText' => array(
						'type'    => 'string',
						'default' => __( 'Updated on', 'kadence-blocks-pro' ),
					),
					'displayAuthor' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'authorPreText' => array(
						'type'    => 'string',
						'default' => __( 'By', 'kadence-blocks-pro' ),
					),
					'displayCategory' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'categoryPreText' => array(
						'type'    => 'string',
						'default' => __( 'Posted in', 'kadence-blocks-pro' ),
					),
					'displayComment' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'metaColor' => array(
						'type' => 'string',
					),
					'metaLinkColor' => array(
						'type' => 'string',
					),
					'metaLinkHoverColor' => array(
						'type' => 'string',
					),
					'metaFont'=> array(
						'type' => 'array',
						'default' => array(
							array(
								'size' => array( '', '', '' ),
								'sizeType' => 'px',
								'lineHeight' => array( '', '', '' ),
								'lineType' => 'px',
								'letterSpacing' => '',
								'textTransform' => '',
								'family' => '',
								'google' => '',
								'style' => '',
								'weight' => '',
								'variant' => '',
								'subset' => '',
								'loadGoogle' => true,
							)
						),
						'items'   => array(
							'type' => 'object',
						),
					),
					'metaDividerSymbol' => array(
						'type' => 'string',
						'default' => '',
					),
					// Body.
					'bodyBG'=> array(
						'type' => 'string',
					),
					'bodyBGOpacity'=> array(
						'type' => 'number',
						'default' => 1,
					),
					'bodyPadding'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
					),
					'bodyTabletPadding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'bodyMobilePadding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'bodyPaddingType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					'bodyMargin'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
					),
					'bodyTabletMargin' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'bodyMobileMargin' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'bodyMarginType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					// Excerpt.
					'displayExcerpt' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'excerptColor' => array(
						'type' => 'string',
					),
					'excerptCustomLength' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'excerptLength'=> array(
						'type' => 'number',
						'default' => 40,
					),
					'excerptFont'=> array(
						'type' => 'array',
						'default' => array(
							array(
								'size' => array( '', '', '' ),
								'sizeType' => 'px',
								'lineHeight' => array( '', '', '' ),
								'lineType' => 'px',
								'letterSpacing' => '',
								'family' => '',
								'google' => '',
								'style' => '',
								'weight' => '',
								'variant' => '',
								'subset' => '',
								'loadGoogle' => true,
							)
						),
						'items'   => array(
							'type' => 'object',
						),
					),
					// Footer.
					'footerBG'=> array(
						'type' => 'string',
					),
					'footerBGOpacity'=> array(
						'type' => 'number',
						'default' => 1,
					),
					'footerPadding'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
					),
					'footerTabletPadding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'footerMobilePadding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'footerPaddingType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					'footerMargin'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
					),
					'footerTabletMargin' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'footerMobileMargin' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'footerMarginType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					'footerBorderColor' => array(
						'type' => 'string',
					),
					'footerBorderOpacity' => array(
						'type' => 'number',
						'default' => 1,
					),
					'footerBorderWidth'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
					),
					// Footer inner
					'footerDisplayDate' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'footerDisplayCategories' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'footerDisplayTags' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'footerDisplayAuthor' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'footerDisplayComment' => array(
						'type' => 'boolean',
						'default' => false,
					),
					// Footer Font
					'footerColor' => array(
						'type' => 'string',
					),
					'footerLinkColor' => array(
						'type' => 'string',
					),
					'footerLinkHoverColor' => array(
						'type' => 'string',
					),
					'footerFont'=> array(
						'type' => 'array',
						'default' => array(
							array(
								'size' => array( '', '', '' ),
								'sizeType' => 'px',
								'lineHeight' => array( '', '', '' ),
								'lineType' => 'px',
								'letterSpacing' => '',
								'textTransform' => '',
								'family' => '',
								'google' => '',
								'style' => '',
								'weight' => '',
								'variant' => '',
								'subset' => '',
								'loadGoogle' => true,
							)
						),
						'items'   => array(
							'type' => 'object',
						),
					),
					// Read More
					'displayReadMore' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'readMoreText'  => array(
						'type'    => 'string',
						'default' => __( 'Read More', 'kadence-blocks-pro' ),
					),
					'readMoreBorder'=> array(
						'type' => 'number',
						'default' => 0,
					),
					'readMoreBorderRadius'=> array(
						'type' => 'number',
						'default' => 0,
					),
					'readMoreColor' => array(
						'type' => 'string',
						'default' => '#ffffff',
					),
					'readMoreHoverColor' => array(
						'type' => 'string',
						'default' => '#ffffff',
					),
					'readMoreBorderColor' => array(
						'type' => 'string',
						'default' => '#444444',
					),
					'readMoreHoverBorderColor' => array(
						'type' => 'string',
						'default' => '#555555',
					),
					'readMoreBackground' => array(
						'type' => 'string',
						'default' => '#444444',
					),
					'readMoreHoverBackground' => array(
						'type' => 'string',
						'default' => '#555555',
					),
					'readMoreFont'=> array(
						'type' => 'array',
						'default' => array(
							array(
								'size' => array( '', '', '' ),
								'sizeType' => 'px',
								'lineHeight' => array( '', '', '' ),
								'lineType' => 'px',
								'letterSpacing' => '',
								'family' => '',
								'google' => '',
								'style' => '',
								'weight' => '',
								'variant' => '',
								'subset' => '',
								'loadGoogle' => true,
							)
						),
						'items'   => array(
							'type' => 'object',
						),
					),
					'readMorePadding'=> array(
						'type' => 'array',
						'default' => array( 4, 8, 4, 8 ),
					),
					'readMoreTabletPadding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'readMoreMobilePadding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'readMorePaddingType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					'readMoreMargin'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
					),
					'readMoreTabletMargin' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'readMoreMobileMargin' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'readMoreMarginType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					'postType' => array(
						'type' => 'string',
						'default' => 'post',
					),
					'taxType' => array(
						'type' => 'string',
						'default' => '',
					),
					'textAlign' => array(
						'type' => 'string',
						'default' => '',
					),
					'offsetQuery' => array(
						'type' => 'number',
						'default' => 0,
					),
					'postTax' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'excludeTax' => array(
						'type' => 'string',
						'default' => 'include',
					),
					'showUnique' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'excludeCurrent' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'displayShadow' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'shadow'=> array(
						'type' => 'array',
						'default' => array(
							array(
								'color' => '#000000',
								'opacity' => 0.2,
								'spread' => 0,
								'blur' => 14,
								'hOffset' => 0,
								'vOffset' => 0,
								'inset' => false,
							)
						),
						'items'   => array(
							'type' => 'object',
						),
					),
					'footerAlignBottom' => array(
						'type' => 'boolean',
						'default' => false,
					),
					// Filter.
					'displayFilter' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'filterAllText' => array(
						'type' => 'string',
					),
					'filterTaxType' => array(
						'type' => 'string',
					),
					'filterTaxSelect' => array(
						'type' => 'array',
						'default' => '',
						'items'   => array(
							'type' => 'object',
						),
					),
					'filterBackground'=> array(
						'type' => 'string',
					),
					'filterBackgroundOpacity'=> array(
						'type' => 'number',
					),
					'filterHoverBackground'=> array(
						'type' => 'string',
					),
					'filterHoverBackgroundOpacity'=> array(
						'type' => 'number',
					),
					'filterActiveBackground'=> array(
						'type' => 'string',
					),
					'filterActiveBackgroundOpacity'=> array(
						'type' => 'number',
					),
					'filterBorder'=> array(
						'type' => 'string',
					),
					'filterBorderOpacity'=> array(
						'type' => 'number',
					),
					'filterHoverBorder'=> array(
						'type' => 'string',
					),
					'filterHoverBorderOpacity'=> array(
						'type' => 'number',
					),
					'filterActiveBorder'=> array(
						'type' => 'string',
					),
					'filterActiveBorderOpacity'=> array(
						'type' => 'number',
					),
					'filterColor' => array(
						'type' => 'string',
					),
					'filterHoverColor' => array(
						'type' => 'string',
					),
					'filterActiveColor' => array(
						'type' => 'string',
					),
					'filterBorderRadius'=> array(
						'type' => 'number',
					),
					'filterBorderWidth'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 2, 0 ),
					),
					'filterPadding' => array(
						'type' => 'array',
						'default' => array( 5, 8, 5, 8 ),
					),
					'filterTabletPadding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'filterMobilePadding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
					),
					'filterPaddingType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					'filterMargin'=> array(
						'type' => 'array',
						'default' => array( 0, 10, 0, 0 ),
					),
					'filterTabletMargin' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'filterMobileMargin' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'filterMarginType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					'filterFont'=> array(
						'type' => 'array',
						'default' => array(
							array(
								'size' => array( '', '', '' ),
								'sizeType' => 'px',
								'lineHeight' => array( '', '', '' ),
								'lineType' => 'px',
								'letterSpacing' => '',
								'textTransform' => '',
								'family' => '',
								'google' => '',
								'style' => '',
								'weight' => '',
								'variant' => '',
								'subset' => '',
								'loadGoogle' => true,
							)
						),
						'items'   => array(
							'type' => 'object',
						),
					),
					'filterAlign' => array(
						'type' => 'string',
						'default' => '',
					),
					'allowSticky' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'imageFileSize'=> array(
						'type' => 'string',
						'default' => 'large',
					),
					'openNewTab' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'imageLink' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'titleLink' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'displayAboveTaxonomy' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'aboveTaxType'  => array(
						'type' => 'string',
					),
					'autoScroll' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'autoScrollSpeed'=> array(
						'type' => 'number',
						'default' => 0.4,
					),
					'autoScrollPause' => array(
						'type' => 'boolean',
						'default' => true,
					),
				),
				'render_callback' => array( $this, 'render_post_block' ),
			)
		);

	}

	/**
	 * Create API fields for additional info
	 */
	public function kadence_blocks_pro_register_rest_fields() {
		// Add featured image source
		$post_types = kadence_blocks_pro_get_post_types();
		foreach ( $post_types as $key => $post_type ) {
			register_rest_field(
				$post_type['value'],
				'featured_image_src_large',
				array(
					'get_callback'    => array( $this, 'kadence_blocks_pro_get_large_image_src' ),
					'update_callback' => null,
					'schema'          => null,
				)
			);
			// Add author info
			register_rest_field(
				$post_type['value'],
				'author_info',
				array(
					'get_callback'    => array( $this, 'kadence_blocks_pro_get_author_info' ),
					'update_callback' => null,
					'schema'          => null,
				)
			);
			// Add comment info.
			register_rest_field(
				$post_type['value'],
				'comment_info',
				array(
					'get_callback'    => array( $this, 'kadence_blocks_pro_get_comment_info' ),
					'update_callback' => null,
					'schema'          => null,
				)
			);
		}
		// Add category info
		register_rest_field(
			'post',
			'category_info',
			array(
				'get_callback'    => array( $this, 'kadence_blocks_pro_get_category_info' ),
				'update_callback' => null,
				'schema'          => null,
			)
		);
		// Add tag info
		register_rest_field(
			'post',
			'tag_info',
			array(
				'get_callback'    => array( $this, 'kadence_blocks_pro_get_tag_info' ),
				'update_callback' => null,
				'schema'          => null,
			)
		);
	}


	/**
	 * Get category info for the rest field
	 *
	 * @param array/object $object Post Object.
	 * @param string $field_name Field name.
	 * @param object $request Request Object.
	 */
	public function kadence_blocks_pro_get_category_info( $object, $field_name, $request ) {
		$category_array = get_the_category( $object['id'] );
		return $category_array;
	}

	/**
	 * Get tag info for the rest field
	 *
	 * @param array/object $object Post Object.
	 * @param string $field_name Field name.
	 * @param object $request Request Object.
	 */
	public function kadence_blocks_pro_get_tag_info( $object, $field_name, $request ) {
		$tag_array = get_the_tags( $object['id'] );
		return $tag_array;
	}

	/**
	 * Get author info for the rest field
	 *
	 * @param array/object $object Post Object.
	 * @param string $field_name Field name.
	 * @param object $request Request Object.
	 */
	public function kadence_blocks_pro_get_comment_info( $object, $field_name, $request ) {
		$number = '';
		// Get the comments count.
		if ( is_array( $object ) && post_type_supports( $object['type'], 'comments' ) ) {
			$comments_count = wp_count_comments( $object['id'] );
			$number = $comments_count->total_comments;
		}
		return $number;
	}

	/**
	 * Get author info for the rest field
	 *
	 * @param array/object $object Post Object.
	 * @param string $field_name Field name.
	 * @param object $request Request Object.
	 */
	public function kadence_blocks_pro_get_author_info( $object, $field_name, $request ) {
		$author_data = array();
		if ( is_array( $object ) && post_type_supports( $object['type'], 'author' ) ) {
			// Get the author name
			$author_data['display_name'] = get_the_author_meta( 'display_name', $object['author'] );

			// Get the author link
			$author_data['author_link'] = get_author_posts_url( $object['author'] );
		}

		// Return the author data
		return $author_data;
	}
	/**
	 * Get image info for the rest field
	 *
	 * @param array/object $object Post Object.
	 * @param string $field_name Field name.
	 * @param object $request Request Object
	 */
	public function kadence_blocks_pro_get_large_image_src( $object, $field_name, $request ) {
		$feat_img_array = array();
		if ( is_array( $object ) && post_type_supports( $object['type'], 'thumbnail' ) ) {
			$feat_img_array = wp_get_attachment_image_src(
				$object['featured_media'],
				'large',
				false
			);
		}
		return $feat_img_array;
	}
	/**
	 * Server rendering for post Block Inner Loop
	 *
	 * @param array $attributes the block attributes.
	 */
	public function render_post_block_filter( $attributes ) {
		if ( isset( $attributes['filterTaxType'] ) && ! empty( $attributes['filterTaxType'] ) ) {
			echo '<div class="kb-post-filter-container">';
			if ( isset( $attributes['filterTaxSelect'] ) && is_array( $attributes['filterTaxSelect'] ) && 1 <= count( $attributes['filterTaxSelect'] ) ) {
				echo '<button class="kb-filter-item is-active" data-filter="*">';
					echo ( isset( $attributes['filterAllText'] ) && ! empty( $attributes['filterAllText'] ) ? esc_html( $attributes['filterAllText'] ) : __( 'All', 'kadence-blocks-pro' ) );
				echo '</button>';
				foreach ( $attributes['filterTaxSelect'] as $value ) {
					$term = get_term( $value['value'], $attributes['filterTaxType'] );
					echo '<button class="kb-filter-item" data-filter=".kb-filter-' . esc_attr( $term->term_id ) . '">';
					echo esc_html( $term->name );
					echo '</button>';
				}
			} else {
				$terms = get_terms( $attributes['filterTaxType'] );
				if ( ! empty( $terms ) ) {
					echo '<button class="kb-filter-item is-active" data-filter="*">';
						echo ( isset( $attributes['filterAllText'] ) && ! empty( $attributes['filterAllText'] ) ? esc_html( $attributes['filterAllText'] ) : __( 'All', 'kadence-blocks-pro' ) );
					echo '</button>';
					foreach ( $terms as $term_key => $term_item ) {
						echo '<button class="kb-filter-item" data-filter=".kb-filter-' . esc_attr( $term_item->term_id ) . '">';
						echo esc_html( $term_item->name );
						echo '</button>';
					}
				}
			}
			echo '</div>';
		}
	}
	/**
	 * Server rendering for Post Block
	 */
	public function render_post_block( $attributes ) {
		if ( ! wp_style_is( 'kadence-blocks-post-grid', 'enqueued' ) ) {
			$this->enqueue_style( 'kadence-blocks-post-grid' );
		}
		$css = '';
		if ( isset( $attributes['uniqueID'] ) ) {
			$style_id = 'kt-blocks' . esc_attr( $attributes['uniqueID'] );
			if ( ! wp_style_is( $style_id, 'enqueued' ) && apply_filters( 'kadence_blocks_render_inline_css', true, 'post', $attributes['uniqueID'] ) ) {
				$css .= $this->kadence_blocks_postgrid_googlefont_check( $attributes );
				$css .= '<style id="' . esc_attr( $style_id ) . '">';
				$unique_id = $attributes['uniqueID'];
				$css .= $this->output_css( $attributes, $unique_id );
				$css .= '</style>';
			}
		}
		$layout = ! empty( $attributes['layout'] ) ? $attributes['layout'] : 'grid';
		if ( ( 'masonry' === $layout || 'grid' === $layout ) && isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] ) {
			$this->enqueue_script( 'kadence-blocks-pro-iso-post-init' );
		} elseif ( 'masonry' === $layout ) {
			$this->enqueue_script( 'kadence-blocks-pro-masonry-init' );
		} elseif ( 'carousel' === $layout ) {
			$this->enqueue_style( 'kad-splide' );
			$this->enqueue_script( 'kadence-blocks-pro-splide-init' );
			if ( isset( $attributes['autoScroll'] ) && true === $attributes['autoScroll'] ) {
				$this->enqueue_script( 'kadence-splide-auto-scroll' );
				global $wp_scripts;
				$script = $wp_scripts->query( 'kadence-blocks-pro-splide-init', 'registered' );
				if ( $script ) {
					if ( ! in_array( 'kadence-splide-auto-scroll', $script->deps ) ) {
						$script->deps[] = 'kadence-splide-auto-scroll';
					}
				}
			}
		}
		ob_start();
		if ( 'carousel' === $layout ) {
			$carouselclasses = ' kadence-splide-wrap';
		} else {
			$carouselclasses = '';
		}
		if ( empty( $carouselclasses ) && isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] && ( ! isset( $attributes['pagination'] ) || isset( $attributes['pagination'] ) && false === $attributes['pagination'] ) ) {
			$filter_class = 'kb-filter-enabled';
		} else {
			$filter_class = '';
		}
		echo '<div class="wp-block-kadence-postgrid kt-blocks-post-loop-block align' . ( isset( $attributes['blockAlignment'] ) ? $attributes['blockAlignment'] : 'none' ) . ' kt-post-loop' . ( isset( $attributes['uniqueID'] ) ? $attributes['uniqueID'] : 'block-id' ) . ' kt-post-grid-layout-'. esc_attr( $layout ) . esc_attr( $carouselclasses ) . ' ' . esc_attr( $filter_class ) . ( isset( $attributes['className'] ) && ! empty( $attributes['className'] ) ? ' ' . esc_attr( $attributes['className'] ) : '' ) . '">';
		if ( empty( $carouselclasses ) && isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] && ( ! isset( $attributes['pagination'] ) || isset( $attributes['pagination'] ) && false === $attributes['pagination'] ) ) {
			$this->render_post_block_filter( $attributes );
		}
			$this->render_post_block_query( $attributes );
		echo '</div><!-- .wp-block-kadence-postgrid -->';

		$output = ob_get_contents();
		ob_end_clean();
		return $css.$output;
	}

	/**
	 * Server rendering for Post Block Inner Loop
	 */
	public function render_post_block_query( $attributes ) {
		global $kadence_blocks_posts_not_in;
		if ( ! isset( $kadence_blocks_posts_not_in ) || ! is_array( $kadence_blocks_posts_not_in ) ) {
			$kadence_blocks_posts_not_in = array();
		}
		$layout = ! empty( $attributes['layout'] ) ? $attributes['layout'] : 'grid';
		if ( 'carousel' === $layout ) {
			$gap_unit        = ( ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px' );
			$gap             = ( isset( $attributes['columnGap'] ) && is_numeric( $attributes['columnGap'] ) ? $attributes['columnGap'] : '30' );
			$gap_tablet      = ( isset( $attributes['columnGapTablet'] ) && is_numeric( $attributes['columnGapTablet'] ) ? $attributes['columnGapTablet'] : $gap );
			$gap_mobile      = ( isset( $attributes['columnGapMobile'] ) && is_numeric( $attributes['columnGapMobile'] ) ? $attributes['columnGapMobile'] : $gap_tablet );
			$auto_play       = ( isset( $attributes['autoPlay'] ) && ! $attributes['autoPlay'] ? 'false' : 'true' );
			$auto_scroll       = ( $auto_play === 'true' && isset( $attributes['autoScroll'] ) && true === $attributes['autoScroll'] ? 'true' : 'false' );
			$auto_scroll_pause = ( isset( $attributes['autoScrollPause'] ) && ! $attributes['autoScrollPause'] ? 'false' : 'true' );
			$auto_speed = ( isset( $attributes['autoSpeed'] ) ? esc_attr( $attributes['autoSpeed'] ) : '7000' );
			$auto_scroll_speed = ( isset( $attributes['autoScrollSpeed'] ) ? esc_attr( $attributes['autoScrollSpeed'] ) : '0.4' );
			$speed = ( $auto_scroll === 'true' ? $auto_scroll_speed : $auto_speed );
			$carouselclasses = ' splide kt-carousel-arrowstyle-' . ( isset( $attributes['arrowStyle'] ) ? esc_attr( $attributes['arrowStyle'] ) : 'whiteondark' ) . ' kt-carousel-dotstyle-' . ( isset( $attributes['dotStyle'] ) ? esc_attr( $attributes['dotStyle'] ) : 'dark' );
			$slider_data = ' data-slider-anim-speed="' . ( isset( $attributes['transSpeed'] ) ? esc_attr( $attributes['transSpeed'] ) : '400' ) . '" data-slider-scroll="' . ( isset( $attributes['slidesScroll'] ) ? esc_attr( $attributes['slidesScroll'] ) : '1' ) . '" data-slider-dots="' . ( isset( $attributes['dotStyle'] ) && 'none' === $attributes['dotStyle'] ? 'false' : 'true' ) . '" data-slider-arrows="' . ( isset( $attributes['arrowStyle'] ) && 'none' === $attributes['arrowStyle'] ? 'false' : 'true' ) . '" data-slider-hover-pause="' . ( 'true' === $auto_scroll ? $auto_scroll_pause : 'false' ) . '" data-slider-auto="' . esc_attr( $auto_play ) . '"  data-slider-auto-scroll="' . esc_attr( $auto_scroll ) . '" data-slider-speed="' . esc_attr( $speed ) . '" data-slider-gap="' . esc_attr( $gap ) . '" data-slider-gap-tablet="' . esc_attr( $gap_tablet ) . '" data-slider-gap-mobile="' . esc_attr( $gap_mobile ) . '" data-slider-gap-unit="' . esc_attr( $gap_unit ) . '" ';
		} elseif ( 'masonry' === $layout ) {
			$carouselclasses = ' kt-post-grid-wrap kb-pro-masonry-init';
			$slider_data = '';
		} else {
			$carouselclasses = ' kt-post-grid-wrap';
			$slider_data = '';
		}
		$exclude_current = isset( $attributes['excludeCurrent'] ) ? $attributes['excludeCurrent'] : true;
		if ( apply_filters( 'kadence_blocks_pro_posts_block_exclude_current', true ) && $exclude_current && is_singular() ) {
			if ( ! in_array( get_the_ID(), $kadence_blocks_posts_not_in, true ) ) {
				$kadence_blocks_posts_not_in[] = get_the_ID();
			}
		}
		$cutoff  = false;
		$columns = ( isset( $attributes['postColumns'] ) && is_array( $attributes['postColumns'] ) && 6 === count( $attributes['postColumns'] ) ? $attributes['postColumns'] : array( 2, 2, 2, 2, 1, 1 ) );
		$post_type = ( isset( $attributes['postType'] ) && ! empty( $attributes['postType'] ) ? $attributes['postType'] : 'post' );
		echo '<div class="kt-post-grid-layout-'. esc_attr( $layout ) . '-wrap' . esc_attr( $carouselclasses ) . '" data-columns-xxl="' . esc_attr( $columns[0] ) . '" data-columns-xl="' . esc_attr( $columns[1] ) . '" data-columns-md="' . esc_attr( $columns[2] ) . '" data-columns-sm="' . esc_attr( $columns[3] ) . '" data-columns-xs="' . esc_attr( $columns[4] ) . '" data-columns-ss="' . esc_attr( $columns[5] ) . '"' . wp_kses_post( $slider_data ) . 'data-item-selector=".kt-post-masonry-item">';
		if ( 'carousel' === $layout ) {
			echo '<div class="kadence-splide-slider-init splide__track">';
			echo '<div class="kadence-splide-slider-wrap kt-post-grid-wrap" data-columns-xxl="' . esc_attr( $columns[0] ) . '" data-columns-xl="' . esc_attr( $columns[1] ) . '" data-columns-md="' . esc_attr( $columns[2] ) . '" data-columns-sm="' . esc_attr( $columns[3] ) . '" data-columns-xs="' . esc_attr( $columns[4] ) . '" data-columns-ss="' . esc_attr( $columns[5] ) . '">';
		}
		if ( isset( $attributes['queryType'] ) && 'individual' === $attributes['queryType'] ) {
			$args = array(
				'post_type' => $post_type,
				'orderby' => 'post__in',
				'posts_per_page' => -1,
				'post__in'  => ( isset( $attributes['postIds'] ) && ! empty( $attributes['postIds'] ) ? $attributes['postIds'] : 0 ),
				'ignore_sticky_posts' => 1,
			);
		} else {
			$args = array(
				'post_type'           => $post_type,
				'posts_per_page'      => ( isset( $attributes['postsToShow'] ) && ! empty( $attributes['postsToShow'] ) ? $attributes['postsToShow'] : 6 ),
				'post_status'         => 'publish',
				'order'               => ( isset( $attributes['order'] ) && ! empty( $attributes['order'] ) ? $attributes['order'] : 'desc' ),
				'orderby'             => ( isset( $attributes['orderBy'] ) && ! empty( $attributes['orderBy'] ) ? $attributes['orderBy'] : 'date' ),
				'ignore_sticky_posts' => ( isset( $attributes['allowSticky'] ) && $attributes['allowSticky'] ? 0 : 1 ),
				'post__not_in'        => ( isset( $kadence_blocks_posts_not_in ) && is_array( $kadence_blocks_posts_not_in ) ? $kadence_blocks_posts_not_in : array() ),
			);
			if ( isset( $attributes['offsetQuery'] ) && ! empty( $attributes['offsetQuery'] ) ) {
				$args['offset'] = $attributes['offsetQuery'];
			}
			if ( isset( $attributes['categories'] ) && ! empty( $attributes['categories'] ) && is_array( $attributes['categories'] ) ) {
				$categories = array();
				$i = 1;
				foreach ( $attributes['categories'] as $key => $value ) {
					$categories[] = $value['value'];
				}
			} else {
				$categories = array();
			}
			if ( 'post' !== $post_type || ( isset( $attributes['postTax'] ) && true === $attributes['postTax'] ) ) {
				if ( isset( $attributes['taxType'] ) && ! empty( $attributes['taxType'] ) ) {
					$args['tax_query'][] = array(
						'taxonomy' => ( isset( $attributes['taxType'] ) ) ? $attributes['taxType'] : 'category',
						'field'    => 'id',
						'terms'    => $categories,
						'operator' => ( isset( $attributes['excludeTax'] ) && 'exclude' === $attributes['excludeTax'] ? 'NOT IN' : 'IN' ),
					);
				}
			} else {
				if ( isset( $attributes['tags'] ) && ! empty( $attributes['tags'] ) && is_array( $attributes['tags'] ) ) {
					$tags = array();
					$i = 1;
					foreach ( $attributes['tags'] as $key => $value ) {
						$tags[] = $value['value'];
					}
				} else {
					$tags = array();
				}
				if ( isset( $attributes['excludeTax'] ) && 'exclude' === $attributes['excludeTax'] ) {
					$args['category__not_in'] = $categories;
					$args['tag__not_in'] = $tags;
				} else {
					$args['category__in'] = $categories;
					$args['tag__in'] = $tags;
				}
			}
			if ( 'carousel' !== $layout && ( ( isset( $attributes['offsetQuery'] ) && 1 > $attributes['offsetQuery'] ) || ! isset( $attributes['offsetQuery'] ) ) && isset( $attributes['pagination'] ) && true === $attributes['pagination'] ) {
				if ( get_query_var( 'paged' ) ) {
					$args['paged'] = get_query_var( 'paged' );
				} else if ( get_query_var( 'page' ) ) {
					$args['paged'] = get_query_var( 'page' );
				} else {
					$args['paged'] = 1;
				}
			}
			if ( isset( $attributes['allowSticky'] ) && $attributes['allowSticky'] && ! isset( $args['paged'] ) && apply_filters( 'kadence_blocks_pro_posts_grid_force_post_count_with_sticky', true ) ) {
				$cutoff = true;
				$cutoff_count = ( isset( $attributes['postsToShow'] ) && ! empty( $attributes['postsToShow'] ) ? $attributes['postsToShow'] : 6 );
			}
		}
		$args = apply_filters( 'kadence_blocks_pro_posts_grid_query_args', $args );
		$loop_number = 1;
		$loop = new WP_Query( $args );
		if ( 'carousel' !== $layout && ( ( isset( $attributes['offsetQuery'] ) && 1 > $attributes['offsetQuery'] ) || ! isset( $attributes['offsetQuery'] ) ) && isset( $attributes['pagination'] ) && true === $attributes['pagination'] ) {
			global $wp_query;
			$wp_query = $loop;
		}
		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) {
				if ( $cutoff && $loop_number > $cutoff_count ) {
					break;
				}
				$loop->the_post();
				if ( isset( $attributes['showUnique'] ) && true === $attributes['showUnique'] ) {
					$kadence_blocks_posts_not_in[] = get_the_ID();
				}
				if ( 'masonry' === $layout ) {
					$tax_filter_classes = '';
					if ( isset( $attributes['filterTaxType'] ) && ! empty( $attributes['filterTaxType'] ) ) {
						global $post;
						$terms = get_the_terms( $post->ID, $attributes['filterTaxType'] );
						if ( $terms && ! is_wp_error( $terms ) ) {
							foreach( $terms as $term ) {
								$tax_filter_classes .= ' kb-filter-' . $term->term_id;
							}
						}
					}
					echo '<div class="' . esc_attr( implode( ' ', get_post_class( 'kt-post-masonry-item' . $tax_filter_classes ) ) ) . '">';
				} else if ( 'grid' === $layout && isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] && ( ! isset( $attributes['pagination'] ) || isset( $attributes['pagination'] ) && false === $attributes['pagination'] ) ) {
					$tax_filter_classes = '';
					if ( isset( $attributes['filterTaxType'] ) && ! empty( $attributes['filterTaxType'] ) ) {
						global $post;
						$terms = get_the_terms( $post->ID, $attributes['filterTaxType'] );
						if ( $terms && ! is_wp_error( $terms ) ) {
							foreach( $terms as $term ) {
								$tax_filter_classes .= ' kb-filter-' . $term->term_id;
							}
						}
					}
					echo '<div class="' . esc_attr( implode( ' ', get_post_class( 'kt-post-masonry-item' . $tax_filter_classes ) ) ) . '">';
				} else if ( 'carousel' === $layout || 'fluidcarousel' === $layout ) {
					echo '<div class="' . esc_attr( implode( ' ', get_post_class( 'kt-post-slider-item' ) ) ) . '">';
				}
					$this->render_post_block_loop( $attributes );
				if ( 'grid' !== $layout ) {
					echo '</div>';
				}
				if ( 'grid' === $layout && isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] && ( ! isset( $attributes['pagination'] ) || isset( $attributes['pagination'] ) && false === $attributes['pagination'] ) ) {
					echo '</div>';
				}
				$loop_number ++;
			}
		} else {
			/**
			 * @hooked get_no_posts - 10
			 */
			do_action( 'kadence_blocks_post_no_posts', $attributes );
		}
		if ( 'carousel' === $layout ) {
			echo '</div>';
			echo '</div>';
		}
		echo '</div>';
		wp_reset_postdata();
		if ( 'carousel' !== $layout && ( ( isset( $attributes['offsetQuery'] ) && 1 > $attributes['offsetQuery'] ) || ! isset( $attributes['offsetQuery'] ) ) && isset( $attributes['pagination'] ) && true === $attributes['pagination'] ) {
			if ( $loop->max_num_pages > 1 ) {
				$this->pagination();
			}
			wp_reset_query();
		}
	}
	/**
	 * Server rendering for Post Block Inner Loop
	 *
	 * @param array $attributes the block attritbutes.
	 */
	public function render_post_block_loop( $attributes ) {
		$image_align        = ( isset( $attributes['alignImage'] ) && isset( $attributes['displayImage'] ) && true === $attributes['displayImage'] && has_post_thumbnail() ? $attributes['alignImage'] : 'none' );
		$footer_bottom_align = ( isset( $attributes['layout'] ) && 'masonry' !== $attributes['layout'] && isset( $attributes['footerAlignBottom'] ) && true === $attributes['footerAlignBottom'] ? ' kb-post-footer-bottom-align' : '' );
		$image_mobile_align  = ( isset( $attributes['sideImageMoveAboveMobile'] ) && true === $attributes['sideImageMoveAboveMobile'] ? 'kt-feat-image-mobile-align-top' : 'kt-feat-image-mobile-align-side' );
		if ( isset( $attributes['layout'] ) && 'grid' === $attributes['layout'] && isset( $attributes['displayFilter'] ) && false === $attributes['displayFilter'] ) {
			$post_classes = get_post_class( 'kt-blocks-post-grid-item' );
		} else {
			$post_classes = array( 'kt-blocks-post-grid-item' );
		}
		echo '<article class="' . esc_attr( implode( ' ', $post_classes ) ) . '">';
			echo '<div class="kt-blocks-post-grid-item-inner-wrap kt-feat-image-align-' . esc_attr( $image_align ) . ' ' . esc_attr( $image_mobile_align ) . esc_attr( $footer_bottom_align ) . '">';
				/**
				 * Kadence Blocks Post Loop Start
				 *
				 * @hooked Kadence_Blocks_Pro_Post_Grid/get_post_image - 20
				 */
				do_action( 'kadence_blocks_post_loop_start', $attributes );
				echo '<div class="kt-blocks-post-grid-item-inner">';
					echo '<header>';
					/**
					 * @hooked Kadence_Blocks_Pro_Post_Grid/get_above_categories - 10
					 * @hooked Kadence_Blocks_Pro_Post_Grid/get_post_title - 20
					 * * @hooked Kadence_Blocks_Pro_Post_Grid/get_meta_area - 30
					 */
					do_action( 'kadence_blocks_post_loop_header', $attributes );
					echo '</header>';
					echo '<div class="entry-content">';
						/**
						 * @hooked Kadence_Blocks_Pro_Post_Grid/get_post_excerpt - 20
						 * @hooked Kadence_Blocks_Pro_Post_Grid/get_post_read_more - 30
						 */
						do_action( 'kadence_blocks_post_loop_content', $attributes );
					echo '</div>';
					echo '<footer class="kt-blocks-post-footer">';
						echo '<div class="kt-blocks-post-footer-left">';
							/**
							 * @hooked Kadence_Blocks_Pro_Post_Grid/get_post_footer_date - 10
							 * @hooked Kadence_Blocks_Pro_Post_Grid/get_post_footer_categories - 15
							 * @hooked Kadence_Blocks_Pro_Post_Grid/get_post_footer_tags - 20
							 */
							do_action( 'kadence_blocks_post_loop_footer_start', $attributes );
						echo '</div>';
						echo '<div class="kt-blocks-post-footer-right">';
							/**
							 * @hooked Kadence_Blocks_Pro_Post_Grid/get_post_footer_author - 10
							 * @hooked Kadence_Blocks_Pro_Post_Grid/get_post_footer_comments - 15
							 */
							do_action( 'kadence_blocks_post_loop_footer_end', $attributes );
						echo '</div>';
					echo '</footer>';
				echo '</div>';
			echo '</div>';
		do_action( 'kadence_blocks_post_loop_end' );
		echo '</article>';
	}
	/**
	 * Server rendering for Post Block pagination.
	 *
	 * @param array $attributes the block attritbutes.
	 */
	public function pagination() {
		$args = array();
		$args['mid_size'] = 3;
		$args['end_size'] = 1;
		$args['prev_text'] = '<span class="screen-reader-text">' . __( 'Previous Page', 'kadence-blocks-pro' ) . '</span><svg style="display:inline-block;vertical-align:middle" aria-hidden="true" class="kt-blocks-pagination-left-svg" viewBox="0 0 320 512" height="14" width="8" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M34.52 239.03L228.87 44.69c9.37-9.37 24.57-9.37 33.94 0l22.67 22.67c9.36 9.36 9.37 24.52.04 33.9L131.49 256l154.02 154.75c9.34 9.38 9.32 24.54-.04 33.9l-22.67 22.67c-9.37 9.37-24.57 9.37-33.94 0L34.52 272.97c-9.37-9.37-9.37-24.57 0-33.94z"></path></svg>';
		$args['next_text'] = '<span class="screen-reader-text">' . __( 'Next Page', 'kadence-blocks-pro' ) . '</span><svg style="display:inline-block;vertical-align:middle" aria-hidden="true" class="kt-blocks-pagination-right-svg" viewBox="0 0 320 512" height="14" width="8" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path></svg>';
		echo '<div class="kt-blocks-page-nav">';
			the_posts_pagination(
				apply_filters(
					'kadence_blocks_pagination_args',
					$args
				)
			);
		echo '</div>';
	}
	public function kt_blocks_pro_hex2rgba( $color, $opacity = null ) {
		if ( strpos( $color, 'palette' ) === 0 ) {
			$color = 'var(--global-' . $color . ')';
		} else if ( isset( $opacity ) && is_numeric( $opacity ) ) {
			$color = $this->kadence_blocks_pro_hex2rgba( $color, $opacity );
		}
		return $color;
	}
	public function kadence_blocks_pro_hex2rgba( $hex, $alpha ) {
		if ( empty( $hex ) ) {
			return '';
		}
		$hex = str_replace( '#', '', $hex );

		if ( strlen( $hex ) == 3 ) {
			$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}
		$rgba = 'rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $alpha . ')';
		return $rgba;
	}
	/**
	 * Builds CSS for Post Grid block.
	 *
	 * @param array  $attr the blocks attr.
	 * @param string $unique_id the blocks attr ID.
	 */
	public function output_css( $attributes, $unique_id ) {
		if ( ! class_exists( 'Kadence_Blocks_Pro_CSS' ) ) {
			return '';
		}
		$css                    = new Kadence_Blocks_Pro_CSS();
		$media_query            = array();
		$media_query['mobile']  = apply_filters( 'kadence_mobile_media_query', '(max-width: 767px)' );
		$media_query['tablet']  = apply_filters( 'kadence_tablet_media_query', '(max-width: 1024px)' );
		$media_query['desktop'] = apply_filters( 'kadence_desktop_media_query', '(min-width: 1025px)' );
		$layout = isset( $attributes['layout'] ) ? $attributes['layout'] : 'grid';
		// Image.
		if ( isset( $attributes['imagePadding'] ) && isset( $attributes['imagePadding'][0] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kadence-post-image' );
			if ( is_numeric( $attributes['imagePadding'][0] ) ) {
				$css->add_property( 'padding-top', $attributes['imagePadding'][0] . ( isset( $attributes['imagePaddingType'] ) && ! empty( $attributes['imagePaddingType'] ) ? $attributes['imagePaddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['imagePadding'][1] ) ) {
				$css->add_property( 'padding-right', $attributes['imagePadding'][1] . ( isset( $attributes['imagePaddingType'] ) && ! empty( $attributes['imagePaddingType'] ) ? $attributes['imagePaddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['imagePadding'][2] ) ) {
				$css->add_property( 'padding-bottom', $attributes['imagePadding'][2] . ( isset( $attributes['imagePaddingType'] ) && ! empty( $attributes['imagePaddingType'] ) ? $attributes['imagePaddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['imagePadding'][3] ) ) {
				$css->add_property( 'padding-left', $attributes['imagePadding'][3] . ( isset( $attributes['imagePaddingType'] ) && ! empty( $attributes['imagePaddingType'] ) ? $attributes['imagePaddingType'] : 'px' ) );
			}
		}
		if ( isset( $attributes['imageTabletPadding'] ) ) {
			// Tablet.
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kadence-post-image' );
			if ( isset( $attributes['imageTabletPadding'] ) && is_array( $attributes['imageTabletPadding'] ) ) {
				if ( is_numeric( $attributes['imageTabletPadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['imageTabletPadding'][0] . ( ! empty( $attributes['imagePaddingType'] ) ? $attributes['imagePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['imageTabletPadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['imageTabletPadding'][1] . ( ! empty( $attributes['imagePaddingType'] ) ? $attributes['imagePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['imageTabletPadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['imageTabletPadding'][2] . ( ! empty( $attributes['imagePaddingType'] ) ? $attributes['imagePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['imageTabletPadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['imageTabletPadding'][3] . ( ! empty( $attributes['imagePaddingType'] ) ? $attributes['imagePaddingType'] : 'px' ) );
				}
			}
			$css->stop_media_query();
		}
		if ( isset( $attributes['imageMobilePadding'] ) ) {
			// Mobile.
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kadence-post-image' );
			if ( isset( $attributes['imageMobilePadding'] ) && is_array( $attributes['imageMobilePadding'] ) ) {
				if ( is_numeric( $attributes['imageMobilePadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['imageMobilePadding'][0] . ( ! empty( $attributes['imagePaddingType'] ) ? $attributes['imagePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['imageMobilePadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['imageMobilePadding'][1] . ( ! empty( $attributes['imagePaddingType'] ) ? $attributes['imagePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['imageMobilePadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['imageMobilePadding'][2] . ( ! empty( $attributes['imagePaddingType'] ) ? $attributes['imagePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['imageMobilePadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['imageMobilePadding'][3] . ( ! empty( $attributes['imagePaddingType'] ) ? $attributes['imagePaddingType'] : 'px' ) );
				}
			}
			$css->stop_media_query();
		}
		if ( isset( $attributes['sideImageWidth'] ) && isset( $attributes['alignImage'] ) && 'left' === $attributes['alignImage'] ) {
			if ( isset( $attributes['displayImage'] ) && false === $attributes['displayImage'] ) {
				// Don't want to add this for no image.
			} else {
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-feat-image-align-left' );
				$css->add_property( 'grid-template-columns', $attributes['sideImageWidth'] . '% auto' );
			}
		}
		// Columns.
		// if ( isset( $attributes['columnGap'] ) && isset( $attributes['layout'] ) && 'carousel' === $attributes['layout'] ) {
		// 	$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-post-slider-item' );
		// 	$css .= '.kt-post-loop' . $unique_id . ' .kt-post-slider-item {';
		// 		$css .= 'padding:0 ' . $attributes['columnGap'] / 2 . 'px;';
		// 	$css .= '}';
		// 	$css .= '.kt-post-loop' . $unique_id . ' .kt-post-grid-layout-carousel-wrap {';
		// 		$css .= 'margin-left:-' . $attributes['columnGap'] / 2 . 'px;';
		// 		$css .= 'margin-right:-' . $attributes['columnGap'] / 2 . 'px;';
		// 	$css .= '}';
		// 	$css .= '.kt-post-loop' . $unique_id . ' .kt-post-grid-layout-carousel-wrap .slick-prev {';
		// 		$css .= 'left:' . $attributes['columnGap'] / 2 . 'px;';
		// 	$css .= '}';
		// 	$css .= '.kt-post-loop' . $unique_id . ' .kt-post-grid-layout-carousel-wrap .slick-next {';
		// 		$css .= 'right:' . $attributes['columnGap'] / 2 . 'px;';
		// 	$css .= '}';
		// }
		if ( isset( $attributes['columnGap'] ) && 'masonry' === $layout ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-post-grid-layout-masonry-wrap .kt-post-masonry-item' );
			$css->add_property( 'padding-left', $attributes['columnGap'] / 2 . ( ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px' ) );
			$css->add_property( 'padding-right', $attributes['columnGap'] / 2 . ( ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px' ) );
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-post-grid-layout-masonry-wrap' );
			$css->add_property( 'margin-left', '-' . $attributes['columnGap'] / 2 . ( ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px' ) );
			$css->add_property( 'margin-right', '-' . $attributes['columnGap'] / 2 . ( ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px' ) );
		}
		if ( 'grid' === $layout && isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] ) {
			if ( isset( $attributes['columnGap'] ) ) {
				$css->set_selector( '.kt-post-loop' . $unique_id . '.kb-filter-enabled .kt-post-grid-layout-grid-wrap .kt-post-masonry-item' );
				$css->add_property( 'padding-left', $attributes['columnGap'] / 2 . ( ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px' ) );
				$css->add_property( 'padding-right', $attributes['columnGap'] / 2 . ( ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px' ) );
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-post-grid-layout-masonry-wrap' );
				$css->add_property( 'margin-left', '-' . $attributes['columnGap'] / 2 . ( ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px' ) );
				$css->add_property( 'margin-right', '-' . $attributes['columnGap'] / 2 . ( ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px' ) );
			}
			if ( isset( $attributes['rowGap'] ) ) {
				$css->set_selector( '.kt-post-loop' . $unique_id . '.kb-filter-enabled .kt-post-grid-layout-grid-wrap .kt-post-masonry-item' );
				$css->add_property( 'padding-bottom', $attributes['rowGap'] . ( ! empty( $attributes['rowGapUnit'] ) ? $attributes['rowGapUnit'] : 'px' ) );
			}
		}
		if ( isset( $attributes['rowGap'] ) && 'masonry' === $layout ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-post-grid-layout-masonry-wrap .kt-post-masonry-item' );
			$css->add_property( 'padding-bottom', $attributes['rowGap'] . ( ! empty( $attributes['rowGapUnit'] ) ? $attributes['rowGapUnit'] : 'px' ) );
		}
		if ( ( isset( $attributes['columnGap'] ) || isset( $attributes['rowGap'] ) ) && ( 'grid' === $layout || 'carousel' === $layout ) ) {
			$rowgap = ( isset( $attributes['rowGap'] ) ? $attributes['rowGap'] : '30' );
			$columngap = ( isset( $attributes['columnGap'] ) ? $attributes['columnGap'] : '30' );
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-post-grid-wrap' );
			$css->add_property( 'gap', $rowgap .  ( ! empty( $attributes['rowGapUnit'] ) ? $attributes['rowGapUnit'] : 'px' ) . ' ' . $columngap . ( ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px' ) );
		}
		if ( ( isset( $attributes['columnGapTablet'] ) || isset( $attributes['rowGapTablet'] ) ) && ( 'grid' === $layout || 'carousel' === $layout ) ) {
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-post-grid-wrap' );
			if ( isset( $attributes['columnGapTablet'] ) && is_numeric( $attributes['columnGapTablet'] ) ) {
				$css->add_property( 'column-gap', $attributes['columnGapTablet'] . ( ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px' ) );
			}
			if ( isset( $attributes['rowGapTablet'] ) && is_numeric( $attributes['rowGapTablet'] ) ) {
				$css->add_property( 'row-gap', $attributes['rowGapTablet'] . ( ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px' ) );
			}
			$css->stop_media_query();
		}
		if ( ( isset( $attributes['columnGapMobile'] ) || isset( $attributes['rowGapMobile'] ) ) && ( 'grid' === $layout || 'carousel' === $layout ) ) {
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-post-grid-wrap' );
			if ( isset( $attributes['columnGapMobile'] ) && is_numeric( $attributes['columnGapMobile'] ) ) {
				$css->add_property( 'column-gap', $attributes['columnGapMobile'] . ( ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px' ) );
			}
			if ( isset( $attributes['rowGapMobile'] ) && is_numeric( $attributes['rowGapMobile'] ) ) {
				$css->add_property( 'row-gap', $attributes['rowGapMobile'] . ( ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px' ) );
			}
			$css->stop_media_query();
		}
		// Container.
		if ( isset( $attributes['backgroundColor'] ) || isset( $attributes['borderColor'] ) || isset( $attributes['borderWidth'] ) || isset( $attributes['borderRadius'] ) || ( isset( $attributes['displayShadow'] ) && true == $attributes['displayShadow'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item' );
			if ( isset( $attributes['backgroundColor'] ) ) {
				$css->add_property( 'background-color', $css->render_color( $attributes['backgroundColor'] ) );
			}
			if ( isset( $attributes['borderColor'] ) && ! empty( $attributes['borderColor'] ) ) {
				$bcoloralpha = ( isset( $attributes['borderOpacity'] ) ? $attributes['borderOpacity'] : 1 );
				$css->add_property( 'border-color', $css->render_color(  $attributes['borderColor'], $bcoloralpha ) );
			}
			if ( isset( $attributes['borderWidth'] ) && is_array( $attributes['borderWidth'] ) ) {
				if ( is_numeric( $attributes['borderWidth'][0] ) ) {
					$css->add_property( 'border-top-width', $attributes['borderWidth'][0] . 'px' );
				}
				if ( is_numeric( $attributes['borderWidth'][1] ) ) {
					$css->add_property( 'border-right-width', $attributes['borderWidth'][1] . 'px' );
				}
				if ( is_numeric( $attributes['borderWidth'][2] ) ) {
					$css->add_property( 'border-bottom-width', $attributes['borderWidth'][2] . 'px' );
				}
				if ( is_numeric( $attributes['borderWidth'][3] ) ) {
					$css->add_property( 'border-left-width', $attributes['borderWidth'][3] . 'px' );
				}
			}
			if ( isset( $attributes['borderRadius'] ) && is_array( $attributes['borderRadius'] ) ) {
				if ( ! empty( $attributes['borderRadius'][0] ) || ! empty( $attributes['borderRadius'][0] ) || ! empty( $attributes['borderRadius'][0] ) || ! empty( $attributes['borderRadius'][0] ) ) {
					$css->add_property( 'overflow', 'hidden' );
				}
				if ( is_numeric( $attributes['borderRadius'][0] ) ) {
					$css->add_property( 'border-top-left-radius', $attributes['borderRadius'][0] . 'px' );
				}
				if ( is_numeric( $attributes['borderRadius'][1] ) ) {
					$css->add_property( 'border-top-right-radius', $attributes['borderRadius'][1] . 'px' );
				}
				if ( is_numeric( $attributes['borderRadius'][2] ) ) {
					$css->add_property( 'border-bottom-right-radius', $attributes['borderRadius'][2] . 'px' );
				}
				if ( is_numeric( $attributes['borderRadius'][3] ) ) {
					$css->add_property( 'border-bottom-left-radius', $attributes['borderRadius'][3] . 'px' );
				}
			}
			if ( isset( $attributes['displayShadow'] ) && true == $attributes['displayShadow'] && isset( $attributes['shadow'] ) && is_array( $attributes['shadow'] ) && isset( $attributes['shadow'][0] ) && is_array( $attributes['shadow'][0] ) ) {
				$css->add_property( 'box-shadow', ( isset( $attributes['shadow'][0]['inset'] ) && true === $attributes['shadow'][0]['inset'] ? 'inset ' : '' ) . ( isset( $attributes['shadow'][0]['hOffset'] ) && is_numeric( $attributes['shadow'][0]['hOffset'] ) ? $attributes['shadow'][0]['hOffset'] : '0' ) . 'px ' . ( isset( $attributes['shadow'][0]['vOffset'] ) && is_numeric( $attributes['shadow'][0]['vOffset'] ) ? $attributes['shadow'][0]['vOffset'] : '0' ) . 'px ' . ( isset( $attributes['shadow'][0]['blur'] ) && is_numeric( $attributes['shadow'][0]['blur'] ) ? $attributes['shadow'][0]['blur'] : '14' ) . 'px ' . ( isset( $attributes['shadow'][0]['spread'] ) && is_numeric( $attributes['shadow'][0]['spread'] ) ? $attributes['shadow'][0]['spread'] : '0' ) . 'px ' . kbp_post_grid_color_output( ( isset( $attributes['shadow'][0]['color'] ) && ! empty( $attributes['shadow'][0]['color'] ) ? $attributes['shadow'][0]['color'] : '#000000' ), ( isset( $attributes['shadow'][0]['opacity'] ) && is_numeric( $attributes['shadow'][0]['opacity'] ) ? $attributes['shadow'][0]['opacity'] : 0.2 ) ) );
			} else if ( isset( $attributes['displayShadow'] ) && true == $attributes['displayShadow'] && ! isset( $attributes['shadow'] ) ) {
				$css->add_property( 'box-shadow', '0px 0px 14px 0px rgba(0, 0, 0, 0.2)' );
			}
		}
		if ( isset( $attributes['containerPadding'] ) && isset( $attributes['containerPadding'][0] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .kt-blocks-post-grid-item-inner' );
			if ( is_numeric( $attributes['containerPadding'][0] ) ) {
				$css->add_property( 'padding-top', $attributes['containerPadding'][0] . ( ! empty( $attributes['containerPaddingType'] ) ? $attributes['containerPaddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['containerPadding'][1] ) ) {
				$css->add_property( 'padding-right', $attributes['containerPadding'][1] . ( ! empty( $attributes['containerPaddingType'] ) ? $attributes['containerPaddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['containerPadding'][2] ) ) {
				$css->add_property( 'padding-bottom', $attributes['containerPadding'][2] . ( ! empty( $attributes['containerPaddingType'] ) ? $attributes['containerPaddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['containerPadding'][3] ) ) {
				$css->add_property( 'padding-left', $attributes['containerPadding'][3] . ( ! empty( $attributes['containerPaddingType'] ) ? $attributes['containerPaddingType'] : 'px' ) );
			}
		}
		if ( isset( $attributes['containerTabletPadding'] ) || isset( $attributes['containerTabletMargin'] ) ) {
			// Tablet.
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .kt-blocks-post-grid-item-inner' );
			if ( isset( $attributes['containerTabletPadding'] ) && is_array( $attributes['containerTabletPadding'] ) ) {
				if ( is_numeric( $attributes['containerTabletPadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['containerTabletPadding'][0] . ( ! empty( $attributes['containerPaddingType'] ) ? $attributes['containerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['containerTabletPadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['containerTabletPadding'][1] . ( ! empty( $attributes['containerPaddingType'] ) ? $attributes['containerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['containerTabletPadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['containerTabletPadding'][2] . ( ! empty( $attributes['containerPaddingType'] ) ? $attributes['containerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['containerTabletPadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['containerTabletPadding'][3] . ( ! empty( $attributes['containerPaddingType'] ) ? $attributes['containerPaddingType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['containerTabletMargin'] ) && is_array( $attributes['containerTabletMargin'] ) ) {
				if ( is_numeric( $attributes['containerTabletMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['containerTabletMargin'][0] . ( ! empty( $attributes['containerMarginType'] ) ? $attributes['containerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['containerTabletMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['containerTabletMargin'][1] . ( ! empty( $attributes['containerMarginType'] ) ? $attributes['containerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['containerTabletMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['containerTabletMargin'][2] . ( ! empty( $attributes['containerMarginType'] ) ? $attributes['containerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['containerTabletMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['containerTabletMargin'][3] . ( ! empty( $attributes['containerMarginType'] ) ? $attributes['containerMarginType'] : 'px' ) );
				}
			}
			$css->stop_media_query();
		}
		if ( isset( $attributes['containerMobilePadding'] ) || isset( $attributes['containerMobileMargin'] ) ) {
			// Mobile.
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .kt-blocks-post-grid-item-inner' );
			if ( isset( $attributes['containerMobilePadding'] ) && is_array( $attributes['containerMobilePadding'] ) ) {
				if ( is_numeric( $attributes['containerMobilePadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['containerMobilePadding'][0] . ( ! empty( $attributes['containerPaddingType'] ) ? $attributes['containerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['containerMobilePadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['containerMobilePadding'][1] . ( ! empty( $attributes['containerPaddingType'] ) ? $attributes['containerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['containerMobilePadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['containerMobilePadding'][2] . ( ! empty( $attributes['containerPaddingType'] ) ? $attributes['containerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['containerMobilePadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['containerMobilePadding'][3] . ( ! empty( $attributes['containerPaddingType'] ) ? $attributes['containerPaddingType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['containerMobileMargin'] ) && is_array( $attributes['containerMobileMargin'] ) ) {
				if ( is_numeric( $attributes['containerMobileMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['containerMobileMargin'][0] . ( ! empty( $attributes['containerMarginType'] ) ? $attributes['containerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['containerMobileMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['containerMobileMargin'][1] . ( ! empty( $attributes['containerMarginType'] ) ? $attributes['containerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['containerMobileMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['containerMobileMargin'][2] . ( ! empty( $attributes['containerMarginType'] ) ? $attributes['containerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['containerMobileMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['containerMobileMargin'][3] . ( ! empty( $attributes['containerMarginType'] ) ? $attributes['containerMarginType'] : 'px' ) );
				}
			}
			$css->stop_media_query();
		}
		if ( isset( $attributes['textAlign'] ) && ! empty( $attributes['textAlign'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .kt-blocks-post-grid-item-inner' );
			$css->add_property( 'text-align', $attributes['textAlign'] );
			if ( 'center' === $attributes['textAlign'] ) {
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .kt-blocks-post-top-meta' );
				$css->add_property( 'justify-content', $attributes['textAlign'] );
			}
			if ( 'right' === $attributes['textAlign'] ) {
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .kt-blocks-post-top-meta' );
				$css->add_property( 'justify-content', 'flex-end' );
			}
			if ( 'justify' === $attributes['textAlign'] ) {
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .kt-blocks-post-top-meta' );
				$css->add_property( 'justify-content', 'space-between' );
			}
		}
		// Header
		if ( isset( $attributes['headerBG'] ) || isset( $attributes['headerPadding'] ) || isset( $attributes['headerMargin'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item header' );
			if ( isset( $attributes['headerBG'] ) ) {
				$headerbgcoloralpha = ( isset( $attributes['headerBGOpacity'] ) ? $attributes['headerBGOpacity'] : 1 );
				$css->add_property( 'background-color', $css->render_color( $attributes['headerBG'], $headerbgcoloralpha ) );
			}
			if ( isset( $attributes['headerPadding'] ) && is_array( $attributes['headerPadding'] ) ) {
				if ( is_numeric( $attributes['headerPadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['headerPadding'][0] . ( ! empty( $attributes['headerPaddingType'] ) ? $attributes['headerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['headerPadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['headerPadding'][1] . ( ! empty( $attributes['headerPaddingType'] ) ? $attributes['headerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['headerPadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['headerPadding'][2] . ( ! empty( $attributes['headerPaddingType'] ) ? $attributes['headerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['headerPadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['headerPadding'][3] . ( ! empty( $attributes['headerPaddingType'] ) ? $attributes['headerPaddingType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['headerMargin'] ) && is_array( $attributes['headerMargin'] ) ) {
				if ( is_numeric( $attributes['headerMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['headerMargin'][0] . ( ! empty( $attributes['headerMarginType'] ) ? $attributes['headerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['headerMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['headerMargin'][1] . ( ! empty( $attributes['headerMarginType'] ) ? $attributes['headerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['headerMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['headerMargin'][2] . ( ! empty( $attributes['headerMarginType'] ) ? $attributes['headerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['headerMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['headerMargin'][3] . ( ! empty( $attributes['headerMarginType'] ) ? $attributes['headerMarginType'] : 'px' ) );
				}
			}
		}
		if ( isset( $attributes['headerTabletPadding'] ) || isset( $attributes['headerTabletMargin'] ) ) {
			// Tablet.
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item header' );
			if ( isset( $attributes['headerTabletPadding'] ) && is_array( $attributes['headerTabletPadding'] ) ) {
				if ( is_numeric( $attributes['headerTabletPadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['headerTabletPadding'][0] . ( ! empty( $attributes['headerPaddingType'] ) ? $attributes['headerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['headerTabletPadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['headerTabletPadding'][1] . ( ! empty( $attributes['headerPaddingType'] ) ? $attributes['headerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['headerTabletPadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['headerTabletPadding'][2] . ( ! empty( $attributes['headerPaddingType'] ) ? $attributes['headerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['headerTabletPadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['headerTabletPadding'][3] . ( ! empty( $attributes['headerPaddingType'] ) ? $attributes['headerPaddingType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['headerTabletMargin'] ) && is_array( $attributes['headerTabletMargin'] ) ) {
				if ( is_numeric( $attributes['headerTabletMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['headerTabletMargin'][0] . ( ! empty( $attributes['headerMarginType'] ) ? $attributes['headerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['headerTabletMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['headerTabletMargin'][1] . ( ! empty( $attributes['headerMarginType'] ) ? $attributes['headerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['headerTabletMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['headerTabletMargin'][2] . ( ! empty( $attributes['headerMarginType'] ) ? $attributes['headerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['headerTabletMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['headerTabletMargin'][3] . ( ! empty( $attributes['headerMarginType'] ) ? $attributes['headerMarginType'] : 'px' ) );
				}
			}
			$css->stop_media_query();
		}
		if ( isset( $attributes['headerMobilePadding'] ) || isset( $attributes['headerMobileMargin'] ) ) {
			// Mobile.
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item header' );
			if ( isset( $attributes['headerMobilePadding'] ) && is_array( $attributes['headerMobilePadding'] ) ) {
				if ( is_numeric( $attributes['headerMobilePadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['headerMobilePadding'][0] . ( ! empty( $attributes['headerPaddingType'] ) ? $attributes['headerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['headerMobilePadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['headerMobilePadding'][1] . ( ! empty( $attributes['headerPaddingType'] ) ? $attributes['headerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['headerMobilePadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['headerMobilePadding'][2] . ( ! empty( $attributes['headerPaddingType'] ) ? $attributes['headerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['headerMobilePadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['headerMobilePadding'][3] . ( ! empty( $attributes['headerPaddingType'] ) ? $attributes['headerPaddingType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['headerMobileMargin'] ) && is_array( $attributes['headerMobileMargin'] ) ) {
				if ( is_numeric( $attributes['headerMobileMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['headerMobileMargin'][0] . ( ! empty( $attributes['headerMarginType'] ) ? $attributes['headerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['headerMobileMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['headerMobileMargin'][1] . ( ! empty( $attributes['headerMarginType'] ) ? $attributes['headerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['headerMobileMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['headerMobileMargin'][2] . ( ! empty( $attributes['headerMarginType'] ) ? $attributes['headerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['headerMobileMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['headerMobileMargin'][3] . ( ! empty( $attributes['headerMarginType'] ) ? $attributes['headerMarginType'] : 'px' ) );
				}
			}
			$css->stop_media_query();
		}
		// Above Title.
		if ( isset( $attributes['aboveColor'] ) || isset( $attributes['aboveFont'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .kt-blocks-above-categories' );
			if ( isset( $attributes['aboveColor'] ) ) {
				$css->add_property( 'color', $css->render_color( $attributes['aboveColor'] ) );
			}
			if ( isset( $attributes['aboveFont'] ) && is_array( $attributes['aboveFont'] ) && isset( $attributes['aboveFont'][0] ) && is_array( $attributes['aboveFont'][0] ) ) {
				$above_font = $attributes['aboveFont'][0];
				if ( isset( $above_font['size'] ) && isset( $above_font['size'][0] ) && is_numeric( $above_font['size'][0] ) ) {
					$css->add_property( 'font-size', $above_font['size'][0] . ( isset( $above_font['sizeType'] ) && ! empty( $above_font['sizeType'] ) ? $above_font['sizeType'] : 'px' ) );
				}
				if ( isset( $above_font['lineHeight'] ) && isset( $above_font['lineHeight'][0] ) && is_numeric( $above_font['lineHeight'][0] ) ) {
					$css->add_property( 'line-height', $above_font['lineHeight'][0] . ( isset( $above_font['lineType'] ) && ! empty( $above_font['lineType'] ) ? $above_font['lineType'] : 'px' ) );
				}
				if ( isset( $above_font['letterSpacing'] ) && is_numeric( $above_font['letterSpacing'] ) ) {
					$css->add_property( 'letter-spacing', $above_font['letterSpacing'] . 'px' );
				}
				if ( isset( $above_font['family'] ) && ! empty( $above_font['family'] ) ) {
					$google = isset( $above_font['google'] ) && $above_font['google'] ? true : false;
					$google = $google && ( isset( $above_font['loadGoogle'] ) && $above_font['loadGoogle'] || ! isset( $above_font['loadGoogle'] ) ) ? true : false;
					$css->add_property( 'font-family', $css->render_font_family( $above_font['family'], $google, ( isset( $above_font['variation'] ) ? $above_font['variation'] : '' ), ( isset( $above_font['subset'] ) ? $above_font['subset'] : '' ) ) );
				}
				if ( isset( $above_font['weight'] ) && ! empty( $above_font['weight'] ) ) {
					$css->add_property( 'font-weight', $css->render_string( $above_font['weight'] ) );
				}
				if ( isset( $above_font['style'] ) && ! empty( $above_font['style'] ) ) {
					$css->add_property( 'font-style', $css->render_string( $above_font['style'] ) );
				}
				if ( isset( $above_font['textTransform'] ) && ! empty( $above_font['textTransform'] ) ) {
					$css->add_property( 'text-transform', $css->render_string( $above_font['textTransform'] ) );
				}
			}
			if ( isset( $attributes['aboveFont'] ) && is_array( $attributes['aboveFont'] ) && isset( $attributes['aboveFont'][0] ) && is_array( $attributes['aboveFont'][0] ) && ( ( isset( $attributes['aboveFont'][0]['size'] ) && is_array( $attributes['aboveFont'][0]['size'] ) && isset( $attributes['aboveFont'][0]['size'][1] ) && ! empty( $attributes['aboveFont'][0]['size'][1] ) ) || ( isset( $attributes['aboveFont'][0]['lineHeight'] ) && is_array( $attributes['aboveFont'][0]['lineHeight'] ) && isset( $attributes['aboveFont'][0]['lineHeight'][1] ) && ! empty( $attributes['aboveFont'][0]['lineHeight'][1] ) ) ) ) {
				$above_font = $attributes['aboveFont'][0];
				// Tablet.
				$css->start_media_query( $media_query['tablet'] );
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .kt-blocks-above-categories' );
				if ( isset( $above_font['size'] ) && isset( $above_font['size'][1] ) && is_numeric( $above_font['size'][1] ) ) {
					$css->add_property( 'font-size', $above_font['size'][1] . ( isset( $above_font['sizeType'] ) && ! empty( $above_font['sizeType'] ) ? $above_font['sizeType'] : 'px' ) );
				}
				if ( isset( $above_font['lineHeight'] ) && isset( $above_font['lineHeight'][1] ) && is_numeric( $above_font['lineHeight'][1] ) ) {
					$css->add_property( 'line-height', $above_font['lineHeight'][1] . ( isset( $above_font['lineType'] ) && ! empty( $above_font['lineType'] ) ? $above_font['lineType'] : 'px' ) );
				}
				$css->stop_media_query();
			}
			if ( isset( $attributes['aboveFont'] ) && is_array( $attributes['aboveFont'] ) && isset( $attributes['aboveFont'][0] ) && is_array( $attributes['aboveFont'][0] ) && ( ( isset( $attributes['aboveFont'][0]['size'] ) && is_array( $attributes['aboveFont'][0]['size'] ) && isset( $attributes['aboveFont'][0]['size'][2] ) && ! empty( $attributes['aboveFont'][0]['size'][2] ) ) || ( isset( $attributes['aboveFont'][0]['lineHeight'] ) && is_array( $attributes['aboveFont'][0]['lineHeight'] ) && isset( $attributes['aboveFont'][0]['lineHeight'][2] ) && ! empty( $attributes['aboveFont'][0]['lineHeight'][2] ) ) ) ) {
				$above_font = $attributes['aboveFont'][0];
				// Mobile.
				$css->start_media_query( $media_query['mobile'] );
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .kt-blocks-above-categories' );
				if ( isset( $above_font['size'] ) && isset( $above_font['size'][2] ) && is_numeric( $above_font['size'][2] ) ) {
					$css->add_property( 'font-size', $above_font['size'][2] . ( isset( $above_font['sizeType'] ) && ! empty( $above_font['sizeType'] ) ? $above_font['sizeType'] : 'px' ) );
				}
				if ( isset( $above_font['lineHeight'] ) && isset( $above_font['lineHeight'][2] ) && is_numeric( $above_font['lineHeight'][2] ) ) {
					$css->add_property( 'line-height', $above_font['lineHeight'][2] . ( isset( $above_font['lineType'] ) && ! empty( $above_font['lineType'] ) ? $above_font['lineType'] : 'px' ) );
				}
				$css->stop_media_query();
			}
		}
		if ( isset( $attributes['aboveLinkColor'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .kt-blocks-above-categories a' );
			$css->add_property( 'color', $css->render_color( $attributes['aboveLinkColor'] ) );
		}
		if ( isset( $attributes['aboveLinkHoverColor'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .kt-blocks-above-categories a:hover' );
			$css->add_property( 'color', $css->render_color( $attributes['aboveLinkHoverColor'] ) );
		}
		// Title
		if ( isset( $attributes['titleColor'] ) || isset( $attributes['titleFont'] ) || isset( $attributes['titlePadding'] ) || isset( $attributes['titleMargin'] ) || isset( $attributes['titleTabletPadding'] ) || isset( $attributes['titleTabletMargin'] ) || isset( $attributes['titleMobilePadding'] ) || isset( $attributes['titleMobileMargin'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .entry-title' );
			if ( isset( $attributes['titleColor'] ) && ! empty( $attributes['titleColor'] ) ) {
				$css->add_property( 'color', $css->render_color( $attributes['titleColor'] ) );
			}
			if ( isset( $attributes['titlePadding'] ) && is_array( $attributes['titlePadding'] ) ) {
				if ( is_numeric( $attributes['titlePadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['titlePadding'][0] . ( ! empty( $attributes['titlePaddingType'] ) ? $attributes['titlePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['titlePadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['titlePadding'][1] . ( ! empty( $attributes['titlePaddingType'] ) ? $attributes['titlePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['titlePadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['titlePadding'][2] . ( ! empty( $attributes['titlePaddingType'] ) ? $attributes['titlePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['titlePadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['titlePadding'][3] . ( ! empty( $attributes['titlePaddingType'] ) ? $attributes['titlePaddingType'] : 'px' ) );
				}
			} else if ( ! isset( $attributes['titlePadding'] ) ) {
				$css->add_property( 'padding', '5px 0px 10px 0px' );
			}
			if ( isset( $attributes['titleMargin'] ) && is_array( $attributes['titleMargin'] ) ) {
				if ( is_numeric( $attributes['titleMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['titleMargin'][0] . ( ! empty( $attributes['titleMarginType'] ) ? $attributes['titleMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['titleMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['titleMargin'][1] . ( ! empty( $attributes['titleMarginType'] ) ? $attributes['titleMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['titleMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['titleMargin'][2] . ( ! empty( $attributes['titleMarginType'] ) ? $attributes['titleMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['titleMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['titleMargin'][3] . ( ! empty( $attributes['titleMarginType'] ) ? $attributes['titleMarginType'] : 'px' ) );
				}
			} else if ( ! isset( $attributes['titleMargin'] ) ) {
				$css->add_property( 'margin', '0px 0px 0px 0px' );
			}
			if ( isset( $attributes['titleFont'] ) && is_array( $attributes['titleFont'] ) && isset( $attributes['titleFont'][0] ) && is_array( $attributes['titleFont'][0] ) ) {
				$title_font = $attributes['titleFont'][0];
				if ( isset( $title_font['size'] ) && isset( $title_font['size'][0] ) && is_numeric( $title_font['size'][0] ) ) {
					$css->add_property( 'font-size', $title_font['size'][0] . ( isset( $title_font['sizeType'] ) && ! empty( $title_font['sizeType'] ) ? $title_font['sizeType'] : 'px' ) );
				}
				if ( isset( $title_font['lineHeight'] ) && isset( $title_font['lineHeight'][0] ) && is_numeric( $title_font['lineHeight'][0] ) ) {
					$css->add_property( 'line-height', $title_font['lineHeight'][0] . ( isset( $title_font['lineType'] ) && ! empty( $title_font['lineType'] ) ? $title_font['lineType'] : 'px' ) );
				}
				if ( isset( $title_font['letterSpacing'] ) && is_numeric( $title_font['letterSpacing'] ) ) {
					$css->add_property( 'letter-spacing', $title_font['letterSpacing'] . 'px' );
				}
				if ( isset( $title_font['family'] ) && ! empty( $title_font['family'] ) ) {
					$google = isset( $title_font['google'] ) && $title_font['google'] ? true : false;
					$google = $google && ( isset( $title_font['loadGoogle'] ) && $title_font['loadGoogle'] || ! isset( $title_font['loadGoogle'] ) ) ? true : false;
					$css->add_property( 'font-family', $css->render_font_family( $title_font['family'], $google, ( isset( $title_font['variation'] ) ? $title_font['variation'] : '' ), ( isset( $title_font['subset'] ) ? $title_font['subset'] : '' ) ) );
				}
				if ( isset( $title_font['weight'] ) && ! empty( $title_font['weight'] ) ) {
					$css->add_property( 'font-weight', $css->render_string( $title_font['weight'] ) );
				}
				if ( isset( $title_font['style'] ) && ! empty( $title_font['style'] ) ) {
					$css->add_property( 'font-style', $css->render_string( $title_font['style'] ) );
				}
				if ( isset( $title_font['textTransform'] ) && ! empty( $title_font['textTransform'] ) ) {
					$css->add_property( 'text-transform', $css->render_string( $title_font['textTransform'] ) );
				}
			}
			// Tablet.
			$css->start_media_query( $media_query['tablet'] );
			if ( isset( $attributes['titleFont'] ) && is_array( $attributes['titleFont'] ) && isset( $attributes['titleFont'][0] ) && is_array( $attributes['titleFont'][0] ) && ( ( isset( $attributes['titleFont'][0]['size'] ) && is_array( $attributes['titleFont'][0]['size'] ) && isset( $attributes['titleFont'][0]['size'][1] ) && ! empty( $attributes['titleFont'][0]['size'][1] ) ) || ( isset( $attributes['titleFont'][0]['lineHeight'] ) && is_array( $attributes['titleFont'][0]['lineHeight'] ) && isset( $attributes['titleFont'][0]['lineHeight'][1] ) && ! empty( $attributes['titleFont'][0]['lineHeight'][1] ) ) ) ) {
				$title_font = $attributes['titleFont'][0];
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .entry-title' );
				if ( isset( $title_font['size'] ) && isset( $title_font['size'][1] ) && is_numeric( $title_font['size'][1] ) ) {
					$css->add_property( 'font-size', $title_font['size'][1] . ( isset( $title_font['sizeType'] ) && ! empty( $title_font['sizeType'] ) ? $title_font['sizeType'] : 'px' ) );
				}
				if ( isset( $title_font['lineHeight'] ) && isset( $title_font['lineHeight'][1] ) && is_numeric( $title_font['lineHeight'][1] ) ) {
					$css->add_property( 'line-height', $title_font['lineHeight'][1] . ( isset( $title_font['lineType'] ) && ! empty( $title_font['lineType'] ) ? $title_font['lineType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['titleTabletPadding'] ) && is_array( $attributes['titleTabletPadding'] ) ) {
				if ( is_numeric( $attributes['titleTabletPadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['titleTabletPadding'][0] . ( ! empty( $attributes['titlePaddingType'] ) ? $attributes['titlePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['titleTabletPadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['titleTabletPadding'][1] . ( ! empty( $attributes['titlePaddingType'] ) ? $attributes['titlePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['titleTabletPadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['titleTabletPadding'][2] . ( ! empty( $attributes['titlePaddingType'] ) ? $attributes['titlePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['titleTabletPadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['titleTabletPadding'][3] . ( ! empty( $attributes['titlePaddingType'] ) ? $attributes['titlePaddingType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['titleTabletMargin'] ) && is_array( $attributes['titleTabletMargin'] ) ) {
				if ( is_numeric( $attributes['titleTabletMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['titleTabletMargin'][0] . ( ! empty( $attributes['titleMarginType'] ) ? $attributes['titleMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['titleTabletMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['titleTabletMargin'][1] . ( ! empty( $attributes['titleMarginType'] ) ? $attributes['titleMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['titleTabletMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['titleTabletMargin'][2] . ( ! empty( $attributes['titleMarginType'] ) ? $attributes['titleMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['titleTabletMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['titleTabletMargin'][3] . ( ! empty( $attributes['titleMarginType'] ) ? $attributes['titleMarginType'] : 'px' ) );
				}
			}
			$css->stop_media_query();
			// Mobile Title.
			$css->start_media_query( $media_query['mobile'] );
			if ( isset( $attributes['titleFont'] ) && is_array( $attributes['titleFont'] ) && isset( $attributes['titleFont'][0] ) && is_array( $attributes['titleFont'][0] ) && ( ( isset( $attributes['titleFont'][0]['size'] ) && is_array( $attributes['titleFont'][0]['size'] ) && isset( $attributes['titleFont'][0]['size'][2] ) && ! empty( $attributes['titleFont'][0]['size'][2] ) ) || ( isset( $attributes['titleFont'][0]['lineHeight'] ) && is_array( $attributes['titleFont'][0]['lineHeight'] ) && isset( $attributes['titleFont'][0]['lineHeight'][2] ) && ! empty( $attributes['titleFont'][0]['lineHeight'][2] ) ) ) ) {
				$title_font = $attributes['titleFont'][0];
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .entry-title' );
				if ( isset( $title_font['size'] ) && isset( $title_font['size'][2] ) && is_numeric( $title_font['size'][2] ) ) {
					$css->add_property( 'font-size', $title_font['size'][2] . ( isset( $title_font['sizeType'] ) && ! empty( $title_font['sizeType'] ) ? $title_font['sizeType'] : 'px' ) );
				}
				if ( isset( $title_font['lineHeight'] ) && isset( $title_font['lineHeight'][2] ) && is_numeric( $title_font['lineHeight'][2] ) ) {
					$css->add_property( 'line-height', $title_font['lineHeight'][2] . ( isset( $title_font['lineType'] ) && ! empty( $title_font['lineType'] ) ? $title_font['lineType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['titleMobilePadding'] ) && is_array( $attributes['titleMobilePadding'] ) ) {
				if ( is_numeric( $attributes['titleMobilePadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['titleMobilePadding'][0] . ( ! empty( $attributes['titlePaddingType'] ) ? $attributes['titlePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['titleMobilePadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['titleMobilePadding'][1] . ( ! empty( $attributes['titlePaddingType'] ) ? $attributes['titlePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['titleMobilePadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['titleMobilePadding'][2] . ( ! empty( $attributes['titlePaddingType'] ) ? $attributes['titlePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['titleMobilePadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['titleMobilePadding'][3] . ( ! empty( $attributes['titlePaddingType'] ) ? $attributes['titlePaddingType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['titleMobileMargin'] ) && is_array( $attributes['titleMobileMargin'] ) ) {
				if ( is_numeric( $attributes['titleMobileMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['titleMobileMargin'][0] . ( ! empty( $attributes['titleMarginType'] ) ? $attributes['titleMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['titleMobileMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['titleMobileMargin'][1] . ( ! empty( $attributes['titleMarginType'] ) ? $attributes['titleMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['titleMobileMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['titleMobileMargin'][2] . ( ! empty( $attributes['titleMarginType'] ) ? $attributes['titleMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['titleMobileMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['titleMobileMargin'][3] . ( ! empty( $attributes['titleMarginType'] ) ? $attributes['titleMarginType'] : 'px' ) );
				}
			}
			$css->stop_media_query();
		}
		if ( isset( $attributes['titleHoverColor'] ) && ! empty( $attributes['titleHoverColor'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .entry-title:hover' );
			$css->add_property( 'color', $css->render_color( $attributes['titleHoverColor'] ) );
		}
		// Meta
		if ( isset( $attributes['metaColor'] ) || isset( $attributes['metaFont'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .kt-blocks-post-top-meta' );
			if ( isset( $attributes['metaColor'] ) && ! empty( $attributes['metaColor'] ) ) {
				$css->add_property( 'color', $css->render_color( $attributes['metaColor'] ) );
			}
			if ( isset( $attributes['metaFont'] ) && is_array( $attributes['metaFont'] ) && isset( $attributes['metaFont'][0] ) && is_array( $attributes['metaFont'][0] ) ) {
				$meta_font = $attributes['metaFont'][0];
				if ( isset( $meta_font['size'] ) && isset( $meta_font['size'][0] ) && is_numeric( $meta_font['size'][0] ) ) {
					$css->add_property( 'font-size', $meta_font['size'][0] . ( isset( $meta_font['sizeType'] ) && ! empty( $meta_font['sizeType'] ) ? $meta_font['sizeType'] : 'px' ) );
				}
				if ( isset( $meta_font['lineHeight'] ) && isset( $meta_font['lineHeight'][0] ) && is_numeric( $meta_font['lineHeight'][0] ) ) {
					$css->add_property( 'line-height', $meta_font['lineHeight'][0] . ( isset( $meta_font['lineType'] ) && ! empty( $meta_font['lineType'] ) ? $meta_font['lineType'] : 'px' ) );
				}
				if ( isset( $meta_font['letterSpacing'] ) && is_numeric( $meta_font['letterSpacing'] ) ) {
					$css->add_property( 'letter-spacing', $meta_font['letterSpacing'] . 'px' );
				}
				if ( isset( $meta_font['family'] ) && ! empty( $meta_font['family'] ) ) {
					$google = isset( $meta_font['google'] ) && $meta_font['google'] ? true : false;
					$google = $google && ( isset( $meta_font['loadGoogle'] ) && $meta_font['loadGoogle'] || ! isset( $meta_font['loadGoogle'] ) ) ? true : false;
					$css->add_property( 'font-family', $css->render_font_family( $meta_font['family'], $google, ( isset( $meta_font['variation'] ) ? $meta_font['variation'] : '' ), ( isset( $meta_font['subset'] ) ? $meta_font['subset'] : '' ) ) );
				}
				if ( isset( $meta_font['weight'] ) && ! empty( $meta_font['weight'] ) ) {
					$css->add_property( 'font-weight', $css->render_string( $meta_font['weight'] ) );
				}
				if ( isset( $meta_font['style'] ) && ! empty( $meta_font['style'] ) ) {
					$css->add_property( 'font-style', $css->render_string( $meta_font['style'] ) );
				}
				if ( isset( $meta_font['textTransform'] ) && ! empty( $meta_font['textTransform'] ) ) {
					$css->add_property( 'text-transform', $css->render_string( $meta_font['textTransform'] ) );
				}
			}
			if ( isset( $attributes['metaFont'] ) && is_array( $attributes['metaFont'] ) && isset( $attributes['metaFont'][0] ) && is_array( $attributes['metaFont'][0] ) && ( ( isset( $attributes['metaFont'][0]['size'] ) && is_array( $attributes['metaFont'][0]['size'] ) && isset( $attributes['metaFont'][0]['size'][1] ) && ! empty( $attributes['metaFont'][0]['size'][1] ) ) || ( isset( $attributes['metaFont'][0]['lineHeight'] ) && is_array( $attributes['metaFont'][0]['lineHeight'] ) && isset( $attributes['metaFont'][0]['lineHeight'][1] ) && ! empty( $attributes['metaFont'][0]['lineHeight'][1] ) ) ) ) {
				$meta_font = $attributes['metaFont'][0];
				// Tablet.
				$css->start_media_query( $media_query['tablet'] );
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .kt-blocks-post-top-meta' );
				if ( isset( $meta_font['size'] ) && isset( $meta_font['size'][1] ) && is_numeric( $meta_font['size'][1] ) ) {
					$css->add_property( 'font-size', $meta_font['size'][1] . ( isset( $meta_font['sizeType'] ) && ! empty( $meta_font['sizeType'] ) ? $meta_font['sizeType'] : 'px' ) );
				}
				if ( isset( $meta_font['lineHeight'] ) && isset( $meta_font['lineHeight'][1] ) && is_numeric( $meta_font['lineHeight'][1] ) ) {
					$css->add_property( 'line-height', $meta_font['lineHeight'][1] . ( isset( $meta_font['lineType'] ) && ! empty( $meta_font['lineType'] ) ? $meta_font['lineType'] : 'px' ) );
				}
				$css->stop_media_query();
			}
			// Mobile.
			if ( isset( $attributes['metaFont'] ) && is_array( $attributes['metaFont'] ) && isset( $attributes['metaFont'][0] ) && is_array( $attributes['metaFont'][0] ) && ( ( isset( $attributes['metaFont'][0]['size'] ) && is_array( $attributes['metaFont'][0]['size'] ) && isset( $attributes['metaFont'][0]['size'][2] ) && ! empty( $attributes['metaFont'][0]['size'][2] ) ) || ( isset( $attributes['metaFont'][0]['lineHeight'] ) && is_array( $attributes['metaFont'][0]['lineHeight'] ) && isset( $attributes['metaFont'][0]['lineHeight'][2] ) && ! empty( $attributes['metaFont'][0]['lineHeight'][2] ) ) ) ) {
				$meta_font = $attributes['metaFont'][0];
				// Mobile.
				$css->start_media_query( $media_query['mobile'] );
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .kt-blocks-post-top-meta' );
				if ( isset( $meta_font['size'] ) && isset( $meta_font['size'][2] ) && is_numeric( $meta_font['size'][2] ) ) {
					$css->add_property( 'font-size', $meta_font['size'][2] . ( isset( $meta_font['sizeType'] ) && ! empty( $meta_font['sizeType'] ) ? $meta_font['sizeType'] : 'px' ) );
				}
				if ( isset( $meta_font['lineHeight'] ) && isset( $meta_font['lineHeight'][2] ) && is_numeric( $meta_font['lineHeight'][2] ) ) {
					$css->add_property( 'line-height', $meta_font['lineHeight'][2] . ( isset( $meta_font['lineType'] ) && ! empty( $meta_font['lineType'] ) ? $meta_font['lineType'] : 'px' ) );
				}
				$css->stop_media_query();
			}
		}
		if ( isset( $attributes['metaLinkColor'] ) && ! empty( $attributes['metaLinkColor'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .kt-blocks-post-top-meta a' );
			$css->add_property( 'color', $css->render_color( $attributes['metaLinkColor'] ) );
		}
		if ( isset( $attributes['metaLinkHoverColor'] ) && ! empty( $attributes['metaLinkHoverColor'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-grid-item .kt-blocks-post-top-meta a:hover' );
			$css->add_property( 'color', $css->render_color( $attributes['metaLinkHoverColor'] ) );
		}
		// Body
		if ( isset( $attributes['bodyBG'] ) || isset( $attributes['bodyPadding'] ) || isset( $attributes['bodyMargin'] ) || isset( $attributes['excerptFont'] ) || isset( $attributes['excerptColor'] ) || isset( $attributes['bodyTabletPadding'] ) || isset( $attributes['bodyTabletMargin'] ) || isset( $attributes['bodyMobilePadding'] ) || isset( $attributes['bodyMobileMargin'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .entry-content' );
			if ( isset( $attributes['bodyBG'] ) ) {
				$bodybgcoloralpha = ( isset( $attributes['bodyBGOpacity'] ) ? $attributes['bodyBGOpacity'] : 1 );
				$css->add_property( 'background-color', $css->render_color( $attributes['bodyBG'], $bodybgcoloralpha ) );
			}
			if ( isset( $attributes['excerptColor'] ) && ! empty( $attributes['excerptColor'] ) ) {
				$css->add_property( 'color', $css->render_color( $attributes['excerptColor'] ) );
			}
			if ( isset( $attributes['bodyPadding'] ) && is_array( $attributes['bodyPadding'] ) ) {
				if ( is_numeric( $attributes['bodyPadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['bodyPadding'][0] . ( ! empty( $attributes['bodyPaddingType'] ) ? $attributes['bodyPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['bodyPadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['bodyPadding'][1] . ( ! empty( $attributes['bodyPaddingType'] ) ? $attributes['bodyPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['bodyPadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['bodyPadding'][2] . ( ! empty( $attributes['bodyPaddingType'] ) ? $attributes['bodyPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['bodyPadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['bodyPadding'][3] . ( ! empty( $attributes['bodyPaddingType'] ) ? $attributes['bodyPaddingType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['bodyMargin'] ) && is_array( $attributes['bodyMargin'] ) ) {
				if ( is_numeric( $attributes['bodyMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['bodyMargin'][0] . ( ! empty( $attributes['bodyMarginType'] ) ? $attributes['bodyMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['bodyMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['bodyMargin'][1] . ( ! empty( $attributes['bodyMarginType'] ) ? $attributes['bodyMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['bodyMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['bodyMargin'][2] . ( ! empty( $attributes['bodyMarginType'] ) ? $attributes['bodyMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['bodyMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['bodyMargin'][3] . ( ! empty( $attributes['bodyMarginType'] ) ? $attributes['bodyMarginType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['excerptFont'] ) && is_array( $attributes['excerptFont'] ) && isset( $attributes['excerptFont'][0] ) && is_array( $attributes['excerptFont'][0] ) ) {
				$excerpt_font = $attributes['excerptFont'][0];
				if ( isset( $excerpt_font['size'] ) && isset( $excerpt_font['size'][0] ) && is_numeric( $excerpt_font['size'][0] ) ) {
					$css->add_property( 'font-size', $excerpt_font['size'][0] . ( isset( $excerpt_font['sizeType'] ) && ! empty( $excerpt_font['sizeType'] ) ? $excerpt_font['sizeType'] : 'px' ) );
				}
				if ( isset( $excerpt_font['lineHeight'] ) && isset( $excerpt_font['lineHeight'][0] ) && is_numeric( $excerpt_font['lineHeight'][0] ) ) {
					$css->add_property( 'line-height', $excerpt_font['lineHeight'][0] . ( isset( $excerpt_font['lineType'] ) && ! empty( $excerpt_font['lineType'] ) ? $excerpt_font['lineType'] : 'px' ) );
				}
				if ( isset( $excerpt_font['letterSpacing'] ) && is_numeric( $excerpt_font['letterSpacing'] ) ) {
					$css->add_property( 'letter-spacing', $excerpt_font['letterSpacing'] . 'px' );
				}
				if ( isset( $excerpt_font['family'] ) && ! empty( $excerpt_font['family'] ) ) {
					$google = isset( $excerpt_font['google'] ) && $excerpt_font['google'] ? true : false;
					$google = $google && ( isset( $excerpt_font['loadGoogle'] ) && $excerpt_font['loadGoogle'] || ! isset( $excerpt_font['loadGoogle'] ) ) ? true : false;
					$css->add_property( 'font-family', $css->render_font_family( $excerpt_font['family'], $google, ( isset( $excerpt_font['variation'] ) ? $excerpt_font['variation'] : '' ), ( isset( $excerpt_font['subset'] ) ? $excerpt_font['subset'] : '' ) ) );
				}
				if ( isset( $excerpt_font['weight'] ) && ! empty( $excerpt_font['weight'] ) ) {
					$css->add_property( 'font-weight', $css->render_string( $excerpt_font['weight'] ) );
				}
				if ( isset( $excerpt_font['style'] ) && ! empty( $excerpt_font['style'] ) ) {
					$css->add_property( 'font-style', $css->render_string( $excerpt_font['style'] ) );
				}
				if ( isset( $excerpt_font['textTransform'] ) && ! empty( $excerpt_font['textTransform'] ) ) {
					$css->add_property( 'text-transform', $css->render_string( $excerpt_font['textTransform'] ) );
				}
			}
			// excerpt Tablet.
			$css->start_media_query( $media_query['tablet'] );
			if ( isset( $attributes['excerptFont'] ) && is_array( $attributes['excerptFont'] ) && isset( $attributes['excerptFont'][0] ) && is_array( $attributes['excerptFont'][0] ) && ( ( isset( $attributes['excerptFont'][0]['size'] ) && is_array( $attributes['excerptFont'][0]['size'] ) && isset( $attributes['excerptFont'][0]['size'][1] ) && ! empty( $attributes['excerptFont'][0]['size'][1] ) ) || ( isset( $attributes['excerptFont'][0]['lineHeight'] ) && is_array( $attributes['excerptFont'][0]['lineHeight'] ) && isset( $attributes['excerptFont'][0]['lineHeight'][1] ) && ! empty( $attributes['excerptFont'][0]['lineHeight'][1] ) ) ) ) {
				$excerpt_font = $attributes['excerptFont'][0];
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .entry-content' );
				if ( isset( $excerpt_font['size'] ) && isset( $excerpt_font['size'][1] ) && is_numeric( $excerpt_font['size'][1] ) ) {
					$css->add_property( 'font-size', $excerpt_font['size'][1] . ( isset( $excerpt_font['sizeType'] ) && ! empty( $excerpt_font['sizeType'] ) ? $excerpt_font['sizeType'] : 'px' ) );
				}
				if ( isset( $excerpt_font['lineHeight'] ) && isset( $excerpt_font['lineHeight'][1] ) && is_numeric( $excerpt_font['lineHeight'][1] ) ) {
					$css->add_property( 'line-height', $excerpt_font['lineHeight'][1] . ( isset( $excerpt_font['lineType'] ) && ! empty( $excerpt_font['lineType'] ) ? $excerpt_font['lineType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['bodyTabletPadding'] ) && is_array( $attributes['bodyTabletPadding'] ) ) {
				if ( is_numeric( $attributes['bodyTabletPadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['bodyTabletPadding'][0] . ( ! empty( $attributes['bodyPaddingType'] ) ? $attributes['bodyPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['bodyTabletPadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['bodyTabletPadding'][1] . ( ! empty( $attributes['bodyPaddingType'] ) ? $attributes['bodyPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['bodyTabletPadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['bodyTabletPadding'][2] . ( ! empty( $attributes['bodyPaddingType'] ) ? $attributes['bodyPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['bodyTabletPadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['bodyTabletPadding'][3] . ( ! empty( $attributes['bodyPaddingType'] ) ? $attributes['bodyPaddingType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['bodyTabletMargin'] ) && is_array( $attributes['bodyTabletMargin'] ) ) {
				if ( is_numeric( $attributes['bodyTabletMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['bodyTabletMargin'][0] . ( ! empty( $attributes['bodyMarginType'] ) ? $attributes['bodyMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['bodyTabletMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['bodyTabletMargin'][1] . ( ! empty( $attributes['bodyMarginType'] ) ? $attributes['bodyMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['bodyTabletMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['bodyTabletMargin'][2] . ( ! empty( $attributes['bodyMarginType'] ) ? $attributes['bodyMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['bodyTabletMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['bodyTabletMargin'][3] . ( ! empty( $attributes['bodyMarginType'] ) ? $attributes['bodyMarginType'] : 'px' ) );
				}
			}
			$css->stop_media_query();
			// excerpt Mobile.
			$css->start_media_query( $media_query['mobile'] );
			if ( isset( $attributes['excerptFont'] ) && is_array( $attributes['excerptFont'] ) && isset( $attributes['excerptFont'][0] ) && is_array( $attributes['excerptFont'][0] ) && ( ( isset( $attributes['excerptFont'][0]['size'] ) && is_array( $attributes['excerptFont'][0]['size'] ) && isset( $attributes['excerptFont'][0]['size'][2] ) && ! empty( $attributes['excerptFont'][0]['size'][2] ) ) || ( isset( $attributes['excerptFont'][0]['lineHeight'] ) && is_array( $attributes['excerptFont'][0]['lineHeight'] ) && isset( $attributes['excerptFont'][0]['lineHeight'][2] ) && ! empty( $attributes['excerptFont'][0]['lineHeight'][2] ) ) ) ) {
				$excerpt_font = $attributes['excerptFont'][0];
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .entry-content' );
				if ( isset( $excerpt_font['size'] ) && isset( $excerpt_font['size'][2] ) && is_numeric( $excerpt_font['size'][2] ) ) {
					$css->add_property( 'font-size', $excerpt_font['size'][2] . ( isset( $excerpt_font['sizeType'] ) && ! empty( $excerpt_font['sizeType'] ) ? $excerpt_font['sizeType'] : 'px' ) );
				}
				if ( isset( $excerpt_font['lineHeight'] ) && isset( $excerpt_font['lineHeight'][2] ) && is_numeric( $excerpt_font['lineHeight'][2] ) ) {
					$css->add_property( 'line-height', $excerpt_font['lineHeight'][2] . ( isset( $excerpt_font['lineType'] ) && ! empty( $excerpt_font['lineType'] ) ? $excerpt_font['lineType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['bodyMobilePadding'] ) && is_array( $attributes['bodyMobilePadding'] ) ) {
				if ( is_numeric( $attributes['bodyMobilePadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['bodyMobilePadding'][0] . ( ! empty( $attributes['bodyPaddingType'] ) ? $attributes['bodyPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['bodyMobilePadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['bodyMobilePadding'][1] . ( ! empty( $attributes['bodyPaddingType'] ) ? $attributes['bodyPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['bodyMobilePadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['bodyMobilePadding'][2] . ( ! empty( $attributes['bodyPaddingType'] ) ? $attributes['bodyPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['bodyMobilePadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['bodyMobilePadding'][3] . ( ! empty( $attributes['bodyPaddingType'] ) ? $attributes['bodyPaddingType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['bodyMobileMargin'] ) && is_array( $attributes['bodyMobileMargin'] ) ) {
				if ( is_numeric( $attributes['bodyMobileMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['bodyMobileMargin'][0] . ( ! empty( $attributes['bodyMarginType'] ) ? $attributes['bodyMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['bodyMobileMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['bodyMobileMargin'][1] . ( ! empty( $attributes['bodyMarginType'] ) ? $attributes['bodyMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['bodyMobileMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['bodyMobileMargin'][2] . ( ! empty( $attributes['bodyMarginType'] ) ? $attributes['bodyMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['bodyMobileMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['bodyMobileMargin'][3] . ( ! empty( $attributes['bodyMarginType'] ) ? $attributes['bodyMarginType'] : 'px' ) );
				}
			}
			$css->stop_media_query();
		}
		// Footer.
		if ( isset( $attributes['footerBG'] ) || isset( $attributes['footerPadding'] ) || isset( $attributes['footerMargin'] ) || isset( $attributes['footerBorderColor'] ) || isset( $attributes['footerBorderWidth'] ) || isset( $attributes['footerColor'] ) || isset( $attributes['footerFont'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-footer' );
			if ( isset( $attributes['footerBG'] ) && ! empty( $attributes['footerBG'] ) ) {
				$footerbgcoloralpha = ( isset( $attributes['footerBGOpacity'] ) ? $attributes['footerBGOpacity'] : 1 );
				$css->add_property( 'background-color', $css->render_color( $attributes['footerBG'], $footerbgcoloralpha ) );
			}
			if ( isset( $attributes['footerBorderColor'] ) && ! empty( $attributes['footerBorderColor'] ) ) {
				$footerbcoloralpha = ( isset( $attributes['footerBorderOpacity'] ) ? $attributes['footerBorderOpacity'] : 1 );
				$css->add_property( 'border-color', $css->render_color( $attributes['footerBorderColor'], $footerbcoloralpha ) );
			}
			if ( isset( $attributes['footerColor'] ) && ! empty( $attributes['footerColor'] ) ) {
				$css->add_property( 'color', $css->render_color( $attributes['footerColor'] ) );
			}
			if ( isset( $attributes['footerBorderWidth'] ) && is_array( $attributes['footerBorderWidth'] ) ) {
				if ( is_numeric( $attributes['footerBorderWidth'][0] ) ) {
					$css->add_property( 'border-top-width', $attributes['footerBorderWidth'][0] . 'px' );
				}
				if ( is_numeric( $attributes['footerBorderWidth'][1] ) ) {
					$css->add_property( 'border-right-width', $attributes['footerBorderWidth'][1] . 'px' );
				}
				if ( is_numeric( $attributes['footerBorderWidth'][2] ) ) {
					$css->add_property( 'border-bottom-width', $attributes['footerBorderWidth'][2] . 'px' );
				}
				if ( is_numeric( $attributes['footerBorderWidth'][3] ) ) {
					$css->add_property( 'border-left-width', $attributes['footerBorderWidth'][3] . 'px' );
				}
			}
			if ( isset( $attributes['footerPadding'] ) && is_array( $attributes['footerPadding'] ) ) {
				if ( is_numeric( $attributes['footerPadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['footerPadding'][0] . ( ! empty( $attributes['footerPaddingType'] ) ? $attributes['footerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['footerPadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['footerPadding'][1] . ( ! empty( $attributes['footerPaddingType'] ) ? $attributes['footerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['footerPadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['footerPadding'][2] . ( ! empty( $attributes['footerPaddingType'] ) ? $attributes['footerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['footerPadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['footerPadding'][3] . ( ! empty( $attributes['footerPaddingType'] ) ? $attributes['footerPaddingType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['footerMargin'] ) && is_array( $attributes['footerMargin'] ) ) {
				if ( is_numeric( $attributes['footerMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['footerMargin'][0] . ( ! empty( $attributes['footerMarginType'] ) ? $attributes['footerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['footerMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['footerMargin'][1] . ( ! empty( $attributes['footerMarginType'] ) ? $attributes['footerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['footerMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['footerMargin'][2] . ( ! empty( $attributes['footerMarginType'] ) ? $attributes['footerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['footerMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['footerMargin'][3] . ( ! empty( $attributes['footerMarginType'] ) ? $attributes['footerMarginType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['footerFont'] ) && is_array( $attributes['footerFont'] ) && isset( $attributes['footerFont'][0] ) && is_array( $attributes['footerFont'][0] ) ) {
				$footer_font = $attributes['footerFont'][0];
				if ( isset( $footer_font['size'] ) && isset( $footer_font['size'][0] ) && is_numeric( $footer_font['size'][0] ) ) {
					$css->add_property( 'font-size', $footer_font['size'][0] . ( isset( $footer_font['sizeType'] ) && ! empty( $footer_font['sizeType'] ) ? $footer_font['sizeType'] : 'px' ) );
				}
				if ( isset( $footer_font['lineHeight'] ) && isset( $footer_font['lineHeight'][0] ) && is_numeric( $footer_font['lineHeight'][0] ) ) {
					$css->add_property( 'line-height', $footer_font['lineHeight'][0] . ( isset( $footer_font['lineType'] ) && ! empty( $footer_font['lineType'] ) ? $footer_font['lineType'] : 'px' ) );
				}
				if ( isset( $footer_font['letterSpacing'] ) && is_numeric( $footer_font['letterSpacing'] ) ) {
					$css->add_property( 'letter-spacing', $footer_font['letterSpacing'] . 'px' );
				}
				if ( isset( $footer_font['family'] ) && ! empty( $footer_font['family'] ) ) {
					$google = isset( $footer_font['google'] ) && $footer_font['google'] ? true : false;
					$google = $google && ( isset( $footer_font['loadGoogle'] ) && $footer_font['loadGoogle'] || ! isset( $footer_font['loadGoogle'] ) ) ? true : false;
					$css->add_property( 'font-family', $css->render_font_family( $footer_font['family'], $google, ( isset( $footer_font['variation'] ) ? $footer_font['variation'] : '' ), ( isset( $footer_font['subset'] ) ? $footer_font['subset'] : '' ) ) );
				}
				if ( isset( $footer_font['weight'] ) && ! empty( $footer_font['weight'] ) ) {
					$css->add_property( 'font-weight', $css->render_string( $footer_font['weight'] ) );
				}
				if ( isset( $footer_font['style'] ) && ! empty( $footer_font['style'] ) ) {
					$css->add_property( 'font-style', $css->render_string( $footer_font['style'] ) );
				}
				if ( isset( $footer_font['textTransform'] ) && ! empty( $footer_font['textTransform'] ) ) {
					$css->add_property( 'text-transform', $css->render_string( $footer_font['textTransform'] ) );
				}
			}
			if ( isset( $attributes['footerAlignBottom'] ) && true === isset( $attributes['footerAlignBottom'] ) && isset( $attributes['footerMargin'] ) && is_array( $attributes['footerMargin'] ) ) {
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .entry-content:after' );
				$css->add_property( 'height', $attributes['footerMargin'][0] . ( ! empty( $attributes['footerMarginType'] ) ? $attributes['footerMarginType'] : 'px' ) );
			}
			// Footer Tablet.
			$css->start_media_query( $media_query['tablet'] );
			if ( isset( $attributes['footerFont'] ) && is_array( $attributes['footerFont'] ) && isset( $attributes['footerFont'][0] ) && is_array( $attributes['footerFont'][0] ) && ( ( isset( $attributes['footerFont'][0]['size'] ) && is_array( $attributes['footerFont'][0]['size'] ) && isset( $attributes['footerFont'][0]['size'][1] ) && ! empty( $attributes['footerFont'][0]['size'][1] ) ) || ( isset( $attributes['footerFont'][0]['lineHeight'] ) && is_array( $attributes['footerFont'][0]['lineHeight'] ) && isset( $attributes['footerFont'][0]['lineHeight'][1] ) && ! empty( $attributes['footerFont'][0]['lineHeight'][1] ) ) ) ) {
				$footer_font = $attributes['footerFont'][0];
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-footer' );
				if ( isset( $footer_font['size'] ) && isset( $footer_font['size'][1] ) && is_numeric( $footer_font['size'][1] ) ) {
					$css->add_property( 'font-size', $footer_font['size'][1] . ( isset( $footer_font['sizeType'] ) && ! empty( $footer_font['sizeType'] ) ? $footer_font['sizeType'] : 'px' ) );
				}
				if ( isset( $footer_font['lineHeight'] ) && isset( $footer_font['lineHeight'][1] ) && is_numeric( $footer_font['lineHeight'][1] ) ) {
					$css->add_property( 'line-height', $footer_font['lineHeight'][1] . ( isset( $footer_font['lineType'] ) && ! empty( $footer_font['lineType'] ) ? $footer_font['lineType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['footerTabletPadding'] ) && is_array( $attributes['footerTabletPadding'] ) ) {
				if ( is_numeric( $attributes['footerTabletPadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['footerTabletPadding'][0] . ( ! empty( $attributes['footerPaddingType'] ) ? $attributes['footerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['footerTabletPadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['footerTabletPadding'][1] . ( ! empty( $attributes['footerPaddingType'] ) ? $attributes['footerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['footerTabletPadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['footerTabletPadding'][2] . ( ! empty( $attributes['footerPaddingType'] ) ? $attributes['footerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['footerTabletPadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['footerTabletPadding'][3] . ( ! empty( $attributes['footerPaddingType'] ) ? $attributes['footerPaddingType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['footerTabletMargin'] ) && is_array( $attributes['footerTabletMargin'] ) ) {
				if ( is_numeric( $attributes['footerTabletMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['footerTabletMargin'][0] . ( ! empty( $attributes['footerMarginType'] ) ? $attributes['footerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['footerTabletMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['footerTabletMargin'][1] . ( ! empty( $attributes['footerMarginType'] ) ? $attributes['footerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['footerTabletMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['footerTabletMargin'][2] . ( ! empty( $attributes['footerMarginType'] ) ? $attributes['footerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['footerTabletMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['footerTabletMargin'][3] . ( ! empty( $attributes['footerMarginType'] ) ? $attributes['footerMarginType'] : 'px' ) );
				}
			}
			$css->stop_media_query();
			// Footer Mobile.
			$css->start_media_query( $media_query['mobile'] );
			if ( isset( $attributes['footerFont'] ) && is_array( $attributes['footerFont'] ) && isset( $attributes['footerFont'][0] ) && is_array( $attributes['footerFont'][0] ) && ( ( isset( $attributes['footerFont'][0]['size'] ) && is_array( $attributes['footerFont'][0]['size'] ) && isset( $attributes['footerFont'][0]['size'][2] ) && ! empty( $attributes['footerFont'][0]['size'][2] ) ) || ( isset( $attributes['footerFont'][0]['lineHeight'] ) && is_array( $attributes['footerFont'][0]['lineHeight'] ) && isset( $attributes['footerFont'][0]['lineHeight'][2] ) && ! empty( $attributes['footerFont'][0]['lineHeight'][2] ) ) ) ) {
				$footer_font = $attributes['footerFont'][0];
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-footer' );
				if ( isset( $footer_font['size'] ) && isset( $footer_font['size'][2] ) && is_numeric( $footer_font['size'][2] ) ) {
					$css->add_property( 'font-size', $footer_font['size'][2] . ( isset( $footer_font['sizeType'] ) && ! empty( $footer_font['sizeType'] ) ? $footer_font['sizeType'] : 'px' ) );
				}
				if ( isset( $footer_font['lineHeight'] ) && isset( $footer_font['lineHeight'][2] ) && is_numeric( $footer_font['lineHeight'][2] ) ) {
					$css->add_property( 'line-height', $footer_font['lineHeight'][2] . ( isset( $footer_font['lineType'] ) && ! empty( $footer_font['lineType'] ) ? $footer_font['lineType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['footerMobilePadding'] ) && is_array( $attributes['footerMobilePadding'] ) ) {
				if ( is_numeric( $attributes['footerMobilePadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['footerMobilePadding'][0] . ( ! empty( $attributes['footerPaddingType'] ) ? $attributes['footerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['footerMobilePadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['footerMobilePadding'][1] . ( ! empty( $attributes['footerPaddingType'] ) ? $attributes['footerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['footerMobilePadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['footerMobilePadding'][2] . ( ! empty( $attributes['footerPaddingType'] ) ? $attributes['footerPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['footerMobilePadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['footerMobilePadding'][3] . ( ! empty( $attributes['footerPaddingType'] ) ? $attributes['footerPaddingType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['footerMobileMargin'] ) && is_array( $attributes['footerMobileMargin'] ) ) {
				if ( is_numeric( $attributes['footerMobileMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['footerMobileMargin'][0] . ( ! empty( $attributes['footerMarginType'] ) ? $attributes['footerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['footerMobileMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['footerMobileMargin'][1] . ( ! empty( $attributes['footerMarginType'] ) ? $attributes['footerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['footerMobileMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['footerMobileMargin'][2] . ( ! empty( $attributes['footerMarginType'] ) ? $attributes['footerMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['footerMobileMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['footerMobileMargin'][3] . ( ! empty( $attributes['footerMarginType'] ) ? $attributes['footerMarginType'] : 'px' ) );
				}
			}
			$css->stop_media_query();
		}
		if ( isset( $attributes['footerLinkColor'] ) && ! empty( $attributes['footerLinkColor'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-footer a' );
			$css->add_property( 'color', $css->render_color( $attributes['footerLinkColor'] ) );
		}
		if ( isset( $attributes['footerLinkHoverColor'] ) && ! empty( $attributes['footerLinkHoverColor'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kt-blocks-post-footer a:hover' );
			$css->add_property( 'color', $css->render_color( $attributes['footerLinkHoverColor'] ) );
		}
		// Read More.
		if ( isset( $attributes['displayReadMore'] ) && true == $attributes['displayReadMore'] ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .entry-content .kt-blocks-post-readmore' );
			if ( isset( $attributes['readMoreBackground'] ) && ! empty( $attributes['readMoreBackground'] ) ) {
				$css->add_property( 'background-color', $css->render_color( $attributes['readMoreBackground'] ) );
			} else if ( ! isset( $attributes['readMoreBackground'] ) ) {
				$css->add_property( 'background-color', '#444444' );
			}
			if ( isset( $attributes['readMoreBorderColor'] ) && ! empty( $attributes['readMoreBorderColor'] ) ) {
				$css->add_property( 'border-color', $css->render_color( $attributes['readMoreBorderColor'] ) );
			} else if ( ! isset( $attributes['readMoreBorderColor'] ) ) {
				$css->add_property( 'border-color', '#444444' );
			}
			if ( isset( $attributes['readMoreColor'] ) && ! empty( $attributes['readMoreColor'] ) ) {
				$css->add_property( 'color', $css->render_color( $attributes['readMoreColor'] ) );
			} else if ( ! isset( $attributes['readMoreColor'] ) ) {
				$css->add_property( 'border-color', '#ffffff' );
			}
			if ( isset( $attributes['readMoreBorder'] ) && ! empty( $attributes['readMoreBorder'] ) ) {
				$css->add_property( 'border-width', $attributes['readMoreBorder'] . 'px' );
			}
			if ( isset( $attributes['readMoreBorderRadius'] ) && ! empty( $attributes['readMoreBorderRadius'] ) ) {
				$css->add_property( 'border-radius', $attributes['readMoreBorderRadius'] . 'px' );
			}
			if ( isset( $attributes['readMorePadding'] ) && is_array( $attributes['readMorePadding'] ) ) {
				if ( is_numeric( $attributes['readMorePadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['readMorePadding'][0] . ( ! empty( $attributes['readMorePaddingType'] ) ? $attributes['readMorePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['readMorePadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['readMorePadding'][1] . ( ! empty( $attributes['readMorePaddingType'] ) ? $attributes['readMorePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['readMorePadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['readMorePadding'][2] . ( ! empty( $attributes['readMorePaddingType'] ) ? $attributes['readMorePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['readMorePadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['readMorePadding'][3] . ( ! empty( $attributes['readMorePaddingType'] ) ? $attributes['readMorePaddingType'] : 'px' ) );
				}
			} else if ( ! isset( $attributes['readMorePadding'] ) ) {
				$css->add_property( 'padding', '4px 8px 4px 8px' );
			}
			if ( isset( $attributes['readMoreMargin'] ) && is_array( $attributes['readMoreMargin'] ) ) {
				if ( is_numeric( $attributes['readMoreMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['readMoreMargin'][0] . ( ! empty( $attributes['readMoreMarginType'] ) ? $attributes['readMoreMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['readMoreMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['readMoreMargin'][1] . ( ! empty( $attributes['readMoreMarginType'] ) ? $attributes['readMoreMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['readMoreMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['readMoreMargin'][2] . ( ! empty( $attributes['readMoreMarginType'] ) ? $attributes['readMoreMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['readMoreMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['readMoreMargin'][3] . ( ! empty( $attributes['readMoreMarginType'] ) ? $attributes['readMoreMarginType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['readMoreFont'] ) && is_array( $attributes['readMoreFont'] ) && isset( $attributes['readMoreFont'][0] ) && is_array( $attributes['readMoreFont'][0] ) ) {
				$readmore_font = $attributes['readMoreFont'][0];
				if ( isset( $readmore_font['size'] ) && isset( $readmore_font['size'][0] ) && is_numeric( $readmore_font['size'][0] ) ) {
					$css->add_property( 'font-size', $readmore_font['size'][0] . ( isset( $readmore_font['sizeType'] ) && ! empty( $readmore_font['sizeType'] ) ? $readmore_font['sizeType'] : 'px' ) );
				}
				if ( isset( $readmore_font['lineHeight'] ) && isset( $readmore_font['lineHeight'][0] ) && is_numeric( $readmore_font['lineHeight'][0] ) ) {
					$css->add_property( 'line-height', $readmore_font['lineHeight'][0] . ( isset( $readmore_font['lineType'] ) && ! empty( $readmore_font['lineType'] ) ? $readmore_font['lineType'] : 'px' ) );
				}
				if ( isset( $readmore_font['letterSpacing'] ) && is_numeric( $readmore_font['letterSpacing'] ) ) {
					$css->add_property( 'letter-spacing', $readmore_font['letterSpacing'] . 'px' );
				}
				if ( isset( $readmore_font['family'] ) && ! empty( $readmore_font['family'] ) ) {
					$google = isset( $readmore_font['google'] ) && $readmore_font['google'] ? true : false;
					$google = $google && ( isset( $readmore_font['loadGoogle'] ) && $readmore_font['loadGoogle'] || ! isset( $readmore_font['loadGoogle'] ) ) ? true : false;
					$css->add_property( 'font-family', $css->render_font_family( $readmore_font['family'], $google, ( isset( $readmore_font['variation'] ) ? $readmore_font['variation'] : '' ), ( isset( $readmore_font['subset'] ) ? $readmore_font['subset'] : '' ) ) );
				}
				if ( isset( $readmore_font['weight'] ) && ! empty( $readmore_font['weight'] ) ) {
					$css->add_property( 'font-weight', $css->render_string( $readmore_font['weight'] ) );
				}
				if ( isset( $readmore_font['style'] ) && ! empty( $readmore_font['style'] ) ) {
					$css->add_property( 'font-style', $css->render_string( $readmore_font['style'] ) );
				}
				if ( isset( $readmore_font['textTransform'] ) && ! empty( $readmore_font['textTransform'] ) ) {
					$css->add_property( 'text-transform', $css->render_string( $readmore_font['textTransform'] ) );
				}
			}
			// Read More Tablet
			$css->start_media_query( $media_query['tablet'] );
			if ( isset( $attributes['readMoreFont'] ) && is_array( $attributes['readMoreFont'] ) && isset( $attributes['readMoreFont'][0] ) && is_array( $attributes['readMoreFont'][0] ) && ( ( isset( $attributes['readMoreFont'][0]['size'] ) && is_array( $attributes['readMoreFont'][0]['size'] ) && isset( $attributes['readMoreFont'][0]['size'][1] ) && ! empty( $attributes['readMoreFont'][0]['size'][1] ) ) || ( isset( $attributes['readMoreFont'][0]['lineHeight'] ) && is_array( $attributes['readMoreFont'][0]['lineHeight'] ) && isset( $attributes['readMoreFont'][0]['lineHeight'][1] ) && ! empty( $attributes['readMoreFont'][0]['lineHeight'][1] ) ) ) ) {
				$readmore_font = $attributes['readMoreFont'][0];
				// Tablet.
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .entry-content .kt-blocks-post-readmore' );
				if ( isset( $readmore_font['size'] ) && isset( $readmore_font['size'][1] ) && is_numeric( $readmore_font['size'][1] ) ) {
					$css->add_property( 'font-size', $readmore_font['size'][1] . ( isset( $readmore_font['sizeType'] ) && ! empty( $readmore_font['sizeType'] ) ? $readmore_font['sizeType'] : 'px' ) );
				}
				if ( isset( $readmore_font['lineHeight'] ) && isset( $readmore_font['lineHeight'][1] ) && is_numeric( $readmore_font['lineHeight'][1] ) ) {
					$css->add_property( 'line-height', $readmore_font['lineHeight'][1] . ( isset( $readmore_font['lineType'] ) && ! empty( $readmore_font['lineType'] ) ? $readmore_font['lineType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['readMoreTabletPadding'] ) && is_array( $attributes['readMoreTabletPadding'] ) ) {
				if ( is_numeric( $attributes['readMoreTabletPadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['readMoreTabletPadding'][0] . ( ! empty( $attributes['readMorePaddingType'] ) ? $attributes['readMorePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['readMoreTabletPadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['readMoreTabletPadding'][1] . ( ! empty( $attributes['readMorePaddingType'] ) ? $attributes['readMorePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['readMoreTabletPadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['readMoreTabletPadding'][2] . ( ! empty( $attributes['readMorePaddingType'] ) ? $attributes['readMorePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['readMoreTabletPadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['readMoreTabletPadding'][3] . ( ! empty( $attributes['readMorePaddingType'] ) ? $attributes['readMorePaddingType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['readMoreTabetMargin'] ) && is_array( $attributes['readMoreTabetMargin'] ) ) {
				if ( is_numeric( $attributes['readMoreTabetMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['readMoreTabetMargin'][0] . ( ! empty( $attributes['readMoreMarginType'] ) ? $attributes['readMoreMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['readMoreTabetMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['readMoreTabetMargin'][1] . ( ! empty( $attributes['readMoreMarginType'] ) ? $attributes['readMoreMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['readMoreTabetMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['readMoreTabetMargin'][2] . ( ! empty( $attributes['readMoreMarginType'] ) ? $attributes['readMoreMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['readMoreTabetMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['readMoreTabetMargin'][3] . ( ! empty( $attributes['readMoreMarginType'] ) ? $attributes['readMoreMarginType'] : 'px' ) );
				}
			}
			$css->stop_media_query();
			// Read More Mobile.
			$css->start_media_query( $media_query['mobile'] );
			if ( isset( $attributes['readMoreFont'] ) && is_array( $attributes['readMoreFont'] ) && isset( $attributes['readMoreFont'][0] ) && is_array( $attributes['readMoreFont'][0] ) && ( ( isset( $attributes['readMoreFont'][0]['size'] ) && is_array( $attributes['readMoreFont'][0]['size'] ) && isset( $attributes['readMoreFont'][0]['size'][2] ) && ! empty( $attributes['readMoreFont'][0]['size'][2] ) ) || ( isset( $attributes['readMoreFont'][0]['lineHeight'] ) && is_array( $attributes['readMoreFont'][0]['lineHeight'] ) && isset( $attributes['readMoreFont'][0]['lineHeight'][2] ) && ! empty( $attributes['readMoreFont'][0]['lineHeight'][2] ) ) ) ) {
				$readmore_font = $attributes['readMoreFont'][0];
				// Mobile.
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .entry-content .kt-blocks-post-readmore' );
				if ( isset( $readmore_font['size'] ) && isset( $readmore_font['size'][2] ) && is_numeric( $readmore_font['size'][2] ) ) {
					$css->add_property( 'font-size', $readmore_font['size'][2] . ( isset( $readmore_font['sizeType'] ) && ! empty( $readmore_font['sizeType'] ) ? $readmore_font['sizeType'] : 'px' ) );
				}
				if ( isset( $readmore_font['lineHeight'] ) && isset( $readmore_font['lineHeight'][2] ) && is_numeric( $readmore_font['lineHeight'][2] ) ) {
					$css->add_property( 'line-height', $readmore_font['lineHeight'][2] . ( isset( $readmore_font['lineType'] ) && ! empty( $readmore_font['lineType'] ) ? $readmore_font['lineType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['readMoreMobilePadding'] ) && is_array( $attributes['readMoreMobilePadding'] ) ) {
				if ( is_numeric( $attributes['readMoreMobilePadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['readMoreMobilePadding'][0] . ( ! empty( $attributes['readMorePaddingType'] ) ? $attributes['readMorePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['readMoreMobilePadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['readMoreMobilePadding'][1] . ( ! empty( $attributes['readMorePaddingType'] ) ? $attributes['readMorePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['readMoreMobilePadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['readMoreMobilePadding'][2] . ( ! empty( $attributes['readMorePaddingType'] ) ? $attributes['readMorePaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['readMoreMobilePadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['readMoreMobilePadding'][3] . ( ! empty( $attributes['readMorePaddingType'] ) ? $attributes['readMorePaddingType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['readMoreMobileMargin'] ) && is_array( $attributes['readMoreMobileMargin'] ) ) {
				if ( is_numeric( $attributes['readMoreMobileMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['readMoreMobileMargin'][0] . ( ! empty( $attributes['readMoreMarginType'] ) ? $attributes['readMoreMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['readMoreMobileMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['readMoreMobileMargin'][1] . ( ! empty( $attributes['readMoreMarginType'] ) ? $attributes['readMoreMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['readMoreMobileMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['readMoreMobileMargin'][2] . ( ! empty( $attributes['readMoreMarginType'] ) ? $attributes['readMoreMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['readMoreMobileMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['readMoreMobileMargin'][3] . ( ! empty( $attributes['readMoreMarginType'] ) ? $attributes['readMoreMarginType'] : 'px' ) );
				}
			}
			$css->stop_media_query();
		}
		if ( isset( $attributes['displayReadMore'] ) && true == $attributes['displayReadMore'] ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .entry-content .kt-blocks-post-readmore:hover' );
			if ( isset( $attributes['readMoreHoverColor'] ) && ! empty( $attributes['readMoreHoverColor'] ) ) {
				$css->add_property( 'color', $css->render_color( $attributes['readMoreHoverColor'] ) );
			} else if ( ! isset( $attributes['readMoreHoverColor'] ) ) {
				$css->add_property( 'color', '#ffffff' );
			}
			if ( isset( $attributes['readMoreHoverBorderColor'] ) && ! empty( $attributes['readMoreHoverBorderColor'] ) ) {
				$css->add_property( 'border-color', $css->render_color( $attributes['readMoreHoverBorderColor'] ) );
			} else if ( ! isset( $attributes['readMoreHoverBorderColor'] ) ) {
				$css->add_property( 'border-color', '#555555' );
			}
			if ( isset( $attributes['readMoreHoverBackground'] ) && ! empty( $attributes['readMoreHoverBackground'] ) ) {
				$css->add_property( 'background-color', $css->render_color( $attributes['readMoreHoverBackground'] ) );
			} else if ( ! isset( $attributes['readMoreHoverBackground'] ) ) {
				$css->add_property( 'background-color', '#555555' );
			}
		}
		// Filter.
		if ( isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] && isset( $attributes['filterAlign'] ) && ! empty( $attributes['filterAlign'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kb-post-filter-container' );
			$css->add_property( 'text-align', $attributes['filterAlign'] );
			if ( 'right' === $attributes['filterAlign'] ) {
				$css->add_property( 'justify-content', 'flex-end' );
			}
			if ( 'left' === $attributes['filterAlign'] ) {
				$css->add_property( 'justify-content', 'flex-start' );
			}
		}
		// Filter Font.
		if ( isset( $attributes['filterColor'] ) || isset( $attributes['filterBorderRadius'] ) || isset( $attributes['filterFont'] ) || isset( $attributes['filterBorder'] ) || isset( $attributes['filterBackground'] ) || isset( $attributes['filterBorderWidth'] ) || isset( $attributes['filterPadding'] ) || isset( $attributes['filterMargin'] ) || isset( $attributes['filterTabletPadding'] ) || isset( $attributes['filterTabletMargin'] ) || isset( $attributes['filterMobilePadding'] ) || isset( $attributes['filterMobileMargin'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kb-filter-item' );
			if ( isset( $attributes['filterColor'] ) && ! empty( $attributes['filterColor'] ) ) {
				$css->add_property( 'color', $css->render_color( $attributes['filterColor'] ) );
			}
			if ( isset( $attributes['filterBorderRadius'] ) && is_numeric( $attributes['filterBorderRadius'] ) ) {
				$css->add_property( 'border-radius', $attributes['filterBorderRadius'] . 'px' );
			}
			if ( isset( $attributes['filterBackground'] ) && ! empty( $attributes['filterBackground'] ) ) {
				$bcoloralpha = ( isset( $attributes['filterBackgroundOpacity'] ) ? $attributes['filterBackgroundOpacity'] : 1 );
				$bcolorhex = ( isset( $attributes['filterBackground'] ) ? $attributes['filterBackground'] : '#ffffff' );
				$css->add_property( 'background', $css->render_color( $bcolorhex, $bcoloralpha ) );
			}
			if ( isset( $attributes['filterBorder'] ) && ! empty( $attributes['filterBorder'] ) ) {
				$bcoloralpha = ( isset( $attributes['filterBorderOpacity'] ) ? $attributes['filterBorderOpacity'] : 1 );
				$bcolorhex = ( isset( $attributes['filterBorder'] ) ? $attributes['filterBorder'] : '#ffffff' );
				$css->add_property( 'border-color', $css->render_color( $bcolorhex, $bcoloralpha ) );
			}
			if ( isset( $attributes['filterBorderWidth'] ) && is_array( $attributes['filterBorderWidth'] ) && isset( $attributes['filterBorderWidth'][0] ) && is_numeric( $attributes['filterBorderWidth'][0] ) ) {
				if ( is_numeric( $attributes['filterBorderWidth'][0] ) ) {
					$css->add_property( 'border-top-width', $attributes['filterBorderWidth'][0] . 'px' );
				}
				if ( is_numeric( $attributes['filterBorderWidth'][1] ) ) {
					$css->add_property( 'border-right-width', $attributes['filterBorderWidth'][1] . 'px' );
				}
				if ( is_numeric( $attributes['filterBorderWidth'][2] ) ) {
					$css->add_property( 'border-bottom-width', $attributes['filterBorderWidth'][2] . 'px' );
				}
				if ( is_numeric( $attributes['filterBorderWidth'][3] ) ) {
					$css->add_property( 'border-left-width', $attributes['filterBorderWidth'][3] . 'px' );
				}
			}
			if ( isset( $attributes['filterPadding'] ) && is_array( $attributes['filterPadding'] ) ) {
				if ( is_numeric( $attributes['filterPadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['filterPadding'][0] . ( ! empty( $attributes['filterPaddingType'] ) ? $attributes['filterPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['filterPadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['filterPadding'][1] . ( ! empty( $attributes['filterPaddingType'] ) ? $attributes['filterPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['filterPadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['filterPadding'][2] . ( ! empty( $attributes['filterPaddingType'] ) ? $attributes['filterPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['filterPadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['filterPadding'][3] . ( ! empty( $attributes['filterPaddingType'] ) ? $attributes['filterPaddingType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['filterMargin'] ) && is_array( $attributes['filterMargin'] ) ) {
				if ( is_numeric( $attributes['filterMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['filterMargin'][0] . ( ! empty( $attributes['filterMarginType'] ) ? $attributes['filterMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['filterMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['filterMargin'][1] . ( ! empty( $attributes['filterMarginType'] ) ? $attributes['filterMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['filterMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['filterMargin'][2] . ( ! empty( $attributes['filterMarginType'] ) ? $attributes['filterMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['filterMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['filterMargin'][3] . ( ! empty( $attributes['filterMarginType'] ) ? $attributes['filterMarginType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['filterFont'] ) && is_array( $attributes['filterFont'] ) && isset( $attributes['filterFont'][0] ) && is_array( $attributes['filterFont'][0] ) ) {
				$filter_font = $attributes['filterFont'][0];
				if ( isset( $filter_font['size'] ) && isset( $filter_font['size'][0] ) && is_numeric( $filter_font['size'][0] ) ) {
					$css->add_property( 'font-size', $filter_font['size'][0] . ( isset( $filter_font['sizeType'] ) && ! empty( $filter_font['sizeType'] ) ? $filter_font['sizeType'] : 'px' ) );
				}
				if ( isset( $filter_font['lineHeight'] ) && isset( $filter_font['lineHeight'][0] ) && is_numeric( $filter_font['lineHeight'][0] ) ) {
					$css->add_property( 'line-height', $filter_font['lineHeight'][0] . ( isset( $filter_font['lineType'] ) && ! empty( $filter_font['lineType'] ) ? $filter_font['lineType'] : 'px' ) );
				}
				if ( isset( $filter_font['letterSpacing'] ) && is_numeric( $filter_font['letterSpacing'] ) ) {
					$css->add_property( 'letter-spacing', $filter_font['letterSpacing'] . 'px' );
				}
				if ( isset( $filter_font['family'] ) && ! empty( $filter_font['family'] ) ) {
					$google = isset( $filter_font['google'] ) && $filter_font['google'] ? true : false;
					$google = $google && ( isset( $filter_font['loadGoogle'] ) && $filter_font['loadGoogle'] || ! isset( $filter_font['loadGoogle'] ) ) ? true : false;
					$css->add_property( 'font-family', $css->render_font_family( $filter_font['family'], $google, ( isset( $filter_font['variation'] ) ? $filter_font['variation'] : '' ), ( isset( $filter_font['subset'] ) ? $filter_font['subset'] : '' ) ) );
				}
				if ( isset( $filter_font['weight'] ) && ! empty( $filter_font['weight'] ) ) {
					$css->add_property( 'font-weight', $css->render_string( $filter_font['weight'] ) );
				}
				if ( isset( $filter_font['style'] ) && ! empty( $filter_font['style'] ) ) {
					$css->add_property( 'font-style', $css->render_string( $filter_font['style'] ) );
				}
				if ( isset( $filter_font['textTransform'] ) && ! empty( $filter_font['textTransform'] ) ) {
					$css->add_property( 'text-transform', $css->render_string( $filter_font['textTransform'] ) );
				}
			}
			// Filter Tablet.
			$css->start_media_query( $media_query['tablet'] );
			if ( isset( $attributes['filterFont'] ) && is_array( $attributes['filterFont'] ) && isset( $attributes['filterFont'][0] ) && is_array( $attributes['filterFont'][0] ) && ( ( isset( $attributes['filterFont'][0]['size'] ) && is_array( $attributes['filterFont'][0]['size'] ) && isset( $attributes['filterFont'][0]['size'][1] ) && ! empty( $attributes['filterFont'][0]['size'][1] ) ) || ( isset( $attributes['filterFont'][0]['lineHeight'] ) && is_array( $attributes['filterFont'][0]['lineHeight'] ) && isset( $attributes['filterFont'][0]['lineHeight'][1] ) && ! empty( $attributes['filterFont'][0]['lineHeight'][1] ) ) ) ) {
				$filter_font = $attributes['filterFont'][0];
				// Tablet.
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .kb-filter-item' );
				if ( isset( $filter_font['size'] ) && isset( $filter_font['size'][1] ) && is_numeric( $filter_font['size'][1] ) ) {
					$css->add_property( 'font-size', $filter_font['size'][1] . ( isset( $filter_font['sizeType'] ) && ! empty( $filter_font['sizeType'] ) ? $filter_font['sizeType'] : 'px' ) );
				}
				if ( isset( $filter_font['lineHeight'] ) && isset( $filter_font['lineHeight'][1] ) && is_numeric( $filter_font['lineHeight'][1] ) ) {
					$css->add_property( 'line-height', $filter_font['lineHeight'][1] . ( isset( $filter_font['lineType'] ) && ! empty( $filter_font['lineType'] ) ? $filter_font['lineType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['filterTabletPadding'] ) && is_array( $attributes['filterTabletPadding'] ) ) {
				if ( is_numeric( $attributes['filterTabletPadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['filterTabletPadding'][0] . ( ! empty( $attributes['filterPaddingType'] ) ? $attributes['filterPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['filterTabletPadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['filterTabletPadding'][1] . ( ! empty( $attributes['filterPaddingType'] ) ? $attributes['filterPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['filterTabletPadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['filterTabletPadding'][2] . ( ! empty( $attributes['filterPaddingType'] ) ? $attributes['filterPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['filterTabletPadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['filterTabletPadding'][3] . ( ! empty( $attributes['filterPaddingType'] ) ? $attributes['filterPaddingType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['filterTabletMargin'] ) && is_array( $attributes['filterTabletMargin'] ) ) {
				if ( is_numeric( $attributes['filterTabletMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['filterTabletMargin'][0] . ( ! empty( $attributes['filterMarginType'] ) ? $attributes['filterMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['filterTabletMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['filterTabletMargin'][1] . ( ! empty( $attributes['filterMarginType'] ) ? $attributes['filterMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['filterTabletMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['filterTabletMargin'][2] . ( ! empty( $attributes['filterMarginType'] ) ? $attributes['filterMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['filterTabletMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['filterTabletMargin'][3] . ( ! empty( $attributes['filterMarginType'] ) ? $attributes['filterMarginType'] : 'px' ) );
				}
			}
			$css->stop_media_query();
			// Filter Mobile
			$css->start_media_query( $media_query['mobile'] );
			if ( isset( $attributes['filterFont'] ) && is_array( $attributes['filterFont'] ) && isset( $attributes['filterFont'][0] ) && is_array( $attributes['filterFont'][0] ) && ( ( isset( $attributes['filterFont'][0]['size'] ) && is_array( $attributes['filterFont'][0]['size'] ) && isset( $attributes['filterFont'][0]['size'][2] ) && ! empty( $attributes['filterFont'][0]['size'][2] ) ) || ( isset( $attributes['filterFont'][0]['lineHeight'] ) && is_array( $attributes['filterFont'][0]['lineHeight'] ) && isset( $attributes['filterFont'][0]['lineHeight'][2] ) && ! empty( $attributes['filterFont'][0]['lineHeight'][2] ) ) ) ) {
				$filter_font = $attributes['filterFont'][0];
				// Mobile.
				$css->set_selector( '.kt-post-loop' . $unique_id . ' .kb-filter-item' );
				if ( isset( $filter_font['size'] ) && isset( $filter_font['size'][2] ) && is_numeric( $filter_font['size'][2] ) ) {
					$css->add_property( 'font-size', $filter_font['size'][2] . ( isset( $filter_font['sizeType'] ) && ! empty( $filter_font['sizeType'] ) ? $filter_font['sizeType'] : 'px' ) );
				}
				if ( isset( $filter_font['lineHeight'] ) && isset( $filter_font['lineHeight'][2] ) && is_numeric( $filter_font['lineHeight'][2] ) ) {
					$css->add_property( 'line-height', $filter_font['lineHeight'][2] . ( isset( $filter_font['lineType'] ) && ! empty( $filter_font['lineType'] ) ? $filter_font['lineType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['filterMobilePadding'] ) && is_array( $attributes['filterMobilePadding'] ) ) {
				if ( is_numeric( $attributes['filterMobilePadding'][0] ) ) {
					$css->add_property( 'padding-top', $attributes['filterMobilePadding'][0] . ( ! empty( $attributes['filterPaddingType'] ) ? $attributes['filterPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['filterMobilePadding'][1] ) ) {
					$css->add_property( 'padding-right', $attributes['filterMobilePadding'][1] . ( ! empty( $attributes['filterPaddingType'] ) ? $attributes['filterPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['filterMobilePadding'][2] ) ) {
					$css->add_property( 'padding-bottom', $attributes['filterMobilePadding'][2] . ( ! empty( $attributes['filterPaddingType'] ) ? $attributes['filterPaddingType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['filterMobilePadding'][3] ) ) {
					$css->add_property( 'padding-left', $attributes['filterMobilePadding'][3] . ( ! empty( $attributes['filterPaddingType'] ) ? $attributes['filterPaddingType'] : 'px' ) );
				}
			}
			if ( isset( $attributes['filterMobileMargin'] ) && is_array( $attributes['filterMobileMargin'] ) ) {
				if ( is_numeric( $attributes['filterMobileMargin'][0] ) ) {
					$css->add_property( 'margin-top', $attributes['filterMobileMargin'][0] . ( ! empty( $attributes['filterMarginType'] ) ? $attributes['filterMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['filterMobileMargin'][1] ) ) {
					$css->add_property( 'margin-right', $attributes['filterMobileMargin'][1] . ( ! empty( $attributes['filterMarginType'] ) ? $attributes['filterMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['filterMobileMargin'][2] ) ) {
					$css->add_property( 'margin-bottom', $attributes['filterMobileMargin'][2] . ( ! empty( $attributes['filterMarginType'] ) ? $attributes['filterMarginType'] : 'px' ) );
				}
				if ( is_numeric( $attributes['filterMobileMargin'][3] ) ) {
					$css->add_property( 'margin-left', $attributes['filterMobileMargin'][3] . ( ! empty( $attributes['filterMarginType'] ) ? $attributes['filterMarginType'] : 'px' ) );
				}
			}
			$css->stop_media_query();
		}
		if ( isset( $attributes['filterHoverColor'] ) || isset( $attributes['filterHoverBorder'] ) || isset( $attributes['filterHoverBackground'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kb-filter-item:hover, .kt-post-loop' . $unique_id . ' .kb-filter-item:focus' );
			if ( ! empty( $attributes['filterHoverColor'] ) ) {
				$css->add_property( 'color', $css->render_color( $attributes['filterHoverColor'] ) );
			}
			if ( isset( $attributes['filterHoverBackground'] ) && ! empty( $attributes['filterHoverBackground'] ) ) {
				$bcoloralpha = ( isset( $attributes['filterHoverBackgroundOpacity'] ) ? $attributes['filterHoverBackgroundOpacity'] : 1 );
				$bcolorhex = ( isset( $attributes['filterHoverBackground'] ) ? $attributes['filterHoverBackground'] : '#ffffff' );
				$css->add_property( 'background', $css->render_color( $bcolorhex, $bcoloralpha ) );
			}
			if ( isset( $attributes['filterHoverBorder'] ) && ! empty( $attributes['filterHoverBorder'] ) ) {
				$bcoloralpha = ( isset( $attributes['filterHoverBorderOpacity'] ) ? $attributes['filterHoverBorderOpacity'] : 1 );
				$bcolorhex = ( isset( $attributes['filterHoverBorder'] ) ? $attributes['filterHoverBorder'] : '#ffffff' );
				$css->add_property( 'border-color', $css->render_color( $bcolorhex, $bcoloralpha ) );
			}
		}
		if ( isset( $attributes['filterActiveColor'] ) || isset( $attributes['filterActiveBorder'] ) || isset( $attributes['filterActiveBackground'] ) ) {
			$css->set_selector( '.kt-post-loop' . $unique_id . ' .kb-filter-item.is-active' );
			if ( ! empty( $attributes['filterActiveColor'] ) ) {
				$css->add_property( 'color', $css->render_color( $attributes['filterActiveColor'] ) );
			}
			if ( isset( $attributes['filterActiveBackground'] ) && ! empty( $attributes['filterActiveBackground'] ) ) {
				$bcoloralpha = ( isset( $attributes['filterActiveBackgroundOpacity'] ) ? $attributes['filterActiveBackgroundOpacity'] : 1 );
				$bcolorhex = ( isset( $attributes['filterActiveBackground'] ) ? $attributes['filterActiveBackground'] : '#ffffff' );
				$css->add_property( 'background', $css->render_color( $bcolorhex, $bcoloralpha ) );
			}
			if ( isset( $attributes['filterActiveBorder'] ) && ! empty( $attributes['filterActiveBorder'] ) ) {
				$bcoloralpha = ( isset( $attributes['filterActiveBorderOpacity'] ) ? $attributes['filterActiveBorderOpacity'] : 1 );
				$bcolorhex = ( isset( $attributes['filterActiveBorder'] ) ? $attributes['filterActiveBorder'] : '#ffffff' );
				$css->add_property( 'border-color', $css->render_color( $bcolorhex, $bcoloralpha ) );
			}
		}
		self::$gfonts = $css->fonts_output();
		return $css->css_output();
	}
	/**
	 * Get Post Loop Image
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function get_post_image( $attributes ) {
		global $post;
		if ( isset( $attributes['displayImage'] ) && true === $attributes['displayImage'] && has_post_thumbnail() ) {
			$image_ratio = ( isset( $attributes['imageRatio'] ) ?  $attributes['imageRatio'] : '75' );
			$image_link = ( isset( $attributes['imageLink'] ) && ! $attributes['imageLink'] ? false : true );
			$image_size = ( isset( $attributes['imageFileSize'] ) && ! empty( $attributes['imageFileSize'] ) ? $attributes['imageFileSize'] : 'large' );
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->id ), $image_size );
			if ( $image ) {
				echo '<div class="kadence-post-image">';
				echo '<div class="kadence-post-image-intrisic kt-image-ratio-'. esc_attr( str_replace( '.', '-', $image_ratio ) ) .'" style="padding-bottom:' . ( 'nocrop' === $image_ratio ? ( ( $image[2] / $image[1] ) * 100 ) . '%' : $image_ratio . '%' ) . '">';
				echo '<div class="kadence-post-image-inner-intrisic">';
				if ( $image_link ) {
					echo '<a href="' . esc_url( get_permalink() ) . '"' . ( isset( $attributes['openNewTab'] ) && true == $attributes['openNewTab'] ? ' target="_blank"' : '' ) . ' class="kadence-post-image-inner-wrap">';
					the_post_thumbnail( $image_size );
					echo '</a>';
				} else {
					echo '<div class="kadence-post-image-inner-wrap">';
					the_post_thumbnail( $image_size );
					echo '</div>';
				}
				echo '</div>';
				echo '</div>';
				echo '</div>';
			}
		}
	}

	/**
	 * Get Post Loop Above Categories
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function get_above_categories( $attributes ) {
		if ( isset( $attributes['displayAboveCategories'] ) && true === $attributes['displayAboveCategories'] && 'post' === get_post_type() ) {
			$sep_name = ( isset( $attributes['aboveDividerSymbol'] ) ? $attributes['aboveDividerSymbol'] : 'line' );
			if ( 'dash' === $sep_name ) {
				$sep = '&#8208;';
			} else if ( 'line' === $sep_name ) {
				$sep = '&#124;';
			} else if ( 'dot' === $sep_name ) {
				$sep = '&#183;';
			} else if ( 'bullet' === $sep_name ) {
				$sep = '&#8226;';
			} else if ( 'tilde' === $sep_name ) {
				$sep = '&#126;';
			} else {
				$sep = '';
			}
			echo '<div class="kt-blocks-above-categories">';
			the_category( ' ' . $sep . ' ' );
			echo '</div>';
		} else if ( isset( $attributes['displayAboveTaxonomy'] ) && true === $attributes['displayAboveTaxonomy'] && 'post' !== get_post_type() && isset( $attributes['aboveTaxType'] ) && ! empty( $attributes['aboveTaxType'] ) ) {
			global $post;
			$sep_name = ( isset( $attributes['aboveDividerSymbol'] ) ? $attributes['aboveDividerSymbol'] : 'line' );
			if ( 'dash' === $sep_name ) {
				$sep = '&#8208;';
			} else if ( 'line' === $sep_name ) {
				$sep = '&#124;';
			} else if ( 'dot' === $sep_name ) {
				$sep = '&#183;';
			} else if ( 'bullet' === $sep_name ) {
				$sep = '&#8226;';
			} else if ( 'tilde' === $sep_name ) {
				$sep = '&#126;';
			} else {
				$sep = '';
			}
			$terms = get_the_terms( $post->ID , $attributes['aboveTaxType'] );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$output = array();
				foreach( $terms as $term ) {
					$output[] = '<a href="' . esc_url( get_term_link( $term->term_id ) ) . '">' . $term->name . '</a>';
				}
				echo '<div class="kt-blocks-above-categories">';
				echo implode( ' ' . $sep . ' ', $output );
				echo '</div>';
			}
		}
	}

	/**
	 * Get Post Loop Title
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function get_post_title( $attributes ) {
		if ( isset( $attributes['displayTitle'] ) && true === $attributes['displayTitle'] ) {
			$title_link = ( isset( $attributes['titleLink'] ) && ! $attributes['titleLink'] ? false : true );
			if ( $title_link ) {
				echo ( isset( $attributes['titleFont'] ) && isset( $attributes['titleFont'][0] ) && isset( $attributes['titleFont'][0]['level'] ) && ! empty( $attributes['titleFont'][0]['level'] ) ? '<h' . esc_attr( $attributes['titleFont'][0]['level'] ) . ' class="entry-title">' : '<h2 class="entry-title">' );
				echo '<a href="' . esc_url( get_permalink() ) . '"' . ( isset( $attributes['openNewTab'] ) && true == $attributes['openNewTab'] ? ' target="_blank"' : '' ) . '>';
					the_title();
				echo '</a>';
				echo ( isset( $attributes['titleFont'] ) && isset( $attributes['titleFont'][0] ) && isset( $attributes['titleFont'][0]['level'] ) && ! empty( $attributes['titleFont'][0]['level'] ) ? '</h' . esc_attr( $attributes['titleFont'][0]['level'] ) . '>' : '</h2>' );
			} else {
				echo ( isset( $attributes['titleFont'] ) && isset( $attributes['titleFont'][0] ) && isset( $attributes['titleFont'][0]['level'] ) && ! empty( $attributes['titleFont'][0]['level'] ) ? '<h' . esc_attr( $attributes['titleFont'][0]['level'] ) . ' class="entry-title">' : '<h2 class="entry-title">' );
					the_title();
				echo ( isset( $attributes['titleFont'] ) && isset( $attributes['titleFont'][0] ) && isset( $attributes['titleFont'][0]['level'] ) && ! empty( $attributes['titleFont'][0]['level'] ) ? '</h' . esc_attr( $attributes['titleFont'][0]['level'] ) . '>' : '</h2>' );
			}
		}
	}

	/**
	 * Get Post Loop Header Meta Area
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function get_meta_area( $attributes ) {
		echo '<div class="kt-blocks-post-top-meta">';
		/**
		 * @hooked get_meta_date - 10
		 * @hooked get_meta_modified_date - 10
		 * @hooked get_meta_author - 15
		 * @hooked get_meta_category - 20
		 * @hooked get_meta_comments - 25
		 */
		do_action( 'kadence_blocks_post_loop_header_meta', $attributes );
		echo '</div>';
	}

	/**
	 * Get Post Loop Header Meta Date
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function get_meta_date( $attributes ) {
		if ( isset( $attributes['displayDate'] ) && true === $attributes['displayDate'] ) {
			echo '<div class="kt-blocks-date">';
			if ( isset( $attributes['datePreText'] ) && ! empty( $attributes['datePreText'] ) ) {
				echo '<span class="kt-blocks-date-pretext">';
				echo esc_html( $attributes['datePreText'] );
				echo ' </span>';
			}
			echo '<time datetime="' . esc_attr( get_the_date( 'c' ) ) . '" class="kt-blocks-post-date">';
			echo get_the_date( get_option( 'date_format' ) );
			echo '</time>';
			echo '</div>';
			if ( ( isset( $attributes['displayModifiedDate'] ) && true === $attributes['displayModifiedDate'] ) || ( isset( $attributes['displayAuthor'] ) && true === $attributes['displayAuthor'] ) || ( isset( $attributes['displayCategory'] ) && true === $attributes['displayCategory'] ) || ( isset( $attributes['displayComment'] ) && true === $attributes['displayComment'] ) ) {
				$sep_name = ( isset( $attributes['metaDividerSymbol'] ) ? $attributes['metaDividerSymbol'] : '' );
				$sep_di_class = 'kt-blocks-meta-has-divider';
				if ( 'dash' === $sep_name ) {
					$sep = '&#8208;';
				} else if ( 'line' === $sep_name ) {
					$sep = '&#124;';
				} else if ( 'dot' === $sep_name ) {
					$sep = '&#183;';
				} else if ( 'bullet' === $sep_name ) {
					$sep = '&#8226;';
				} else if ( 'tilde' === $sep_name ) {
					$sep = '&#126;';
				} else {
					$sep = '';
					$sep_di_class = 'kt-blocks-meta-no-divider';
				}
				echo '<div class="kt-blocks-meta-divider ' . esc_attr( $sep_di_class ) . '">';
				echo $sep;
				echo '</div>';
			}
		}
	}

	/**
	 * Get Post Loop Header Meta Modified Date
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function get_meta_modified_date( $attributes ) {
		if ( isset( $attributes['displayModifiedDate'] ) && true === $attributes['displayModifiedDate'] ) {
			echo '<div class="kt-blocks-date-updated">';
			if ( isset( $attributes['modifiedDatePreText'] ) && ! empty( $attributes['modifiedDatePreText'] ) ) {
				echo '<span class="kt-blocks-updated-date-pretext">';
				echo esc_html( $attributes['modifiedDatePreText'] );
				echo ' </span>';
			}
			echo '<time datetime="' . esc_attr( get_the_modified_date( 'c' ) ) . '" class="kt-blocks-post-date">';
			echo get_the_modified_date( get_option( 'date_format' ) );
			echo '</time>';
			echo '</div>';
			if ( ( isset( $attributes['displayAuthor'] ) && true === $attributes['displayAuthor'] ) || ( isset( $attributes['displayCategory'] ) && true === $attributes['displayCategory'] ) || ( isset( $attributes['displayComment'] ) && true === $attributes['displayComment'] ) ) {
				$sep_name = ( isset( $attributes['metaDividerSymbol'] ) ? $attributes['metaDividerSymbol'] : '' );
				$sep_di_class = 'kt-blocks-meta-has-divider';
				if ( 'dash' === $sep_name ) {
					$sep = '&#8208;';
				} else if ( 'line' === $sep_name ) {
					$sep = '&#124;';
				} else if ( 'dot' === $sep_name ) {
					$sep = '&#183;';
				} else if ( 'bullet' === $sep_name ) {
					$sep = '&#8226;';
				} else if ( 'tilde' === $sep_name ) {
					$sep = '&#126;';
				} else {
					$sep = '';
					$sep_di_class = 'kt-blocks-meta-no-divider';
				}
				echo '<div class="kt-blocks-meta-divider ' . esc_attr( $sep_di_class ) . '">';
				echo $sep;
				echo '</div>';
			}
		}
	}

	/**
	 * Get Post Loop Header Meta Author
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function get_meta_author( $attributes ) {
		if ( isset( $attributes['displayAuthor'] ) && true === $attributes['displayAuthor'] && post_type_supports( get_post_type(), 'author' ) ) {
			echo '<div class="kt-blocks-post-author">';
			if ( isset( $attributes['authorPreText'] ) && ! empty( $attributes['authorPreText'] ) ) {
				echo '<span class="kt-blocks-author-pretext">';
				echo esc_html( $attributes['authorPreText'] );
				echo ' </span>';
			}
			echo '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" class="kt-blocks-post-author-link fn">';
			echo get_the_author();
			echo '</a>';
			echo '</div>';
			if ( ( isset( $attributes['displayCategory'] ) && true === $attributes['displayCategory'] ) || ( isset( $attributes['displayComment'] ) && true === $attributes['displayComment'] ) ) {
				$sep_name = ( isset( $attributes['metaDividerSymbol'] ) ? $attributes['metaDividerSymbol'] : '' );
				$sep_di_class = 'kt-blocks-meta-has-divider';
				if ( 'dash' === $sep_name ) {
					$sep = '&#8208;';
				} else if ( 'line' === $sep_name ) {
					$sep = '&#124;';
				} else if ( 'dot' === $sep_name ) {
					$sep = '&#183;';
				} else if ( 'bullet' === $sep_name ) {
					$sep = '&#8226;';
				} else if ( 'tilde' === $sep_name ) {
					$sep = '&#126;';
				} else {
					$sep = '';
					$sep_di_class = 'kt-blocks-meta-no-divider';
				}
				echo '<div class="kt-blocks-meta-divider ' . esc_attr( $sep_di_class ) . '">';
				echo $sep;
				echo '</div>';
			}
		}
	}

	/**
	 * Get Post Loop Header Meta Category
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function get_meta_category( $attributes ) {
		if ( isset( $attributes['displayCategory'] ) && true === $attributes['displayCategory'] && 'post' === get_post_type() && has_category() ) {
			echo '<div class="kt-blocks-post-author">';
			if ( isset( $attributes['categoryPreText'] ) && ! empty( $attributes['categoryPreText'] ) ) {
				echo '<span class="kt-blocks-category-pretext">';
				echo esc_html( $attributes['categoryPreText'] );
				echo ' </span>';
			}
			$cats = get_taxonomy( 'category' );
			echo '<span class="kt-blocks-categories">';
			the_category( ', ' );
			echo '</span>';
			echo '</div>';
			if ( ( isset( $attributes['displayComment'] ) && true === $attributes['displayComment'] ) ) {
				$sep_name = ( isset( $attributes['metaDividerSymbol'] ) ? $attributes['metaDividerSymbol'] : '' );
				$sep_di_class = 'kt-blocks-meta-has-divider';
				if ( 'dash' === $sep_name ) {
					$sep = '&#8208;';
				} else if ( 'line' === $sep_name ) {
					$sep = '&#124;';
				} else if ( 'dot' === $sep_name ) {
					$sep = '&#183;';
				} else if ( 'bullet' === $sep_name ) {
					$sep = '&#8226;';
				} else if ( 'tilde' === $sep_name ) {
					$sep = '&#126;';
				} else {
					$sep = '';
					$sep_di_class = 'kt-blocks-meta-no-divider';
				}
				echo '<div class="kt-blocks-meta-divider ' . esc_attr( $sep_di_class ) . '">';
				echo $sep;
				echo '</div>';
			}
		}
	}

	/**
	 * Get Post Loop Header Meta Category
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function get_meta_comment( $attributes ) {
		if ( isset( $attributes['displayComment'] ) && true === $attributes['displayComment'] ) {
			echo '<div class="kt-blocks-post-comments">';
			echo '<a class="kt-blocks-post-comments-link" href="' . esc_url( get_permalink() ) . '#comments">';
			if ( '1' === get_comments_number() ) {
				echo get_comments_number() . ' ' .  __( 'Comment', 'kadence-blocks-pro' );
			} else {
				echo get_comments_number() . ' ' . __( 'Comments', 'kadence-blocks-pro' );
			}
			echo '</a>';
			echo '</div>';
		}
	}

	/**
	 * Callback for the excerpt_length filter used by
	 * the Latest Posts block at render time.
	 *
	 * @return int Returns the global $kadence_blocks_post_grid_get_excerpt_length variable
	 *             to allow the excerpt_length filter respect the Latest Block setting.
	 */
	function kadence_blocks_post_get_excerpt_length() {
		global $kadence_blocks_post_grid_get_excerpt_length;
		return $kadence_blocks_post_grid_get_excerpt_length;
	}
	/**
	 * Get Post Loop Excerpt
	 *
	 * @param array $attributes Block Attributes.
	 */
	function get_post_excerpt( $attributes ) {
		global $kadence_blocks_post_grid_get_excerpt_length;
		if ( isset( $attributes['displayExcerpt'] ) && true === $attributes['displayExcerpt'] ) {
			if ( isset( $attributes['excerptCustomLength'] ) && true === $attributes['excerptCustomLength'] ) {
				$kadence_blocks_post_grid_get_excerpt_length = $attributes['excerptLength'];
				add_filter( 'excerpt_length', array( $this, 'kadence_blocks_post_get_excerpt_length' ), 20 );
				echo get_the_excerpt();
				remove_filter( 'excerpt_length', array( $this, 'kadence_blocks_post_get_excerpt_length' ), 20 );
			} else {
				echo get_the_excerpt();
			}
		}
	}
	/**
	 * Get Post Loop Read More
	 *
	 * @param array $attributes Block Attributes.
	 */
	function get_post_read_more( $attributes ) {
		if ( isset( $attributes['displayReadMore'] ) && true === $attributes['displayReadMore'] && isset( $attributes['readMoreText'] ) && ! empty( $attributes['readMoreText'] ) ) {
			echo '<div class="kt-blocks-post-readmore-wrap">';
			echo '<a class="kt-blocks-post-readmore" href="' . esc_url( get_permalink() ) . '"' . ( isset( $attributes['openNewTab'] ) && true == $attributes['openNewTab'] ? ' target="_blank"' : '' ) . '>';
				echo esc_html( $attributes['readMoreText'] );
				echo wp_kses(
					'<span class="screen-reader-text"> ' . get_the_title() . '</span>',
					array(
						'span' => array(
							'class' => array(),
						),
					)
				);
			echo '</a>';
			echo '</div>';
		}
	}

	/**
	 * Get Post Loop Footer Date
	 *
	 * @param array $attributes Block Attributes.
	 */
	function get_post_footer_date( $attributes ) {
		if ( isset( $attributes['footerDisplayDate'] ) && true === $attributes['footerDisplayDate'] ) {
			echo '<div class="kt-blocks-date kt-blocks-post-footer-section">';
			echo '<time dateTime="' . esc_attr( get_the_date( get_option( 'date_format' ) ) ) . '" class="kt-blocks-post-date">';
			echo get_the_date( get_option( 'date_format' ) );
			echo '</time>';
			echo '</div>';
		}
	}

	/**
	 * Get Post Loop Footer Categories
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function get_post_footer_categories( $attributes ) {
		if ( isset( $attributes['footerDisplayCategories'] ) && true === $attributes['footerDisplayCategories'] && has_category() ) {
			$cats = get_taxonomy( 'category' );
			echo '<div class="kt-blocks-categories kt-blocks-post-footer-section">';
			echo '<span class="kt-blocks-tags-icon"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" class="kt-blocks-cat-svg" fill="currentColor" width="32" height="32" viewBox="0 0 32 32"><title>'. esc_html( $cats->label ) . '</title>
			<path d="M0 10h32l-2 20h-28l-2-20zM29 6l1 2h-28l2-4h11l1 2h13z"></path></svg></span>';
			the_category( ', ' );
			echo '</div>';
		}
	}
	/**
	 * Get Post Loop Footer Tags
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function get_post_footer_tags( $attributes ) {
		if ( isset( $attributes['footerDisplayTags'] ) && true === $attributes['footerDisplayTags'] && has_tag() ) {
			$tags = get_taxonomy( 'post_tag' );
			echo '<div class="kt-blocks-tags kt-blocks-post-footer-section">';
			echo '<span class="kt-blocks-tags-icon"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" class="kt-blocks-tag-svg" width="36" height="32" fill="currentColor" viewBox="0 0 36 32"><title>'. esc_html( $tags->label ) . '</title><path d="M34.939 19.939l-8.879-8.879c-0.583-0.583-1.736-1.061-2.561-1.061h-18c-0.825 0-1.5 0.675-1.5 1.5v19c0 0.825 0.675 1.5 1.5 1.5h18c0.825 0 1.977-0.477 2.561-1.061l8.879-8.879c0.583-0.583 0.583-1.538-0-2.121zM25 24c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"></path><path d="M2 8h21l-0.939-0.939c-0.583-0.583-1.736-1.061-2.561-1.061h-18c-0.825 0-1.5 0.675-1.5 1.5v19c0 0.825 0.675 1.5 1.5 1.5h0.5v-20z"></path></svg></span>';
			the_tags( '', ', ' );
			echo '</div>';
		}
	}

	/**
	 * Get Post Loop Footer Author
	 *
	 * @param array $attributes Block Attributes.
	 */
	function get_post_footer_author( $attributes ) {
		if ( isset( $attributes['footerDisplayAuthor'] ) && true === $attributes['footerDisplayAuthor'] ) {
			echo '<div class="kt-blocks-author kt-blocks-post-footer-section">';
			echo '<span class="kt-blocks-post-author-inner kt-blocks-css-tool-top" aria-label="' . esc_attr( get_the_author() ) . '">';
			echo '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="32" height="32" class="kt-blocks-user-svg" fill="currentColor" viewBox="0 0 32 32"><title>' . esc_attr( get_the_author() ) . '</title><path d="M18 22.082v-1.649c2.203-1.241 4-4.337 4-7.432 0-4.971 0-9-6-9s-6 4.029-6 9c0 3.096 1.797 6.191 4 7.432v1.649c-6.784 0.555-12 3.888-12 7.918h28c0-4.030-5.216-7.364-12-7.918z"></path></svg>';
			echo '</span>';
			echo '</div>';
		}
	}

	/**
	 * Get Post Loop Footer Comments
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function get_post_footer_comments( $attributes ) {
		if ( isset( $attributes['footerDisplayComment'] ) && true === $attributes['footerDisplayComment'] && '0' !== get_comments_number() ) {
			echo '<div class="kt-blocks-post-comments kt-blocks-post-footer-section">';
			echo '<a class="kt-blocks-post-comments-link" href="' . esc_url( get_permalink() ) . '#comments">';
			echo '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" class="kt-blocks-comments-svg" width="36" height="32" fill="currentColor" viewBox="0 0 36 32"><title>' . esc_attr( __( 'Comment Count', 'kadence-blocks-pro' ) ) . '</title><path d="M15 4c-1.583 0-3.112 0.248-4.543 0.738-1.341 0.459-2.535 1.107-3.547 1.926-1.876 1.518-2.91 3.463-2.91 5.474 0 1.125 0.315 2.217 0.935 3.247 0.646 1.073 1.622 2.056 2.821 2.842 0.951 0.624 1.592 1.623 1.761 2.748 0.028 0.187 0.051 0.375 0.068 0.564 0.085-0.079 0.169-0.16 0.254-0.244 0.754-0.751 1.771-1.166 2.823-1.166 0.167 0 0.335 0.011 0.503 0.032 0.605 0.077 1.223 0.116 1.836 0.116 1.583 0 3.112-0.248 4.543-0.738 1.341-0.459 2.535-1.107 3.547-1.926 1.876-1.518 2.91-3.463 2.91-5.474s-1.033-3.956-2.91-5.474c-1.012-0.819-2.206-1.467-3.547-1.926-1.431-0.49-2.96-0.738-4.543-0.738zM15 0v0c8.284 0 15 5.435 15 12.139s-6.716 12.139-15 12.139c-0.796 0-1.576-0.051-2.339-0.147-3.222 3.209-6.943 3.785-10.661 3.869v-0.785c2.008-0.98 3.625-2.765 3.625-4.804 0-0.285-0.022-0.564-0.063-0.837-3.392-2.225-5.562-5.625-5.562-9.434 0-6.704 6.716-12.139 15-12.139zM31.125 27.209c0 1.748 1.135 3.278 2.875 4.118v0.673c-3.223-0.072-6.181-0.566-8.973-3.316-0.661 0.083-1.337 0.126-2.027 0.126-2.983 0-5.732-0.805-7.925-2.157 4.521-0.016 8.789-1.464 12.026-4.084 1.631-1.32 2.919-2.87 3.825-4.605 0.961-1.84 1.449-3.799 1.449-5.825 0-0.326-0.014-0.651-0.039-0.974 2.268 1.873 3.664 4.426 3.664 7.24 0 3.265-1.88 6.179-4.82 8.086-0.036 0.234-0.055 0.474-0.055 0.718z"></path></svg>';
			echo get_comments_number();
			echo '</a>';
			echo '</div>';
		}
	}
	/**
	 * Grabs the Google Fonts that are needed so we can load in the footer.
	 *
	 * @param array $attr the blocks attr.
	 */
	public function kadence_blocks_postgrid_googlefont_check( $attributes ) {
		$footer_gfonts = array();
		if ( isset( $attributes['aboveFont'] ) && is_array( $attributes['aboveFont'] ) && isset( $attributes['aboveFont'][0] ) && is_array( $attributes['aboveFont'][0] ) && isset( $attributes['aboveFont'][0]['google'] ) && $attributes['aboveFont'][0]['google'] && ( ! isset( $attributes['aboveFont'][0]['loadGoogle'] ) || true === $attributes['aboveFont'][0]['loadGoogle'] ) && isset( $attributes['aboveFont'][0]['family'] ) ) {
			$above_font = $attributes['aboveFont'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $above_font['family'], $footer_gfonts ) ) {
				$add_font = array(
					'fontfamily' => $above_font['family'],
					'fontvariants' => ( isset( $above_font['variant'] ) && ! empty( $above_font['variant'] ) ? array( $above_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $above_font['subset'] ) && ! empty( $above_font['subset'] ) ? array( $above_font['subset'] ) : array() ),
				);
				$footer_gfonts[ $above_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $above_font['variant'], $footer_gfonts[ $above_font['family'] ]['fontvariants'], true ) ) {
					array_push( $footer_gfonts[ $above_font['family'] ]['fontvariants'], $above_font['variant'] );
				}
				if ( ! in_array( $above_font['subset'], $footer_gfonts[ $above_font['family'] ]['fontsubsets'], true ) ) {
					array_push( $footer_gfonts[ $above_font['family'] ]['fontsubsets'], $above_font['subset'] );
				}
			}
		}
		if ( isset( $attributes['titleFont'] ) && is_array( $attributes['titleFont'] ) && isset( $attributes['titleFont'][0] ) && is_array( $attributes['titleFont'][0] ) && isset( $attributes['titleFont'][0]['google'] ) && $attributes['titleFont'][0]['google'] && ( ! isset( $attributes['titleFont'][0]['loadGoogle'] ) || true === $attributes['titleFont'][0]['loadGoogle'] ) && isset( $attributes['titleFont'][0]['family'] ) ) {
			$title_font = $attributes['titleFont'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $title_font['family'], $footer_gfonts ) ) {
				$add_font = array(
					'fontfamily' => $title_font['family'],
					'fontvariants' => ( isset( $title_font['variant'] ) && ! empty( $title_font['variant'] ) ? array( $title_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $title_font['subset'] ) && ! empty( $title_font['subset'] ) ? array( $title_font['subset'] ) : array() ),
				);
				$footer_gfonts[ $title_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $title_font['variant'], $footer_gfonts[ $title_font['family'] ]['fontvariants'], true ) ) {
					array_push( $footer_gfonts[ $title_font['family'] ]['fontvariants'], $title_font['variant'] );
				}
				if ( ! in_array( $title_font['subset'], $footer_gfonts[ $title_font['family'] ]['fontsubsets'], true ) ) {
					array_push( $footer_gfonts[ $title_font['family'] ]['fontsubsets'], $title_font['subset'] );
				}
			}
		}
		if ( isset( $attributes['metaFont'] ) && is_array( $attributes['metaFont'] ) && isset( $attributes['metaFont'][0] ) && is_array( $attributes['metaFont'][0] ) && isset( $attributes['metaFont'][0]['google'] ) && $attributes['metaFont'][0]['google'] && ( ! isset( $attributes['metaFont'][0]['loadGoogle'] ) || true === $attributes['metaFont'][0]['loadGoogle'] ) && isset( $attributes['metaFont'][0]['family'] ) ) {
			$meta_font = $attributes['metaFont'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $meta_font['family'], $footer_gfonts ) ) {
				$add_font = array(
					'fontfamily' => $meta_font['family'],
					'fontvariants' => ( isset( $meta_font['variant'] ) && ! empty( $meta_font['variant'] ) ? array( $meta_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $meta_font['subset'] ) && ! empty( $meta_font['subset'] ) ? array( $meta_font['subset'] ) : array() ),
				);
				$footer_gfonts[ $meta_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $meta_font['variant'], $footer_gfonts[ $meta_font['family'] ]['fontvariants'], true ) ) {
					array_push( $footer_gfonts[ $meta_font['family'] ]['fontvariants'], $meta_font['variant'] );
				}
				if ( ! in_array( $meta_font['subset'], $footer_gfonts[ $meta_font['family'] ]['fontsubsets'], true ) ) {
					array_push( $footer_gfonts[ $meta_font['family'] ]['fontsubsets'], $meta_font['subset'] );
				}
			}
		}
		if ( isset( $attributes['excerptFont'] ) && is_array( $attributes['excerptFont'] ) && isset( $attributes['excerptFont'][0] ) && is_array( $attributes['excerptFont'][0] ) && isset( $attributes['excerptFont'][0]['google'] ) && $attributes['excerptFont'][0]['google'] && ( ! isset( $attributes['excerptFont'][0]['loadGoogle'] ) || true === $attributes['excerptFont'][0]['loadGoogle'] ) && isset( $attributes['excerptFont'][0]['family'] ) ) {
			$excerpt_font = $attributes['excerptFont'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $excerpt_font['family'], $footer_gfonts ) ) {
				$add_font = array(
					'fontfamily' => $excerpt_font['family'],
					'fontvariants' => ( isset( $excerpt_font['variant'] ) && ! empty( $excerpt_font['variant'] ) ? array( $excerpt_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $excerpt_font['subset'] ) && ! empty( $excerpt_font['subset'] ) ? array( $excerpt_font['subset'] ) : array() ),
				);
				$footer_gfonts[ $excerpt_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $excerpt_font['variant'], $footer_gfonts[ $excerpt_font['family'] ]['fontvariants'], true ) ) {
					array_push( $footer_gfonts[ $excerpt_font['family'] ]['fontvariants'], $excerpt_font['variant'] );
				}
				if ( ! in_array( $excerpt_font['subset'], $footer_gfonts[ $excerpt_font['family'] ]['fontsubsets'], true ) ) {
					array_push( $footer_gfonts[ $excerpt_font['family'] ]['fontsubsets'], $excerpt_font['subset'] );
				}
			}
		}
		if ( isset( $attributes['readMoreFont'] ) && is_array( $attributes['readMoreFont'] ) && isset( $attributes['readMoreFont'][0] ) && is_array( $attributes['readMoreFont'][0] ) && isset( $attributes['readMoreFont'][0]['google'] ) && $attributes['readMoreFont'][0]['google'] && ( ! isset( $attributes['readMoreFont'][0]['loadGoogle'] ) || true === $attributes['readMoreFont'][0]['loadGoogle'] ) && isset( $attributes['readMoreFont'][0]['family'] ) ) {
			$read_more_font = $attributes['readMoreFont'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $read_more_font['family'], $footer_gfonts ) ) {
				$add_font = array(
					'fontfamily' => $read_more_font['family'],
					'fontvariants' => ( isset( $read_more_font['variant'] ) && ! empty( $read_more_font['variant'] ) ? array( $read_more_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $read_more_font['subset'] ) && ! empty( $read_more_font['subset'] ) ? array( $read_more_font['subset'] ) : array() ),
				);
				$footer_gfonts[ $read_more_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $read_more_font['variant'], $footer_gfonts[ $read_more_font['family'] ]['fontvariants'], true ) ) {
					array_push( $footer_gfonts[ $read_more_font['family'] ]['fontvariants'], $read_more_font['variant'] );
				}
				if ( ! in_array( $read_more_font['subset'], $footer_gfonts[ $read_more_font['family'] ]['fontsubsets'], true ) ) {
					array_push( $footer_gfonts[ $read_more_font['family'] ]['fontsubsets'], $read_more_font['subset'] );
				}
			}
		}
		if ( isset( $attributes['footerFont'] ) && is_array( $attributes['footerFont'] ) && isset( $attributes['footerFont'][0] ) && is_array( $attributes['footerFont'][0] ) && isset( $attributes['footerFont'][0]['google'] ) && $attributes['footerFont'][0]['google'] && ( ! isset( $attributes['footerFont'][0]['loadGoogle'] ) || true === $attributes['footerFont'][0]['loadGoogle'] ) && isset( $attributes['footerFont'][0]['family'] ) ) {
			$footer_font = $attributes['footerFont'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $footer_font['family'], $footer_gfonts ) ) {
				$add_font = array(
					'fontfamily' => $footer_font['family'],
					'fontvariants' => ( isset( $footer_font['variant'] ) && ! empty( $footer_font['variant'] ) ? array( $footer_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $footer_font['subset'] ) && ! empty( $footer_font['subset'] ) ? array( $footer_font['subset'] ) : array() ),
				);
				$footer_gfonts[ $footer_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $footer_font['variant'], $footer_gfonts[ $footer_font['family'] ]['fontvariants'], true ) ) {
					array_push( $footer_gfonts[ $footer_font['family'] ]['fontvariants'], $footer_font['variant'] );
				}
				if ( ! in_array( $footer_font['subset'], $footer_gfonts[ $footer_font['family'] ]['fontsubsets'], true ) ) {
					array_push( $footer_gfonts[ $footer_font['family'] ]['fontsubsets'], $footer_font['subset'] );
				}
			}
		}
		if ( empty( $footer_gfonts ) ) {
			return;
		}
		$print_google_fonts = apply_filters( 'kadence_blocks_postgrid_print_footer_google_fonts', true );
		if ( ! $print_google_fonts ) {
			return;
		}
		$link    = '';
		$subsets = array();
		foreach ( $footer_gfonts as $key => $gfont_values ) {
			if ( ! empty( $link ) ) {
				$link .= '%7C'; // Append a new font to the string.
			}
			$link .= $gfont_values['fontfamily'];
			if ( ! empty( $gfont_values['fontvariants'] ) ) {
				$link .= ':';
				$link .= implode( ',', $gfont_values['fontvariants'] );
			}
			if ( ! empty( $gfont_values['fontsubsets'] ) ) {
				foreach ( $gfont_values['fontsubsets'] as $subset ) {
					if ( ! empty( $subset ) && ! in_array( $subset, $subsets ) ) {
						array_push( $subsets, $subset );
					}
				}
			}
		}
		if ( ! empty( $subsets ) ) {
			$link .= '&amp;subset=' . implode( ',', $subsets );
		}
		if ( apply_filters( 'kadence_display_swap_google_fonts', true ) ) {
			$link .= '&amp;display=swap';
		}
		return '<link href="//fonts.googleapis.com/css?family=' . esc_attr( str_replace( '|', '%7C', $link ) ) . '" rel="stylesheet">';
	}
	/**
	 * Get no Posts text.
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function get_no_posts( $attributes ) {
		echo '<p>' . esc_html__( 'No posts', 'kadence-blocks-pro' ) . '</p>';
	}
	/**
	 * Get Color Output
	 *
	 * @param string $color the color string
	 * @param string $opacity the alpha level
	 */
	public function kbp_post_grid_color_output( $color, $opacity = null ) {
		if ( strpos( $color, 'palette' ) === 0 ) {
			$color = 'var(--global-' . $color . ')';
		} else if ( isset( $opacity ) && is_numeric( $opacity ) ) {
			$color = $this->kadence_blocks_pro_hex2rgba( $color, $opacity );
		}
		return $color;
	}
}
Kadence_Blocks_Pro_Post_Grid::get_instance();
