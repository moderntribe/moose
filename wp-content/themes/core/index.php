<?php declare(strict_types=1);

get_header(); ?>

		<main id="main-content">
		<?php if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				the_title( '<h1>', '</h1>', true );
				the_content();
			}
		} else {
			echo __( 'No posts found.', 'tribe' );
		}
		?>
	</main>
<?php
get_footer();
