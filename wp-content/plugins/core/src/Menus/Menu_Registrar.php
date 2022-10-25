<?php declare(strict_types=1);

namespace Tribe\Plugin\Menus;

class Menu_Registrar {

	public const PRIMARY = 'primary';

	public function register(): void {
		register_nav_menus( [
			self::PRIMARY => esc_html__( 'Primary Menu', 'tribe' ),
		] );
	}

}
