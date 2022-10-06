<?php declare(strict_types=1);

namespace Tribe\Theme\Components\footer;

use Tribe\Plugin\Settings\Footer_Settings;
use Tribe\Theme\Components\Abstract_Controller;
use Tribe\Theme\Components\footer\navigation\Footer_Walker_Nav_Menu;
use Tribe\Theme\Menus\Register;

class Footer_Controller extends Abstract_Controller {

	public const MAIN_MENU_ITEM_CLASSES = [
		'footer-block-main-menu__item',
		'lazy',
	];

	public const SECONDARY_MENU_ITEM_CLASSES = [
		'footer-block-legal-menu__item',
		'lazy',
	];

	private Footer_Settings $settings;
	private string $main_menu;
	private string $secondary_menu;

	public function __construct( Footer_Settings $settings ) {
		$this->settings       = $settings;
		$this->main_menu      = Register::FOOTER_MAIN;
		$this->secondary_menu = Register::FOOTER_SECONDARY;
	}

	public function get_footer_locations(): array {
		return (array) $this->settings->get_setting( Footer_Settings::FOOTER_LOCATIONS );
	}

	public function get_footer_logo(): string {
		return (string) wp_get_attachment_image_url(
			(int) $this->settings->get_setting( Footer_Settings::FOOTER_LOGO ),
			'full'
		);
	}

	public function get_main_footer_nav(): string {
		return $this->get_registered_menu(
			$this->main_menu,
			new Footer_Walker_Nav_Menu( self::MAIN_MENU_ITEM_CLASSES ),
		);
	}

	public function get_secondary_footer_nav(): string {
		return $this->get_registered_menu(
			$this->secondary_menu,
			new Footer_Walker_Nav_Menu( self::SECONDARY_MENU_ITEM_CLASSES ),
		);
	}

	public function get_copyright(): string {
		return sprintf(
			(string) $this->settings->get_setting( Footer_Settings::FOOTER_COPYRIGHT ),
			date( 'Y' )
		);
	}

	protected function get_registered_menu( string $location, \Walker_Nav_Menu $walker ): string {
		return (string) wp_nav_menu( [
			'container'       => '',
			'container_class' => '',
			'fallback_cb'     => false,
			'echo'            => false,
			'theme_location'  => $location,
			'walker'          => $walker,
			'depth'           => 1,
			'items_wrap'      => '%3$s',
		] );
	}

}
