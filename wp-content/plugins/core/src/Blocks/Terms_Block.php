<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks;

use Tribe\Plugin\Templates\Traits\Primary_Term;

class Terms_Block {

	use Primary_Term;

	private string $taxonomy        = 'category';
	private bool $only_primary_term = false;
	private bool $has_links         = false;

	/**
	 * @var \WP_Term[]
	 */
	private array $terms = [];

	public function __construct( array $block_attributes ) {
		$this->taxonomy          = $block_attributes['taxonomyToUse'] ?? 'category';
		$this->only_primary_term = $block_attributes['onlyPrimaryTerm'] ?? false;
		$this->has_links         = $block_attributes['hasLinks'] ?? false;
	}

	/**
	 * @return \WP_Term[]
	 */
	public function get_the_terms(): array {
		$post_id = get_the_ID();

		if ( ! $post_id ) {
			return $this->terms;
		}

		if ( $this->only_primary_term ) {
			$term = $this->get_primary_term( $post_id, $this->taxonomy );
			if ( $term ) {
				$this->terms[] = $term;
			}
		} else {
			$terms = get_the_terms( $post_id, $this->taxonomy );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$this->terms = $terms;
			}
		}

		return $this->terms;
	}

	public function display_as_links(): bool {
		return (bool) $this->has_links;
	}

	public function get_taxonomy_name(): string {
		$taxonomy_object = get_taxonomy( $this->taxonomy );

		return $taxonomy_object
			? $taxonomy_object->labels->name
			: esc_html_x( 'terms', 'Generic name for an unknown taxonomy\'s items', 'tribe' );
	}

}
