<?php declare(strict_types=1);

/**
 * Title: Post Card
 * Slug: patterns/card-post
 * Categories: cards
 * Description: Post card with featured image, primary category, and post title
 * Keywords: post, card
 * Block Types: core/query
 */
?>
<!-- wp:query {"queryId":0,"query":{"perPage":"18","pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":null,"parents":[]},"displayLayout":{"type":"flex","columns":3},"align":"wide"} -->
<div class="wp-block-query alignwide"><!-- wp:post-template -->
	<!-- wp:tribe/post-card {"headingLevel":"h2"} /-->
<!-- /wp:post-template --></div>
<!-- /wp:query -->
