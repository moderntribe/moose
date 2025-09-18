<?php declare(strict_types=1);

namespace Tribe\Plugin\Components\Announcements\Rules;

use Tribe\Plugin\Object_Meta\Post_Types\Announcement_Meta;

class Placement_Rule implements Rule_Interface {

	public function passes( \WP_Post $announcement, array $context ): bool {
		$placement           = get_post_meta( $announcement->ID, Announcement_Meta::PLACEMENT, true ); // @todo
		$requested_placement = $context['placement'] ?? '';

		return $placement === $requested_placement;
	}

	public function get_name(): string {
		return 'placement';
	}
}
