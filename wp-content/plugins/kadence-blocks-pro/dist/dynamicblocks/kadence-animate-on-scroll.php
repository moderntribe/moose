<?php
/**
 * Enqueue JS for Custom Icons and build admin for icons.
 *
 * @since   1.4.0
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue JS for Custom Icons and build admin for icons.
 *
 * @category class
 */
class Kadence_Blocks_Pro_AOS {
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
		add_action( 'init', array( $this, 'on_init' ) );

	}
	/**
	 * On init
	 */
	public function on_init() {
		if ( ! is_admin() ) {
			//add_action( 'render_block', array( $this, 'render_blocks' ), 13, 2 );
		}
	}
	/**
	 * Check for animate on scroll settings.
	 *
	 * @param mixed $block_content The block content.
	 * @param array $block The block data.
	 *
	 * @return mixed Returns the block content.
	 */
	public function render_blocks( $block_content, $block ) {
		//print_r( $block );
		$blocks_to_add_meta = array(
			'kadence/image'
		);
		if ( isset( $block['blockName'] ) && ( in_array( $block['blockName'], $blocks_to_add_meta ) ) ) {
			if ( ! empty( $block['attrs']['kadenceAnimation'] ) ) {
				$replace_string = 'data-aos="' . $block['attrs']['kadenceAnimation'] . '"';
				if ( isset( $block['attrs']['kadenceAOSOptions'][ 0 ] ) && is_array( $block['attrs']['kadenceAOSOptions'][ 0 ] ) ) {
					if ( ! empty( $block['attrs']['kadenceAOSOptions'][ 0 ]['offset'] ) ) {
						$replace_string = 'data-aos-offset="' . $block['attrs']['kadenceAOSOptions'][ 0 ]['offset'] . '" ' . $replace_string;
					}
					if ( ! empty( $block['attrs']['kadenceAOSOptions'][ 0 ]['duration'] ) ) {
						$replace_string = 'data-aos-duration="' . $block['attrs']['kadenceAOSOptions'][ 0 ]['duration'] . '" ' . $replace_string;
					}
					if ( ! empty( $block['attrs']['kadenceAOSOptions'][ 0 ]['delay'] ) ) {
						$replace_string = 'data-aos-delay="' . $block['attrs']['kadenceAOSOptions'][ 0 ]['delay'] . '" ' . $replace_string;
					}
					if ( ! empty( $block['attrs']['kadenceAOSOptions'][ 0 ]['easing'] ) ) {
						$replace_string = 'data-aos-easing="' . $block['attrs']['kadenceAOSOptions'][ 0 ]['easing'] . '" ' . $replace_string;
					}
					if ( ! empty( $block['attrs']['kadenceAOSOptions'][ 0 ]['once'] ) ) {
						$replace_string = 'data-aos-once="' . $block['attrs']['kadenceAOSOptions'][ 0 ]['once'] . '" ' . $replace_string;
					}
				}
				switch ( $block['blockName'] ) {
					case 'kadence/image':
						$replace_string = $replace_string . ' class="wp-block-kadence-image';
						$block_content = str_replace( 'class="wp-block-kadence-image', $replace_string, $block_content );
						break;			
				}
			}

		}
		return $block_content;
	}
}
Kadence_Blocks_Pro_AOS::get_instance();
