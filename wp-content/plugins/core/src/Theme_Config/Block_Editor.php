<?php declare(strict_types=1);

namespace Tribe\Plugin\Theme_Config;

class Block_Editor {

	/**
	 * Dsiable the WP block editor font library
	 */
	public function disable_font_library_ui( array $settings ): array {
		$settings['fontLibraryEnabled'] = false;

		return $settings;
	}

}
