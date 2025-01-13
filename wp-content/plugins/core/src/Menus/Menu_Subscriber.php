<?php declare(strict_types=1);

namespace Tribe\Plugin\Menus;

use Tribe\Plugin\Core\Abstract_Subscriber;

class Menu_Subscriber extends Abstract_Subscriber {

	public function register(): void {
		add_filter( 'custom_menu_order', function (): bool {
			return $this->container->get( Menu_Order::class )->site_has_custom_menu_order();
		} );

		add_filter( 'menu_order', function ( array $menu_ord ): array {
			return $this->container->get( Menu_Order::class )->custom_menu_order( $menu_ord );
		} );
	}

}
