<?php declare( strict_types=1 );

namespace Tribe\Plugin\Object_Meta\Post_Types;

use Extended\ACF\ConditionalLogic;
use Extended\ACF\Fields\DateTimePicker;
use Extended\ACF\Fields\RadioButton;
use Extended\ACF\Fields\Relationship;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\TrueFalse;
use Extended\ACF\Location;
use Tribe\Plugin\Object_Meta\Meta_Object;
use Tribe\Plugin\Post_Types\Announcement\Announcement;
use WP_Post_Type;
use WP_Taxonomy;

class Announcement_Meta extends Meta_Object {

	public const string POSITION              = 'normal';
	public const string GROUP_SLUG            = 'announcement_meta';
	public const string SETUP_TAB             = 'setup_tab';
	public const string DISMISSIBLE           = 'dismissible';
	public const string COLOR_THEME           = 'color_theme';
	public const string COLOR_THEME_DEFAULT   = 'default';
	public const string COLOR_THEME_PRIMARY   = 'color_theme_primary';
	public const string COLOR_THEME_SECONDARY = 'color_theme_secondary';
	public const string ALIGNMENT             = 'alignment';
	public const string ALIGNMENT_LEFT        = 'align_left';
	public const string ALIGNMENT_RIGHT       = 'align_right';
	public const string DISPLAY_TAB           = 'display_tab';
	public const string PLACEMENT             = 'placement';
	public const string PLACEMENT_ABOVE       = 'placement_above';
	public const string PLACEMENT_BELOW       = 'placement_below';
	public const string SCHEDULING_START_TIME = 'schedule_start_time';
	public const string SCHEDULING_END_TIME   = 'schedule_end_time';
	public const string FIELD_RULES_DISPLAY_TYPE        = 'rules_display_type';
	public const string FIELD_RULES_APPLY_TO_FRONT_PAGE = 'rules_apply_to_front_page';
	public const string FIELD_RULES_INCLUDE_PAGES = 'rules_include_pages';
	public const string FIELD_RULES_EXCLUDE_PAGES = 'rules_exclude_pages';
	public const string FIELD_TAXONOMY_ARCHIVES  = 'taxonomy_archives';
	public const string FIELD_POST_TYPE_ARCHIVES = 'post_type_archives';
	public const string OPTION_EVERY_PAGE        = 'every_page';
	public const string OPTION_INCLUDE    = 'include';
	public const string OPTION_EXCLUDE = 'exclude';
	public const int MAX_POSTS         = 50;

	public function get_slug(): string {
		return self::GROUP_SLUG;
	}

	public function get_title(): string {
		return esc_html__( 'Announcement', 'tribe' );
	}

	public function get_fields(): array {
		return [
			Tab::make( esc_html__( 'Setup', 'tribe' ), self::SETUP_TAB )
				->placement( 'left'),
			TrueFalse::make( esc_html__( 'Dismissible', 'tribe' ), self::DISMISSIBLE )
				->defaultValue( 0 )
				->stylisedUi( esc_html__( 'Yes', 'tribe' ), esc_html__( 'No', 'tribe' ) ),
			Select::make( esc_html__( 'Color Theme', 'tribe' ), self::COLOR_THEME )
				->choices( [
					self::COLOR_THEME_DEFAULT   => esc_html__( 'Default', 'tribe' ),
					self::COLOR_THEME_PRIMARY   => esc_html__( 'Primary', 'tribe' ),
					self::COLOR_THEME_SECONDARY => esc_html__( 'Secondary', 'tribe' ),
				] ),
			RadioButton::make( esc_html__( 'Alignment', 'tribe' ), self::ALIGNMENT )
				->choices( [
					self::ALIGNMENT_LEFT  => esc_html__( 'Left', 'tribe' ),
					self::ALIGNMENT_RIGHT => esc_html__( 'Right', 'tribe' ),
				] ),

			Tab::make( esc_html__( 'Display', 'tribe' ), self::DISPLAY_TAB )
				->placement( 'left'),
			RadioButton::make( esc_html__( 'Placement', 'tribe' ), self::PLACEMENT )
				->choices( [
					self::PLACEMENT_ABOVE => esc_html__( 'Above Header', 'tribe' ),
					self::PLACEMENT_BELOW => esc_html__( 'Below Header', 'tribe' ),
				] ),
			DateTimePicker::make( esc_html__( 'Start Date', 'tribe' ), self::SCHEDULING_START_TIME )
				->displayFormat('d/m/Y g:i a')
				->returnFormat('U')
				->column(50),
			DateTimePicker::make( esc_html__( 'End Date', 'tribe' ), self::SCHEDULING_END_TIME )
				->displayFormat('d/m/Y g:i a')
				->returnFormat('U')
				->column(50),
			RadioButton::make(esc_html__('Select a rule', 'tribe-alerts'), self::FIELD_RULES_DISPLAY_TYPE)
				->choices([
					self::OPTION_EVERY_PAGE => esc_html__('Show everywhere', 'tribe-alerts'),
					self::OPTION_INCLUDE => esc_html__('Show only on specified pages', 'tribe-alerts'),
					self::OPTION_EXCLUDE => esc_html__('Exclude from specific pages', 'tribe-alerts'),
				])
				->defaultValue(self::OPTION_EVERY_PAGE),
			TrueFalse::make(esc_html__('Apply the selected rule to the Front Page', 'tribe-alerts'), self::FIELD_RULES_APPLY_TO_FRONT_PAGE)
				->instructions(sprintf(
					'%s<a href="%s">%s</a>%s',
					esc_html__('Regardless of the configuration in ', 'tribe-alerts'),
					esc_url(admin_url('options-reading.php')),
					esc_html__('Settings > Reading', 'tribe-alerts'),
					esc_html__(', always apply these rules to the front page', 'tribe-alerts')
				))
				->stylisedUi(esc_html__('Yes', 'tribe-alerts'), esc_html__('No', 'tribe-alerts'))
				->defaultValue(0)
				->conditionalLogic([
					ConditionalLogic::where( self::FIELD_RULES_DISPLAY_TYPE, '!=', self::OPTION_EVERY_PAGE )
				]),
			Relationship::make(esc_html__('Select pages where the alert will appear', 'tribe-alerts'), self::FIELD_RULES_INCLUDE_PAGES)
				->instructions(sprintf(
					esc_html__('Select up to %d posts', 'tribe-alerts'),
					(int) apply_filters('tribe/alerts/meta/max_posts', self::MAX_POSTS)
				))
				->postTypes($this->get_allowed_post_types())
				->filters(['search', 'post_type', 'taxonomy'])
				->min(0)
				->max((int) apply_filters('tribe/alerts/meta/max_posts', self::MAX_POSTS))
				->returnFormat('object')
				->conditionalLogic([
					ConditionalLogic::where( self::FIELD_RULES_DISPLAY_TYPE, '==', self::OPTION_INCLUDE ),
				]),
			Relationship::make(esc_html__('Will appear on every page but the following selected pages', 'tribe-alerts'), self::FIELD_RULES_EXCLUDE_PAGES)
				->instructions(sprintf(
					esc_html__('Select up to %d posts', 'tribe-alerts'),
					(int) apply_filters('tribe/alerts/meta/max_posts', self::MAX_POSTS)
				))
				->postTypes($this->get_allowed_post_types())
				->filters(['search', 'post_type', 'taxonomy'])
				->min(0)
				->max((int) apply_filters('tribe/alerts/meta/max_posts', self::MAX_POSTS))
				->returnFormat('object')
				->conditionalLogic([
					ConditionalLogic::where( self::FIELD_RULES_DISPLAY_TYPE, '==', self::OPTION_EXCLUDE ),
				]),
			Select::make(esc_html__('Apply the selected rule to the following Taxonomy Archives:', 'tribe-alerts'), self::FIELD_TAXONOMY_ARCHIVES)
				->choices(array_reduce(
					get_taxonomies(['public' => true], 'objects'),
					static function (array $taxonomies, WP_Taxonomy $tax) {
						$taxonomies[$tax->name] = $tax->labels->name;
						return $taxonomies;
					},
					[]
				))
				->stylisedUi()
				->allowNull()
				->defaultValue([])
				->returnFormat('value')
				->conditionalLogic([
					ConditionalLogic::where( self::FIELD_RULES_DISPLAY_TYPE, '!=', self::OPTION_EVERY_PAGE ),
				]),
			Select::make(esc_html__('Apply the selected rule to the following Post Type Archives:', 'tribe-alerts'), self::FIELD_POST_TYPE_ARCHIVES)
				->choices(array_reduce(
					get_post_types(['public' => true, 'publicly_queryable' => true, 'has_archive' => true], 'objects'),
					static function (array $post_types, WP_Post_Type $post_type) {
						$post_types[$post_type->name] = $post_type->labels->name;
						return $post_types;
					},
					[]
				))
				->stylisedUi()
				->allowNull()
				->defaultValue([])
				->returnFormat('value')
				->conditionalLogic([
					ConditionalLogic::where( self::FIELD_RULES_DISPLAY_TYPE, '!=', self::OPTION_EVERY_PAGE ),
				]),
		];
	}

	private function get_allowed_post_types() : array {
		return \acf_get_post_types(['exclude' => [Announcement::NAME, 'attachment']]);
	}

	public function get_locations(): array {
		return [
			Location::where( 'post_type', '=', Announcement::NAME ),
		];
	}
}
