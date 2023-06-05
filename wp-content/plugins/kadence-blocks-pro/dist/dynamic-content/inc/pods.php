<?php
/**
 * Handle Pods Rendering.
 *
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle Pods Rendering.
 *
 * @param string  $meta_key the meta key.
 * @param string  $meta_type the meta type.
 * @param integer $object_id The source object id.
 * @param array   $args The args for the meta field.
 *
 * @return mixed Returns the dynamic content.
 */
function kbp_dynamic_content_pods( $meta_key, $meta_type, $type, $object_id, $args ) {
	$output = '';
	if ( function_exists( 'pods' ) ) {
		if ( 'pod_option' === $meta_type ) {
			list( $option_id, $real_key ) = explode( ':', $meta_key );
			$pod_settings = pods( $option_id );
			if ( $pod_settings ) {
				$output = $pod_settings->display( $real_key );
			}
		} elseif ( strpos( $object_id, ':' ) !== false ) {
			list( $object_type, $real_id ) = explode( ':', $object_id );
			if ( $object_type === 'term' ) {
				$term = get_queried_object();
				if ( is_object( $term ) && isset( $term->term_id ) ) {
					$output = get_term_meta( $term->term_id, $meta_key, true );
				}
			}
		} else {
			if ( 'relationship' === $type || 'background' === $type || 'image' === $type || 'gallery' === $type  ) {
				$pod_object = pods( get_post_type( $object_id ), $object_id );
				if ( is_object( $pod_object ) && $pod_object->exists() ) {
					$output = $pod_object->field( $meta_key );
				}
			} else {
				$output = pods_field_display( get_post_type( $object_id ), $object_id, $meta_key );
			}
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
				if ( ! empty( $output['ID'] ) ) {
					$final_output = $output['ID'];
				} else if ( ! empty( $output[0] ) ) {
					$final_output = $output[0];
				} else {
					$final_output = '';
				}
				$output = $final_output;
			}
		}
	}
	return $output;
}
