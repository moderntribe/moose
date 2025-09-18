<?php declare(strict_types=1);

namespace Tribe\Plugin\Components\Announcements;

class Announcement_Renderer {

	/**
	 * @var Announcement_Controller
	 */
	private Announcement_Controller $controller;

	public function __construct( Announcement_Controller $controller ) {
		$this->controller = $controller;
	}

	/**
	 * Render Announcements for a specific placement as block markup
	 *
	 * @param string $placement
	 * @param array $context
	 *
	 * @return string
	 */
	public function render_announcements( string $placement, array $context = [] ): string {
		$announcements = $this->controller->get_announcement_for_placement( $placement, $context );

		if ( empty( $announcements ) ) {
			return '';
		}

		$output = '';

		foreach ( $announcements as $announcement ) {
			$output .= $this->render_announcement_block( $announcement );
		}

		return $output;
	}

	/**
	 * Render a single announcement as a block with JSON attributes
	 *
	 * @param \WP_Post $announcement
	 *
	 * @return string
	 */
	public function render_announcement_block( \WP_Post $announcement ): string {
		$attributes = $this->get_announcement_attributes( $announcement );
		$json_attributes = wp_json_encode( $attributes, JSON_UNESCAPED_SLASHES );

		return sprintf(
			'<!-- wp:tribe/announcements %s /-->',
			$json_attributes
		);
	}

	/**
	 * Get announcement data as block attributes
	 *
	 * @param \WP_Post $announcement
	 *
	 * @return array
	 */
	private function get_announcement_attributes( \WP_Post $announcement ): array {
		return [
			'announcement_id' => $announcement->ID,
		];
	}

	/**
	 * Parse and render block markup
	 *
	 * @param string $placement
	 * @param array $context
	 *
	 * @return string Rendered HTML
	 */
	public function parse_and_render( string $placement, array $context = [] ): string {
		$block_markup = $this->render_announcements( $placement, $context );

		if ( empty( $block_markup ) ) {
			return '';
		}

		// Parse the blocks and render them
		$blocks = parse_blocks( $block_markup );
		$output = '';

		foreach ( $blocks as $block ) {
			$output .= render_block( $block );
		}

		return $output;
	}

}
