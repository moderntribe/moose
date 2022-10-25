<?php declare(strict_types=1);

namespace Tribe\Plugin\Object_Meta;

use Tribe\Libs\Container\Abstract_Subscriber;

class Meta_Subscriber extends Abstract_Subscriber {

	public function register(): void {
		add_action( 'init', function (): void {
			foreach ( $this->container->get( Meta_Definer::OBJECT_META ) as $meta ) {
				$this->container->get( Meta_Registrar::class )->register( $meta );
			}
		}, 10, 0 );
	}

}
