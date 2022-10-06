<?php declare(strict_types=1);

get_header();
?>
	<main id="main-content">
	<?php if ( have_posts() ) { ?>
		<div class="l-container">
			<p><?php echo wp_count_posts()->publish . ' ' . __( 'posts in', 'tribe' ) . ' "' . get_the_archive_title() . '"';  ?></p>
		</div>
		<?php
		while ( have_posts() ) {
			the_post();
			the_title( '<h1>', '</h1>', true );
			the_excerpt();
		}
	} else {
		echo __( 'No posts found.', 'tribe' );
	}
	?>
	</main>
<?php
get_footer();
