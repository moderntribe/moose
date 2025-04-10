<?php declare(strict_types=1);

namespace Tribe\Plugin\Post_Types;

class Post_Object {

	public const NAME = '';

	protected int $post_id = 0;

	public function __construct( int $post_id = 0 ) {
		$this->post_id = $post_id;
	}

	/**
	 * Get an instance of the Post_Object corresponding
	 * to the \WP_Post with the given $post_id
	 *
	 * @param int $post_id The ID of an existing post
	 */
	public static function factory( int $post_id ): self {
		return new self( $post_id );
	}

	/**
	 * Get the value for the given meta key corresponding
	 * to this post.
	 *
	 * @param string $key
	 */
	public function get_meta( string $key ): mixed {
		return get_post_meta( $this->post_id, $key, true );
	}

	public function __get( mixed $key ): mixed {
		return $this->get_meta( $key );
	}

}
