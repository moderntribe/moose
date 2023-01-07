# Blocks Starer

## Requirements

* Git
* Composer
* Node v16.13.1 or higher & NPM v8.1.2 or higher
* NVM is recommended for managing multiple versions of node on the same workstation.

## Local Development

One of the goals of this starter is to allow developers to use whatever local development platform that works best for them. There are some details below for Lando and Local by Flywheel. If you are using a different environment, feel free to add it.

### Lando

You can use [Lando](https://lando.dev/download/) to for your local development. When starting a new project, change the name value in the `.lando.yml` file to the name of the project. Then run `lando start` to build the environment. The `local-config.php` is setup to support lando out of the box. Once the lando is running, you can follow the BE Setup instructions for the composer commands to finish the setup.

### Local by Flywheel

It is recommeneded to create a blank blueprint in Local by Flywheel in order to make it easier to startup a project. Select the blank blueprint, clone in the repository to the public folder and then follow the BE Setup instructions for getting started.

## Getting Started

### BE Setup

Run `composer run setup-project` to copy the `.env`, and `local-config` files over. Once that has completed, update the `.evn` file to include the required licenses for ACF Pro, and Gravity Forms. Once the keys are up to date, run `composer install` to pull in the required libraries.  Then run `composer setup-wordpress` to install WordPress using WP Cli. Depending on your local environment you may need to update your `local-config.php` for the local environment you are working in.

``` bash
composer setup-project
# ... update .env file if you need ACF Pro and Gravity Forms
composer install
composer setup-wordpress
```

For WordPress updates, you can change the `--version=` value in the `setup-wordpress` composer script.

### Front End Dev

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

## Modern Tribe

[![Modern Tribe](https://moderntribe-common.s3.us-west-2.amazonaws.com/marketing/ModernTribe-Banner.png)](https://tri.be/contact/)
