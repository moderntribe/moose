<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks;

use DI;
use Tribe\Libs\Container\Definer_Interface;
use Tribe\Plugin\Blocks\Filters\Contracts\Filter_Factory;
use Tribe\Theme\blocks\core\button\Button;
use Tribe\Theme\blocks\core\heading\Heading;
use Tribe\Theme\blocks\core\paragraph\Paragraph;
use Tribe\Theme\blocks\core\spacer\Spacer;

class Blocks_Definer implements Definer_Interface {

	public const TYPES    = 'blocks.types';
	public const EXTENDED = 'blocks.extended';
	public const CORE     = 'blocks.core';
	public const PATTERNS = 'blocks.patterns';
	public const FILTERS  = 'blocks.filters';

	public function define(): array {
		return [
			self::TYPES           => DI\add( [
				// 'tribe/example',
			] ),

			self::CORE            => DI\add( [
				'core/button',
				'core/embed',
				'core/heading',
				'core/image',
				'core/lists',
				'core/paragraph',
				'core/spacer',
				'core/table',
				'core/video',
				'core/querypagination',
			] ),

			self::EXTENDED        => DI\add( [
				DI\get( Button::class ),
				DI\get( Heading::class ),
				DI\get( Paragraph::class ),
				DI\get( Spacer::class ),
			] ),

			self::PATTERNS        => DI\add( [
			] ),

			self::FILTERS         => DI\add( [
			] ),

			Filter_Factory::class => DI\autowire()->constructorParameter( 'filters', DI\get( self::FILTERS ) ),
		];
	}

}
