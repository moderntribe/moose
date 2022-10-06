<?php declare(strict_types=1);

use Tribe\Theme\Components\footer\locations\Footer_Locations_Controller;

/**
 * @var array $args Arguments passed to the template
 */
$c = Footer_Locations_Controller::factory( $args );

$city = $c->get_city();
if ( ! $city ) {
	return;
}
$description  = $c->get_description();
$location_url = $c->get_location_url();

?>

<div class="footer-block-locations__item">
	<span class="footer-block-locations__time js-time-value" data-timezone="<?php echo esc_attr( $c->get_timezone() ); ?>" data-time-dynamic="true">
		<?php echo esc_html( $c->get_timezone_time() ); ?>
	</span>
	<span class="footer-block-locations__title">
		<?php echo esc_html( $city ); ?>
	</span>
	<span class="footer-block-locations__hover">
		<?php if ( $description ) : ?>
			<span class="footer-block-locations__address">
				<?php echo esc_html( $description ); ?>
			</span>
		<?php endif; ?>
		<?php if ( $location_url ) : ?>
			<a href="<?php echo esc_url( $location_url ); ?>" class="btn btn--transparent btn--white footer-block-locations__cta" target="_blank" rel="noreferrer">
				<?php esc_html_e( 'Get directions', 'tribe' ); ?><i class="btn__arrow-right"></i>
			</a>
		<?php endif; ?>
	</span>
</div>
