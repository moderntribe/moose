<?php declare(strict_types=1);

global $wp_query;
$output     = '';
$is_search  = is_search();
$post_count = (int) $wp_query->post_count;

if ( $is_search ) {
	$output = $post_count !== 1
				? $post_count . ' results for <span class="search-term">"'. get_search_query() .'"</span>'
				: '1 result for <span class="search-term">"'. get_search_query() .'"</span>';
} else {
	$output = "$wp_query->post_count results";
}
?>

<div <?php echo get_block_wrapper_attributes(); ?>>
	<p><?php echo wp_kses_post( $output ); ?></p>
</div>
