<?php declare(strict_types=1);

namespace Tribe\Plugin\Taxonomies;

class Term_Object {

	public const string NAME = '';

	protected int $term_id = 0;

	/**
	 * Post_Object constructor.
	 *
	 * @param int $term_id The ID of a taxonomy term.
	 */
	public function __construct( int $term_id = 0 ) {
		$this->term_id = $term_id;
	}

	/**
	 * Get an instance of the Term_Object corresponding
	 * to the term with the given $term_id
	 *
	 * @param int $term_id The ID of an existing taxonomy term.
	 */
	public static function factory( int $term_id ): Term_Object {
		return new self( $term_id );
	}

	/**
	 * Get the value for the given meta key corresponding
	 * to this taxonomy term.
	 *
	 * @param string $key
	 */
	public function get_meta( string $key ): mixed {
		return get_term_meta( $this->term_id, $key, true );
	}

	/**
	 * Get term meta for the given key.
	 *
	 * @param string $key
	 */
	public function __get( string $key ): mixed {
		return $this->get_meta( $key );
	}

}
