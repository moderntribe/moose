<?php declare(strict_types=1);

/**
 * Title: Example
 * Slug: tribe/example
 * Categories: custom
 * Description: Example pattern with media & text
 */

namespace Tribe\Theme\Patterns;

use Tribe\Plugin\Blocks\Patterns\Pattern_Base;
use Tribe\Plugin\Blocks\Patterns\Pattern_Category;

class Example extends Pattern_Base {

	public const NAME = 'tribe/example';

	public function get_name(): string {
		return self::NAME;
	}

	public function get_args(): array {
		return [
			self::TITLE       => esc_html__( 'Example', 'tribe' ),
			self::DESCRIPTION => esc_html__( 'Example pattern with media & text', 'tribe' ),
			self::CONTENT     => $this->get_content(),
			self::CATEGORIES  => [
				Pattern_Category::CUSTOM_PATTERN_CATEGORY_SLUG,
			],
			self::KEYWORDS    => [ esc_html__( 'example', 'tribe' ) ],
		];
	}

	public function enqueue_pattern_styles(): void {
		// Enqueue pattern-specific styles for this pattern via ????
	}

	public function enqueue_block_scripts(): void {
		// Enqueue pattern-specific scripts for this pattern via ????
	}

	private function get_content(): string {
		return '<!-- wp:group {"templateLock":"contentOnly","tagName":"section","className":"hero-block lazy layout","layout":{"type":"default"}} -->
		<section class="wp-block-group hero-block lazy layout"><!-- wp:media-text {"mediaPosition":"right"} -->
		<div class="wp-block-media-text alignwide has-media-on-the-right is-stacked-on-mobile"><div class="wp-block-media-text__content"><!-- wp:heading {"level": 1,"fontSize":"display-large"} -->
		<h1 class="has-display-large-font-size">Lorem Ipsum Dolor Set</h1>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"placeholder":"Content…"} -->
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ac velit at tellus cursus ultricies. Proin semper risus vitae justo laoreet, eget consectetur mauris tincidunt. Ut pretium lobortis ligula, a suscipit elit ullamcorper id.</p>
		<!-- /wp:paragraph -->
		<!-- wp:buttons -->
		<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-arrow-right"} -->
		<div class="wp-block-button is-style-arrow-right"><a class="wp-block-button__link wp-element-button">Button Text</a></div>
		<!-- /wp:button --></div>
		<!-- /wp:buttons --></div><figure class="wp-block-media-text__media"></figure></div>
		<!-- /wp:media-text --></section>
		<!-- /wp:group -->';
	}

}
