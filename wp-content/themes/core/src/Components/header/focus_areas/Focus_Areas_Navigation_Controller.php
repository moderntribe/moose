<?php declare(strict_types=1);

namespace Tribe\Theme\Components\header\focus_areas;

use Tribe\Plugin\Post_Types\Post\Post;
use Tribe\Theme\Components\Abstract_Controller;
use Tribe\Theme\Components\header\Menu_Item;
use Tribe\Theme\Menus\Register;
use WP_Post;

class Focus_Areas_Navigation_Controller extends Abstract_Controller {

	use Menu_Item;

	public const OUR_EXPERTISE = 'our-expertise';

	protected string $focus_areas;

	public function __construct() {
		$this->focus_areas = Register::FOCUS_AREAS;
	}

	public function get_focus_areas_location(): string {
		return $this->focus_areas;
	}

	public function get_focus_areas(): array {
		$locations = get_nav_menu_locations();

		if ( ! isset( $locations[ $this->focus_areas ] ) ) {
			return [];
		}

		$menu       = get_term( $locations[ $this->focus_areas ], 'nav_menu' );
		$menu_items = (array) wp_get_nav_menu_items( $menu->term_id );

		return array_filter( $menu_items, [ $this, 'filter_first_level_items' ] );
	}

	public function get_our_expertise_url(): string {
		$post = get_page_by_path( self::OUR_EXPERTISE, OBJECT, Post::NAME );
		if ( ! $post instanceof WP_Post ) {
			return '';
		}

		return (string) get_permalink( $post->ID );
	}

	protected function filter_first_level_items( WP_Post $post ): bool {
		return isset( $post->menu_item_parent ) && $post->menu_item_parent !== 0;
	}

}
