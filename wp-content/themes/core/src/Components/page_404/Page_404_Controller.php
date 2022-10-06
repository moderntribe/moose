<?php declare(strict_types=1);

namespace Tribe\Theme\Components\page_404;

use Tribe\Plugin\Settings\Page_404_Settings;
use Tribe\Theme\Components\Abstract_Controller;

class Page_404_Controller extends Abstract_Controller {

	private Page_404_Settings $settings;

	public function __construct( Page_404_Settings $settings ) {
		$this->settings = $settings;
	}

	public function get_text_value( string $key ): string {
		return (string) $this->settings->get_setting( $key );
	}

	public function get_image(): string {
		return wp_get_attachment_image( (int) $this->settings->get_setting( Page_404_Settings::IMAGE ) );
	}

}
