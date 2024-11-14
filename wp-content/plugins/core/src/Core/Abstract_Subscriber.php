<?php declare(strict_types=1);

namespace Tribe\Plugin\Core;

use Psr\Container\ContainerInterface;
use Tribe\Plugin\Core\Interfaces\Subscriber_Interface;

abstract class Abstract_Subscriber implements Subscriber_Interface {

	protected ContainerInterface $container;

	/**
	 * Abstract_Subscriber constructor.
	 */
	public function __construct( ContainerInterface $container ) {
		$this->container = $container;
	}

}
