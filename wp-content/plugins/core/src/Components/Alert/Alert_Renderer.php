<?php declare(strict_types=1);

namespace Tribe\Plugin\Components\Alert;

class Alert_Renderer {

	/**
	 * @var Alert_Controller
	 */
	private Alert_Controller $controller;

	public function __construct( Alert_Controller $controller ) {
		$this->controller = $controller;
	}

	/**
	 * Render alerts for a specific placement as block markup
	 *
	 * @param string $placement
	 * @param array $context
	 *
	 * @return string
	 */
	public function render_alerts( string $placement, array $context = [] ): string {
		$alerts = $this->controller->get_alerts_for_placement( $placement, $context );

		if ( empty( $alerts ) ) {
			return '';
		}

		$output = '';

		foreach ( $alerts as $alert ) {
			$output .= $this->render_alert_block( $alert );
		}

		return $output;
	}

	/**
	 * Render a single alert as a block with JSON attributes
	 *
	 * @param \WP_Post $alert
	 *
	 * @return string
	 */
	public function render_alert_block( \WP_Post $alert ): string {
		$attributes = $this->get_alert_attributes( $alert );
		$json_attributes = wp_json_encode( $attributes, JSON_UNESCAPED_SLASHES );

		return sprintf(
			'<!-- wp:tribe/announcements %s /-->',
			$json_attributes
		);
	}

	/**
	 * Get alert data as block attributes
	 *
	 * @param \WP_Post $alert
	 *
	 * @return array
	 */
	private function get_alert_attributes( \WP_Post $alert ): array {
		return [
			'alertId' => $alert->ID,
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
		$block_markup = $this->render_alerts( $placement, $context );

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
