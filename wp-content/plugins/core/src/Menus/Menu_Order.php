<?php declare(strict_types=1);

namespace Tribe\Plugin\Menus;

class Menu_Order {

	public const SITE_HAS_CUSTOM_MENU_ORDER = true;

	public function site_has_custom_menu_order(): bool {
		return self::SITE_HAS_CUSTOM_MENU_ORDER;
	}

	public function custom_menu_order( array $menu_ord ): array {
		if ( ! $this->site_has_custom_menu_order() ) {
			return $menu_ord;
		}

		return [
			'index.php',
			'separator1',
			'edit.php?post_type=page',
			// place CPTs here
			'edit.php',
			'upload.php',
			'gf_edit_forms',
			'separator2',
			'themes.php',
			'plugins.php',
			'users.php',
			'tools.php',
			'options-general.php',
			'edit.php?post_type=training',
			'separator-last',
			'edit.php?post_type=acf-field-group',
			'rank-math',
		];
	}

}
