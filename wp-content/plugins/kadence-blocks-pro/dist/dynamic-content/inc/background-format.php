<?php
/**
 * Handle Background Format Rendering.
 *
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle Background Format Rendering.
 *
 * @param mixed  $value the meta key.
 * @param string $size the image size - optional.
 *
 * @return string Returns the background with meta data.
 */
function kbp_dynamic_content_background_format( $value, $size = '' ) {
	$output = $value;
	if ( $value && is_array( $value ) && isset( $value['ID'] ) ) {
		$attachment_id = $value['ID'];
		if ( ! empty( $size ) ) {
			$image = wp_get_attachment_image_src( $attachment_id, $size );
		} else {
			$image = wp_get_attachment_image_src( $attachment_id, 'full' );
		}
		if ( $image ) {
			$output = ( $image && $image[0] ? $image[0] : '' );
		}
	} elseif ( $value && is_array( $value ) && isset( $value['id'] ) ) {
		$attachment_id = $value['id'];
		if ( ! empty( $size ) ) {
			$image = wp_get_attachment_image_src( $attachment_id, $size );
		} else {
			$image = wp_get_attachment_image_src( $attachment_id, 'full' );
		}
		if ( $image ) {
			$output = ( $image && $image[0] ? $image[0] : '' );
		}
	} elseif ( $value === absint( $value ) ) {
		if ( ! empty( $size ) ) {
			$image = wp_get_attachment_image_src( $value, $size );
		} else {
			$image = wp_get_attachment_image_src( $value, 'full' );
		}
		if ( $image ) {
			$output = ( $image && $image[0] ? $image[0] : '' );
		}
	} elseif ( is_string( $value ) ) {
		$attachment_id = attachment_url_to_postid( $value );
		if ( $attachment_id ) {
			if ( ! empty( $size ) ) {
				$image = wp_get_attachment_image_src( $attachment_id, $size );
			} else {
				$image = wp_get_attachment_image_src( $attachment_id, 'full' );
			}
			if ( $image ) {
				$output = ( $image && $image[0] ? $image[0] : '' );
			}
		}
	}
	return $output;
}
