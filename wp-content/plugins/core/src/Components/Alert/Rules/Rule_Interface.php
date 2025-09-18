<?php declare(strict_types=1);

namespace Tribe\Plugin\Components\Alert\Rules;

interface Rule_Interface {
	/**
	 * Check if an alert passes this rule
	 *
	 * @param \WP_Post $alert
	 * @param array $context
	 *
	 * @return bool
	 */
	public function passes( \WP_Post $alert, array $context ): bool;

	/**
	 * Get the rule name for debugging
	 *
	 * @return string
	 */
	public function get_name(): string;
}
