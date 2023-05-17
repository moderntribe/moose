<?php declare(strict_types=1);

namespace Tribe\Plugin\Assets;

use Tribe\Plugin\Assets\Traits\Assets;

abstract class Assets_Enqueuer {

	use Assets;

	protected string $assets_path;
	protected string $assets_path_uri;

	abstract public function register(): void;

	public function __construct( string $assets_folder = 'dist/assets/' ) {
		$this->assets_path     = trailingslashit( get_stylesheet_directory() ) . $assets_folder;
		$this->assets_path_uri = trailingslashit( get_stylesheet_directory_uri() ) . $assets_folder;
	}

}
