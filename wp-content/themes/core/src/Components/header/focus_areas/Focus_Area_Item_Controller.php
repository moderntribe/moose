<?php declare(strict_types=1);

namespace Tribe\Theme\Components\header\focus_areas;

use Tribe\Theme\Components\Abstract_Controller;

class Focus_Area_Item_Controller extends Abstract_Controller {

	public function get_thumbnail_id( int $item_id ): int {
		return (int) get_post_thumbnail_id( $item_id );
	}

	public function get_thumbnail_url( int $id ): string {
		return (string) wp_get_attachment_url( $id );
	}

	public function get_thumbnail_alt( int $id ): string {
		return (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
	}

}
