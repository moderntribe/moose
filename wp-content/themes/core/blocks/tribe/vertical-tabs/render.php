<?php declare(strict_types=1);

use Tribe\Plugin\Blocks\Helpers\Block_Animation_Attributes;

/**
 * @var array $attributes
 * @var string $content
 */

$animation_attributes = new Block_Animation_Attributes( $attributes );
$animation_classes    = $animation_attributes->get_classes();
$animation_styles     = $animation_attributes->get_styles();
$classes              = 'b-vertical-tabs';
$tabs                 = $attributes['tabs'] ?? [];

if ( $animation_classes !== '' ) {
	$classes .= ' ' . $animation_classes;
}
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => $classes, 'style' => $animation_styles ] ); ?> data-js="tabs-block">
	<div class="b-vertical-tabs__tab-container" role="tablist">
		<?php foreach ( $tabs as $index => $tab ) : ?>
			<?php
			if ( ! $tab['title'] ) {
				continue;
			}

			$tab_id          = $tab['id'];
			$tab_button_id   = $tab['buttonId'];
			$tab_title       = $tab['title'];
			$tab_description = $tab['content'] ?? '';
			?>
			<div
				id="<?php echo esc_attr( $tab_button_id ); ?>"
				class="b-vertical-tabs__tab"
				aria-controls="<?php echo esc_attr( $tab_id ); ?>"
				role="tab"
				aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
				tabindex="<?php echo $index === 0 ? '0' : '-1'; ?>"
			>
				<span class="b-vertical-tabs__tab-title t-display-xx-small s-remove-margin--top t-animated-underline"><?php echo esc_html( $tab_title ); ?></span>
				<div class="b-vertical-tabs__tab-hidden">
					<?php if ( $tab_description ) : ?>
						<span class="b-vertical-tabs__tab-description t-body"><?php echo wp_kses_post( $tab_description ); ?></span>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="b-vertical-tabs__tab-content">
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</div>
