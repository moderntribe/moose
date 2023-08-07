<?php declare(strict_types=1);

namespace Tribe\Plugin\Assets;

class Public_Assets_Enqueuer extends Assets_Enqueuer {

	public const PUBLIC      = 'theme';
	public const ASSETS_FILE = self::PUBLIC . '.asset.php';

	public function register(): void {
		$args = $this->get_asset_file_args( $this->assets_path . self::ASSETS_FILE );

		wp_enqueue_style(
			self::PUBLIC,
			$this->assets_path_uri . self::PUBLIC . '.css',
			[],
			$args['version'] ?? false,
			'all',
		);

		wp_enqueue_script(
			self::PUBLIC,
			$this->assets_path_uri . self::PUBLIC . '.js',
			$args['dependencies'] ?? [],
			$args['version'] ?? false,
			true,
		);
	}

}
