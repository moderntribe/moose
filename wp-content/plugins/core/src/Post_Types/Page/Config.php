<?php declare(strict_types=1);

namespace Tribe\Plugin\Post_Types\Page;

class Config {

	protected string $post_type = Page::NAME;

	/**
	 * Define block templates for initial editor state
	 */
	public function register_block_template(): void {
		$post_type_object           = get_post_type_object( Page::NAME );
		$post_type_object->template = [
			[
				'core/pattern',
				[
					'slug' => 'patterns/page',
				],
			],
		];
	}

}
