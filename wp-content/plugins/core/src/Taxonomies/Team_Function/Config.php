<?php declare(strict_types=1);

namespace Tribe\Plugin\Taxonomies\Team_Function;

// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
use Tribe\Libs\Taxonomy\Taxonomy_Config;
use Tribe\Plugin\Post_Types\Team\Team;

class Config extends Taxonomy_Config {

	/**
	 * @var string
	 */
	protected $taxonomy = Team_Function::NAME;

	/**
	 * @var string[]
	 */
	protected $post_types = [ Team::NAME ];

	/**
	 * @var int
	 */
	protected $version = 0;

	public function get_args(): array {
		return [
			'show_in_rest' => true,
			'hierarchical' => false,
		];
	}

	public function get_labels(): array {
		return [
			'singular' => esc_html__( 'Function', 'tribe' ),
			'plural'   => esc_html__( 'Functions', 'tribe' ),
			'slug'     => 'function',
		];
	}

	public function default_terms(): array {
		return [];
	}

}
