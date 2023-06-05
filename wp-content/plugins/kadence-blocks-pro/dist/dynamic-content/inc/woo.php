<?php
/**
 * Handle ACF Rendering.
 *
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle WooCommerce Rendering.
 *
 * @param string  $meta_key the meta key.
 * @param string  $meta_type the meta type.
 * @param integer $object_id The source object id.
 * @param array   $args The args for the meta field.
 *
 * @return mixed Returns the block content.
 */
function kbp_dynamic_content_woo( $meta_key, $meta_type, $type, $object_id, $args ) {
	$output = '';
	if ( class_exists( 'woocommerce' ) ) {
		$product = wc_get_product( $object_id );
		if ( is_object( $product ) ) {
			switch ( $meta_key ) {
				case 'product_gallery':
					$feature_id = array( get_post_thumbnail_id( $object_id ) );
					$attachment_ids = $product->get_gallery_image_ids();
					$images = array_merge( $feature_id, $attachment_ids );
					$size   = ( isset( $args['before'] ) ? $args['before'] : '' );
					$output = kbp_dynamic_content_gallery_format( $images, $size );
					break;
				case 'only_product_gallery':
					$attachment_ids = $product->get_gallery_image_ids();
					$size   = ( isset( $args['before'] ) ? $args['before'] : '' );
					if ( $attachment_ids ) {
						$output = kbp_dynamic_content_gallery_format( $attachment_ids, $size );
					}
					break;
			}
		}
	}
	return $output;
}
