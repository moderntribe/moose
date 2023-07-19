<?php declare(strict_types=1);

// this gets us the post type, but we really want the name
$post_type = get_post_type();

if ( ! $post_type ) {
	return;
}

$post_object = get_post_type_object( $post_type );

if ( ! $post_object ) {
	return;
}
?>

<div class="wp-block-tribe-post-type-name">
	<span class="wp-block-tribe-post-type-name__label"><?php esc_html_e( $post_object->labels->singular_name, 'tribe' ); ?></span>
</div>
