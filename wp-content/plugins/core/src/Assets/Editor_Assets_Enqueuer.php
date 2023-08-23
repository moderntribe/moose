<?php declare(strict_types=1);

namespace Tribe\Plugin\Assets;

class Editor_Assets_Enqueuer extends Assets_Enqueuer {

	public const EDITOR           = 'tribe-editor';
	public const EDITOR_FILE_NAME = 'editor';
	public const ASSETS_FILE      = self::EDITOR_FILE_NAME . '.asset.php';

	public function register(): void {
		$args = $this->get_asset_file_args( $this->assets_path . self::ASSETS_FILE );

		// add_editor_style( $this->assets_path_uri . self::EDITOR_FILE_NAME . '.css' );

		wp_enqueue_style(
			self::EDITOR,
			$this->assets_path_uri . self::EDITOR_FILE_NAME . '.css',
			[],
			$args['version'] ?? false,
			'all',
		);

		wp_enqueue_script(
			self::EDITOR,
			$this->assets_path_uri . self::EDITOR_FILE_NAME . '.js',
			$args['dependencies'] ?? [],
			$args['version'] ?? false,
			true,
		);
	}

}
