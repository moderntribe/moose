<?php
/**
 * Handle gallery Format Rendering.
 *
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle Gallery Format Rendering.
 *
 * @param mixed  $value the meta key.
 * @param string $size the image size - optional.
 *
 * @return array Returns the images in a gallery with meta data.
 */
function kbp_dynamic_content_gallery_format( $value, $size = '' ) {
	$output = $value;
	if ( $value && is_array( $value ) ) {
		$final_output = array();
		$i = 0;
		foreach ( $value as $key => $image ) {
			if ( is_array( $image ) ) {
				//error_log( print_r( $image, true ) );
				$image_id = isset( $image['ID'] ) ? $image['ID'] : '';
				if ( empty( $image_id ) ) {
					$image_id = isset( $image['id'] ) ? $image['id'] : '';
				}
				if ( ! empty( $image_id ) ) {
					$final_output[] = $image;
					$final_output[ $i ]['id'] = $image_id;
					if ( ! empty( $size ) ) {
						$image_array = wp_get_attachment_image_src( $image_id, $size );
						if ( ! empty( $image_array ) ) {
							$final_output[ $i ]['url'] = $image_array[0];
							$final_output[ $i ]['width'] = $image_array[1];
							$final_output[ $i ]['height'] = $image_array[2];
						}
					}
					$full_image_array = wp_get_attachment_image_src( $image_id, 'full' );
					if ( ! empty( $full_image_array ) ) {
						if ( ! isset( $final_output[ $i ]['url'] ) ) {
							$final_output[ $i ]['url'] = $full_image_array[0];
							$final_output[ $i ]['width'] = $full_image_array[1];
							$final_output[ $i ]['height'] = $full_image_array[2];
						}
						$final_output[ $i ]['fullUrl'] = $full_image_array[0];
					}
				}
			} else {
				$image_id = ! is_array( $image ) && is_numeric( $image ) ? $image : '';
				if ( ! empty( $image_id ) ) {
					$final_output[ $i ] = array();
					$final_output[ $i ]['id'] = $image_id;
					if ( ! empty( $size ) ) {
						$image_array = wp_get_attachment_image_src( $image_id, $size );
						if ( ! empty( $image_array ) ) {
							$final_output[ $i ]['url'] = $image_array[0];
							$final_output[ $i ]['width'] = $image_array[1];
							$final_output[ $i ]['height'] = $image_array[2];
						}
					}
					$full_image_array = wp_get_attachment_image_src( $image_id, 'full' );
					if ( ! empty( $full_image_array ) ) {
						if ( ! isset( $final_output[ $i ]['url'] ) ) {
							$final_output[ $i ]['url'] = $full_image_array[0];
							$final_output[ $i ]['width'] = $full_image_array[1];
							$final_output[ $i ]['height'] = $full_image_array[2];
						}
						$final_output[ $i ]['fullUrl'] = $full_image_array[0];
					}
				}
			}
			$i ++;
		}
		$output = $final_output;
	}
	return $output;
}
