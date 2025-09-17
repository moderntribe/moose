<?php

namespace Tribe\Plugin\Components\Alert;

use Tribe\Plugin\Components\Abstract_Controller;
use Tribe\Plugin\Post_Types\Alert\Alert;

class Alert_Controller extends Abstract_Controller {
	const string PLACEMENT_ABOVE_HEADER = 'above_header';
	const string PLACEMENT_BELOW_HEADER = 'below_header';

	private array $rules = [];

	private array $context = [];

	public function __construct() {
		$this->add_rule( new Schedule_Rule() );
	}

	/**
	 * Get alerts for a specific placement
	 *
	 * @param string $placement The placement location (above_header|below_header)
	 * @param array  $context   Additional context for rule processing
	 *
	 * @return \WP_Post[]
	 */
	public function get_alerts_for_placement( string $placement, array $context = [] ): array {
		$this->context = array_merge( $context, [
			'placement'       => $placement,
			'current_post_id' => get_queried_object_id(),
			'is_home'         => is_home(),
			'is_front_page'   => is_front_page(),
			'is_single'       => is_single(),
			'is_page'         => is_page(),
			'is_category'     => is_category(),
			'is_archive'      => is_archive(),
			'current_time'    => current_time( 'timestamp' ),
		] );

		$alerts = $this->get_all_alerts();

		return $this->process_pipeline( $alerts );
	}

	/**
	 * Get alerts for above header placement
	 *
	 * @param array $context
	 *
	 * @return \WP_Post[]
	 */
	public function get_above_header_alerts( array $context = [] ): array {
		return $this->get_alerts_for_placement( self::PLACEMENT_ABOVE_HEADER, $context );
	}

	/**
	 * Get alerts for below header placement
	 *
	 * @param array $context
	 *
	 * @return \WP_Post[]
	 */
	public function get_below_header_alerts( array $context = [] ): array {
		return $this->get_alerts_for_placement( self::PLACEMENT_BELOW_HEADER, $context );
	}

	/**
	 * Add a rule to the processing pipeline
	 *
	 * @param Rule_Interface $rule
	 *
	 * @return self
	 */
	public function add_rule( Rule_Interface $rule ): self {
		$this->rules[] = $rule;

		return $this;
	}

	/**
	 * Process alerts through the rule pipeline
	 *
	 * @param \WP_Post[] $alerts
	 *
	 * @return \WP_Post[]
	 */
	private function process_pipeline( array $alerts ): array {
		foreach ( $this->rules as $rule ) {
			$alerts = array_filter( $alerts, function ( $alert ) use ( $rule ) {
				return $rule->passes( $alert, $this->context );
			} );
		}

		// Sort by menu_order (priority) and then by post date
		usort( $alerts, function ( $a, $b ) {
			if ( $a->menu_order === $b->menu_order ) {
				return strtotime( $b->post_date ) - strtotime( $a->post_date );
			}

			return $a->menu_order - $b->menu_order;
		} );

		return $alerts;
	}

	/**
	 * Get all published alerts
	 *
	 * @return \WP_Post[]
	 */
	private function get_all_alerts(): array {
		$query = new \WP_Query( [
			'post_type'      => Alert::NAME,
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'DESC' ],
		] );

		return $query->posts;
	}
}
