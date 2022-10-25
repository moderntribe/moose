<?php declare(strict_types=1);

namespace Tribe\Plugin\Object_Meta;

abstract class Meta_Object {

	public const POSITION = 'side';

	abstract public function get_slug(): string;
	abstract public function get_title(): string;

	/**
	 * @return \Extended\ACF\Fields\Field[]
	 */
	abstract public function get_fields(): array;

	/**
	 * @return \Extended\ACF\Location[]
	 */
	abstract public function get_locations(): array;

	/**
	 * @return string Determines the position on the edit screen. Defaults to normal. Choices of 'acf_after_title', 'normal' or 'side'.
	 */
	public function get_position(): string {
		return static::POSITION;
	}

	/**
	 * @return string Determines the metabox style. Defaults to 'default'. Choices of 'default' or 'seamless'.
	 */
	public function get_style(): string {
		return 'default';
	}

}
