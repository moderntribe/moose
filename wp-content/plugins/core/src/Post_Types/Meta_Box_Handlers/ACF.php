<?php declare(strict_types=1);

namespace Tribe\Plugin\Post_Types\Meta_Box_Handlers;

use Tribe\Plugin\Post_Types\Post_Type_Config;

class ACF implements Meta_Box_Handler_Interface {

	/**
	 * Registers the meta boxes for a post type.
	 *
	 * @param \Tribe\Plugin\Post_Types\Post_Type_Config $config
	 */
	public function register_meta_boxes( Post_Type_Config $config ): void {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group( $config->get_meta_boxes() );
	}

	/**
	 * Hooks the meta box handler class to the required filters/actions if needed.
	 */
	public function hook(): void {
		add_filter( Meta_Box_Handler_Interface::INSTANCE_FILTER, function () {
			return $this;
		} );
	}

}
