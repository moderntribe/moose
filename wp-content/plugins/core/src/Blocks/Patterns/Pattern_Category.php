<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks\Patterns;

class Pattern_Category {

	public const CUSTOM_PATTERN_CATEGORIES = [
		[ 'slug' => 'cta', 'name' => 'Calls to Action' ],
		[ 'slug' => 'cards', 'name' => 'Cards' ],
		[ 'slug' => 'headers', 'name' => 'Headers' ],
		[ 'slug' => 'media', 'name' => 'Media' ],
		[ 'slug' => 'templates', 'name' => 'Templates' ],
	];

	public function register_pattern_category(): void {
		foreach ( self::CUSTOM_PATTERN_CATEGORIES as $category ) {
			register_block_pattern_category(
				$category['slug'],
				[
					'label' => esc_html__( $category['name'], 'tribe' ),
				]
			);
		}
	}

}
