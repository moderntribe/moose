<?php declare(strict_types=1);

?>
<div class="c-menu-toggle" data-wp-interactive="menuToggle" data-wp-context="<?php echo json_encode( '{ "isOpen": false }' ); ?>">
	<button type="button" class="c-menu-toggle__toggle" data-js="menu-toggle" aria-label="<?php esc_html_e( 'Toggle Mobile Menu', 'tribe' ); ?>" data-wp-on--click="actions.toggleMenu" data-wp-bind--aria-expanded="context.isOpen">
		<span class="c-menu-toggle__inner" aria-hidden="true">
			<span class="c-menu-toggle__bar"></span>
			<span class="c-menu-toggle__bar"></span>
			<span class="c-menu-toggle__bar"></span>
		</span>
	</button>
</div>
