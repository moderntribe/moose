<?php declare(strict_types=1);

use Tribe\Plugin\Post_Types\Post\Post;
use Tribe\Plugin\Taxonomies\Category\Category;

/**
 * @var array $attributes
 */

$post_id                 = get_the_ID();
$has_automatic_selection = $attributes['hasAutomaticSelection'] ?? true;
$chosen_posts            = $attributes['chosenPosts'] ?? [];
$posts_to_show           = $attributes['postsToShow'] ?? 3;
$block_layout            = $attributes['layout'] ?? 'horizontal';
$query_args              = [
	'post_type'      => Post::NAME,
	'post_status'    => 'publish',
	'posts_per_page' => (int) $posts_to_show,
	'post__not_in'   => [ $post_id ],
	'tax_query'      => [],
];

if ( $has_automatic_selection && empty( $chosen_posts ) ) {
	$post_terms = get_the_terms( $post_id, Category::NAME );

	if ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ) {
		$term_ids = wp_list_pluck( $post_terms, 'term_id' );

		$query_args['tax_query'][] = [
			'taxonomy' => Category::NAME,
			'field'    => 'term_id',
			'terms'    => $term_ids,
		];
	}
}

if ( ! empty( $chosen_posts ) && is_array( $chosen_posts ) ) {
	unset(
		$query_args['tax_query'],
		$query_args['post__not_in'],
		$query_args['posts_per_page']
	);

	/**
	 * because of the way FormTokenField's work, the $chosen_posts array contains a value and an ID
	 * so we need to map the array to get just the IDs when we pass it into post__in
	 *
	 * @see https://developer.wordpress.org/block-editor/reference-guides/components/form-token-field/
	 */
	$query_args['post__in'] = array_map( static fn( $post ): int => intval( $post['id'] ), $chosen_posts );
	$query_args['orderby']  = 'post__in';
}

$query = new WP_Query( $query_args );

if ( ! $query->have_posts() ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'b-related-posts b-related-posts--layout-' . $block_layout ] ); ?>>
	<?php while ( $query->have_posts() ) : ?>
		<?php $query->the_post(); ?>
		<?php get_template_part( 'components/cards/post', null, [
			'post_id' => get_the_ID(),
		] ); ?>
	<?php endwhile; ?>
</div>
<?php wp_reset_postdata(); ?>
