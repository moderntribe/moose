<?php declare(strict_types=1);

get_header(); ?>
	<main>
		<?php the_title( '<h1>', '</h1>', true ); ?>
		<?php the_content(); ?>
	</main>
<?php
get_footer();
