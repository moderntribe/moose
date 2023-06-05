<?php
/**
 * Enqueue admin CSS/JS and edit width functions
 *
 * @since   1.0.0
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Get all the registered image sizes along with their dimensions
 *
 * @global array $_wp_additional_image_sizes
 *
 * @link http://core.trac.wordpress.org/ticket/18947 Reference ticket
 *
 * @return array $image_sizes The image sizes
 */
function kadence_blocks_pro_get_all_image_sizes() {
	global $_wp_additional_image_sizes;

	$default_image_sizes = get_intermediate_image_sizes();
	$image_sizes = array();
	foreach ( $default_image_sizes as $size ) {
		$image_sizes[ $size ]['width']  = intval( get_option( "{$size}_size_w" ) );
		$image_sizes[ $size ]['height'] = intval( get_option( "{$size}_size_h" ) );
		$image_sizes[ $size ]['crop']   = get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false;
	}

	if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
		$image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
	}
	return $image_sizes;
}

/**
 * Get all the registered image sizes along with their dimensions
 *
 * @global array $_wp_additional_image_sizes
 *
 * @link http://core.trac.wordpress.org/ticket/18947 Reference ticket
 *
 * @return array $image_sizes The image sizes
 */
function kadence_blocks_pro_get_all_image_sizes_array() {
	$image_sizes = kadence_blocks_pro_get_all_image_sizes();
	$image_sizes_array = array();
	foreach ( $image_sizes as $size_key => $size_item ) {
		$image_sizes_array[] = array(
			'value' => $size_key,
			'label' => $size_key . ' (' . $size_item['width'] . 'x' . $size_item['height'] . ')',
		);
	}
	return $image_sizes_array;
}
/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * @since 1.0.0
 */
function kadence_blocks_pro_editor_assets() {
	// Scripts.
	$asset_meta = kadence_blocks_pro_get_asset_file( 'dist/build/blocks' );
	//wp_register_script( 'kadence-blocks-pro-vendor', KBP_URL . 'dist/build/vendors/blocks.js', array_merge( $asset_meta['dependencies'], array( 'wp-api', 'kadence-blocks-js' ) ), $asset_meta['version'], true );
	wp_register_script( 'kadence-blocks-pro-js', KBP_URL . 'dist/build/blocks.js', array_merge( $asset_meta['dependencies'], array( 'wp-api', 'kadence-blocks-js' ) ), $asset_meta['version'], true );

	// Styles.
	wp_register_style( 'kadence-blocks-pro-editor-css', KBP_URL . 'dist/build/blocks.css', array( 'wp-edit-blocks' ), $asset_meta['version'] );

	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'kadence-blocks-pro-js', 'kadence-blocks-pro', KBP_PATH . 'languages' );
	}
}
add_action( 'admin_init', 'kadence_blocks_pro_editor_assets', 30 );
/**
 * Enqueue Gutenberg block assets for backend editor.
 */
function kadence_blocks_pro_gutenberg_editor_assets_variables() {
	$recent_posts = wp_get_recent_posts( array( 'numberposts' => '1' ) );
	$the_post_id  = ( ! empty( $recent_posts[0]['ID'] ) ? $recent_posts[0]['ID'] : null );
	wp_localize_script(
		'kadence-blocks-pro-js',
		'kbpData',
		array(
			'restBase' => esc_url_raw( get_rest_url() ),
			'postSelectEndpoint' => '/kbpp/v1/post-select',
			'postQueryEndpoint' => '/kbpp/v1/post-query',
			'termEndpoint' => '/kbpp/v1/term-select',
			'taxonomiesEndpoint' => '/kbpp/v1/taxonomies-select',
			'postTypes' => kadence_blocks_pro_get_post_types(),
			'taxonomies' => array(),
			'isKadenceT'  => class_exists( 'Kadence\Theme' ),
			'isKadenceE' => ( 'kadence_element' === get_post_type() ? true : false ),
			'previewPost' => apply_filters( 'kadence_blocks_pro_dynamic_content_preview_post', $the_post_id ),
			'wcIsActive' => class_exists( 'Woocommerce' ),
			'imageSizes' => kadence_blocks_pro_get_all_image_sizes_array(),
			'wcProductCarouselPlaceholder' => ( function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src() : '' ),
		)
	);
}
add_action( 'enqueue_block_editor_assets', 'kadence_blocks_pro_gutenberg_editor_assets_variables' );
/**
 * Get the asset file produced by wp scripts.
 *
 * @param string $filepath the file path.
 * @return array
 */
function kadence_blocks_pro_get_asset_file( $filepath ) {
	$asset_path = KBP_PATH . $filepath . '.asset.php';

	return file_exists( $asset_path )
		? include $asset_path
		: array(
			'dependencies' => array( 'lodash', 'react', 'react-dom', 'wp-block-editor', 'wp-blocks', 'wp-data', 'wp-element', 'wp-i18n', 'wp-polyfill', 'wp-primitives', 'wp-api' ),
			'version'      => KBP_VERSION,
		);
}
/**
 * Enqueue Gutenberg block assets for backend editor.
 */
function kadence_blocks_pro_early_editor_assets() {
	if ( ! is_admin() ) {
		return;
	}
	$asset_meta = kadence_blocks_pro_get_asset_file( 'dist/build/early-filters' );
	//wp_register_script( 'kadence-blocks-pro-early-filters-vendor-js', KBP_URL . 'dist/build/vendors/blocks_early-filters.js', array_merge( $asset_meta['dependencies'], array( 'wp-blocks', 'wp-i18n', 'wp-element' ) ), $asset_meta['version'], true );
	wp_enqueue_script( 'kadence-blocks-pro-early-filters-js', KBP_URL . 'dist/build/early-filters.js', array_merge( $asset_meta['dependencies'], array( 'wp-blocks', 'wp-i18n', 'wp-element' ) ), $asset_meta['version'], true );
}
add_action( 'enqueue_block_editor_assets', 'kadence_blocks_pro_early_editor_assets', 1 );

add_action( 'rest_api_init', 'kadence_blocks_pro_register_api_endpoints' );
/**
 * Setup the post select API endpoint.
 *
 * @return void
 */
function kadence_blocks_pro_register_api_endpoints() {
	$controller = new Kadence_Blocks_Pro_Post_Select_Controller;
	$controller->register_routes();
	$mailchimp_controller = new Kadence_MailChimp_REST_Controller;
	$mailchimp_controller->register_routes();
	$activecampaign_controller = new KBP_ActiveCampaign_REST_Controller;
	$activecampaign_controller->register_routes();
	$sendinblue_controller = new Kadence_SendInBlue_REST_Controller;
	$sendinblue_controller->register_routes();
	$dynamic_controller = new Kadence_Blocks_Dynamic_Content_Controller;
	$dynamic_controller->register_routes();
}
/**
 * Setup the post type options for post blocks.
 *
 * @return array
 */
function kadence_blocks_pro_get_post_types() {
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
		if ( 'attachment' == $post_type->name || 'leaderboard' == $post_type->name ) {
			continue;
		}
		$output[] = array(
			'value' => $post_type->name,
			'label' => $post_type->label,
		);
	}
	return apply_filters( 'kadence_blocks_post_types', $output );
}
/**
 * Setup the post type taxonomies for post blocks.
 *
 * @return array
 */
function kadence_blocks_pro_get_taxonomies() {
	$post_types = kadence_blocks_pro_get_post_types();
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
	return apply_filters( 'kadence_blocks_taxonomies', $output );
}
/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @param string $template_name Template name.
 * @param array  $args          Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path  Default path. (default: '').
 */
function kadence_blocks_pro_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	$cache_key = sanitize_key( implode( '-', array( 'template', $template_name, $template_path, $default_path, KBP_VERSION ) ) );
	$template  = (string) wp_cache_get( $cache_key, 'kadence-blocks' );

	if ( ! $template ) {
		$template = kadence_blocks_pro_locate_template( $template_name, $template_path, $default_path );
		wp_cache_set( $cache_key, $template, 'kadence-blocks' );
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$filter_template = apply_filters( 'kadence_blocks_get_template', $template, $template_name, $args, $template_path, $default_path );

	if ( $filter_template !== $template ) {
		if ( ! file_exists( $filter_template ) ) {
			return;
		}
		$template = $filter_template;
	}

	$action_args = array(
		'template_name' => $template_name,
		'template_path' => $template_path,
		'located'       => $template,
		'args'          => $args,
	);

	if ( ! empty( $args ) && is_array( $args ) ) {
		if ( isset( $args['action_args'] ) ) {
			unset( $args['action_args'] );
		}
		extract( $args ); // @codingStandardsIgnoreLine
	}

	do_action( 'kadence_blocks_before_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );

	include $action_args['located'];

	do_action( 'kadence_blocks_before_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );
}
/**
 * Like kadence_blocks_pro_get_template, but returns the HTML instead of outputting.
 *
 * @see kadence_blocks_pro_get_template
 * @param string $template_name Template name.
 * @param array  $args          Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path  Default path. (default: '').
 *
 * @return string
 */
function kadence_blocks_pro_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	ob_start();
	kadence_blocks_pro_get_template( $template_name, $args, $template_path, $default_path );
	return ob_get_clean();
}
/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 * yourtheme/$template_path/$template_name
 * yourtheme/$template_name
 * $default_path/$template_name
 *
 * @param string $template_name Template name.
 * @param string $template_path Template path. (default: '').
 * @param string $default_path  Default path. (default: '').
 * @return string
 */
function kadence_blocks_pro_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = apply_filters( 'kadence_blocks_template_path', 'kadenceblocks/' );
	}

	if ( ! $default_path ) {
		$default_path = KBP_PATH . 'dist/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);

	// Get default template/.
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'kadence_blocks_locate_template', $template, $template_name, $template_path );
}
/**
 * Wrapper for set_time_limit to see if it is enabled.
 *
 * @param int $limit Time limit.
 */
function kadence_blocks_pro_set_time_limit( $limit = 0 ) {
	if ( function_exists( 'set_time_limit' ) && false === strpos( ini_get( 'disable_functions' ), 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) { // phpcs:ignore PHPCompatibility.IniDirectives.RemovedIniDirectives.safe_modeDeprecatedRemoved
		@set_time_limit( $limit ); // @codingStandardsIgnoreLine
	}
}
/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $var Data to sanitize.
 * @return string|array
 */
function kadence_blocks_pro_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'kadence_blocks_pro_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}
/**
 * Register Meta for blocks code.
 */
function kadence_blocks_pro_code_post_meta() {
	register_post_meta(
		'',
		'_kad_blocks_custom_css',
		array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
			'auth_callback' => '__return_true',
		)
	);
	register_post_meta(
		'',
		'_kad_blocks_head_custom_js',
		array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
			'auth_callback' => '__return_true',
		)
	);
	register_post_meta(
		'',
		'_kad_blocks_body_custom_js',
		array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
			'auth_callback' => '__return_true',
		)
	);
	register_post_meta(
		'',
		'_kad_blocks_footer_custom_js',
		array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
			'auth_callback' => '__return_true',
		)
	);
}
add_action( 'init', 'kadence_blocks_pro_code_post_meta' );

/**
 * Output the custom head scripts.
 */
function kadence_blocks_pro_output_head_scripts() {
	if ( ! is_singular() ) {
		return;
	}
	$css_output = get_post_meta( get_the_ID(), '_kad_blocks_custom_css', true );
	if ( ! empty( $css_output ) ) {
		echo '<style id="kadence-blocks-post-custom-css">';
		echo $css_output;
		echo '</style>';
	}
	$js_output = get_post_meta( get_the_ID(), '_kad_blocks_head_custom_js', true );
	if ( ! empty( $js_output ) ) {
		echo $js_output;
	}
}
add_action( 'wp_head', 'kadence_blocks_pro_output_head_scripts', 30 );

/**
 * Output the custom body scripts.
 */
function kadence_blocks_pro_output_body_scripts() {
	if ( ! is_singular() ) {
		return;
	}
	$js_output = get_post_meta( get_the_ID(), '_kad_blocks_body_custom_js', true );
	if ( ! empty( $js_output ) ) {
		echo $js_output;
	}
}
add_action( 'wp_body_open', 'kadence_blocks_pro_output_body_scripts', 10 );

/**
 * Output the custom footer scripts.
 */
function kadence_blocks_pro_output_footer_scripts() {
	if ( ! is_singular() ) {
		return;
	}
	$js_output = get_post_meta( get_the_ID(), '_kad_blocks_footer_custom_js', true );
	if ( ! empty( $js_output ) ) {
		echo $js_output;
	}
}
add_action( 'wp_footer', 'kadence_blocks_pro_output_footer_scripts', 20 );

function kadence_blocks_pro_dynamic_enable() {
	add_filter( 'kadence_blocks_dynamic_enabled', '__return_true' );
}
add_action( 'init', 'kadence_blocks_pro_dynamic_enable', 1 );