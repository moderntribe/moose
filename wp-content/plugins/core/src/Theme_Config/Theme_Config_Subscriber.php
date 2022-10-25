<?php declare(strict_types=1);

namespace Tribe\Plugin\Theme_Config;

use Tribe\Libs\Container\Abstract_Subscriber;
use Tribe\Plugin\Menus\Menu_Registrar;

class Theme_Config_Subscriber extends Abstract_Subscriber {

	public function register(): void {
		add_action( 'after_setup_theme', function (): void {
			$this->container->get( Theme_Support::class )->add_theme_supports();

			$this->container->get( Menu_Registrar::class )->register();

			$this->container->get( Image_Sizes::class )->register_sizes();
		}, 10, 0 );
	}

}
