<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\button;

use Tribe\Plugin\Blocks\Styles\Block_Styles_Base;

class Button implements Block_Styles_Base {

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
