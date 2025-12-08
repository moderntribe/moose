<?php declare(strict_types=1);

namespace Tribe\Plugin\Menus;

class Admin_Menu_Order {

	public function custom_menu_order(): array {
		$cpts   = $this->get_post_types();
		$before = [
			'index.php',
			'separator1',
			'edit.php?post_type=page',
		];

		foreach ( $cpts as $post_type ) {
			$before[] = 'edit.php?post_type=' . $post_type;
		}

		$after = [
			'edit.php',
			'upload.php',
			'gf_edit_forms',
			'separator2',
			'themes.php',
			'plugins.php',
			'users.php',
			'tools.php',
			'options-general.php',
			'edit.php?post_type=training',
			'separator-last',
			'edit.php?post_type=acf-field-group',
			'wpseo_dashboard',
			'rank-math',
		];

		return array_merge( $before, $after );
	}

	/**
	 * @throws \DI\DependencyException
	 * @throws \DI\NotFoundException
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	protected function get_post_types(): array {
		$subscribers = tribe_project()->get_subscribers();

		$post_types = [];
		foreach ( $subscribers as $subscriber ) {
			// skip non-post types subscribers
			if ( ! str_contains( $subscriber, 'Post_Types\\' ) ) {
				continue;
			}

			// skip std post types
			if ( str_contains( $subscriber, 'Post_Types\\Page' ) || str_contains( $subscriber, 'Post_Types\\Post' ) ) {
				continue;
			}

			$parts = explode( '\\', $subscriber );
			array_pop( $parts );

			$config_class = implode( '\\', $parts ) . '\\Config';
			$post_type    = tribe_project()->container()->get( $config_class )->post_type();

			/**
			 * @var \WP_Post_Type $post_object
			 */
			$post_object = get_post_type_object( $post_type );

			if ( ! $post_type->show_ui || ! $post_type->public ) {
				continue;
			}

			$post_types[ $post_object->menu_position ] = $post_type;
		}

		ksort( $post_types );

		return $post_types;
	}

}
