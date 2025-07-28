<?php declare(strict_types=1);

$icons_path     = get_theme_file_path( 'blocks/tribe/rating-stars/icons/svg/' );
$rating         = isset( $attributes['rating'] ) ? floatval( $attributes['rating'] ) : 0;
$container_size = isset( $attributes['containerSize'] ) ? intval( $attributes['containerSize'] ) : 100;

?>
<div
	<?php echo  wp_kses_data( get_block_wrapper_attributes( [
		'role'       => 'img',
		'aria-label' => "Rated {$rating} out of 5 stars",
		] ) ) ?>
	>
	<div
		class="stars-wrapper"
		style="--rating-stars--size: <?php echo esc_attr( (string) $container_size ) . 'px'; ?>"
	>
		<?php
		$remaining = $rating;

		for ( $i = 0; $i < 5; $i++ ) {
			if ( $remaining >= 1 ) {
				readfile( $icons_path . 'icon-star-full.svg' );
				$remaining -= 1;
			} elseif ( $remaining >= 0.5 ) {
				readfile( $icons_path . 'icon-star-half.svg' );
				$remaining -= 0.5;
			} else {
				readfile( $icons_path . 'icon-star-empty.svg' );
			}
		}
		?>
	</div>
</div>
