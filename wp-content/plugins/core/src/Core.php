<?php declare(strict_types=1);

namespace Tribe\Plugin;

use DI\ContainerBuilder;

class Core {

	public const PLUGIN_FILE = 'plugin.file';

	/**
	 * @var \Psr\Container\ContainerInterface|\Invoker\InvokerInterface|\DI\FactoryInterface
	 */
	private $container;

	/**
	 * @var string[] Names of classes implementing Definer_Interface.
	 */
	private array $definers = [
		\Tribe\Libs\Settings\Settings_Definer::class,
		Blocks\Blocks_Definer::class,
		Object_Meta\Meta_Definer::class,
		Settings\Settings_Definer::class,
	];

	/**
	 * @var string[] Names of classes extending Abstract_Subscriber.
	 */
	private array $subscribers = [
		\Tribe\Libs\Settings\Settings_Subscriber::class,
		Assets\Assets_Subscriber::class,
		Blocks\Blocks_Subscriber::class,
		Integrations\Integrations_Subscriber::class,
		Object_Meta\Meta_Subscriber::class,
		Theme_Config\Theme_Config_Subscriber::class,
	];

	private static self $instance;

	public static function instance(): self {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @throws \Exception
	 */
	public function init( string $plugin_path ): void {
		$this->init_container( $plugin_path );
	}

	/**
	 * @return \Psr\Container\ContainerInterface|\Invoker\InvokerInterface|\DI\FactoryInterface
	 */
	public function container() {
		return $this->container;
	}

	/**
	 * @throws \Exception
	 */
	private function init_container( string $plugin_path ): void {

		/**
		 * Filter the list of definers that power the plugin
		 *
		 * @param string[] $definers The class names of definers that will be instantiated
		 */
		$definers = apply_filters( 'tribe/plugin/definers', $this->definers );

		/**
		 * Filter the list subscribers that power the plugin
		 *
		 * @param string[] $subscribers The class names of subscribers that will be instantiated
		 */
		$subscribers = apply_filters( 'tribe/plugin/subscribers', $this->subscribers );

		$builder = new ContainerBuilder();
		$builder->useAutowiring( true );
		$builder->useAnnotations( false );
		$builder->addDefinitions( [ self::PLUGIN_FILE => $plugin_path ] );
		$builder->addDefinitions( ...array_map( static fn ( $classname ) => ( new $classname() )->define(), $definers ) );

		$this->container = $builder->build();

		foreach ( $subscribers as $subscriber_class ) {
			( new $subscriber_class( $this->container ) )->register();
		}
	}

}
