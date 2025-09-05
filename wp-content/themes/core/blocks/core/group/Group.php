<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\group;

use Tribe\Plugin\Blocks\Block_Base;

class Group extends Block_Base {

	public function get_block_name(): string {
		return 'core/group';
	}

	public function get_block_styles(): array {
		return [
			'light' => __( 'Light', 'tribe' ),
			'dark'  => __( 'Dark', 'tribe' ),
			'brand' => __( 'Brand', 'tribe' ),
		];
	}

}
