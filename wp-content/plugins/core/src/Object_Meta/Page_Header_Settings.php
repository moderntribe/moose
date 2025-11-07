<?php declare(strict_types=1);

namespace Tribe\Plugin\Object_Meta;

use Extended\ACF\Location;
use Tribe\Plugin\Integrations\ACF_Fields\Color_Picker\Color_Picker;
use Tribe\Plugin\Post_Types\Page\Page;

class Page_Header_Settings extends Meta_Object {

	public const string GROUP_SLUG = 'page_header_settings';
	public const HEADER_COLOR      = 'header_color';

	public function get_slug(): string {
		return self::GROUP_SLUG;
	}

	#[\Override] public function get_title(): string {
		return esc_html__( 'Header Settings', 'tribe' );
	}

	#[\Override] public function get_fields(): array {
		return [
			Color_Picker::make( esc_html__( 'Header Background Color', 'tribe' ), self::HEADER_COLOR ),
		];
	}

	#[\Override] public function get_locations(): array {
		return [
			Location::where( 'post_type', Page::NAME ),
		];
	}
}
