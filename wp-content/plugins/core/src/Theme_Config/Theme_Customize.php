<?php declare(strict_types=1);

namespace Tribe\Plugin\Theme_Config;

use Tribe\Plugin\Object_Meta\Page_Header_Settings;

class Theme_Customize {

	/**
	 * Add header color class to body on all pages
	 *
	 * @param array<string> $classes Array of body classes
	 *
	 * @return array<string> Modified body classes
	 */
	public function add_header_color_body_class( array $classes ): array {
		$post_id = get_the_ID();

		if ( ! is_page() || ! $post_id ) {
			return array_merge( $classes, [ 'is-header--default' ] );
		}

		$header_color = get_field( Page_Header_Settings::HEADER_COLOR, $post_id );

		if ( empty( $header_color ) || ! is_string( $header_color ) ) {
			return array_merge( $classes, [ 'is-header--default' ] );
		}

		return array_merge( $classes, [ esc_attr( $header_color ) ] );
	}

}
