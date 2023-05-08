<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\spacer;

use Tribe\Plugin\Blocks\Styles\Block_Styles_Base;

class Spacer implements Block_Styles_Base {

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

	public function get_block_variations(): array {
		return [];
	}

	public function enqueue_block_styles(): void {
		// Enqueue block-specific styles for this block via `wp_enqueue_block_style()`
	}

	public function enqueue_block_scripts(): void {
		// Enqueue block-specific scripts for this block via ????
	}

}
