<?php declare(strict_types=1);

// .phpstorm.meta.php
namespace PHPSTORM_META {

    override( \Psr\Container\ContainerInterface::get( 0 ), map( [
        '' => '@',
    ] ) );
    override( \DI\Container::get( 0 ), map( [
        '' => '@',
    ] ) );
    override( \DI\FactoryInterface::make( 0 ), map( [
        '' => '@',
    ] ) );
    override( \DI\Container::make( 0 ), map( [
        '' => '@',
    ] ) );

}
