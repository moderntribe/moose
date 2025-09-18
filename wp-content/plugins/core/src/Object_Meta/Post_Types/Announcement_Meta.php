<?php declare( strict_types=1 );

namespace Tribe\Plugin\Object_Meta\Post_Types;

use Extended\ACF\Fields\RadioButton;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\TrueFalse;
use Extended\ACF\Location;
use Tribe\Plugin\Object_Meta\Meta_Object;
use Tribe\Plugin\Post_Types\Announcement\Announcement;

class Alert_Meta extends Meta_Object {

	public const string POSITION              = 'normal';
	public const string GROUP_SLUG            = 'alert_meta';
	public const string DISMISSIBLE           = 'dismissible';
	public const string COLOR_THEME           = 'color_theme';
	public const string COLOR_THEME_DEFAULT   = 'default';
	public const string COLOR_THEME_PRIMARY   = 'color_theme_primary';
	public const string COLOR_THEME_SECONDARY = 'color_theme_secondary';
	public const string PLACEMENT             = 'placement';
	public const string PLACEMENT_ABOVE       = 'placement_above';
	public const string PLACEMENT_BELOW       = 'placement_below';
	public const string ALIGNMENT             = 'alignment';
	public const string ALIGNMENT_LEFT        = 'align_left';
	public const string ALIGNMENT_RIGHT       = 'align_right';

	public function get_slug(): string {
		return self::GROUP_SLUG;
	}

	public function get_title(): string {
		return esc_html__( 'Alert', 'tribe' );
	}

	public function get_fields(): array {
		return [
			TrueFalse::make( esc_html__( 'Dismissible', 'tribe' ), self::DISMISSIBLE )
				->defaultValue( 0 )
				->stylisedUi( esc_html__( 'Yes', 'tribe' ), esc_html__( 'No', 'tribe' ) ),
			Select::make( esc_html__( 'Color Theme', 'tribe' ), self::COLOR_THEME )
				->choices( [
					self::COLOR_THEME_DEFAULT   => esc_html__( 'Default', 'tribe' ),
					self::COLOR_THEME_PRIMARY   => esc_html__( 'Primary', 'tribe' ),
					self::COLOR_THEME_SECONDARY => esc_html__( 'Secondary', 'tribe' ),
				] ),
			RadioButton::make( esc_html__( 'Placement', 'tribe' ), self::PLACEMENT )
				->choices( [
					self::PLACEMENT_ABOVE => esc_html__( 'Above', 'tribe' ),
					self::PLACEMENT_BELOW => esc_html__( 'Below', 'tribe' ),
				] ),
			RadioButton::make( esc_html__( 'Alignment', 'tribe' ), self::ALIGNMENT_LEFT )
				->choices( [
					self::ALIGNMENT_LEFT  => esc_html__( 'Left', 'tribe' ),
					self::ALIGNMENT_RIGHT => esc_html__( 'Right', 'tribe' ),
				] ),
		];
	}

	public function get_locations(): array {
		return [
			Location::where( 'post_type', '=', Announcement::NAME ),
		];
	}
}
