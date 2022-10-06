<?php declare(strict_types=1);

get_header();
?>
	<main id="main-content">
		<h1 class="h2 search-results__title"><?php _e( 'Search', 'tribe' ); ?></h1>
		<?php
		global $wp_query;
		$total      = absint( $wp_query->found_posts );
		$query_term = get_search_query();
		?>
		<div>
			<?php
			echo sprintf(
				_n( 'Showing %d result for &lsquo;%s&rsquo;', 'Showing %d results for &lsquo;%s&rsquo;', $total, 'tribe' ),
				$total,
				$query_term
			);
			?>
		</div>
		<?php if ( have_posts() ) { ?>
			<?php while ( have_posts() ) { ?>
				<?php the_post(); ?>
				<?php the_title( '<h1>', '</h1>', true ); ?>
				<?php the_excerpt(); ?>
			<?php } ?>
		<?php } else { ?>
			<?php echo __( 'No posts found.', 'tribe' ); ?>
		<?php } ?>
	</main>
<?php
get_footer();
