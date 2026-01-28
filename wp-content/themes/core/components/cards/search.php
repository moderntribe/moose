<?php declare(strict_types=1);

use Tribe\Plugin\Components\Search_Card_Controller;

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

$c = Search_Card_Controller::factory( [
	'post_id'              => $post_id,
	'animation_attributes' => $animation_attributes,
] );
?>
<article class="c-search-card <?php echo esc_attr( $c->get_classes() ); ?>"<?php echo ( '' !== $c->get_styles() ) ? sprintf( 'style="%s"', $c->get_styles() ) : ''; ?>>
	<div class="c-search-card__inner">
		<?php if ( $c->has_media() ) : ?>
			<div class="c-search-card__image aspect-ratio-cover aspect-ratio-4-3">
				<?php echo $c->get_media(); ?>
			</div>
		<?php endif; ?>
		<?php if ( $c->has_post_type() ) : ?>
			<p class="c-search-card__post-type t-category"><?php echo esc_html( $c->get_post_type_name() ); ?></p>
		<?php endif; ?>
		<div class="c-search-card__title-wrap">
			<h2 class="c-search-card__title t-display-x-small t-animated-underline"><?php echo esc_html( $c->get_title() ); ?></h2>
		</div>
		<?php if ( $c->has_author() || $c->has_date() ) : ?>
			<p class="c-search-card__metadata is-color-text-secondary">
				<?php if ( $c->has_author() ) : ?>
					<span class="c-search-card__metadata-author t-body-small"><?php esc_html_e( 'by', 'tribe' ); ?> <?php echo esc_html( $c->get_author_name() ); ?></span>
				<?php endif; ?>
				<?php if ( $c->has_author() && $c->has_date() ) : ?>
					<span class="c-search-card__metadata-separator t-body-small">•</span>
				<?php endif; ?>
				<?php if ( $c->has_date() ) : ?>
					<span class="c-search-card__metadata-date t-body-small"><?php echo esc_html( $c->get_date() ); ?></span>
				<?php endif; ?>
			</p>
		<?php endif; ?>
		<?php if ( $c->has_date() ) : ?>
			<p class="c-search-card__excerpt t-body-small"><?php echo esc_html( $c->get_excerpt() ); ?></p>
		<?php endif; ?>
		<p class="c-search-card__visible-permalink t-body-small"><?php echo esc_html( $c->get_permalink() ); ?></p>
	</div>
	<a href="<?php echo esc_url( $c->get_permalink() ); ?>" class="c-search-card__link-overlay" aria-label="<?php echo sprintf( '%s %s', esc_html__( 'Read more about', 'tribe' ), $c->get_title() ); ?>"></a>
</article>
