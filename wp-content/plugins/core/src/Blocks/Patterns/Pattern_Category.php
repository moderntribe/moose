<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks\Patterns;

class Pattern_Category {

	public const CUSTOM_PATTERN_CATEGORY_SLUG = 'tribe_custom';

	public function register_pattern_category(): void {
		register_block_pattern_category(
			self::CUSTOM_PATTERN_CATEGORY_SLUG,
			[
				'label' => esc_html__( 'Custom', 'tribe' ),
			]
		);
	}

}
