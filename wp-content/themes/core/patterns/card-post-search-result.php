<?php declare(strict_types=1);

/**
 * Title: Search Result Post Card
 * Slug: patterns/search-result-post-card
 * Categories: cards
 * Description: Post card with featured image, post type, post title, excerpt, and URL
 * Keywords: search, post, card
 * Inserter: yes
 * Block Types: core/query
 */
?>
<!-- wp:query {"queryId":0,"query":{"perPage":"16","pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":null,"parents":[]},"displayLayout":{"type":"list","columns":3},"align":"wide"} -->
<div class="wp-block-query alignwide"><!-- wp:post-template -->
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"0","bottom":"var:preset|spacing|40","left":"0"}}},"className":"p-card-search-result l-clear-both","layout":{"type":"default"}} -->
<div class="wp-block-group p-card-search-result l-clear-both" style="padding-top:var(--wp--preset--spacing--40);padding-right:0;padding-bottom:var(--wp--preset--spacing--40);padding-left:0"><!-- wp:post-featured-image {"width":"22%","align":"right","className":"s-aspect-ratio-cover s-aspect-ratio-4-3"} /-->

<!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|10"}}}} /-->

<!-- wp:post-excerpt /--></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"align":"left"} -->
<p class="has-text-align-left">No Results</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query -->
