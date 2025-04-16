<?php declare(strict_types=1);

namespace Tribe\Plugin\Post_Types\Meta_Box_Handlers;

use Tribe\Plugin\Post_Types\Post_Type_Config;

interface Meta_Box_Handler_Interface {

	public const INSTANCE_FILTER = 'tribe_libs_meta_box_handler';

	/**
	 * Hooks the meta box handler class to the required filters/actions if needed.
	 */
	public function hook(): void;

	/**
	 * Registers the meta boxes for a post type.
	 *
	 * @param \Tribe\Plugin\Post_Types\Post_Type_Config $config
	 */
	public function register_meta_boxes( Post_Type_Config $config ): void;

}
