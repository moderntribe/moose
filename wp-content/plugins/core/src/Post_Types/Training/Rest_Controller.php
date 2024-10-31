<?php declare(strict_types=1);

namespace Tribe\Plugin\Post_Types\Training;

use WP_REST_Posts_Controller;

class Rest_Controller extends WP_REST_Posts_Controller {

	public function check_read_permission( $post ) {
		$post_type_object = get_post_type_object( get_post_type( $post ) );

		if ( ! $this->check_is_post_type_allowed( $post_type_object ) ) {
			return false;
		}

		return is_post_type_viewable( $post_type_object );
	}

}
