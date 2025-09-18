<?php declare(strict_types=1);

namespace Tribe\Plugin\Components\Announcements;

use Tribe\Plugin\Components\Abstract_Controller;
use Tribe\Plugin\Components\Announcements\Rules\Placement_Rule;
use Tribe\Plugin\Components\Announcements\Rules\Rule_Interface;
use Tribe\Plugin\Post_Types\Announcement\Announcement;

class Announcement_Controller extends Abstract_Controller {

	private array $rules = [];

	private array $context = [];

	public function __construct() {
		$this->add_rule( new Placement_Rule() );
	}

	/**
	 * Get announcements for a specific placement
	 *
	 * @param string $placement The placement location (above_header|below_header)
	 * @param array  $context   Additional context for rule processing
	 *
	 * @return \WP_Post[]
	 */
	public function get_announcement_for_placement( string $placement, array $context = [] ): array {
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

		$announcements = $this->get_all_announcements();

		return $this->process_pipeline( $announcements );
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
	 * Process announcements through the rule pipeline
	 *
	 * @param \WP_Post[] $announcements
	 *
	 * @return \WP_Post[]
	 */
	private function process_pipeline( array $announcements ): array {
		foreach ( $this->rules as $rule ) {
			$announcements = array_filter( $announcements, function ( $announcement ) use ( $rule ) {
				return $rule->passes( $announcement, $this->context );
			} );
		}

		return $announcements;
	}

	/**
	 * Get all published announcements
	 *
	 * @return \WP_Post[]
	 */
	private function get_all_announcements(): array {
		$query = new \WP_Query( [
			'post_type'      => Announcement::NAME,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => [
				'menu_order' => 'ASC',
				'date' => 'DESC'
			],
		] );

		return $query->posts;
	}
}
