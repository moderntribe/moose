<?php declare(strict_types=1);

namespace Tribe\Plugin\Integrations;

class ACF {

	/**
	 * Hides the ACF Menu item if the HIDE_ACF_MENU constant isset to true or
	 * if we are in a production environment based on the
	 * wp_get_environment_type() function.
	 *
	 * @param bool $show_menu_item
	 */
	public function show_acf_menu_item( bool $show_menu_item ): bool {

		if ( defined( 'HIDE_ACF_MENU' ) && HIDE_ACF_MENU === true ) {
			return false;
		}

		if ( wp_get_environment_type() === 'production' ) {
			return false;
		}

		return $show_menu_item;
	}

}
