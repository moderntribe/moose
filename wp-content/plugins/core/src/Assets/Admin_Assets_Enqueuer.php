<?php declare(strict_types=1);

namespace Tribe\Plugin\Assets;

class Admin_Assets_Enqueuer extends Assets_Enqueuer {

	public const ADMIN       = 'admin';
	public const ASSETS_FILE = self::ADMIN . '.asset.php';

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

}
