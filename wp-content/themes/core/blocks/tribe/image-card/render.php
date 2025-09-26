<?php declare(strict_types=1);

$example_text_control = $attributes['exampleTextControl']; // @phpstan-ignore-line
?>
<section <?php echo get_block_wrapper_attributes(); ?>>
	<p><?php esc_html_e( 'Image Card – hello from a dynamic block!', 'tribe' ); ?></p>
	<?php if ( $example_text_control ) : ?>
		<p><?php echo esc_html( $example_text_control ); ?></p>
	<?php endif; ?>
</section>
