<?php declare(strict_types=1);

use Tribe\Theme\Components\header\focus_areas\Focus_Area_Item_Controller;

$c = Focus_Area_Item_Controller::factory();

$menu_item = $args['item'] ?? null;
if ( ! $menu_item instanceof WP_Post ) {
	return;
}

?>

<a class="header-block-list__child-item" href="<?php echo esc_url( $menu_item->url ?? '' ); ?>">
	<?php
	$img_id = $c->get_thumbnail_id( $menu_item->ID );
	if ( $img_id ) : ?>
		<figure class="header-block-list__child-item-icon-box">
			<div class="header-block-list__child-item-icon-wrapper">
				<img class="header-block-list__child-item-icon"
					 src="<?php echo esc_url( $c->get_thumbnail_url( $img_id ) ); ?>"
					 alt="<?php echo esc_attr( $c->get_thumbnail_alt( $img_id ) ); ?>">
			</div>
		</figure>
	<?php endif; ?>

	<span class="header-block-list__child-item-label"><?php echo esc_html( $menu_item->title ?? '' ); ?></span>
</a>
