<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\paragraph;

use Tribe\Plugin\Blocks\Block_Base;

class Paragraph extends Block_Base {

	public function get_block_name(): string {
		return 'core/paragraph';
	}

	public function get_block_styles(): array {
		return [
			'small'    => esc_html__( 'Small', 'tribe' ),
			'large'    => esc_html__( 'Large', 'tribe' ),
			'overline' => esc_html__( 'Overline', 'tribe' ),
		];
	}

}
