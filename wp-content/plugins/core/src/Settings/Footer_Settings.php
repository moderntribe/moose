<?php declare(strict_types=1);

namespace Tribe\Plugin\Settings;

use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Number;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Url;

class Footer_Settings extends Settings_Sub_Page {

	public const PAGE_SLUG = 'footer-settings';

	public const FOOTER_LOGO      = 'footer_logo';
	public const FOOTER_COPYRIGHT = 'footer_copyright';

	public const FOOTER_LOCATIONS = 'footer_locations';

	public const FOOTER_LOCATION_CITY        = 'footer_location_city';
	public const FOOTER_LOCATION_TIMEZONE    = 'footer_location_timezone';
	public const FOOTER_LOCATION_DESCRIPTION = 'footer_location_description';
	public const FOOTER_LOCATION_URL         = 'footer_location_url';

	public function get_title(): string {
		return esc_html__( 'Footer Settings', 'tribe' );
	}

	public function get_fields(): array {
		return [
			Image::make( esc_html__( 'Footer Logo', 'tribe' ), self::FOOTER_LOGO )
				 ->returnFormat( 'id' ),
			Text::make( esc_html__( 'Footer Copyright', 'tribe' ), self::FOOTER_COPYRIGHT ),
			Repeater::make( esc_html__( 'Locations', 'tribe' ), self::FOOTER_LOCATIONS )
				->fields( [
					Text::make( esc_html__( 'Location City', 'tribe' ), self::FOOTER_LOCATION_CITY ),
					Number::make( esc_html__( 'Location Timezone', 'tribe' ), self::FOOTER_LOCATION_TIMEZONE ),
					Text::make( esc_html__( 'Location Description', 'tribe' ), self::FOOTER_LOCATION_DESCRIPTION ),
					Url::make( esc_html__( 'Location URL', 'tribe' ), self::FOOTER_LOCATION_URL ),
				] )
				->min( 0 )
				->max( 5 )
				->layout( 'table' ),
		];
	}

}
