<?php declare(strict_types=1);

/**
 * Title: Post
 * Slug: patterns/post
 * Categories: templates
 * Description: A simple pattern for a single post page - added automatically to new posts
 * Keywords: post, placeholder
 * Block Types: core/post-content
 * Post Types: post, wp_template
 */
?>
<!-- wp:group {"metadata":{"name":"Post Header"},"align":"wide","className":"alignwide","style":{"spacing":{"padding":{"top":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--50)"><!-- wp:tribe/terms {"onlyPrimaryTerm":true,"hasLinks":true,"align":"center","className":"aligncenter"} /-->

<!-- wp:post-title {"textAlign":"center","level":1,"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|30"}}}} /-->

<!-- wp:paragraph {"align":"center","className":"is-style-large"} -->
<p class="has-text-align-center is-style-large">Post excerpt lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10","padding":{"top":"0","bottom":"0"},"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30);padding-top:0;padding-bottom:0"><!-- wp:post-author-name {"textAlign":"center","isLink":true,"style":{"spacing":{"margin":{"bottom":"0","top":"0","left":"0","right":"0"}},"elements":{"link":{"color":{"text":"var:preset|color|blue"}}}},"textColor":"blue"} /-->

<!-- wp:paragraph -->
<p>•</p>
<!-- /wp:paragraph -->

<!-- wp:post-date {"textAlign":"center","style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"0","bottom":"0","left":"0","right":"0"}}},"fontSize":"10"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:post-featured-image {"align":"wide","className":"alignwide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|70","right":"0","left":"0"}}}} /-->

<!-- wp:heading {"className":"is-style-medium"} -->
<h2 class="wp-block-heading is-style-medium">Heading level two</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"is-style-small"} -->
<h3 class="wp-block-heading is-style-small">Heading Level Three</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":6,"className":"is-style-xx-small"} -->
<h6 class="wp-block-heading is-style-xx-small">Heading Level Six</h6>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"ordered":true} -->
<ol class="wp-block-list"><!-- wp:list-item -->
<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco.</li>
<!-- /wp:list-item --></ol>
<!-- /wp:list -->

<!-- wp:pullquote {"align":"wide","className":"alignwide"} -->
<figure class="wp-block-pullquote alignwide"><blockquote><p>“Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Enim ad minim veniam”</p><cite>Citation</cite></blockquote></figure>
<!-- /wp:pullquote -->

<!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:group {"metadata":{"name":"Post Footer"},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:separator -->
<hr class="wp-block-separator has-alpha-channel-opacity"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"className":"is-style-small"} -->
<p class="is-style-small">Filed under</p>
<!-- /wp:paragraph -->

<!-- wp:tribe/terms {"taxonomyToUse":"post_tag","hasLinks":true,"className":"is-style-pills","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"},"padding":{"top":"0"}}}} /-->

<!-- wp:group {"metadata":{"name":"Post Author (Footer)","categories":["post-parts"],"patternName":"patterns/post-footer-author"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"},"padding":{"right":"0","top":"var:preset|spacing|50","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--40);padding-top:var(--wp--preset--spacing--50);padding-right:0;padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:paragraph {"className":"is-style-small"} -->
<p class="is-style-small">About the author</p>
<!-- /wp:paragraph -->

<!-- wp:post-author {"avatarSize":96,"showBio":true,"isLink":true,"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} /--></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"Share Post"},"style":{"spacing":{"blockGap":"var:preset|spacing|15"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"is-style-small"} -->
<p class="is-style-small">Share this page</p>
<!-- /wp:paragraph -->

<!-- wp:outermost/social-sharing {"iconColor":"base-black","iconColorValue":"#000000","size":"has-small-icon-size","className":"is-style-logos-only","style":{"spacing":{"margin":{"top":"var:preset|spacing|10"},"blockGap":{"left":"var:preset|spacing|20"}}},"layout":{"type":"flex","justifyContent":"left"}} -->
<ul class="wp-block-outermost-social-sharing has-small-icon-size has-icon-color is-style-logos-only" style="margin-top:var(--wp--preset--spacing--10)"><!-- wp:outermost/social-sharing-link {"service":"linkedin","label":"Share on LinkedIn"} /-->

<!-- wp:outermost/social-sharing-link {"service":"x","label":"Share on X"} /-->

<!-- wp:outermost/social-sharing-link {"service":"facebook"} /-->

<!-- wp:outermost/social-sharing-link {"service":"telegram"} /-->

<!-- wp:outermost/social-sharing-link {"service":"whatsapp"} /--></ul>
<!-- /wp:outermost/social-sharing --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"Related Posts"},"className":"aligngrid","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|50"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group aligngrid" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:heading {"className":"is-style-small","style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|40"}}}} -->
<h2 class="wp-block-heading is-style-small" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--40)">Related Posts</h2>
<!-- /wp:heading -->

<!-- wp:query {"queryId":0,"query":{"perPage":"3","pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":null,"parents":[]}} -->
<div class="wp-block-query"><!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:tribe/post-card /-->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->
