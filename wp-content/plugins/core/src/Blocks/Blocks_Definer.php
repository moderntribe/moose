<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks;

use DI;
use Tribe\Libs\Container\Definer_Interface;
use Tribe\Plugin\Blocks\Filters\Contracts\Filter_Factory;
use Tribe\Theme\blocks\core\button\Button;
use Tribe\Theme\blocks\core\embed\Embed;
use Tribe\Theme\blocks\core\heading\Heading;
use Tribe\Theme\blocks\core\image\Image;
use Tribe\Theme\blocks\core\lists\Lists;
use Tribe\Theme\blocks\core\paragraph\Paragraph;
use Tribe\Theme\blocks\core\querypagination\Query_Pagination;
use Tribe\Theme\blocks\core\quote\Quote;
use Tribe\Theme\blocks\core\separator\Separator;
use Tribe\Theme\blocks\core\spacer\Spacer;
use Tribe\Theme\blocks\core\table\Table;
use Tribe\Theme\blocks\core\video\Video;

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

			self::EXTENDED        => DI\add( [
				DI\get( Button::class ),
				DI\get( Embed::class ),
				DI\get( Heading::class ),
				DI\get( Image::class ),
				DI\get( Lists::class ),
				DI\get( Paragraph::class ),
				DI\get( Query_Pagination::class ),
				DI\get( Quote::class ),
				DI\get( Separator::class ),
				DI\get( Spacer::class ),
				DI\get( Table::class ),
				DI\get( Video::class ),
			] ),

			self::PATTERNS        => DI\add( [
			] ),

			self::FILTERS         => DI\add( [
			] ),

			Filter_Factory::class => DI\autowire()->constructorParameter( 'filters', DI\get( self::FILTERS ) ),
		];
	}

}
