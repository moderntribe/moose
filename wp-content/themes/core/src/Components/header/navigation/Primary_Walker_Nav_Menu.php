<?php declare(strict_types=1);

namespace Tribe\Theme\Components\header\navigation;

use Tribe\Libs\Utils\Markup_Utils;
use Tribe\Theme\Components\header\focus_areas\Focus_Areas_Navigation_Controller;
use Tribe\Theme\Components\header\Menu_Item;
use Walker_Nav_Menu;

// phpcs:disable SlevomatCodingStandard.TypeHints
class Primary_Walker_Nav_Menu extends Walker_Nav_Menu {

	use Menu_Item;

	/**
	 * @var string[]
	 */
	protected array $link_classes;

	/**
	 * @var string[]
	 */
	protected array $active_link_classes;

	protected Focus_Areas_Navigation_Controller $focus_areas;

	public function __construct( Focus_Areas_Navigation_Controller $focus_areas ) {
		$this->focus_areas = $focus_areas;

		$this->link_classes        = Main_Navigation_Controller::ITEM_CLASSES;
		$this->active_link_classes = Main_Navigation_Controller::ACTIVE_ITEM_CLASSES;
	}

	/**
	 * Starts the element output.
	 *
	 * Following settings deprecated: 'item_spacing', 'before', 'after, 'link_before', 'link_after'.
	 * Following filters deprecated: 'nav_menu_css_class', 'nav_menu_item_id', 'nav_menu_item_args', 'walker_nav_menu_start_el',
	 *                               'nav_menu_link_attributes', 'nav_menu_item_title'.
	 *
	 * @param string   $output            Used to append additional content (passed by reference).
	 * @param \WP_Post  $data_object       Menu item data object.
	 * @param int      $depth             Depth of menu item. Used for padding.
	 * @param \stdClass $args              An object of wp_nav_menu() arguments.
	 * @param int      $current_object_id Optional. ID of the current menu item. Default 0.
	 *
	 * @see Walker::start_el()
	 *
	 * @see Walker_Nav_Menu::start_el()
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ): void {
		// Restores the more descriptive, specific name for use within this method.
		$menu_item = $data_object;

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $menu_item->title ?? '', $menu_item->ID );

		if ( $title === '#' . $this->focus_areas->get_focus_areas_location() ) {
			ob_start();
			get_component_part( 'header/focus_areas/focus_areas_navigation' );
			$output .= ob_get_contents();
			ob_end_clean();

			return;
		}

		$atts = $this->get_menu_item_attributes( $menu_item );

		$classes = $this->link_classes;

		if ( isset( $menu_item->current ) && $menu_item->current ) {
			$atts['aria-current'] = 'page';
			$classes              = array_merge( $classes, $this->active_link_classes );
		}

		$output .= sprintf(
			'<a %s %s>%s</a>',
			Markup_Utils::class_attribute( $classes ),
			Markup_Utils::concat_attrs( array_filter( $atts ) ),
			$title
		);
	}

}
