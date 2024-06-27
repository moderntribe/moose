<?php declare(strict_types=1);

$columns              = $attributes['columns']; // @phpstan-ignore-line
$component            = $attributes['component']; // @phpstan-ignore-line
$post_types           = $attributes['postTypes']; // @phpstan-ignore-line
$query_post_types     = [];
$posts_per_page       = $attributes['postsPerPage']; // @phpstan-ignore-line
$paged                = max( 1, get_query_var( 'paged' ) );
$post_in              = $attributes['postIn']; // @phpstan-ignore-line
$offset               = $attributes['offset']; // @phpstan-ignore-line
$order                = $attributes['order']; // @phpstan-ignore-line
$orderby              = $attributes['orderby']; // @phpstan-ignore-line
$has_pagination       = $attributes['hasPagination']; // @phpstan-ignore-line
$has_tax_query        = $attributes['hasTaxQuery']; // @phpstan-ignore-line
$tax_query_taxonomies = $attributes['taxQueryTaxonomies']; // @phpstan-ignore-line
$tax_query_fields     = $attributes['taxQueryFields']; // @phpstan-ignore-line
$tax_query_terms      = $attributes['taxQueryTerms']; // @phpstan-ignore-line
$tax_query_operators  = $attributes['taxQueryOperators']; // @phpstan-ignore-line
$has_meta_query       = $attributes['hasMetaQuery']; // @phpstan-ignore-line
$meta_query_keys      = $attributes['metaQueryKeys']; // @phpstan-ignore-line
$meta_query_values    = $attributes['metaQueryValues']; // @phpstan-ignore-line
$meta_query_compares  = $attributes['metaQueryCompares']; // @phpstan-ignore-line
$meta_query_types     = $attributes['metaQueryTypes']; // @phpstan-ignore-line

for ( $i = 0; $i < count( $post_types ); $i++ ) {
	$query_post_types[] = $post_types[ $i ]['slug'];
}

$args = [
	'post_type'      => $query_post_types,
	'post_status'    => 'publish',
	'posts_per_page' => $posts_per_page,
	'order'          => $order,
	'orderby'        => $orderby,
];

if ( $paged > 1 ) {
	$args['paged'] = $paged;
}

if ( $offset > 0 ) {
	$args['offset'] = $offset;
}

if ( count( $post_in ) > 0 ) {
	$args['post__in'] = array_map( static fn($post) => $post['id'], $post_in );
}

if ( $has_tax_query ) {
	$args['tax_query'] = [];

	foreach ( $tax_query_taxonomies as $index => $taxonomy ) {
		$field    = $tax_query_fields[ $index ];
		$terms    = array_map( static fn($term) => $term->{ $field }, json_decode( $tax_query_terms[ $index ] ) );
		$operator = $tax_query_operators[ $index ];

		$args['tax_query'][] = [
			'taxonomy' => $taxonomy,
			'field'    => $field,
			'terms'    => $terms,
			'operator' => $operator,
		];
	}
}

if ( $has_meta_query ) {
	$args['meta_query'] = [];

	foreach ( $meta_query_keys as $index => $key ) {
		$value   = $meta_query_values[ $index ];
		$compare = $meta_query_compares[ $index ];
		$type    = $meta_query_types[ $index ];

		$args['meta_query'][] = [
			'key'     => $key,
			'value'   => $value,
			'compare' => $compare,
			'type'    => $type,
		];
	}
}

$query = new WP_Query( $args );
?>
<?php if ( $query->have_posts() ) : ?>
	<h2 class="aligngrid" style="margin-bottom: var(--wp--preset--spacing--30)">This is a heading 2</h2>
	<ul <?php echo get_block_wrapper_attributes( [ 'class' => "columns-$columns" ] ); ?>>
		<?php while ( $query->have_posts() ) : ?>
				<?php $query->the_post(); ?>
				<li class="<?php echo esc_attr( implode( ' ', get_post_class( 'wp-block-tribe-custom-query-loop__post', get_the_ID() ) ) ); ?>">
					<?php echo get_template_part( "components/cards/$component", null, [ 'id' => get_the_ID() ] ); ?>
				</li>
		<?php endwhile; ?>
	</ul>
	<?php if ( $has_pagination && $query->max_num_pages > 1 ) : ?>
		<?php echo get_template_part( 'components/pagination', null, [ 'total_pages' => $query->max_num_pages ] ); ?>
	<?php endif; ?>
<?php endif; ?>
<?php wp_reset_postdata(); ?>
