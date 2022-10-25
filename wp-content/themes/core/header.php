<?php declare(strict_types=1);

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<?php // TITLE: Handled by WP ?>

	<?php // MISC Meta ?>
	<meta charset="utf-8">
	<meta name="author" content="<?php bloginfo( 'name' ); ?>">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<?php // MOBILE META ?>
	<meta name="HandheldFriendly" content="True">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<?php // PLATFORM META: iOS & Android ?>
	<meta name="apple-mobile-web-app-title" content="<?php echo esc_attr( get_the_title() ); ?>">

	<?php // PLATFORM META: IE ?>
	<meta name="application-name" content="<?php bloginfo( 'name' ); ?>">

	<?php do_action( 'wp_head' ); ?>

</head>

<body <?php body_class(); ?>>

	<?php do_action( 'wp_body_open' ) ?>

	<div class="l-wrapper" data-js="l-wrapper">

		<header>
			<p>Site Header</p>
		</header>
