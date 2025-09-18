<?php declare(strict_types=1);

use Tribe\Plugin\Components\Alert\Alert_Controller;
use Tribe\Plugin\Components\Alert\Alert_Renderer;
use Tribe\Plugin\Object_Meta\Post_Types\Alert_Meta;

$placement = $attributes['placement'] ?? Alert_Meta::PLACEMENT_ABOVE;

$controller = Alert_Controller::factory();
$renderer = new Alert_Renderer( $controller );

echo $renderer->parse_and_render( $placement );
