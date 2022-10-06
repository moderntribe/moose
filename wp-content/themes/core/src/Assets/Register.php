<?php declare(strict_types=1);

namespace Tribe\Theme\Assets;

class Register {

	public const PUBLIC = 'public';
	public const ADMIN  = 'admin';

	private string $assets_path;
	private string $assets_path_uri;

	public function __construct() {
		$this->assets_path     = trailingslashit( get_stylesheet_directory() ) . 'dist/';
		$this->assets_path_uri = trailingslashit( get_stylesheet_directory_uri() ) . 'dist/';

		add_action( 'wp_enqueue_scripts', [ $this, 'register_public_scripts' ], 10 );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_scripts' ], 10 );
	}

	public function register_public_scripts(): void {
		$public_asset_file = $this->assets_path . self::PUBLIC . '.asset.php';
		if ( ! file_exists( $public_asset_file ) ) {
			return;
		}
		$args = require $public_asset_file;
		wp_enqueue_style( self::PUBLIC, $this->assets_path_uri . self::PUBLIC . '.css', [], $args['version'], 'all' );
		wp_enqueue_script( self::PUBLIC, $this->assets_path_uri . self::PUBLIC . '.js', $args['dependencies'], $args['version'], true );
	}

	public function register_admin_scripts(): void {
		$admin_asset_file = $this->assets_path . self::ADMIN . '.asset.php';
		if ( ! file_exists( $admin_asset_file ) ) {
			return;
		}
		$args = require $admin_asset_file;
		wp_enqueue_style( self::PUBLIC, $this->assets_path_uri . self::ADMIN . '.css', [], $args['version'], 'all' );
		wp_enqueue_script( self::PUBLIC, $this->assets_path_uri . self::ADMIN . '.js', $args['dependencies'], $args['version'], true );
	}

}
