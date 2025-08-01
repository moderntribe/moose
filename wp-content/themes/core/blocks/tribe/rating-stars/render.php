<?php declare(strict_types=1);

$rating         = isset( $attributes['rating'] ) ? floatval( $attributes['rating'] ) : 0;
$container_size = isset( $attributes['containerSize'] ) ? intval( $attributes['containerSize'] ) : 100;

?>
<div
	<?php echo wp_kses_data(get_block_wrapper_attributes([
		'role'       => 'img',
		'aria-label' => sprintf(
			__( 'Rated %s out of 5 stars', 'tribe' ),
			$rating
		),
	])); ?>>
	<div
		aria-hidden="true"
		class="stars-wrapper"
		style="--rating-stars--size: <?php echo esc_attr( (string) $container_size ) . 'px'; ?>">
		<?php
		$remaining = $rating;

		for ( $i = 0; $i < 5; $i++ ) {
			if ( $remaining >= 1 ) {
				echo '<span class="star star--full"></span>';
				$remaining -= 1;
			} elseif ( $remaining >= 0.5 ) {
				echo '<span class="star star--half"></span>';
				$remaining -= 0.5;
			} else {
				echo '<span class="star star--empty"></span>';
			}
		}
		?>
	</div>
</div>
