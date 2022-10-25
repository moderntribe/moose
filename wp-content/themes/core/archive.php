<?php declare(strict_types=1);

get_header();
?>
	<main>
	<?php if ( have_posts() ) { ?>
		<h1><?php echo wp_count_posts()->publish . ' ' . __( 'posts in', 'tribe' ) . ' "' . get_the_archive_title() . '"';  ?></h1>
		<?php
		while ( have_posts() ) {
			the_post();
			the_title( '<h2>', '</h2>', true );
			the_excerpt();
		}
	} else {
		echo __( 'No posts found.', 'tribe' );
	}
	?>
	</main>
<?php
get_footer();
