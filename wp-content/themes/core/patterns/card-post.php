<?php declare(strict_types=1);

/**
 * Title: Post Card
 * Slug: patterns/card-post
 * Categories: cards
 * Description: Post card with featured image, primary category, and post title
 * Keywords: post, card
 * Inserter: yes
 * Block Types: core/query
 */
?>
<!-- wp:query {"queryId":0,"query":{"perPage":"18","pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":null,"parents":[]},"displayLayout":{"type":"flex","columns":3},"align":"wide"} -->
<div class="wp-block-query alignwide"><!-- wp:post-template -->
<!-- wp:group {"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}}},"className":"p-card-post l-clearfix","layout":{"type":"default"}} -->
<div class="wp-block-group p-card-post l-clearfix" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:post-featured-image {"width":"100%","className":"s-aspect-ratio-cover s-aspect-ratio-4-3"} /-->

<!-- wp:post-terms {"term":"category","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|10"}}}} /-->

<!-- wp:post-title {"style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|10"}}},"fontSize":"40"} /-->

<!-- wp:read-more {"className":"a-link-cover"} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"align":"left"} -->
<p class="has-text-align-left">No Results</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query -->
