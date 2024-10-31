<?php declare(strict_types=1);

namespace Tribe\Plugin\Post_Types\Training;

use Tribe\Libs\Post_Type\Post_Type_Config;

class Config extends Post_Type_Config {

	// phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
	protected $post_type = Training::NAME;

	public function get_args(): array {
		return [
			'has_archive'     => false,
			'hierarchical'    => false,
			'menu_icon'       => 'dashicons-welcome-learn-more',
			'map_meta_cap'    => true,
			'supports'        => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ],
			'rewrite'         => [ 'slug' => 'company/news' ],
			'capability_type' => 'post',
			'show_in_rest'    => true,
			'menu_position'   => 19,
		];
	}

	public function get_labels(): array {
		return [
			'singular' => __( 'Training', 'tribe' ),
			'plural'   => __( 'Training', 'tribe' ),
			'slug'     => Training::NAME,
		];
	}

	/**
	 * Define block templates for initial editor state
	 */
	public function register_block_template(): void {
		$post_type_object           = get_post_type_object( Training::NAME );
		$post_type_object->template = [
			[
				'core/pattern',
				[
					'slug' => 'patterns/page', // Use patterns for Pages.
				],
			],
		];
	}

}
