<?php declare(strict_types=1);

use \Tribe\Theme\Components\header\navigation\Main_Navigation_Controller;

$c = Main_Navigation_Controller::factory();

$menu = $c->get_primary_menu();

?>

<header class="header-block">
	<div>
		<section class="header-block-burger">
			<a class="header-block-burger__logo" href="<?php echo esc_url( get_home_url() ) ?>">
				<span class="header-block-burger__icon header-block-burger__icon--logo">
					<?php get_component_part( 'header/navigation/logo_svg' ); ?>
				</span>
				<span class="sr-only"><?php esc_html_e( 'Home', 'tribe' ); ?></span>
			</a>

			<button class="header-block-burger__btn js-header-block-drawer-toggle">
				<svg class="header-block-burger__icon header-block-burger__icon--burger" focusable="false"
					 role="presentation">
					<use xlink:href="#icon-burger"></use>
				</svg>
				<svg class="header-block-burger__icon header-block-burger__icon--close" focusable="false"
					 role="presentation">
					<use xlink:href="#icon-close"></use>
				</svg>
				<span class="sr-only"><?php esc_html_e( 'Open or close menu', 'tribe' ); ?></span>
			</button>
		</section>
		<section class="header-block-list">
			<div class="header-block-list__inner">
				<a class="header-block-list__logo" href="<?php echo esc_url( get_home_url() ) ?>">
					<span class="header-block-list__logo-icon">
						<?php get_component_part( 'header/navigation/logo_svg' ); ?>
					</span>
					<span class="sr-only"><?php esc_html_e( 'Home', 'tribe' ); ?></span>
				</a>

				<nav class="header-block-list__nav js-header-block-list-toggle">
					<?php echo $menu; ?>
				</nav>
			</div>
		</section>

		<section class="header-block-drawer">
			<nav class="header-block-drawer__list">
				<?php echo str_replace( 'header-block-list', 'header-block-drawer', $menu ); ?>
			</nav>
		</section>
	</div>

</header>
