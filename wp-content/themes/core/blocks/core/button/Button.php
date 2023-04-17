<?php declare(strict_types=1);

namespace Tribe\Theme\Blocks\Core;

class Button {

	public function get_block_name(): string {
		return 'core/button';
	}

	public function get_block_styles(): array {
		return [
			'arrow-right' => esc_html__( 'Arrow Right', 'tribe' ),
			'arrow-left'  => esc_html__( 'Arrow Left', 'tribe' ),
			'transparent' => esc_html__( 'Transparent', 'tribe' ),
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
