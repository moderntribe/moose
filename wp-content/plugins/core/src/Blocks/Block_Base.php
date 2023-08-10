<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks;

use Tribe\Plugin\Assets\Traits\Assets;

abstract class Block_Base {

	use Assets;

	protected string $assets_path;
	protected string $assets_path_uri;

	abstract public function get_block_name(): string;

	public function __construct( string $assets_folder = 'dist/assets/' ) {
		$this->assets_path     = trailingslashit( get_stylesheet_directory() ) . $assets_folder;
		$this->assets_path_uri = trailingslashit( get_stylesheet_directory_uri() ) . $assets_folder;
	}

	public function get_block_handle(): string {
		return sanitize_title( $this->get_block_name() );
	}

	public function get_block_path(): string {
		return $this->get_block_name();
	}

	/**
	 * Block styles to be defined in extending class
	 *
	 * @return array
	 */
	public function get_block_styles(): array {
		return [];
	}

	/**
	 * Block dependencies to be defined in extending class
	 *
	 * @return array
	 */
	public function get_block_dependencies(): array {
		return [];
	}

	/**
	 * Allows registration of additional block variations (called "Block Styles")
	 * Not to be confused with CSS Styles
	 */
	public function register_core_block_variations(): void {
		if ( ! function_exists( 'register_block_style' ) ) {
			return;
		}

		foreach ( $this->get_block_styles() as $name => $label ) {
			\register_block_style( $this->get_block_name(), [
				'name'  => $name,
				'label' => $label,
			] );
		}
	}

	/**
	 * Enqueue front-end block styles
	 */
	public function enqueue_core_block_front_style(): void {
		$block    = $this->get_block_handle();
		$path     = $this->get_block_path();
		$args     = $this->get_asset_file_args( get_theme_file_path( "dist/blocks/$path/index.asset.php" ) );
		$src_path = get_theme_file_path( "dist/blocks/$path/style-index.css" );
		$src      = get_theme_file_uri( "dist/blocks/$path/style-index.css" );

		if ( ! file_exists( $src_path ) ) {
			return;
		}

		wp_enqueue_block_style( $this->get_block_name(), [
			'handle' => "$block-styles",
			'src'    => $src,
			'deps'   => $this->get_block_dependencies(),
			'ver'    => $args['version'] ?? false,
			'media'  => 'all',
		] );
	}

	/**
	 * Enqueue core block editor front styles
	 * These are the FE styles.
	 */
	public function enqueue_core_block_editor_front_style(): void {
		$path     = $this->get_block_path();
		$src_path = get_theme_file_path( "dist/blocks/$path/style-index.css" );
		$src      = get_theme_file_uri( "dist/blocks/$path/style-index.css" );

		if ( ! file_exists( $src_path ) ) {
			return;
		}

		add_editor_style( $src );
	}

	/**
	 * Enqueue core block editor admin (editor) styles
	 * These are the editor specific style overrides for the block
	 */
	public function enqueue_core_block_editor_style(): void {
		$path            = $this->get_block_path();
		$editor_src_path = get_theme_file_path( "dist/blocks/$path/index.css" );
		$editor_src      = get_theme_file_uri( "dist/blocks/$path/index.css" );

		if ( ! file_exists( $editor_src_path ) ) {
			return;
		}

		add_editor_style( $editor_src );
	}

	public function enqueue_core_block_editor_script(): void {
		$block    = $this->get_block_handle();
		$path     = $this->get_block_path();
		$args     = $this->get_asset_file_args( get_theme_file_path( "dist/blocks/$path/admin.asset.php" ) );
		$src_path = get_theme_file_path( "dist/blocks/$path/editor.js" );
		$src      = get_theme_file_uri( "dist/blocks/$path/editor.js" );

		if ( ! file_exists( $src_path ) ) {
			return;
		}

		wp_enqueue_script(
			"admin-$block-scripts",
			$src,
			$args['dependencies'] ?? [],
			$args['version'] ?? false,
			true
		);
	}

}
