<?php declare(strict_types=1);

namespace Tribe\Plugin\Assets;

use Tribe\Plugin\Settings\Login_Settings;

class Admin_Assets_Enqueuer extends Assets_Enqueuer {

	public const ADMIN             = 'admin';
	public const ASSETS_FILE       = self::ADMIN . '.asset.php';
	public const LOGIN             = 'tribe-login';
	public const LOGIN_FILE_NAME   = 'login';
	public const LOGIN_ASSETS_FILE = self::LOGIN_FILE_NAME . '.asset.php';

	public function register(): void {
		$args = $this->get_asset_file_args( $this->assets_path . self::ASSETS_FILE );
		wp_enqueue_style(
			self::ADMIN,
			$this->assets_path_uri . self::ADMIN . '.css',
			[],
			$args['version'] ?? false,
			'all',
		);
		wp_enqueue_script(
			self::ADMIN,
			$this->assets_path_uri . self::ADMIN . '.js',
			$args['dependencies'] ?? [],
			$args['version'] ?? false,
			true,
		);
	}

	public function enqueue_login_styles(): void {
		$args = $this->get_asset_file_args( $this->assets_path . self::LOGIN_ASSETS_FILE );

		wp_enqueue_style(
			self::LOGIN,
			$this->assets_path_uri . self::LOGIN_FILE_NAME . '.css',
			[],
			$args['version'] ?? false,
			'all',
		);
	}

	public function update_login_header_url(): string {
		return get_bloginfo( 'url' );
	}

	public function update_login_header(): void {
		$login_logo_id = get_field( Login_Settings::LOGIN_LOGO, 'option' );
		$login_logo    = $login_logo_id ? wp_get_attachment_image_src( $login_logo_id ) : false;

		if ( $login_logo === false ) {
			return;
		}

		echo sprintf(
			'<style>body.login #login h1 a {background: url(%s) no-repeat center top transparent;background-size: 200px 44px;height: 44px;width: 200px; margin: 0 auto 45px auto;} body.login #login {padding-top: 86px;}</style>',
			$login_logo
		);
	}

}
