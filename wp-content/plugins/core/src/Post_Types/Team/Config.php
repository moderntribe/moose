<?php declare(strict_types=1);

namespace Tribe\Plugin\Post_Types\Team;

use Tribe\Libs\Post_Type\Post_Type_Config;

class Config extends Post_Type_Config {

	// phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
	protected $post_type = Team::NAME;

	public function get_args(): array {
		return [
			'hierarchical'     => false,
			'enter_title_here' => esc_html__( 'Team', 'tribe' ),
			'menu_icon'        => 'dashicons-groups',
			'map_meta_cap'     => true,
			'supports'         => [
				'title',
				'editor',
			],
			'capability_type'  => 'post',
		];
	}

	public function get_labels(): array {
		return [
			'singular' => esc_html__( 'Team Member', 'tribe' ),
			'plural'   => esc_html__( 'Team', 'tribe' ),
			'slug'     => 'team',
		];
	}

}
