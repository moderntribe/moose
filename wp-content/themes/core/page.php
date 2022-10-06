<?php declare(strict_types=1);

get_header(); ?>
	<main id="main-content">

		<div class="s-sink t-sink l-sink">
			<?php the_title( '<h1>', '</h1>', true ); ?>
			<?php the_content(); ?>
		</div>

	</main>
<?php
get_footer();
