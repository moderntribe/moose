<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\paragraph;

use Tribe\Plugin\Blocks\Block_Base;

class Paragraph extends Block_Base {

	public function get_block_name(): string {
		return 'core/paragraph';
	}

	public function get_block_styles(): array {
		return [
			'large' => esc_html__( 'Large', 'tribe' ),
			'small' => esc_html__( 'Small', 'tribe' ),
		];
	}

}
