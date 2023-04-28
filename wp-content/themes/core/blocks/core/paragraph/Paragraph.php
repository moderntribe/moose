<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\paragraph;

use Tribe\Plugin\Blocks\Styles\Block_Styles_Base;

class Paragraph implements Block_Styles_Base {

	public function get_block_name(): string {
		return 'core/paragraph';
	}

	public function get_block_styles(): array {
		return [
			'large' => esc_html__( 'Large', 'tribe' ),
			'small' => esc_html__( 'Small', 'tribe' ),
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
