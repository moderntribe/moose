# NPM Packages, Scripts & Building Assets

NPM is used to manage frontend dependencies. There are also a number of npm scripts defined to assist in day-to-day
development. These npm scripts are based on WordPress's [WP-Scripts](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/) package. See the documentation there for further details.

## Building Assets

If you are not working with the theme assets locally, and you are using Lando, you can skip this section. Lando will
automatically build the assets for you each time the project is started. To manually build the theme assets for your
local development environment, use the following steps:

1. In the root of the project, run `nvm use` to confirm the correct version of node is in-use.
1. Run `npm install` to install the required dependencies.
1. Run `npm run build` to build the non-production assets.

## Using Browsersync for Local Dev

To handle live-reload for changes, this project utilizes Browsersync to watch for asset file changes and reload the
browser. In addition, Browsersync can be configured via a `local-config.json` file to proxy your local environment's
SSL configuration to allow live-reloading from a specific local project URL rather than localhost.

Lando will automatically generate an proper local-config.json file the first time a project is started. If you are not
using Lando, you'll need to manually create this file using the steps below:

1. Duplicate the `local-config-sample.json` file into a git-ignored `local-config.json` and update the `certsPath`,
   `certName` and `host` values to match your local dev set up. Examples are provided for Lando and LocalWP.
1. In the root of the project, run `nvm use` to confirm the correct version of node is in-use.
1. Run `npm install` to install the required dependencies.
1. Run `npm run dev` to start the webpack watch & browsersync tasks.

## NPM Scripts

* `npm run dist` - Builds production versions of all assets.
* `npm run build` - Builds non-production versions of all assets.
* `npm run dev` - Builds dev assets and starts an instance of browsersync to handle live-reload for changes.
* `npm run format` - Runs Prettier on all theme assets (css, scss, js, & json files).
* `npm run lint` - Prettifies, lints (and fixes) theme & root assets (css, scss, js, & json files).
* `npm run create-block` - Starts an interactive shell script to generate a new block per WordPress's
  [Create Block script](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-create-block/)
  and the theme config.

Several scripts have sub-tasks that can be run individually. Reference `package.json` for details.
Additionally, there are several scripts aliased directly from wp-scripts that may be useful:

* `packages-check`
* `check-engines`
* `check-licenses`
