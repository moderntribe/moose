<?php declare(strict_types=1);

?>
<div class="global-search" data-js="global-search-wrapper">
	<button type="button" class="global-search__icon" data-js="toggle-search-overlay" title="<?php echo esc_attr__( 'Toggle Search Overlay', 'tribe' ); ?>">
		<svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
			<g clip-path="url(#clip0_7570_7586)">
				<path d="M24.4002 24.6666L17.0669 17.3333" stroke="currentcolor" stroke-miterlimit="10" stroke-linecap="square"/>
				<path d="M10.4002 19.9999C15.5549 19.9999 19.7336 15.8212 19.7336 10.6666C19.7336 5.51193 15.5549 1.33325 10.4002 1.33325C5.24557 1.33325 1.06689 5.51193 1.06689 10.6666C1.06689 15.8212 5.24557 19.9999 10.4002 19.9999Z" stroke="currentcolor" stroke-miterlimit="10" stroke-linecap="square"/>
			</g>
			<defs>
				<clipPath id="clip0_7570_7586">
					<rect width="24" height="24" fill="white" transform="translate(0.400146 0.666626)"/>
				</clipPath>
			</defs>
		</svg>
	</button>
	<div class="global-search__overlay" data-js="global-search-overlay" aria-hidden="true">
		<form class="global-search__overlay-form" action="<?php echo esc_url( home_url() ); ?>" method="GET">
			<input type="text" name="s" class="global-search__overlay-form-input" placeholder="<?php echo esc_html__( 'What are you looking for?', 'tribe' ); ?>">
			<button type="submit" class="global-search__overlay-form-submit" title="<?php echo esc_attr__( 'Search', 'tribe' ); ?>">
				<svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
					<g clip-path="url(#clip0_7570_7586)">
						<path d="M24.4002 24.6666L17.0669 17.3333" stroke="currentcolor" stroke-miterlimit="10" stroke-linecap="square"/>
						<path d="M10.4002 19.9999C15.5549 19.9999 19.7336 15.8212 19.7336 10.6666C19.7336 5.51193 15.5549 1.33325 10.4002 1.33325C5.24557 1.33325 1.06689 5.51193 1.06689 10.6666C1.06689 15.8212 5.24557 19.9999 10.4002 19.9999Z" stroke="currentcolor" stroke-miterlimit="10" stroke-linecap="square"/>
					</g>
					<defs>
						<clipPath id="clip0_7570_7586">
							<rect width="24" height="24" fill="white" transform="translate(0.400146 0.666626)"/>
						</clipPath>
					</defs>
				</svg>
			</button>
		</form>
	</div>
</div>
