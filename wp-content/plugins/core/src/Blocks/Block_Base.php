<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks;

use Tribe\Plugin\Assets\Traits\Assets;

abstract class Block_Base {

	use Assets;

	protected string $assets_path;
	protected string $assets_path_uri;

	abstract public function get_block_name(): string;

	abstract public function register_assets(): void;

	public function __construct( string $assets_folder = 'dist/assets/' ) {
		$this->assets_path     = trailingslashit( get_stylesheet_directory() ) . $assets_folder;
		$this->assets_path_uri = trailingslashit( get_stylesheet_directory_uri() ) . $assets_folder;
	}

	public function get_block_path(): string {
		return $this->get_block_name();
	}

	/**
	 * @return array
	 */
	public function get_block_styles(): array {
		return [];
	}

	/**
	 * @return array
	 */
	public function get_block_dependencies(): array {
		return [];
	}

	public function register_block_style(): void {
		if ( ! function_exists( 'register_block_style' ) ) {
			return;
		}

		foreach ( $this->get_block_styles() as $name => $label ) {
			register_block_style( $this->get_block_name(), [
				'name'  => $name,
				'label' => $label,
			] );
		}
	}

	public function enqueue_block_style(): void {
		$block = $this->get_block_name();
		$path  = $this->get_block_path();
		$args  = $this->get_asset_file_args( get_theme_file_path( "dist/blocks/$path/index.asset.php" ) );
		$src   = get_theme_file_uri( "dist/blocks/$path/style-index.css" );

		if ( ! file_get_contents( $src ) ) {
			return;
		}

		wp_enqueue_block_style( $block, [
			'handle' => "$block-styles",
			'src'    => $src,
			'ver'    => $args['version'] ?? false,
			'deps'   => $this->get_block_dependencies(),
		] );
	}

}
