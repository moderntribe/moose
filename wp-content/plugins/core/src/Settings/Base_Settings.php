<?php declare(strict_types=1);

namespace Tribe\Plugin\Settings;

abstract class Base_Settings {

	protected string $slug = '';

	/**
	 * Return the title of the settings screen
	 */
	abstract public function get_title(): string;

	/**
	 * Return the cap the current user needs to have to be able to see this settings screen
	 */
	abstract public function get_capability(): string;

	/**
	 * Return slug of the parent menu where you want the settings page
	 */
	abstract public function get_parent_slug(): string;

	/**
	 * Register the settings screen in WordPress
	 */
	abstract public function register_settings(): void;

	/**
	 * Return the setting value for a given Key.
	 * Return $default if the value is empty.
	 *
	 * @param string     $key
	 * @param mixed|null $default
	 */
	abstract public function get_setting( string $key, mixed $default = null ): mixed;

	public function __construct() {
		$this->set_slug();
	}

	/**
	 * @param int $priority
	 */
	public function hook( int $priority = 10 ): void {
		add_action( 'init', [ $this, 'register_settings' ], $priority, 0 );
	}

	/**
	 * Generates a unique-ish slug for this settings screen
	 */
	protected function set_slug(): void {
		$this->slug = sanitize_title( $this->get_parent_slug() . '-' . $this->get_title() );
	}

}
