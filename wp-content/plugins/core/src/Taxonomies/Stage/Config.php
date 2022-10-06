<?php declare(strict_types=1);

namespace Tribe\Plugin\Taxonomies\Stage;

// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
use Tribe\Libs\Taxonomy\Taxonomy_Config;
use Tribe\Plugin\Post_Types\Portfolio\Portfolio;

class Config extends Taxonomy_Config {

	/**
	 * @var string
	 */
	protected $taxonomy = Stage::NAME;

	/**
	 * @var string[]
	 */
	protected $post_types = [ Portfolio::NAME ];

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
			'singular' => esc_html__( 'Stage', 'tribe' ),
			'plural'   => esc_html__( 'Stages', 'tribe' ),
			'slug'     => 'stage',
		];
	}

	public function default_terms(): array {
		return [];
	}

}
