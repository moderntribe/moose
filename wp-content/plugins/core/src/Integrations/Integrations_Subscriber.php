<?php declare(strict_types=1);

namespace Tribe\Plugin\Integrations;

use Tribe\Libs\Container\Abstract_Subscriber;
use Tribe\Plugin\Integrations\ACF;

class Integrations_Subscriber extends Abstract_Subscriber {

	public function register(): void {

		add_filter( 'acf/settings/show_admin', function ( $show ): bool {
			return $this->container->get( ACF::class )->show_acf_menu_item( (bool) $show );
		}, 10, 1 );
	}

}
