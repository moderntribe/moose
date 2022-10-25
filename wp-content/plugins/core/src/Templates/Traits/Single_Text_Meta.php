<?php declare(strict_types=1);

namespace Tribe\Plugin\Templates\Traits;

trait Single_Text_Meta {

	public function get_single_text_meta( string $key, int $id = 0 ): string {
		return (string) get_post_meta( $id ?: get_the_ID(), $key, true );
	}

}
