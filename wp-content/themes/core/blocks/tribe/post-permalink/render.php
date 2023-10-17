<?php declare(strict_types=1);

$post_permalink = get_the_permalink();
?>

<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<span class="wp-block-tribe-post-permalink__label"><?php esc_html_e( $post_permalink, 'tribe' ); ?></span>
</div>
