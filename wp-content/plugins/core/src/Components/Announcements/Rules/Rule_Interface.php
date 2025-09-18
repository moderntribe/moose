<?php declare(strict_types=1);

namespace Tribe\Plugin\Components\Announcements\Rules;

interface Rule_Interface {
	/**
	 * Check if an announcement passes this rule
	 *
	 * @param \WP_Post $announcement
	 * @param array $context
	 *
	 * @return bool
	 */
	public function passes( \WP_Post $announcement, array $context ): bool;

	/**
	 * Get the rule name for debugging
	 *
	 * @return string
	 */
	public function get_name(): string;
}
