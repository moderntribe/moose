<?php declare(strict_types=1);

/**
 * Title: Styleguide
 * Slug: patterns/styleguide
 * Categories: templates
 * Description: The styleguide pattern combines elements of the styleguide for quick testing
 * Keywords: styleguide, text, buttons, quote, image, table
 */
?>
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Building a braver, brighter, better connected world.</h1>
<!-- /wp:heading -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Building a braver, brighter, better connected world.</h2>
<!-- /wp:heading -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Building a braver, brighter, better connected world.</h3>
<!-- /wp:heading -->

<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">Building a braver, brighter, better connected world.</h4>
<!-- /wp:heading -->

<!-- wp:heading {"level":5} -->
<h5 class="wp-block-heading">Building a braver, brighter, better connected world.</h5>
<!-- /wp:heading -->

<!-- wp:heading {"level":6} -->
<h6 class="wp-block-heading">Building a braver, brighter, better connected world.</h6>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}},"className":"is-style-large"} -->
<p class="is-style-large" style="margin-bottom:var(--wp--preset--spacing--30)">Whatever we’re working on, we push what’s possible to accomplish ambitious goals, to serve your communities, and to build a braver, brighter, better connected world. Here’s how we make it happen.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
<p style="margin-bottom:var(--wp--preset--spacing--30)">Whatever we’re working on, we push what’s possible to accomplish ambitious goals, to serve your communities, and to build a braver, brighter, better connected world. Here’s how we make it happen.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}},"className":"is-style-small"} -->
<p class="is-style-small" style="margin-bottom:var(--wp--preset--spacing--30)">Whatever we’re working on, we push what’s possible to accomplish ambitious goals, to serve your communities, and to build a braver, brighter, better connected world. Here’s how we make it happen.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-primary"} -->
<div class="wp-block-button is-style-primary"><a class="wp-block-button__link wp-element-button" href="https://www.google.com" target="_blank" rel="noreferrer noopener">Primary Button</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-secondary"} -->
<div class="wp-block-button is-style-secondary"><a class="wp-block-button__link wp-element-button" href="https://www.google.com" target="_blank" rel="noreferrer noopener">Secondary Button</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-ghost"} -->
<div class="wp-block-button is-style-ghost"><a class="wp-block-button__link wp-element-button" href="https://www.google.com" target="_blank" rel="noreferrer noopener">Tertiary Button</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30","right":"0","bottom":"var:preset|spacing|30","left":"0"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--30);margin-right:0;margin-bottom:var(--wp--preset--spacing--30);margin-left:0">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt <a rel="noreferrer noopener" href="https://www.google.com" target="_blank">this is an inline link</a> ut labore et dolore magna aliqua. Urna nunc id cursus metus aliquam eleifend mi in nulla. Eu volutpat odio facilisis mauris sit amet massa vitae tortor. Dui accumsan <a rel="noreferrer noopener" href="https://www.google.com" target="_blank">this is an inline link on hover</a> tempus iaculis. Tellus cras adipiscing enim eu turpis egestas pretium. <em>This is some italic text</em> eu consequat ac felis donec et. Viverra vitae congue eu consequat ac felis donec et. Praesent tristique magna sit amet purus. <strong>This is some bold text</strong> purus gravida quis blandit turpis cursus.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"ordered":true} -->
<ol><!-- wp:list-item -->
<li>Whatever we’re working on, we push what’s possible to accomplish ambitious goals, to serve your communities, and to build a braver, brighter, better connected world. Here’s how we make it happen.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>We foster a culture of open communication and no surprises. Despite being remote since day one, everything from our communication tools to our dev infrastructure is designed for close collaboration.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>We believe your tech, tools, and teams work better together. Every “thing” we build is designed to link people and platforms. We connect the dots … and occasionally color outside the lines.</li>
<!-- /wp:list-item --></ol>
<!-- /wp:list -->

<!-- wp:separator -->
<hr class="wp-block-separator has-alpha-channel-opacity"/>
<!-- /wp:separator -->

<!-- wp:list -->
<ul><!-- wp:list-item -->
<li>Whatever we’re working on, we push what’s possible to accomplish ambitious goals, to serve your communities, and to build a braver, brighter, better connected world. Here’s how we make it happen.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>We foster a culture of open communication and no surprises. Despite being remote since day one, everything from our communication tools to our dev infrastructure is designed for close collaboration.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>We believe your tech, tools, and teams work better together. Every “thing” we build is designed to link people and platforms. We connect the dots … and occasionally color outside the lines.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:spacer {"className":"is-style-medium"} -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer is-style-medium"></div>
<!-- /wp:spacer -->

<!-- wp:pullquote {"textAlign":"left"} -->
<figure class="wp-block-pullquote has-text-align-left"><blockquote><p>“We design and engineer each touchpoint across your ecosystem, from websites and applications to plugins and publishing platforms.”</p><cite>Citation</cite></blockquote></figure>
<!-- /wp:pullquote -->

<!-- wp:spacer {"className":"is-style-medium"} -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer is-style-medium"></div>
<!-- /wp:spacer -->

<!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>“We design and engineer each touchpoint across your ecosystem, from websites and applications to plugins and publishing platforms.”</p>
<!-- /wp:paragraph --><cite>Citation</cite></blockquote>
<!-- /wp:quote -->

<!-- wp:spacer {"className":"is-style-medium"} -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer is-style-medium"></div>
<!-- /wp:spacer -->

<!-- wp:image {"sizeSlug":"large"} -->
<figure class="wp-block-image size-large"><img src="https://picsum.photos/640/380" alt=""/><figcaption class="wp-element-caption">Caption lorem ipsum fusce fringilla luctus suscipit. Vivamus elit enim, dapibus sed mollis nec, cursus nec ante. Aliquam erat volutpat.</figcaption></figure>
<!-- /wp:image -->

<!-- wp:spacer {"className":"is-style-medium"} -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer is-style-medium"></div>
<!-- /wp:spacer -->

<!-- wp:table -->
<figure class="wp-block-table"><table><thead><tr><th>Header label</th><th>Header label</th><th>Header label</th></tr></thead><tbody><tr><td>Cell label</td><td>Cell label</td><td>Cell label</td></tr><tr><td>Cell label</td><td>Cell label</td><td>Cell label</td></tr><tr><td>Cell label</td><td>Cell label</td><td>Cell label</td></tr></tbody><tfoot><tr><td>Footer label</td><td>Footer label</td><td>Footer label</td></tr></tfoot></table></figure>
<!-- /wp:table --></div>
<!-- /wp:group -->
