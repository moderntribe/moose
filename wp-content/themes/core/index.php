<?php declare(strict_types=1);

get_header(); ?>
	<main>
		<h1>Blog Index</h1>
		<?php if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				the_title( '<h2>', '</h2>', true );
				the_content();
			}
		} else {
			echo __( 'No posts found.', 'tribe' );
		}
		?>
	</main>
<?php
get_footer();
