<?php declare(strict_types=1);

$search_icon = file_get_contents( get_stylesheet_directory_uri() . '/assets/media/icons/search.svg' );

?>
<div class="masthead-search" data-js="masthead-search-wrapper">
	<button type="button" class="masthead-search__icon" data-js="toggle-search-overlay" title="<?php echo esc_attr__( 'Toggle Search Overlay', 'tribe' ); ?>">
		<?php echo $search_icon; ?>
	</button>
	<div class="masthead-search__overlay" data-js="masthead-search-overlay" aria-hidden="true">
		<form class="masthead-search__overlay-form" action="<?php echo esc_url( home_url() ); ?>" method="GET">
			<input type="text" name="s" class="t-body masthead-search__overlay-form-input" placeholder="<?php echo esc_html__( 'What are you looking for?', 'tribe' ); ?>">
			<button type="submit" class="masthead-search__overlay-form-submit" title="<?php echo esc_attr__( 'Search', 'tribe' ); ?>">
				<?php echo $search_icon; ?>
			</button>
		</form>
	</div>
</div>
