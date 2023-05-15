<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\spacer;

use Tribe\Plugin\Blocks\Block_Base;

class Spacer extends Block_Base {

	public function get_block_name(): string {
		return 'core/spacer';
	}

	public function get_block_styles(): array {
		return [
			'xx-small' => esc_html__( 'XX Small', 'tribe' ),
			'x-small'  => esc_html__( 'X Small', 'tribe' ),
			'small'    => esc_html__( 'Small', 'tribe' ),
			'medium'   => esc_html__( 'Medium', 'tribe' ),
			'large'    => esc_html__( 'Large', 'tribe' ),
			'x-large'  => esc_html__( 'X Large', 'tribe' ),
			'xx-large' => esc_html__( 'XX Large', 'tribe' ),
		];
	}

}
