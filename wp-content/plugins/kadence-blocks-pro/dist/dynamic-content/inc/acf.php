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
 * Handle ACF Rendering.
 *
 * @param string  $meta_key the meta key.
 * @param string  $meta_type the meta type.
 * @param integer $object_id The source object id.
 * @param array   $args The args for the meta field.
 *
 * @return mixed Returns the block content.
 */
function kbp_dynamic_content_acf( $meta_key, $meta_type, $type, $object_id, $args ) {
	$output = '';
	if ( function_exists( 'get_field' ) ) {
		$post_ref = ( 'acf_option' === $meta_type ? 'option' : $object_id );
		$output = get_field( $meta_key, $post_ref );
		if ( 'background' === $type ) {
			$size   = ( isset( $args['before'] ) ? $args['before'] : '' );
			$output = kbp_dynamic_content_background_format( $output, $size );
		} elseif ( 'image' === $type ) {
			$size   = ( isset( $args['before'] ) ? $args['before'] : '' );
			$output = kbp_dynamic_content_image_format( $output, $size );
		} elseif ( 'gallery' === $type ) {
			$size   = ( isset( $args['before'] ) ? $args['before'] : '' );
			$output = kbp_dynamic_content_gallery_format( $output, $size );
		} elseif ( 'url' === $type ) {
			if ( $output && is_array( $output ) && isset( $output['url'] ) ) {
				$output = $output['url'];
			} elseif ( $output === absint( $output ) ) {
				$image  = wp_get_attachment_image_src( $output, 'full' );
				$output = ( $image && $image[0] ? $image[0] : '' );
			}
		} elseif ( 'list' === $type ) {
			if ( ! empty( $output ) && is_array( $output ) ) {
				$final_output = array();
				foreach ( $output as $subkey => $subvalue ) {
					if ( is_array( $subvalue ) ) {
						$sub_value_string = ( isset( $subvalue['value'] ) ? $subvalue['value'] : '' );
						if ( empty( $sub_value_string ) ) {
							$first_key = array_key_first( $subvalue );
							if ( ! empty( $subvalue[ $first_key ] ) && is_string( $subvalue[ $first_key ] ) ) {
								$sub_value_string = $subvalue[ $first_key ];
							}
						}
					} else {
						$sub_value_string = $subvalue;
					}
					if ( ! empty( $sub_value_string ) ) {
						$final_output[] = array(
							'label' => $sub_value_string,
							'value' => $sub_value_string,
						);
					}
				}
				$output = $final_output;
			} elseif ( ! empty( $output ) ) {
				$final_output = array(
					array(
						'label' => $output,
						'value' => $output,
					),
				);
				$output = $final_output;
			}
		} elseif ( 'relationship' === $type ) {
			if ( ! empty( $output ) && is_object( $output ) ) {
				$final_output = '';
				if ( isset( $output->ID ) ) {
					$final_output = $output->ID;
				}
				$output = $final_output;
			} elseif ( ! empty( $output ) && is_array( $output ) ) {
				$final_output = $output[0];
				if ( is_object( $final_output ) && isset( $final_output->ID ) ) {
					$final_output = $final_output->ID;
				}
				$output = $final_output;
			}
		}
	}
	return $output;
}
