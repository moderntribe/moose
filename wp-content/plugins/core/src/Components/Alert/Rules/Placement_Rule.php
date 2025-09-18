<?php declare(strict_types=1);

namespace Tribe\Plugin\Components\Alert\Rules;

use Tribe\Plugin\Object_Meta\Post_Types\Alert_Meta;

class Placement_Rule implements Rule_Interface {

	public function passes( \WP_Post $alert, array $context ): bool {
		$alert_placement     = get_post_meta( $alert->ID, Alert_Meta::PLACEMENT, true );
		$requested_placement = $context['placement'] ?? '';

		return $alert_placement === $requested_placement;
	}

	public function get_name(): string {
		return 'placement';
	}
}
