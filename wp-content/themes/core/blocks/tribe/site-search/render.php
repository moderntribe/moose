<?php declare(strict_types=1);

$search_icon = '<svg width="32" height="33" viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
<g clip-path="url(#clip0_8242_1093)">
<path fill-rule="evenodd" clip-rule="evenodd" d="M7.33268 14.5C7.33268 10.8181 10.3175 7.83329 13.9993 7.83329C17.6812 7.83329 20.666 10.8181 20.666 14.5C20.666 16.2963 19.9555 17.9267 18.8002 19.1255L18.6249 19.3008C17.4261 20.4561 15.7957 21.1666 13.9993 21.1666C10.3175 21.1666 7.33268 18.1819 7.33268 14.5ZM19.5888 21.9752C18.0307 23.1421 16.0958 23.8333 13.9993 23.8333C8.84469 23.8333 4.66602 19.6546 4.66602 14.5C4.66602 9.3453 8.84469 5.16663 13.9993 5.16663C19.154 5.16663 23.3327 9.3453 23.3327 14.5C23.3327 16.5964 22.6415 18.5315 21.4745 20.0896L27.8849 26.5001L25.9993 28.3857L19.5888 21.9752Z" fill="currentColor"/>
</g>
<defs>
<clipPath id="clip0_8242_1093">
<rect width="24" height="24" fill="white" transform="translate(4 4.5)"/>
</clipPath>
</defs>
</svg>
';

?>
<div class="global-search" data-js="global-search-wrapper">
	<button type="button" class="global-search__icon" data-js="toggle-search-overlay" title="<?php echo esc_attr__( 'Toggle Search Overlay', 'tribe' ); ?>">
		<?php echo $search_icon; ?>
	</button>
	<div class="global-search__overlay" data-js="global-search-overlay" aria-hidden="true">
		<form class="global-search__overlay-form" action="<?php echo esc_url( home_url() ); ?>" method="GET">
			<input type="text" name="s" class="global-search__overlay-form-input" placeholder="<?php echo esc_html__( 'What are you looking for?', 'tribe' ); ?>">
			<button type="submit" class="global-search__overlay-form-submit" title="<?php echo esc_attr__( 'Search', 'tribe' ); ?>">
				<?php echo $search_icon; ?>
			</button>
		</form>
	</div>
</div>
