<?php declare(strict_types=1);

namespace Tribe\Plugin\Post_Types\Training;

use Tribe\Libs\Post_Type\Post_Type_Subscriber;

class Training_Subscriber extends Post_Type_Subscriber {

	// phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
	protected $config_class = Config::class;

	public function register(): void {
		parent::register();

			$this->block_templates();
	}

	public function block_templates(): void {
		add_action( 'init', function (): void {
			$this->container->get( Config::class )->register_block_template();
		} );
	}

}
