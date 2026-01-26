<?php declare(strict_types=1);

namespace Tribe\Plugin\Menus;

use Tribe\Plugin\Post_Types\Announcement\Announcement;
use Tribe\Plugin\Post_Types\Page\Page;

class Admin_Menu_Order {

	public function custom_menu_order(): array {
		return [
			'index.php',
			'separator1',
			'edit.php?post_type=' . Page::NAME,
			// place CPTs here
			'edit.php',
			'upload.php',
			'gf_edit_forms',
			'edit.php?post_type=' . Announcement::NAME,
			'separator2',
			'themes.php',
			'plugins.php',
			'users.php',
			'tools.php',
			'options-general.php',
			'edit.php?post_type=training',
			'separator-last',
			'edit.php?post_type=acf-field-group',
			'wpseo_dashboard',
			'rank-math',
		];
	}

}
