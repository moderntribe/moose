<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks\Bindings;

interface Binding_Interface {

	public const SLUG               = 'slug';
	public const LABEL              = 'label';
	public const GET_VALUE_CALLBACK = 'get_value_callback';
	public const USES_CONTEXT       = 'uses_context';

	public function get_slug(): string;

	public function get_properties(): array;

}
