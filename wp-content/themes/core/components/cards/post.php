<?php declare(strict_types=1);

use Tribe\Plugin\Taxonomies\Category\Category;

/**
 * @var object $args
 */

$post_id = $args['post_id'];

// return if no post id
if ( ! $post_id ) {
	return;
}

// get template part args
$heading_level = $args['heading_level'] ?? 'h3';

// get post data
$image_id         = get_post_thumbnail_id( $post_id );
$category_class   = new Category();
$primary_category = $category_class->get_primary_term( $post_id, Category::NAME );
$title            = get_the_title( $post_id );
$author_id        = (int) get_post_field( 'post_author', $post_id );
$author           = get_the_author_meta( 'display_name', $author_id );
$date             = get_the_date( 'M j, Y' );
$permalink        = get_the_permalink( $post_id );
?>
<article class="c-post-card">
	<div class="c-post-card__inner">
		<?php if ( $image_id ) : ?>
			<div class="c-post-card__image aspect-ratio-cover aspect-ratio-3-2">
				<?php echo wp_get_attachment_image( $image_id, 'large' ); ?>
			</div>
		<?php endif; ?>
		<div class="c-post-card__content">
			<?php if ( $primary_category ) : ?>
				<p class="c-post-card__primary-category t-category"><?php echo esc_html( $primary_category->name ); ?></p>
			<?php endif; ?>
			<?php echo sprintf(
				'<%1$s class="c-post-card__title t-display-x-small t-transparent-underline">%2$s</%1$s>',
				esc_html( $heading_level ),
				esc_html( $title )
			); ?>
			<p class="c-post-card__metadata is-color-text-secondary">
				<?php if ( $author ) : ?>
					<span class="c-post-card__metadata-author t-body-small"><?php esc_html_e( 'by', 'tribe' ); ?> <?php echo esc_html( $author ); ?></span>
				<?php endif; ?>
				<?php if ( $author && $date ) : ?>
					<span class="c-post-card__metadata-separator t-body-small">•</span>
				<?php endif; ?>
				<?php if ( $date ) : ?>
					<span class="c-post-card__metadata-date t-body-small"><?php echo esc_html( $date ); ?></span>
				<?php endif; ?>
			</p>
		</div>
	</div>
	<a href="<?php echo esc_url( $permalink ); ?>" class="c-post-card__link-overlay" aria-label="<?php echo esc_html__( 'Read more about ', 'tribe' ) . $title; ?>"></a>
</article>
