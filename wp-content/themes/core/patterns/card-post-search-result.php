<?php declare(strict_types=1);

/**
 * Title: Search Result Post Card
 * Slug: patterns/search-result-post-card
 * Categories: cards
 * Description: Post card with featured image, post type, post title, excerpt, and URL
 * Keywords: search, post, card
 * Block Types: core/query
 */
?>
<!-- wp:query {"queryId":0,"query":{"perPage":"16","pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":null,"parents":[]},"displayLayout":{"type":"list","columns":3},"align":"wide"} -->
<div class="wp-block-query alignwide"><!-- wp:post-template -->
	<!-- wp:tribe/search-card /-->
<!-- /wp:post-template --></div>
<!-- /wp:query -->
