<?php declare(strict_types=1);

namespace Tribe\Plugin\Assets;

abstract class Assets_Enqueuer {

	protected string $assets_path;
	protected string $assets_path_uri;

	abstract public function register(): void;

	public function __construct( string $assets_folder = 'dist/' ) {
		$this->assets_path     = trailingslashit( get_stylesheet_directory() ) . $assets_folder;
		$this->assets_path_uri = trailingslashit( get_stylesheet_directory_uri() ) . $assets_folder;
	}

	protected function get_asset_file_args( string $file_path ): array {
		if ( ! file_exists( $file_path ) ) {
			return [];
		}
		$args = require $file_path;

		return is_array( $args ) ? $args : [];
	}

}
