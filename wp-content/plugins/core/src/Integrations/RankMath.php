<?php declare(strict_types=1);

namespace Tribe\Plugin\Integrations;

use Tribe\Plugin\Post_Types\Training\Training;

class RankMath {

	public function exclude_post_types( array $post_types ): array {
		$post_types = array_diff( $post_types, [ Training::NAME ] );

		return $post_types;
	}

}
