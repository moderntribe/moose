<?php declare(strict_types=1);

namespace Tribe\Theme\bindings;

use Tribe\Plugin\Blocks\Bindings\Binding_Base;

class Post_Permalink extends Binding_Base {

	public function get_slug(): string {
		return 'tribe/post-permalink';
	}

	public function get_args(): array {
		return [
			Binding_Base::LABEL              => __( 'Post Permalink', 'tribe' ),
			Binding_Base::GET_VALUE_CALLBACK => [ $this, 'tribe_get_post_permalink' ],
		];
	}

	public function tribe_get_post_permalink(): string {
		$post_permalink = get_the_permalink();

		return esc_html( $post_permalink );
	}

}
