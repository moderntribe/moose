<?php declare(strict_types=1);

use \Tribe\Theme\Components\header\focus_areas\Focus_Areas_Navigation_Controller;

$c = Focus_Areas_Navigation_Controller::factory();

$items = $c->get_focus_areas();
if ( ! $items ) {
	return;
}

?>

<div class="header-block-list__item-with-children">
	<button class="header-block-list__item js-header-block-item-with-children-toggle"><?php esc_html_e( 'Our Expertise', 'tribe' ); ?></button>
	<div class="header-block-list__item-children">
		<header class="header-block-list__item-children-header">
			<a href="<?php esc_url( $c->get_our_expertise_url() ) ?>" class="header-block-list__child-item-parent-link">
				<span><?php esc_html_e( 'View Our Expertise', 'tribe' ); ?></span>
				<svg role="presentation">
					<use xlink:href="#icon-arrow"></use>
				</svg>
			</a>
			<span class="header-block-list__item-children-title"><?php esc_html_e( 'Focus Areas', 'tribe' ); ?></span>
		</header>
		<div class="header-block-list__item-children-inner">
			<?php foreach ( $items as $item ) {
				get_component_part( 'header/focus_areas/focus_area_item', [
					'item' => $item,
				] );
			} ?>
		</div>
	</div>
</div>
