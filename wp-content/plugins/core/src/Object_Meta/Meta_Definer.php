<?php declare(strict_types=1);

namespace Tribe\Plugin\Object_Meta;

use DI;
use Tribe\Plugin\Core\Interfaces\Definer_Interface;

class Meta_Definer implements Definer_Interface {

	public const OBJECT_META = 'meta.types';

	public function define(): array {
		return [
			self::OBJECT_META => DI\add( [
			] ),
		];
	}

}
