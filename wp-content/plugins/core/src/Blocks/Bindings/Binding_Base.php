<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks\Bindings;

abstract class Binding_Base implements Binding_Interface {

	protected string $slug  = '';
	protected string $label = '';

	/**
	 * @var mixed[]
	 */
	protected array $get_value_callback = [];

	/**
	 * @var string[]
	 */
	protected array $uses_context = [];

	abstract protected function get_args(): array;

	public function __construct() {
		foreach ( $this->get_args() as $key => $value ) {
			if ( ! property_exists( $this, $key ) || $key === self::SLUG ) {
				continue;
			}

			$this->{$key} = $value;
		}
	}

	public function get_properties(): array {
		return array_filter( [
			self::LABEL              => $this->label,
			self::GET_VALUE_CALLBACK => $this->get_value_callback,
			self::USES_CONTEXT       => $this->uses_context,
		] );
	}

}
