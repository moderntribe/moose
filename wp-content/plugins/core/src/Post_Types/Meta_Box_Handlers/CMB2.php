<?php declare(strict_types=1);

namespace Tribe\Plugin\Post_Types\Meta_Box_Handlers;

use Tribe\Plugin\Post_Types\Post_Type_Config;

class CMB2 implements Meta_Box_Handler_Interface {

	protected Post_Type_Config $config;

	/**
	 * Registers the meta boxes for a post type.
	 *
	 * @param \Tribe\Plugin\Post_Types\Post_Type_Config $config
	 */
	public function register_meta_boxes( Post_Type_Config $config ): void {
		$this->config = $config;
	}

	/**
	 * Hooks the meta box handler class to the required filters/actions if needed.
	 */
	public function hook(): void {
		add_filter( Meta_Box_Handler_Interface::INSTANCE_FILTER, function () {
			return $this;
		} );

		$config = $this->config;

		add_filter( 'cmb2_meta_boxes', static function ( $meta_boxes ) use ( $config ) {
			$post_type_meta_boxes = $config->get_meta_boxes();
			$post_type_meta_boxes = apply_filters( "tribe_{$config->post_type()}_meta_boxes", $post_type_meta_boxes );
			$meta_boxes           = array_merge( $meta_boxes, $post_type_meta_boxes );

			return $meta_boxes;
		} );
	}

}
