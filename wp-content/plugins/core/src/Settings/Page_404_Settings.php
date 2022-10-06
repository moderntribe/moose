<?php declare(strict_types=1);

namespace Tribe\Plugin\Settings;

use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Text;

class Page_404_Settings extends Settings_Sub_Page {

	public const PAGE_SLUG = 'page-404';

	public const IMAGE        = '404_image';
	public const TITLE        = '404_title';
	public const DESCRIPTION  = '404_description';
	public const BUTTON_TITLE = '404_button_title';

	public function get_title(): string {
		return esc_html__( '404 Page Settings', 'tribe' );
	}

	public function get_fields(): array {
		return [
			Text::make( esc_html__( 'Title', self::TITLE ) ),
			Image::make( esc_html__( 'Image' ), self::IMAGE )
				 ->returnFormat( 'id' ),
			Text::make( esc_html__( 'Description', self::TITLE ) ),
			Text::make( esc_html__( 'Button Title', self::BUTTON_TITLE ) ),
		];
	}

}
