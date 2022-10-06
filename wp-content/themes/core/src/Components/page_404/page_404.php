<?php declare(strict_types=1);

use Tribe\Plugin\Settings\Page_404_Settings;
use Tribe\Theme\Components\page_404\Page_404_Controller;

$c     = Page_404_Controller::factory();
$title = $c->get_text_value( Page_404_Settings::TITLE );
$desc  = $c->get_text_value( Page_404_Settings::DESCRIPTION );
?>

<section class="hero-block lazyloaded" data-expand="-100">

	<article class="hero-block__content">
		<?php if ( $title ) : ?>
			<h1 class="hero-block__title"><?php echo esc_html( $title ); ?></h1>
		<?php endif; ?>

		<?php if ( $desc ) : ?>
			<p class="hero-block__copy"><?php echo esc_html( $desc ); ?></p>
		<?php endif; ?>

		<a class="hero-block__cta btn" href="<?php esc_url( get_home_url() ) ?>">
			<?php echo esc_html( $c->get_text_value( Page_404_Settings::BUTTON_TITLE ) ?: 'Home' ); ?>
			<i class="btn__arrow-right"></i>
		</a>
	</article>

	<section class="hero-block__img-wrap">
		<?php echo $c->get_image(); ?>
	</section>
</section>
