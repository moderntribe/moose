<?php declare(strict_types=1);

namespace Tribe\Theme\bindings;

use Tribe\Plugin\Blocks\Bindings\Binding_Base;

class Post_Type_Name extends Binding_Base {

	/**
	 * example markup:
<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"tribe/post-type-name"}}}} -->
<p>Post Type Name Placeholder</p>
<!-- /wp:paragraph -->
	 */

	public function get_slug(): string {
		return 'tribe/post-type-name';
	}

	public function get_args(): array {
		return [
			Binding_Base::LABEL              => __( 'Post Type Name', 'tribe' ),
			Binding_Base::GET_VALUE_CALLBACK => [ $this, 'tribe_get_post_type_name' ],
		];
	}

	public function tribe_get_post_type_name(): string {
		// this gets us the post type, but we really want the name
		$block_post_type = get_post_type();

		if ( ! $block_post_type ) {
			return '';
		}

		$post_object = get_post_type_object( $block_post_type );

		if ( ! $post_object ) {
			return '';
		}

		return esc_html__( $post_object->labels->singular_name, 'tribe' );
	}

}
