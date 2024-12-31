# Composer

Composer is configured to manage PHP dependencies. There are also a number of composer scripts set up to assist with
day-today PHP development.

> [!WARNING]
> Running composer commands directly on your local machine will cause conflicts if your locally installed version of
> PHP is different from the version required in `composer.json`. Always be sure you have the correct version of php
> installed locally or run composer commands from within the Lando environment by prefixing them with `lando ...`.
> For example: `lando composer install`.

## Composer Scripts

* `composer create-auth` - Create or update the auth.json file for Composer via 1Password CLI.
* `composer copy-local-configs` - Creates the `local-config.php` and `local-config.json` files from the respective
  sample file.
* `composer install-wordpress` - Runs the WP CLI command to download and install WordPress core. To change the WordPress
  version, update the `--version` value for this script.
* `composer phpcs` - Run PHPCS on the project.
* `composer phpcbf` - Run PHPCBF on the project.
* `composer phpstan` - Run PHPStan on the project.
* `composer update-db` - Runs the WP CLI command to update the WordPress database. This is often required after a version update.

## Updating WordPress

To update the installed version of WordPress, change the `--version=` value in the `install-wordpress` composer script.

## Adding a Paid or Premium WordPress Plugin

A number of premium or paid WordPress plugins may be used for a project. Often, these plugins are not available in the
WordPress plugin directory and thus can't be installed from `https://wpackagist.org`. There are a few options for
installing such premium plugins:

1. Check to see if the plugin maker provides its own composer-based installation method. This is the best option.
Many providers including Advanced Custom Fields, Gravity Forms, and Yoast SEO provide composer-based installation
options.
1. Check the plugin files into the repository directly. This is the simplest option but is not ideal for a number of
reasons, including licensing, security, and ease of management.

## Platform Dependencies

There are several PHP platform dependencies added as composer requirements. These dependencies include the required
version of PHP as well as several PHP extensions required by WordPress (`ext-exif`, `ext-gd`, `ext-intl`, & `ext-json`).
These PHP extensions are installed within a [project's Dokku env](actions.md#dokku-deployment-workflows) and should not 
be removed unless Dokku is not utilized by the project.
