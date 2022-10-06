<?php declare(strict_types=1);

/*
 * This is a sample local-config.php file
 *
 * You may include settings here that you only want
 * enabled on your local development checkouts
 *
 * The default WordPress databases defines are automatically populated in
 * dev/docker/docker-compose.yml which you can override here with custom
 * defines e.g. define( 'DB_NAME', 'tribe_square1' );
*/

/**
 * Set the current environment type. Accepted values:
 * - production (default)
 * - staging
 * - development
 * - local
 *
 * Note, if you are using Local, you will need to define these here.
 *
 * @link https://make.wordpress.org/core/2020/07/24/new-wp_get_environment_type-function-in-wordpress-5-5/
 */
define( 'WP_ENVIRONMENT_TYPE', 'development' );

/*
 * Glomar
 *
 * GLOMAR is a plugin that blocks the frontend of the site from public access.
 * If you would like to disable the plugin locally, add the following to your local-config.php.
 */
define( 'TRIBE_GLOMAR', false );
