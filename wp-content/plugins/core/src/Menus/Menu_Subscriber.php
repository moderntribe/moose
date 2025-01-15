<?php declare(strict_types=1);

namespace Tribe\Plugin\Menus;

use Tribe\Plugin\Core\Abstract_Subscriber;

class Menu_Subscriber extends Abstract_Subscriber {

	public function register(): void {
		add_filter( 'custom_menu_order', '__return_true' );

		add_filter( 'menu_order', function (): array {
			return $this->container->get( Admin_Menu_Order::class )->custom_menu_order();
		} );
	}

}
