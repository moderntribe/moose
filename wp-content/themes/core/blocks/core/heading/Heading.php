<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\heading;

use Tribe\Plugin\Blocks\Block_Base;

class Heading extends Block_Base {

	public function get_block_name(): string {
		return 'core/heading';
	}

	public function get_block_styles(): array {
		return [
			'x-large'  => esc_html__( 'X-Large', 'tribe' ),
			'large'    => esc_html__( 'Large', 'tribe' ),
			'medium'   => esc_html__( 'Medium', 'tribe' ),
			'small'    => esc_html__( 'Small', 'tribe' ),
			'x-small'  => esc_html__( 'X-Small', 'tribe' ),
			'xx-small' => esc_html__( 'XX-Small', 'tribe' ),
		];
	}

}
