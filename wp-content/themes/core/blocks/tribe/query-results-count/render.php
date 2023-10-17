<?php declare(strict_types=1);

global $wp_query;
$is_search = is_search();
$count     = (int) $wp_query->found_posts;
$output    = sprintf( _n( '%d result', '%d results', $count, 'tribe' ), number_format_i18n( $count ) );

if ( $is_search ) {
	$output = sprintf(
		_x(
			'%s %s for <span class="search-term">&ldquo;%s&rdquo;</span>',
			'First value is the number of results, second is word "result" (pluralized if necessary), third is the search term',
			'tribe'
		),
		number_format_i18n( $count ),
		_n( 'result', 'results', $count, 'tribe' ),
		get_search_query()
	);
}
?>

<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<p><?php echo wp_kses_post( $output ); ?></p>
</div>
