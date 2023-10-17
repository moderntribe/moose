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
		if ( $this->only_primary_term ) {
			$primary_term = $this->get_primary_term( get_the_ID(), $this->taxonomy );
			if ( $primary_term ) {
				$this->terms[] = $primary_term;

				return $this->terms;
			}
		}

		$all_terms = get_the_terms( get_the_ID(), $this->taxonomy );
		if ( $all_terms && ! is_wp_error( $all_terms ) ) {
			$this->terms = $all_terms;

			return $this->terms;
		}

		/**
		 * If we are in a REST_REQUEST then we are using the editor and
		 * need to fake a term so that the user can see where it will
		 * show if it is added to the post.  This will not show on
		 * the FE of the site.
		 */
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			$fake_term = new \WP_Term( (object) [
				'term_id'  => -1,
				'name'     => esc_html__( 'Terms Display Here', 'tribe' ),
				'slug'     => 'terms-display-here',
				'taxonomy' => 'tag',
			]);

			$this->terms[] = $fake_term;
		}

		return $this->terms;
	}

	public function display_as_links(): bool {
		return (bool) $this->has_links;
	}

}
