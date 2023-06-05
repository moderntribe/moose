<?php
/**
 * Handle Image Format Rendering.
 *
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle Image Format Rendering.
 *
 * @param mixed  $value the meta key.
 * @param string $size the image size - optional.
 *
 * @return array Returns the image with meta data.
 */
function kbp_dynamic_content_image_format( $value, $size = '' ) {
	$output = $value;
	if ( $value && is_array( $value ) && ( isset( $value['ID'] ) || isset( $value['id'] ) ) ) {
		$attachment_id = isset( $value['ID'] ) ? $value['ID'] : '';
		if ( empty( $attachment_id ) ) {
			$attachment_id = isset( $value['id'] ) ? $value['id'] : '';
		}
		if ( $attachment_id ) {
			if ( ! empty( $size ) ) {
				$output = wp_get_attachment_image_src( $attachment_id, $size );
			} else {
				$output = wp_get_attachment_image_src( $attachment_id, 'full' );
			}
			if ( $output ) {
				$output[4] = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
				$output[5] = $attachment_id;
			}
		}
	} elseif ( $value && is_array( $value ) ) {
		foreach ( $value as $key => $sub_value ) {
			if ( $sub_value && is_array( $sub_value ) && ( isset( $sub_value['ID'] ) || isset( $sub_value['id'] ) ) ) {
				$attachment_id = isset( $sub_value['ID'] ) ? $sub_value['ID'] : '';
				if ( empty( $attachment_id ) ) {
					$attachment_id = isset( $sub_value['id'] ) ? $sub_value['id'] : '';
				}
				if ( $attachment_id ) {
					if ( ! empty( $size ) ) {
						$output = wp_get_attachment_image_src( $attachment_id, $size );
					} else {
						$output = wp_get_attachment_image_src( $attachment_id, 'full' );
					}
					if ( $output ) {
						$output[4] = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
						$output[5] = $attachment_id;
					}
				}
			}
			break;
		}
	} elseif ( ! empty( $value ) && is_numeric( $value ) && $output == absint( $value ) ) {
		$attachment_id = $value;
		if ( ! empty( $size ) ) {
			$output = wp_get_attachment_image_src( $attachment_id, $size );
		} else {
			$output = wp_get_attachment_image_src( $attachment_id, 'full' );
		}
		if ( $output ) {
			$output[4] = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
			$output[5] = $attachment_id;
		}
	} elseif ( is_string( $value ) ) {
		$attachment_id = attachment_url_to_postid( $value );
		if ( $attachment_id ) {
			if ( ! empty( $size ) ) {
				$output = wp_get_attachment_image_src( $attachment_id, $size );
			} else {
				$output = wp_get_attachment_image_src( $attachment_id, 'full' );
			}
			if ( $output ) {
				$output[4] = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
				$output[5] = $attachment_id;
			}
		}
	}
	return $output;
}
