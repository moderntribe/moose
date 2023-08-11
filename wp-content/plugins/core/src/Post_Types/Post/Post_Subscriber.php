<?php declare(strict_types=1);

namespace Tribe\Plugin\Post_Types\Post;

use Tribe\Libs\Container\Abstract_Subscriber;

class Post_Subscriber extends Abstract_Subscriber {

	public function register(): void {
		$this->block_templates();
	}

	public function block_templates(): void {
		add_action( 'init', function (): void {
			$this->container->get( Config::class )->register_block_template();
		} );
	}

}
