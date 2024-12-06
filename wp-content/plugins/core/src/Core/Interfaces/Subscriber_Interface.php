<?php declare(strict_types=1);

namespace Tribe\Plugin\Core\Interfaces;

interface Subscriber_Interface {

	/**
	 * Register action/filter listeners to hook into WordPress
	 */
	public function register(): void;

}
