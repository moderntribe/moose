# NPM Scripts & Building Assets

These scripts are based on WordPress's [WP-Scripts](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/) package. See the documentation there for more information.

## Building Assets

To build the theme assets for your local development environment, the following steps are sufficient:

1. In the root of the project, run `nvm use` to confirm the correct version of node is in-use.
1. Run `npm install` to install the required dependencies.
1. Run `npm run dist` to build the production assets

## Using Browsersync for Local Dev

To handle live-reload for changes, Moose utilizes Browsersync to watch for asset file changes and reload the browser.
In addition, Browsersync can be configured via a `local-config.json` file to proxy your local environment's 
SSL configuration to allow live-reloading from a specific local project URL rather than localhost. To use Browsersync
for local development follow the steps below:

1. Duplicate the `local-config-sample.json` file into a git-ignored `local-config.json` and update the `certsPath`,
`certName` and `host` values to match your local dev set up. Examples are provided for Lando and LocalWP.
1. In the root of the project, run `nvm use` to confirm the correct version of node is in-use.
1. Run `npm install` to install the required dependencies.
1. Run `npm run dev` to start the webpack watch & browsersync tasks.

## NPM Scripts

* `npm run dist`: Builds production versions of all assets.
* `npm run build`: Builds non-production versions of all assets.
* `npm run dev`: Builds dev assets and starts an instance of browsersync to handle live-reload for changes.
* `npm run format`: Runs Prettier on all theme assets (css, scss, js, & json files).
* `npm run lint`: Prettifies, lints (and fixes) theme & root assets (css, scss, js, & json files).
* `npm run create-block`: Starts an interactive shell script to generate a new block per WordPress's
  [Create Block script](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-create-block/) and the theme config.

Several scripts have sub-tasks that can be run individually. Reference `package.json` for details.
Additionally, there are several scripts aliased directly from wp-scripts that may be useful:

* `packages-check`
* `check-engines`
* `check-licenses`
