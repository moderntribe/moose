# Moose

## Requirements
* [Git](https://git-scm.com/)
* [Composer](https://getcomposer.org/)
* [Node & NPM](https://nodejs.org/)
  * [NVM](https://github.com/nvm-sh/nvm) is recommended for managing multiple versions of node on the same workstation.
* [Lando](https://lando.dev/) (Optional) Provides a consistent local development environment for all team members.
* [1Password CLI](https://developer.1password.com/docs/cli/) (Optional) Automates the creation of composer's `auth.json`
file so that paid 3rd-party plugins like Advanced Custom Fields Pro and Gravity Forms can be installed via composer.

> [!TIP]
> This starter is designed to allow developers the freedom to use any local development tooling that works best for
> them. The following instructions assume the use of Lando, but any local development platform should work as long as it
> provides a basic LAMP or LEMP stack and uses the correct version of PHP as defined in `composer.json`.

## Getting Started

1. Clone the repository
2. Run `composer create-auth` to create the `auth.json` file. (Assumes you are using the 1Password CLI.)
3. Run `lando start` to create the local environment.
4. Run `nvm use` to ensure the correct version of node is in use.
5. Run `npm install` to install the required npm dependencies.
6. Run `npm run dist` to build the theme assets.

That should be it! After Lando starts the first time, it should automatically trigger a composer install and create the 
necessary local config files for the project.

## Documentation

### NPM Packages, Scripts & Building Frontend Assets

NPM use used for managing frontend dependencies and npm scripts for managing the frontend assets. Learn more about the 
available scripts and how to use them in the [NPM Docs](./docs/npm.md).

### Composer

## Composer
TBD: Outline deps management, updating WP, scripts, etc. ... For WordPress updates, you can change the `--version=` value in the `setup-wordpress` composer script.

## Lando Updates
TBD: outline lando commands, db management, etc.

### 1Password CLI
TBD: outline 1Password CLI integration

### GitHub Actions

We use GitHub Action as a CI for deployments, testing and many other features. Take a look at the 
[GitHub Action Docs](./docs/actions.md) to learn more about each action.

### Additional Documentation
Specific features and functionality may have additional documentation in the [./docs](./docs) folder.
* [NPM Packages, Scripts & Building Assets](./docs/npm.md)
* [GitHub Actions](./docs/actions.md)
* [PHP Tests](./docs/php-tests.md)
* [Create Block Script Templates](./docs/block-templates.md)
* [Create WP Controls Script](./docs/wp-controls-templates.md)
* [Supported Block Features](./docs/block-features.md)

## Modern Tribe

[![Modern Tribe](https://moderntribe-common.s3.us-west-2.amazonaws.com/marketing/ModernTribe-Banner.png)](https://tri.be/contact/)
