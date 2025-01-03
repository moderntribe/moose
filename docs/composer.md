# Composer

Composer is configured to manage PHP dependencies. There are also a number of composer scripts set up to assist with
day-to-day PHP development.

> [!WARNING]
> Running composer commands directly on your local machine will cause conflicts if your locally installed version of
> PHP is different from the version required in `composer.json`. Always be sure you have the correct version of php
> installed locally or run composer commands from within the Lando environment by prefixing them with `lando ...` such
> as: `lando composer install`.

## Composer Scripts

* `composer create-auth` - Create or update the auth.json file for Composer via 1Password CLI. (Cannot be run within a
  Lando container.)
* `composer create-local-configs` - Creates the `local-config.php` and `local-config.json` files as needed for the
  project.
* `composer install-wordpress` - Runs the WP CLI command to download and install WordPress core.
* `composer phpcs` - Run PHPCS on the project.
* `composer phpcbf` - Run PHPCBF on the project.
* `composer phpstan` - Run PHPStan on the project.
* `composer update-db` - Runs the WP CLI command to update the WordPress database. This is often required after a
  WordPress version update.

## Updating WordPress

To adjust the installed version of WordPress, run `composer config extra.wordpress-version <new-version>` and then
`composer install-wordpress`.

## Adding a Paid or Premium WordPress Plugin

A number of premium or paid WordPress plugins may be used for a project. Often, these plugins are not available in the
WordPress plugin directory and thus can't be installed from `https://wpackagist.org`. There are a few options for
installing such premium plugins:

1. Check to see if the plugin maker provides its own composer-based installation method. This is the best option.
   Many providers including Advanced Custom Fields (ACF), Gravity Forms, and Yoast SEO provide composer-based
   installation
   options. This project is already configured to use composer for both ACF and Gravity Forms.
1. Check the plugin files into the repository directly. This is the simplest option but is not ideal for a number of
   reasons, including licensing, security, and ease of management.

### Creating an auth.json File

If the plugin maker provides a composer-based installation method, you will likely create an `auth.json` file to
store the required credentials. This file is used by composer to install the plugin. This project provides an
auth.json template file that the 1Password CLI can use to automatically generate the required `auth.json` file. See the
[1Password CLI Docs](./1password-cli.md) for more information on this integration.

To manually create the `auth.json` file, copy the `auth.template.json` file to `auth.json` and update the placeholder
values within the file with the required credentials.

> [!IMPORTANT]
> The populated `auth.json` file should never be checked into the git repository as it contains
> project-specific secrets (software license keys) which should never be available in source control.

## Platform Dependencies

There are several PHP platform dependencies added as composer requirements. These dependencies include the required
version of PHP as well as several PHP extensions required by WordPress (`ext-exif`, `ext-gd`, `ext-intl`, & `ext-json`).
These PHP extensions are installed within a [project's Dokku env](actions.md#dokku-deployment-workflows) and should not
be removed unless or until Dokku is not utilized by the project.
