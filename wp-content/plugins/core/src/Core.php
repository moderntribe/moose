<?php declare(strict_types=1);

namespace Tribe\Plugin;

use DI\ContainerBuilder;
use Tribe\Plugin\Settings\Settings_Definer;

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
		Settings_Definer::class,
	];

	/**
	 * @var string[] Names of classes extending Abstract_Subscriber.
	 */
	private array $subscribers = [
		Post_Types\Case_Study\Subscriber::class,
		Post_Types\Portfolio\Subscriber::class,
		Post_Types\Team\Subscriber::class,
		Taxonomies\Stage\Subscriber::class,
		Taxonomies\Sector\Subscriber::class,
		Taxonomies\Team_Function\Subscriber::class,
	];

	/**
	 * @var string[] Names of classes from Tribe Libs implementing Definer_Interface.
	 */
	private array $lib_definers = [
		'\Tribe\Libs\Settings\Settings_Definer',
	];

	/**
	 * @var string[] Names of classes from Tribe Libs extending Abstract_Subscriber.
	 */
	private array $lib_subscribers = [
		'\Tribe\Libs\Settings\Settings_Subscriber',
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

		// combine definers/subscribers from the Plugin and libs
		$definers    = array_merge( array_filter( $this->lib_definers, 'class_exists' ), $this->definers );
		$subscribers = array_merge( array_filter( $this->lib_subscribers, 'class_exists' ), $this->subscribers );

		/**
		 * Filter the list of definers that power the plugin
		 *
		 * @param string[] $definers The class names of definers that will be instantiated
		 */
		$definers = apply_filters( 'tribe/Plugin/definers', $definers );

		/**
		 * Filter the list subscribers that power the plugin
		 *
		 * @param string[] $subscribers The class names of subscribers that will be instantiated
		 */
		$subscribers = apply_filters( 'tribe/Plugin/subscribers', $subscribers );

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
