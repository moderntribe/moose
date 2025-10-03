<?php declare(strict_types=1);

use Tribe\Plugin\Blocks\Helpers\Icon_Picker;

/**
 * @var array $attributes
 */

$icon_picker = new Icon_Picker( $attributes );
$style       = $icon_picker->get_icon_wrapper_styles();
$svg         = $icon_picker->get_svg();

if ( ! empty( $svg ) ) : ?>
<div class="wp-block-tribe-icon-picker">
	<div class="icon-wrapper" style="<?php echo esc_attr( $style ); ?>">
		<?php echo $svg; ?>
	</div>
</div>
<?php endif; ?>
