<?php declare(strict_types=1);

namespace Tribe\Plugin\Config;

class Theme_Support {

	/**
	 * @action after_setup_theme
	 */
	public function add_theme_supports(): void {
		$this->support_thumbnails();
		$this->support_title_tag();
		$this->support_responsive_embeds();
		$this->support_html5();
		$this->support_block_styles();
		$this->remove_support_block_widgets();
	}

	/**
	 * Disable Core Block Patterns.
	 */
	public function disable_block_patterns(): void {
		remove_theme_support( 'core-block-patterns' );
	}

	/**
	 * Supports: enable Featured Images
	 */
	private function support_thumbnails(): void {
		add_theme_support( 'post-thumbnails' );
	}

	/**
	 * Supports: enable Document Title Tag
	 */
	private function support_title_tag(): void {
		add_theme_support( 'title-tag' );
	}

	private function support_responsive_embeds(): void {
		add_theme_support( 'responsive-embeds' );
	}

	/**
	 * Support: switch core WordPress markup to output valid HTML5
	 */
	private function support_html5(): void {
		add_theme_support( 'html5', [
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
			'style',
		] );
	}

	/**
	 * Support: default Gutenberg block styles in the theme.
	 */
	private function support_block_styles(): void {
		add_theme_support( 'wp-block-styles' );
	}

	/**
	 * Disable Block Editor Widget Support
	 */
	private function remove_support_block_widgets(): void {
		remove_theme_support( 'widgets-block-editor' );
	}

}
