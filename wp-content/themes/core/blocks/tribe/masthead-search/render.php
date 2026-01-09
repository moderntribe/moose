<?php declare(strict_types=1);

$search_icon_uri  = trailingslashit( get_stylesheet_directory_uri() ) . '/assets/media/icons/search.svg';
$search_icon_path = trailingslashit( get_stylesheet_directory() ) . '/assets/media/icons/search.svg';
$search_icon      = '';

// Attempt to get the file contents from the file system
if ( file_exists( $search_icon_path ) ) {
	$search_icon = file_get_contents( $search_icon_path );

	// Fallback to wp_remote_get if file_get_contents fails
	if ( $search_icon === false ) {
		$response = wp_remote_get( $search_icon_uri );

		if ( ! is_wp_error( $response ) ) {
			// wp_remote_retrieve_body returns an empty string on failure, so it's fine to end here
			$search_icon = wp_remote_retrieve_body( $response );
		}
	}
}
?>
<div class="masthead-search" data-js="masthead-search-wrapper">
	<button type="button" class="masthead-search__icon" data-js="toggle-search-overlay" title="<?php echo esc_attr__( 'Toggle Search Overlay', 'tribe' ); ?>">
		<?php echo $search_icon; ?>
	</button>
	<div class="masthead-search__overlay" data-js="masthead-search-overlay" aria-hidden="true">
		<form class="masthead-search__overlay-form" action="<?php echo esc_url( home_url() ); ?>" method="GET">
			<label for="masthead-search__input" class="screen-reader-text"><?php echo esc_attr__( 'Search', 'tribe' ); ?></label>
			<input id="masthead-search__input" type="text" name="s" class="t-body masthead-search__overlay-form-input" placeholder="<?php echo esc_html__( 'What are you looking for?', 'tribe' ); ?>">
			<button type="submit" class="masthead-search__overlay-form-submit" title="<?php echo esc_attr__( 'Search', 'tribe' ); ?>">
				<?php echo $search_icon; ?>
			</button>
		</form>
	</div>
</div>
