<?php declare(strict_types=1);

namespace Tribe\Plugin\Assets;

class Admin_Assets_Enqueuer extends Assets_Enqueuer {

	public const ADMIN             = 'admin';
	public const ASSETS_FILE       = self::ADMIN . '.asset.php';
	public const LOGIN             = 'custom-login';
	public const LOGIN_ASSETS_FILE = self::LOGIN . '.asset.php';

	public function register(): void {
		$args = $this->get_asset_file_args( $this->assets_path . self::ASSETS_FILE );
		wp_enqueue_style(
			self::ADMIN,
			$this->assets_path_uri . self::ADMIN . '.css',
			[],
			$args['version'] ?? false,
			'all',
		);
		wp_enqueue_script(
			self::ADMIN,
			$this->assets_path_uri . self::ADMIN . '.js',
			$args['dependencies'] ?? [],
			$args['version'] ?? false,
			true,
		);
	}

	public function enqueue_login_styles(): void {
		$args = $this->get_asset_file_args( $this->assets_path . self::LOGIN_ASSETS_FILE );

		wp_enqueue_style(
			self::LOGIN,
			$this->assets_path_uri . self::LOGIN . '.css',
			[],
			$args['version'] ?? false,
			'all',
		);
	}

}
