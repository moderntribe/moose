<?php declare(strict_types=1);

use Tribe\Theme\Components\footer\Footer_Controller;

$c = Footer_Controller::factory();

$footer_locations = $c->get_footer_locations();
?>

<footer class="footer-block">
	<div>
		<section class="footer-block-locations js-footer-block-locations" data-expand="-100">
			<?php foreach ( $footer_locations as $number => $values ) {
				if ( ! is_array( $values ) ) {
					continue;
				}
				$values['number'] = $number + 1;
				get_component_part( 'footer/locations/footer_location', $values );
			} ?>
		</section>

		<div class="footer-block__grid">
			<section class="footer-block-main-menu">
				<div class="footer-block-main-menu__inner">
					<a class="footer-block-main-menu__logo-link lazyloaded" data-expand="-100"
					   href="<?php echo esc_url( get_home_url() ); ?>">
						<?php echo esc_url( $c->get_footer_logo() ); ?>
						<span class="sr-only"><?php esc_html_e( 'Home', 'tribe' ); ?></span>
					</a>
					<nav class="footer-block-main-menu__list">
						<?php echo $c->get_main_footer_nav(); ?>
					</nav>
				</div>
			</section>

			<section class="footer-block-legal-menu">
				<nav class="footer-block-legal-menu__inner">
					<?php echo $c->get_secondary_footer_nav(); ?>
					<span class="footer-block-legal-menu__item lazy" data-expand="-15">
						<?php echo esc_html( $c->get_copyright() ); ?>
					</span>
				</nav>
			</section>
		</div>
	</div>
</footer>
