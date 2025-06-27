<?php declare(strict_types=1);

/**
 * @var object $args
 */

$post_id = $args['post_id'];

// return if no post id
if ( ! $post_id ) {
	return;
}

$post_type        = get_post_type( $post_id );
$post_type_object = get_post_type_object( $post_type );
$image_id         = get_post_thumbnail_id( $post_id );
$title            = get_the_title( $post_id );
$author_id        = (int) get_post_field( 'post_author', $post_id );
$author           = get_the_author_meta( 'display_name', $author_id );
$date             = get_the_date( 'M j, Y' );
$excerpt          = get_the_excerpt( $post_id );
$permalink        = get_the_permalink( $post_id );
?>
<article class="c-search-card">
	<div class="c-search-card__inner">
		<?php if ( $image_id ) : ?>
			<div class="c-search-card__image aspect-ratio-cover aspect-ratio-4-3">
				<?php echo wp_get_attachment_image( $image_id, 'large' ); ?>
			</div>
		<?php endif; ?>
		<?php if ( $post_type_object ) : ?>
			<p class="c-search-card__post-type t-category"><?php echo esc_html( $post_type_object->labels->singular_name ); ?></p>
		<?php endif; ?>
		<h2 class="c-search-card__title t-display-x-small"><?php echo esc_html( $title ); ?></h2>
		<p class="c-search-card__metadata">
			<?php if ( $author ) : ?>
				<span class="c-search-card__metadata-author t-body-small"><?php esc_html_e( 'by', 'tribe' ); ?> <?php echo esc_html( $author ); ?></span>
			<?php endif; ?>
			<?php if ( $author && $date ) : ?>
				<span class="c-search-card__metadata-separator t-body-small">•</span>
			<?php endif; ?>
			<?php if ( $date ) : ?>
				<span class="c-search-card__metadata-date t-body-small"><?php echo esc_html( $date ); ?></span>
			<?php endif; ?>
		</p>
		<?php if ( $excerpt ) : ?>
			<p class="c-search-card__excerpt t-body-small"><?php echo esc_html( $excerpt ); ?></p>
		<?php endif; ?>
		<p class="c-search-card__visible-permalink t-body-small"><?php echo esc_html( $permalink ); ?></p>
	</div>
	<a href="<?php echo esc_url( $permalink ); ?>" class="c-search-card__link-overlay" aria-label="<?php echo esc_html__( 'Read more about ', 'tribe' ) . $title; ?>"></a>
</article>
