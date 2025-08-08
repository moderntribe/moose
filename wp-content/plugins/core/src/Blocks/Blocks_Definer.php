<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks;

use DI;
use Tribe\Plugin\Blocks\Filters\Contracts\Filter_Factory;
use Tribe\Plugin\Core\Interfaces\Definer_Interface;
use Tribe\Theme\bindings\Query_Results_Count;
use Tribe\Theme\blocks\core\button\Button;
use Tribe\Theme\blocks\core\column\Column;
use Tribe\Theme\blocks\core\columns\Columns;
use Tribe\Theme\blocks\core\details\Details;
use Tribe\Theme\blocks\core\embed\Embed;
use Tribe\Theme\blocks\core\gallery\Gallery;
use Tribe\Theme\blocks\core\heading\Heading;
use Tribe\Theme\blocks\core\image\Image;
use Tribe\Theme\blocks\core\lists\Lists;
use Tribe\Theme\blocks\core\paragraph\Paragraph;
use Tribe\Theme\blocks\core\postauthor\Post_Author;
use Tribe\Theme\blocks\core\postauthorname\Post_Author_Name;
use Tribe\Theme\blocks\core\posttemplate\Post_Template;
use Tribe\Theme\blocks\core\postterms\Post_Terms;
use Tribe\Theme\blocks\core\pullquote\Pullquote;
use Tribe\Theme\blocks\core\querynoresults\Query_No_Results;
use Tribe\Theme\blocks\core\querypagination\Query_Pagination;
use Tribe\Theme\blocks\core\quote\Quote;
use Tribe\Theme\blocks\core\search\Search;
use Tribe\Theme\blocks\core\separator\Separator;
use Tribe\Theme\blocks\core\table\Table;
use Tribe\Theme\blocks\core\video\Video;
use Tribe\Theme\blocks\outermost\socialsharing\Social_Sharing;

class Blocks_Definer implements Definer_Interface {

	public const string TYPES    = 'blocks.types';
	public const string EXTENDED = 'blocks.extended';
	public const string PATTERNS = 'blocks.patterns';
	public const string FILTERS  = 'blocks.filters';
	public const string BINDINGS = 'blocks.bindings';

	public function define(): array {
		return [
			self::TYPES           => DI\add( [
				'tribe/copyright',
				'tribe/logo-marquee',
				'tribe/post-card',
				'tribe/rating-stars',
				'tribe/search-card',
				'tribe/tab',
				'tribe/tabs',
				'tribe/terms',
				'tribe/vertical-tab',
				'tribe/vertical-tabs',
			] ),

			self::EXTENDED        => DI\add( [
				DI\get( Button::class ),
				DI\get( Column::class ),
				DI\get( Columns::class ),
				DI\get( Details::class ),
				DI\get( Embed::class ),
				DI\get( Gallery::class ),
				DI\get( Heading::class ),
				DI\get( Image::class ),
				DI\get( Lists::class ),
				DI\get( Paragraph::class ),
				DI\get( Post_Author::class ),
				DI\get( Post_Author_Name::class ),
				DI\get( Post_Template::class ),
				DI\get( Post_Terms::class ),
				DI\get( Pullquote::class ),
				DI\get( Query_No_Results::class ),
				DI\get( Query_Pagination::class ),
				DI\get( Quote::class ),
				DI\get( Search::class ),
				DI\get( Separator::class ),
				DI\get( Table::class ),
				DI\get( Video::class ),
				DI\get( Social_Sharing::class ),
			] ),

			self::PATTERNS        => DI\add( [
			] ),

			self::FILTERS         => DI\add( [
			] ),

			self::BINDINGS        => DI\add( [
				DI\get( Query_Results_Count::class ),
			] ),

			Filter_Factory::class => DI\autowire()->constructorParameter( 'filters', DI\get( self::FILTERS ) ),
		];
	}

}
