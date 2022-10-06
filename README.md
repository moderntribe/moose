# Blocks Starer

## Installation

## Requirements

### Development

* Git
* Composer
* Node v16.13.1 or higher & NPM v8.1.2 or higher
* NVM is recommended for managing multiple versions of node on the same workstation.

## Getting Started

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

<p align="center">
<a href="https://tri.be/contact/"><img src="https://moderntribe-common.s3.us-west-2.amazonaws.com/marketing/ModernTribe-Banner.png"></a>
</p>
