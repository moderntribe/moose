<?php declare(strict_types=1);

namespace Tribe\Plugin\Assets\Traits;

trait Assets {

	public function get_asset_file_args( string $file_path ): array {
		if ( ! file_exists( $file_path ) ) {
			return [];
		}
		$args = require $file_path;

		return is_array( $args ) ? $args : [];
	}

}
