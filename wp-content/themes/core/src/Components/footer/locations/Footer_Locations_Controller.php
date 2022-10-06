<?php declare(strict_types=1);

namespace Tribe\Theme\Components\footer\locations;

use Tribe\Plugin\Settings\Footer_Settings;
use Tribe\Theme\Components\Abstract_Controller;

class Footer_Locations_Controller extends Abstract_Controller {

	protected string $city;
	protected int $timezone;
	protected string $description;
	protected string $url;

	public function __construct( array $args = [] ) {
		$this->city        = (string) ( $args[ Footer_Settings::FOOTER_LOCATION_CITY ] ?? '' );
		$this->timezone    = (int) ( $args[ Footer_Settings::FOOTER_LOCATION_TIMEZONE ] ?? 0 );
		$this->description = (string) ( $args[ Footer_Settings::FOOTER_LOCATION_DESCRIPTION ] ?? '' );
		$this->url         = (string) ( $args[ Footer_Settings::FOOTER_LOCATION_URL ] ?? '' );
	}

	public function get_city(): string {
		return $this->city;
	}

	public function get_timezone(): string {
		return (string) $this->timezone;
	}

	public function get_timezone_time(): string {
		return gmdate( 'H:i', time() - $this->timezone * 60 * 60 );
	}

	public function get_description(): string {
		return $this->description;
	}

	public function get_location_url(): string {
		return $this->url;
	}

}
