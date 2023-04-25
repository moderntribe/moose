<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\lists;

class Lists {

	public function get_block_name(): string {
		return 'core/lists';
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