# Changelog

All notable changes to this project will be documented in this file. The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/). Each changelog entry gets prefixed with the category of the item (Added, Changed, Depreciated, Removed, Fixed, Security).

## [2023.06]

- Added: Ability to hide ACF menu item using the `HIDE_ACF_MENU` constant (boolean true hides the menu item) or if we are in a production environment.

## [2023.05]

- Security: Removed default support for XML-RCP Authentication.

## [2023.04]

- Added: GitHub actions for Coding Standards, Static Analysis and Testing.
- Added: Default testing suite utilizing Slic.
- Updated: local-config.json & browsersync.config.js keys to work for both Lando and LocalWP.
- Updated: package.json config so npm scripts run using the config keys rather than repeated strings.
- Updated: webpack.config.js to make use of package.json config keys and fix an issue with the block.json file not being copied correctly on build.
- Added: PostCSS custom selectors, custom media queries, and globalCSS configs and examples.
- Chore: Updated WordPress Core to v6.2, Advanced Custom Fields Pro to v6.0.7, and `composer update` for all misc dependencies and plugins.
- Chore: Updated package.json dependencies and related scripts.
- Changed: Moved CHANGELOG.md from `/.github` to project root.

## [2022.10]

- Added: Initial Repo Setup.
