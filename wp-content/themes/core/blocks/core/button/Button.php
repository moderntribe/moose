<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\button;

use Tribe\Plugin\Blocks\Block_Base;

class Button extends Block_Base {

	public function get_block_name(): string {
		return 'core/button';
	}

	public function get_block_styles(): array {
		return [
			'primary'   => esc_html__( 'Primary', 'tribe' ),
			'secondary' => esc_html__( 'Secondary', 'tribe' ),
			'ghost'     => esc_html__( 'Ghost', 'tribe' ),
		];
	}
	
}
