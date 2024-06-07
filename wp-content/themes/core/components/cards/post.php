<?php declare(strict_types=1);

use Tribe\Plugin\Post_Types\Post\Post;

$id           = $args['id']; // @phpstan-ignore-line
$post         = new Post();
$primary_term = $post->get_primary_term( $id, 'category' );
?>
<article class="p-card-post l-clearfix">
	<div class="p-card-post__inner">
		<?php if ( has_post_thumbnail( $id ) ) : ?>
			<figure class="p-card-post__image">
				<?php echo get_the_post_thumbnail( $id, 'post-thumbnail', [ 'style' => 'object-fit: cover;' ] ); ?>
			</figure>
		<?php endif; ?>

		<?php if ( $primary_term !== null ) : ?>
			<div class="p-card-post__categories" style="margin-bottom: var(--wp--preset--spacing--10)">
				<a href="<?php echo get_term_link( $primary_term, 'category' ); ?>" rel="tag" class="t-category"><?php echo esc_html( $primary_term->name ); ?></a>
			</div>
		<?php endif; ?>

		<h2 class="p-card-post__title t-display-xx-small" style="margin-top: 0; margin-bottom: var(--wp--preset--spacing--10)"><?php echo esc_html( get_the_title( $id ) ); ?></h2>

		<a href="<?php echo esc_url( get_the_permalink( $id ) ); ?>" class="p-card-post__link a-link-cover" target="_self"><?php echo esc_html__( 'Read More', 'tribe' ); ?></a>
	</div>
</article>
