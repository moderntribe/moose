<?php declare(strict_types=1);

namespace Tribe\Plugin\Assets;

use Tribe\Libs\Container\Abstract_Subscriber;

class Assets_Subscriber extends Abstract_Subscriber {

	public function register(): void {
		add_action( 'wp_enqueue_scripts', function (): void {
			$this->container->get( Public_Assets_Enqueuer::class )->register();
		}, 10, 0 );

		add_action( 'after_setup_theme', function (): void {
			$this->container->get( Block_Assets_Enqueuer::class )->register();
		}, 99, 0 );

		add_action( 'wp_enqueue_scripts', function (): void {
			$this->container->get( Print_Assets_Enqueuer::class )->register();
		}, 10, 0 );

		add_action( 'admin_enqueue_scripts', function (): void {
			$this->container->get( Admin_Assets_Enqueuer::class )->register();
		}, 10, 0 );
	}

}
