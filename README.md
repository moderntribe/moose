# Moose

## Requirements
* 
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

## Composer
TBD: Outline deps management, updating WP, scripts, etc. ... For WordPress updates, you can change the `--version=` value in the `setup-wordpress` composer script.

## Lando Updates
TBD: outline lando commands, db management, etc.

## 1Password CLI
TBD: outline 1Password CLI integration

## GH Actions
TBD: Outline GH Actions

### Front End Dev
RBD: Update this to latest.
1. Duplicate the `local-config-sample.json` file into a git-ignored `local-config.json` and update the certsPath and host entries to match your local dev set up.
1. In the root of the project, run `nvm use` to confirm the correct version of node is in-use.
1. Run `npm install` to install the required dependencies.
1. Run `npm run dev` to start the webpack watch & browsersync tasks.

### Front End Scripts

* `npm run dist`: Builds production versions of all assets.
* `npm run dev`: Builds dev assets and starts an instance of browsersync to handle live-reload for changes.
* `npm run create-block`: Starts an interactive shell script to generate a new block per WordPress's [Create Block script](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-create-block/) and the theme config.
* `npm run format`: Runs Prettier on all theme assets (css, scss, js, & json files).
* `npm run lint`: Prettifies, lints (and fixes) theme & root assets (css, scss, js, & json files). Also see the sub-tasks for specific file types.
* `npm run server_dist`: Alias for the `dist` task.

These scripts are based on WordPress's [WP-Scripts](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/) package. See the documentation there for additional information.

There are also several additional scripts aliased directly from wp-scripts that may be useful:

* `packages-check`
* `check-engines`
* `check-licenses`

## Testing

A test suite is ready to use utilizing [Slic](https://github.com/stellarwp/slic). You can follow the instructions on the Slic readme to configure testing locally. Slic utilizes [WP-Browser](https://wpbrowser.wptestkit.dev/) and [Codeception](https://codeception.com/) to run tests in a docker container allowing us to use all the generate commands those libraries have to offer.

The only major setup config you must do for slic is set the php-version to 8.0 since the default is 7.4.  You can do this by running `slic php-version set 8.0`.

Once Slic is installed, you can go to the project root and enter `slic here` telling slic that you want to run tests from this folder.  Then run `slic use site` telling slic that you want to run the tests for the full site and not just a singular plugin or theme. Then you are ready to start testing by running `slic run wpunit`. You can exchange out the `wpunit` for any of the testing suites you would like to run (`wpunit`, `unit`, `functional`, or `acceptance`).  

## GitHub Actions

We use GitHub Action as a CI for deployments, testing and many other features.  To learn more about each action, checkout the [./docs/actions.md](./docs/actions.md) for details.

## Additional Documentation
Specific features and functionality may have additional documentation in the [./docs](./docs) folder.
* [GitHub Actions](./docs/actions.md)
* [Create Block Script Templates](./docs/block-templates.md)
* [Create WP Controls Script](./docs/wp-controls-templates.md)
* [Supported Block Features](./docs/block-features.md)

## Modern Tribe

[![Modern Tribe](https://moderntribe-common.s3.us-west-2.amazonaws.com/marketing/ModernTribe-Banner.png)](https://tri.be/contact/)
