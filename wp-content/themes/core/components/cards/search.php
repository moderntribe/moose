<?php declare(strict_types=1);

$id            = $args['id']; // @phpstan-ignore-line
$post_type     = get_post_type( $id );
$post_type_obj = get_post_type_object( $post_type );
?>
<div class="p-card-search-result l-clearfix">
	<?php if ( has_post_thumbnail( $id ) ) : ?>
		<figure class="p-card-search-result__image">
			<?php echo get_the_post_thumbnail( $id, 'post-thumbnail', [ 'style' => 'object-fit: cover;' ] ); ?>
		</figure>
	<?php endif; ?>

	<p class="p-card-search-result__post-type t-category" style="margin-top: 0; margin-bottom: var(--wp--preset--spacing--10)"><?php echo esc_html( $post_type_obj->labels->singular_name ); ?></p>

	<h2 class="p-card-search-result__title t-display-x-small" style="margin-top: 0; margin-bottom: var(--wp--preset--spacing--10)"><?php echo esc_html( get_the_title( $id ) ); ?></h2>

	<p class="p-card-search-result__excerpt"><?php echo esc_html( get_the_excerpt( $id ) ); ?></p>

	<p class="p-card-search-result__permalink t-caption" style="margin-top: var(--wp--preset--spacing--20); color: var(--color-neutral-60)"><?php echo esc_html( get_the_permalink( $id ) ); ?></p>

	<a href="<?php echo esc_url( get_the_permalink( $id ) ); ?>" class="p-card-search-result__link a-link-cover" target="_self"><?php echo esc_html__( 'Read More', 'tribe' ); ?></a>
</div>
