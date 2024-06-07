<?php declare(strict_types=1);

$total_pages = $args['total_pages']; // @phpstan-ignore-line
?>
<div class="wp-block-tribe-custom-query-loop__pagination">
	<?php
	$current_page = max( 1, get_query_var( 'paged' ) );

	echo paginate_links( [
		'base'      => get_pagenum_link( 1 ) . '%_%',
		'format'    => '/page/%#%',
		'current'   => $current_page,
		'total'     => $total_pages,
		'prev_text' => '<span class="arrow" aria-hidden="true"></span> ' . __( 'Previous Page', 'tribe' ),
		'next_text' => __( 'Next Page', 'tribe' ) . ' <span class="arrow" aria-hidden="true"></span>',
	] );
	?>
</div>
