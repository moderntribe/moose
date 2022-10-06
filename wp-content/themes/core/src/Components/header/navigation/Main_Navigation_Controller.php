<?php declare(strict_types=1);

namespace Tribe\Theme\Components\header\navigation;

use Tribe\Theme\Components\Abstract_Controller;
use Tribe\Theme\Components\header\focus_areas\Focus_Areas_Navigation_Controller;
use Tribe\Theme\Menus\Register;

class Main_Navigation_Controller extends Abstract_Controller {

	public const ITEM_CLASSES = [
		'header-block-list__item',
	];

	public const ACTIVE_ITEM_CLASSES = [
		'header-block-list__item--is-active',
	];

	protected string $primary;

	public function __construct() {
		$this->primary = Register::PRIMARY;
	}

	public function get_primary_menu(): string {
		return (string) wp_nav_menu( [
			'container'       => '',
			'container_class' => '',
			'fallback_cb'     => false,
			'echo'            => false,
			'theme_location'  => $this->primary,
			'walker'          => new Primary_Walker_Nav_Menu( new Focus_Areas_Navigation_Controller() ),
			'depth'           => 1,
			'items_wrap'      => '%3$s',
		] );
	}

}
