<?php declare(strict_types=1);

get_header();
?>
	<main role="main" class="main" id="main-content">
		<article role="article" about="/404" typeof="schema:WebPage">
			<span property="schema:name" content="404" class="hidden"></span>
			<div class="layout layout--one-column layout--padding-top-sm layout--padding-bottom-lg">
				<div class="layout__container">
					<div class="layout__region layout__region--content">
						<?php get_component_part( 'page_404/page_404' ); ?>
					</div>
				</div>
			</div>
		</article>
	</main>
<?php
get_footer();
