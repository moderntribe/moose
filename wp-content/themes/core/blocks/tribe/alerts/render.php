<?php declare(strict_types=1);

use Tribe\Plugin\Components\Announcements\Announcement_Controller;
use Tribe\Plugin\Components\Announcements\Announcement_Renderer;
use Tribe\Plugin\Object_Meta\Post_Types\Announcement_Meta;

$placement = $attributes['placement'] ?? Announcement_Meta::PLACEMENT_ABOVE;

$controller = Announcement_Controller::factory();
$renderer = new Announcement_Renderer( $controller );

echo $renderer->parse_and_render( $placement );
