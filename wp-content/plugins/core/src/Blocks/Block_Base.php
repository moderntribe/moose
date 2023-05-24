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
	 * Allows registration of additional block styles
	 */
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

	/**
	 * Register block styles prior to enqueueing to allow other blocks to define these as dependencies
	 */
	public function register_style(): void {
		$block = $this->get_block_handle();
		$path  = $this->get_block_path();
		$args  = $this->get_asset_file_args( get_theme_file_path( "dist/blocks/$path/index.asset.php" ) );
		$src   = get_theme_file_uri( "dist/blocks/$path/style-index.css" );

		if ( ! wp_remote_get( $src ) ) {
			return;
		}

		wp_register_style(
			"$block-styles",
			$src,
			$this->get_block_dependencies(),
			$args['version'] ?? false
		);
	}

	/**
	 * Enqueue our registered stylesheet for this specific block
	 */
	public function enqueue_block_style(): void {
		$block = $this->get_block_handle();

		wp_enqueue_block_style( $this->get_block_name(), [
			'handle' => "$block-styles",
		] );
	}

	public function register_admin_scripts(): void {
		$block = $this->get_block_handle();
		$path  = $this->get_block_path();
		$args  = $this->get_asset_file_args( get_theme_file_path( "dist/blocks/$path/admin.asset.php" ) );
		$src   = get_theme_file_uri( "dist/blocks/$path/admin.js" );
		$file  = wp_remote_get( $src );

		if ( ! is_array( $file ) || is_wp_error( $file ) || 200 !== wp_remote_retrieve_response_code( $file ) ) {
			return;
		}

		wp_register_script(
			"admin-$block-scripts",
			$src,
			$args['dependencies'] ?? [],
			$args['version'] ?? false,
			true
		);
	}

	public function enqueue_admin_scripts(): void {
		$block = $this->get_block_handle();

		wp_enqueue_script( "admin-$block-scripts" );
	}

}
