<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks;

class Block_Category {

	public const CUSTOM_BLOCK_CATEGORY_SLUG = 'tribe-custom';

	/**
	 * Adds the Custom Category to the front of the block list categories.
	 *
	 * @filter block_categories_all
	 *
	 * @param array $categories
	 */
	public function custom_block_category( array $categories ): array {
		return array_merge( [
			[
				'slug'  => self::CUSTOM_BLOCK_CATEGORY_SLUG,
				'title' => esc_html__( 'Custom', 'tribe' ),
				'icon'  => '<svg enable-background="new 0 0 146.3 106.3" version="1.1" viewBox="0 0 146.3 106.3" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"><style type="text/css">.st0{fill:#16D690;}.st1{fill:#21A6CB;}.st2{fill:#008F8F;}</style><polygon class="st0" points="145.2 106.3 72.6 42.3 26.5 1.2 0 106.3"/><polygon class="st1" points="145.2 106.3 0 106.3 72.6 42.3 118.6 1.2"/><polygon class="st2" points="72.6 42.3 145.2 106.3 0 106.3"/></svg>',
			],
		], $categories );
	}

}
