<?php declare(strict_types=1);

namespace Tribe\Plugin\Theme_Config;

class Theme_Support {

	/**
	 * @action after_setup_theme
	 */
	public function add_theme_supports(): void {
		$this->remove_support_block_widgets();
	}

	/**
	 * Disable Core Block Patterns.
	 */
	public function disable_block_patterns(): void {
		remove_theme_support( 'core-block-patterns' );
	}

	/**
	 * Disable Openverse Media Category.
	 *
	 * @param array $settings
	 */
	public function disable_openverse_media_category( array $settings ): array {
		$settings['enableOpenverseMediaCategory'] = false;

		return $settings;
	}

	/**
	 * Dsiable the WP block editor font library
	 */
	public function disable_font_library_ui( array $settings ): array {
		$settings['fontLibraryEnabled'] = false;

		return $settings;
	}

	/**
	 * Disable Block Editor Widget Support
	 */
	private function remove_support_block_widgets(): void {
		remove_theme_support( 'widgets-block-editor' );
	}

}
