<?php declare(strict_types=1);

namespace Tribe\Plugin\Integrations;

use Tribe\Plugin\Post_Types\Training\Training;

class YoastSEO {

	public function exclude_post_types( $post_types ): array {
		if ( ! is_array( $post_types ) ) {
			return [];
		}

		$post_types = array_diff( $post_types, array( Training::NAME ) );

		return $post_types;
	}

}
