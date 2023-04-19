<?php declare(strict_types=1);

namespace Tribe\Plugin\Assets;

class Print_Assets_Enqueuer extends Assets_Enqueuer {

	public const PRINT       = 'print';
	public const ASSETS_FILE = self::PRINT . '.asset.php';

	public function register(): void {
		$args = $this->get_asset_file_args( $this->assets_path . self::ASSETS_FILE );

		wp_enqueue_style(
			self::PRINT,
			$this->assets_path_uri . self::PRINT . '.css',
			[],
			$args['version'] ?? false,
			'print',
		);
	}

}
