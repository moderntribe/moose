<?php declare(strict_types=1);

namespace Tribe\Plugin\Settings;

use Extended\ACF\Location;
use Tribe\Libs\Settings\Base_Settings;

abstract class Settings_Sub_Page extends Base_Settings {

	public const PAGE_SLUG = '';

	/**
	 * @return \Extended\ACF\Fields\Field[]
	 */
	abstract protected function get_fields(): array;

	/**
	 * @param int $priority
	 */
	// phpcs:ignore SlevomatCodingStandard.TypeHints
	public function hook( $priority = 10 ): void {
		// Don't load anything if ACF is not installed
		if ( ! function_exists( 'acf_add_options_sub_page' ) ) {
			return;
		}

		parent::hook( $priority );
	}

	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * Get setting value
	 *
	 * @param string $key
	 * @param null   $default
	 *
	 * @return mixed
	 */
	// phpcs:ignore SlevomatCodingStandard.TypeHints
	public function get_setting( $key, $default = null ) {
		$value = get_field( $key, 'option' );

		return ! empty( $value ) ? $value : $default;
	}

	/**
	 * Registers the settings page with ACF
	 */
	public function register_settings(): void {
		acf_add_options_sub_page( apply_filters( 'core_settings_acf_sub_page', [
			'page_title'  => $this->get_title(),
			'menu_title'  => $this->get_title(),
			'menu_slug'   => $this->slug,
			'redirect'    => true,
			'capability'  => $this->get_capability(),
			'parent_slug' => $this->get_parent_slug(),
		] ) );

		register_extended_field_group( [
			'key'      => 'group_' . $this->get_slug(),
			'title'    => $this->get_title(),
			'style'    => 'default',
			'fields'   => $this->get_fields(),
			'location' => [
				Location::where( 'options_page', static::PAGE_SLUG ),
			],
		] );
	}

	public function get_capability(): string {
		return 'manage_options';
	}

	public function get_parent_slug(): string {
		return 'themes.php';
	}

	protected function set_slug(): void {
		$this->slug = sanitize_title( static::PAGE_SLUG );
	}

}
