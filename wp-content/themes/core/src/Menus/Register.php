<?php declare(strict_types=1);

namespace Tribe\Theme\Menus;

class Register {

	public const PRIMARY          = 'primary';
	public const FOCUS_AREAS      = 'focus_areas';
	public const FOOTER_MAIN      = 'footer_main';
	public const FOOTER_SECONDARY = 'footer_secondary';

	protected string $location;
	protected string $description;

	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'register' ] );
	}

	public function register(): void {
		register_nav_menus( [
			self::PRIMARY          => esc_html__( 'Primary Menu', 'tribe' ),
			self::FOCUS_AREAS      => esc_html__( 'Focus Areas Menu', 'tribe' ),
			self::FOOTER_MAIN      => esc_html__( 'Main Footer Menu', 'tribe' ),
			self::FOOTER_SECONDARY => esc_html__( 'Secondary Footer Menu', 'tribe' ),
		] );
	}

}
