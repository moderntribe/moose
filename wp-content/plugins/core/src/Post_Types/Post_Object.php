<?php declare(strict_types=1);

namespace Tribe\Plugin\Post_Types;

use Tribe\Plugin\Object_Meta\Meta_Map;
use Tribe\Plugin\Object_Meta\Meta_Repository;

class Post_Object {

	public const NAME = '';

	protected mixed $meta;

	protected int $post_id = 0;

	public function __construct( int $post_id = 0, ?Meta_Map $meta = null ) {
		$this->post_id = $post_id;
		if ( isset( $meta ) ) {
			$this->meta = $meta;
		} else {
			$this->meta = new Meta_Map( static::NAME );
		}
	}

	/**
	 * Get an instance of the Post_Object corresponding
	 * to the \WP_Post with the given $post_id
	 *
	 * @param int $post_id The ID of an existing post
	 */
	public static function factory( int $post_id ): self {
		$meta_repo = apply_filters( Meta_Repository::GET_REPO_FILTER, null );

		if ( empty( $meta_repo ) ) {
			$meta_repo = new Meta_Repository();
		}

		$post_type = static::NAME;

		if ( empty( $post_type ) ) {
			$post_type = get_post_type( $post_id );
		}

		return new self( $post_id, $meta_repo->get( $post_type ) );
	}

	/**
	 * Get the value for the given meta key corresponding
	 * to this post.
	 *
	 * @param string $key
	 */
	public function get_meta( string $key ): mixed {
		return $this->meta->get_value( $this->post_id, $key );
	}

	public function __get( mixed $key ): mixed {
		return $this->get_meta( $key );
	}

}
