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
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"0","bottom":"var:preset|spacing|40","left":"0"}}},"className":"p-card-search-result l-clearfix","layout":{"type":"default"}} -->
<div class="wp-block-group p-card-search-result l-clearfix" style="padding-top:var(--wp--preset--spacing--40);padding-right:0;padding-bottom:var(--wp--preset--spacing--40);padding-left:0"><!-- wp:post-featured-image {"width":"22%","align":"right","className":"p-card-search-result__image s-aspect-ratio-cover s-aspect-ratio-4-3"} /-->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"tribe/post-type-name"}}},"className":"t-category","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|10"}}}} -->
<p class="t-category" style="margin-bottom:var(--wp--preset--spacing--10)">Post Type Name Placeholder</p>
<!-- /wp:paragraph -->

<!-- wp:post-title {"style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|10"}}},"className":"p-card-search-result__title","fontSize":"60"} /-->

<!-- wp:post-excerpt /-->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"tribe/post-permalink"}}},"className":"t-caption","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<p class="t-caption" style="margin-top: var(--wp--preset--spacing--20)">Post Permalink Placeholder</p>
<!-- /wp:paragraph -->

<!-- wp:read-more {"className":"a-link-cover"} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query -->
