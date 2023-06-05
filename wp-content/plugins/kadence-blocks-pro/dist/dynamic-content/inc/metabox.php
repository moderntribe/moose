<?php
/**
 * Handle MetaBox Rendering.
 *
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle MetaBox Rendering.
 *
 * @param string  $meta_key the meta key.
 * @param string  $meta_type the meta type.
 * @param integer $object_id The source object id.
 * @param array   $args The args for the meta field.
 *
 * @return mixed Returns the dynamic content.
 */
function kbp_dynamic_content_metabox( $meta_key, $meta_type, $type, $object_id, $args ) {
	$output = '';
	if ( function_exists( 'rwmb_meta' ) ) {
		if ( 'mb_option' === $meta_type ) {
			list( $option_id, $real_key ) = explode( ':', $meta_key );
			$output = rwmb_meta( $real_key, array( 'object_type' => 'setting' ), $option_id );
		} elseif ( strpos( $object_id, ':' ) !== false ) {
			list( $object_type, $real_id ) = explode( ':', $object_id );
			if ( $object_type === 'term' ) {
				$output = rwmb_meta( $meta_key, array( 'object_type' => 'term' ), $real_id );
			}
		} else {
			$output = rwmb_meta( $meta_key, '', $object_id );
		}
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
			// Get the first in the list.
			if ( $output && is_array( $output ) && ! isset( $output['url'] ) && ! isset( $output['ID'] ) ) {
				$output = reset( $output );
			}
			if ( $output && is_array( $output ) && isset( $output['url'] ) ) {
				$output = $output['url'];
			} elseif ( $output && is_array( $output ) && isset( $output['ID'] ) ) {
				$image  = wp_get_attachment_url( $output['ID'] );
				$output = ( $image ? $image : '' );
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
								$sub_value_string =  $subvalue[ $first_key ];
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
				if ( isset( $output->id ) ) {
					$final_output = $output->id;
				}
				$output = $final_output;
			} elseif ( ! empty( $output ) && is_array( $output ) ) {
				$final_output = $output[0];
				$output = $final_output;
			}
		}
	}
	return $output;
}
