<?php declare(strict_types=1);

namespace Tribe\Theme\Components\header;

use WP_Post;

trait Menu_Item {

	/**
	 * @param \WP_Post $menu_item
	 *
	 * @return string[]
	 */
	public function get_menu_item_attributes( WP_Post $menu_item ): array {
		$atts           = [];
		$atts['title']  = (string) ( $menu_item->attr_title ?? '' );
		$atts['target'] = (string) ( $menu_item->target ?? '' );
		$atts['rel']    = (string) ( $menu_item->xfn ?? '' );
		$atts['href']   = (string) ( $menu_item->url ?? '' );

		if ( '_blank' === $atts['target'] && empty( $menu_item->xfn ) ) {
			$atts['rel'] = 'noopener';
		}

		return $atts;
	}

}
