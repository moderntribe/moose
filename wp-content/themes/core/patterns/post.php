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
<!-- wp:group {"metadata":{"name":"Post Header"},"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|50"}}},"className":"alignwide","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--50)"><!-- wp:tribe/terms {"onlyPrimaryTerm":true,"align":"center","className":"aligncenter"} /-->

<!-- wp:post-title {"textAlign":"center","level":1,"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|30"}}}} /-->

<!-- wp:post-author-name {"textAlign":"center","isLink":true,"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} /-->

<!-- wp:post-date {"textAlign":"center","fontSize":"10"} /--></div>
<!-- /wp:group -->

<!-- wp:post-featured-image {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|40","right":"0","left":"0"}}},"className":"alignwide"} /-->

<!-- wp:paragraph -->
<p>Aenean ut aliquet quam, sit amet ullamcorper dolor. Aliquam nec ultricies sem. Nunc malesuada elementum libero non accumsan. Nam at venenatis odio. Aenean euismod rhoncus nulla id varius. Ut ultrices velit vel dui gravida ullamcorper. Proin lobortis leo et venenatis consectetur. Aliquam iaculis ipsum id tristique euismod. Donec tempor a purus ut tempus.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Integer quis lorem pulvinar nulla eleifend pretium sed quis elit. Nullam vehicula id eros vitae blandit. Fusce eu vestibulum erat. Pellentesque molestie in arcu a fermentum. Maecenas tempus vestibulum enim at fermentum. Mauris eget eleifend neque, ac varius lacus. Aliquam quis urna aliquam orci iaculis faucibus. Curabitur at justo vehicula, cursus justo quis, iaculis nisl. Sed facilisis aliquam velit vel tempus. Duis varius nibh sed quam pulvinar imperdiet. Cras turpis nulla, fermentum eget aliquet ac, rutrum id libero.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Vestibulum posuere, ipsum vel ullamcorper consectetur, ipsum sem commodo enim, ut condimentum velit neque in ipsum. Nam pulvinar tristique justo nec tempus. In metus ipsum, porta in posuere id, congue eu felis. Fusce eu mi tristique, sagittis purus et, dictum nibh. Nulla sit amet sapien et tortor sollicitudin iaculis quis eget mauris. Ut at risus felis. Morbi ac massa quis tellus iaculis interdum et vitae nisi. Integer scelerisque condimentum imperdiet. Mauris faucibus venenatis condimentum. In eleifend hendrerit rhoncus. Quisque varius hendrerit eros sed rhoncus.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Phasellus fermentum non nunc fringilla mollis. Quisque tincidunt, mauris in eleifend euismod, turpis augue sodales felis, quis lacinia ex leo non nisi. Proin luctus mauris rhoncus libero suscipit ornare. Nullam turpis magna, aliquam ut dictum at, commodo eu nulla. Cras aliquet volutpat tincidunt. Sed pharetra iaculis quam, id vestibulum nisi efficitur id. Maecenas ut risus a arcu volutpat placerat sed at ante. Nulla sed ultrices odio. Ut cursus imperdiet tortor, vitae sodales turpis placerat at. Proin fringilla ligula eget leo dignissim finibus. Vestibulum id quam varius, suscipit est sit amet, maximus erat.</p>
<!-- /wp:paragraph -->

<!-- wp:tribe/terms {"taxonomyToUse":"post_tag","hasLinks":true,"className":"is-style-pills","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} /-->

<!-- wp:group {"metadata":{"name":"Related Posts"},"align":"grid","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|50"}}},"className":"aligngrid","layout":{"type":"default"}} -->
<div class="wp-block-group aligngrid" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:heading {"style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|40"}}},"className":"is-style-small"} -->
<h2 class="wp-block-heading is-style-small" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--40)">Related Posts</h2>
<!-- /wp:heading -->

<!-- wp:query {"queryId":0,"query":{"perPage":"3","pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":null,"parents":[]}} -->
<div class="wp-block-query"><!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
	<!-- wp:tribe/post-card /-->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->
