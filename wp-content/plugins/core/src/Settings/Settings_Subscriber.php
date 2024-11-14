<?php declare(strict_types=1);

namespace Tribe\Plugin\Settings;

use Tribe\Plugin\Core\Abstract_Subscriber;

class Settings_Subscriber extends Abstract_Subscriber {

	public function register(): void {
		add_action( 'init', function (): void {
			foreach ( $this->container->get( Settings_Definer::PAGES ) as $page ) {
				$page->hook();
			}
		}, 0, 0 );
	}

}
