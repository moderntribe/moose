<?php declare(strict_types=1);

$post_type = get_post_type();
?>

<div class="b-post-type">
	<span class="b-post-type__label"><?php esc_html_e( $post_type, 'tribe' ); ?></span>
</div>
