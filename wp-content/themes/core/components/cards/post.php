<?php declare(strict_types=1);

use Tribe\Plugin\Components\Post_Card_Controller;

/**
 * @var object $args
 */

$post_id = $args['post_id'];

// return if no post id
if ( ! $post_id ) {
	return;
}

// get template part args
$animation_attributes = $args['animation_attributes'] ?? false;
$heading_level        = $args['heading_level'] ?? 'h3';
$layout               = $args['layout'] ?? 'vertical';

$c = new Post_Card_Controller( [
	'post_id'              => $post_id,
	'animation_attributes' => $animation_attributes,
	'layout'               => $layout,
] );
?>
<article class="<?php echo esc_attr( $c->get_classes() ); ?>"<?php echo ( '' !== $c->get_styles() ) ? sprintf( 'style="%s"', $c->get_styles() ) : ''; ?>>
	<div class="c-post-card__inner">
		<?php if ( $c->has_media() ) : ?>
			<div class="c-post-card__image aspect-ratio-cover aspect-ratio-3-2">
				<?php echo $c->get_media(); ?>
			</div>
		<?php endif; ?>
		<div class="c-post-card__content">
			<?php if ( $c->has_primary_category() ) : ?>
				<p class="c-post-card__primary-category t-category"><?php echo esc_html( $c->get_primary_category_name() ); ?></p>
			<?php endif; ?>
			<div class="c-post-card__title-wrap">
				<?php echo sprintf(
					'<%1$s class="c-post-card__title t-display-x-small t-animated-underline">%2$s</%1$s>',
					esc_html( $heading_level ),
					esc_html( $c->get_title() )
				); ?>
			</div>
			<?php if ( $c->has_author() || $c->has_date() ) : ?>
				<p class="c-post-card__metadata is-color-text-secondary">
					<?php if ( $c->has_author() ) : ?>
						<span class="c-post-card__metadata-author t-body-small"><?php esc_html_e( 'by', 'tribe' ); ?> <?php echo esc_html( $c->get_author_name() ); ?></span>
					<?php endif; ?>
					<?php if ( $c->has_author() && $c->has_date() ) : ?>
						<span class="c-post-card__metadata-separator t-body-small">•</span>
					<?php endif; ?>
					<?php if ( $c->has_date() ) : ?>
						<span class="c-post-card__metadata-date t-body-small"><?php echo esc_html( $c->get_date() ); ?></span>
					<?php endif; ?>
				</p>
			<?php endif; ?>
			<?php if ( $c->has_excerpt() ) : ?>
				<p class="c-post-card__excerpt t-body-small"><?php echo esc_html( $c->get_excerpt() ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<a href="<?php echo esc_url( $c->get_permalink() ); ?>" class="c-post-card__link-overlay" aria-label="<?php echo sprintf( '%s %s', esc_html__( 'Read more about', 'tribe' ), $c->get_title() ); ?>"></a>
</article>
