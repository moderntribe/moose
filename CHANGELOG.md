# Changelog

All notable changes to this project will be documented in this file. The format is based
on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/). Each changelog entry gets prefixed with the category of the
item (Added, Changed, Depreciated, Removed, Fixed, Security).

## [2025.01]

- Added: `Admin_Menu_Order` class to handle reordering the WP Admin menu items that Moose loads with by default.
  This also includes Yoast SEO & RankMath, just in case either are used by default. 
- Added: Node service to Lando so FE assets can be built automatically on start up.
- Updated: project start up scripts to automatically generate the correct contents of the lcoal config files.
- Updated: script to install WordPress so we can use a version constant and not install WP every time composer is
  installed or updated.
- Updated: ESLint config now supports browser environment variable such as `IntersectionObserver`
- Added: ability for table blocks to utilize the `overflow-x` set on them by setting a `min-width` property for the
  `table` element within the table block.
- Updated: Enabled background images on the Group block; We should try to use this instead of the Cover block where
  possible.
- Added: 1Password CLI integration for automating the creation of auth.json files.
- Added: A composite GitHub action for composer installs. This composite action can be used in any workflow files that
  need to composer install.
- Updated: `lando start` now automatically creates local config files and composer installs on first run. This
  eliminates those as manual steps when starting a project for the first time.
- Updated: Misc small tweaks to composer & package files & scripts for consistency and ease of use.
- Updated: GitHub action workflows to use new composite action for composer installs as well other small improvements
  and updates to workflows
- Updated: Readme.md and docs for clarity and simplification.

## [2024.12]

- Removed: Removed WordPress Core Font Library support. [#171](https://github.com/moderntribe/moose/pull/171)
- Fix: Only lint root `package.json` file. [#170](https://github.com/moderntribe/moose/pull/170)
- Updated: `local-config-sample` updates for clarity. [#169](https://github.com/moderntribe/moose/pull/169)
- Updated: Lando config updated to use PHP 8.3, MariaDB 11.5, disables MailHog by default (this causes a Lando startup
  error), Removed unused WP Tota11y plugin, moved WP Debug to require so it's available in Dokku
  containers. [#167](https://github.com/moderntribe/moose/pull/167)
- Removed: External SquareOne dependencies.  [#161](https://github.com/moderntribe/moose/pull/161)

## [2024.11]

- Chore: WordPress Core, plugin, and package updates. Add Gravity Forms as a composer dependency. Update
  browserlist. [#166](https://github.com/moderntribe/moose/pull/166)
- Removed: Outdated theme support features. [#165](https://github.com/moderntribe/moose/pull/165)
- Added: PHP extensions required for Dokku containers. [#163](https://github.com/moderntribe/moose/pull/163)
- Updated: Code Quality actions to run on PR sync in addition to PR
  open. [#162](https://github.com/moderntribe/moose/pull/162)
- Added: Add an "internal" custom post type for project-specific training
  materials. [#160](https://github.com/moderntribe/moose/pull/160)
- Chore: WordPress 6.7 Update [#159](https://github.com/moderntribe/moose/pull/159)

## [2024.09]

- Added: Gravity Forms plugin
- Chore: Plugin version updates

## [2024.08]

- Added: cache busting for editor stylesheets and related example Codeception
  test. [#155](https://github.com/moderntribe/moose/pull/155)
- Updated: Browser versions ot remove old Safari. [#154](https://github.com/moderntribe/moose/pull/154)
- Updated: Custom Alignments plugin version to latest. [#153](https://github.com/moderntribe/moose/pull/153)
- Updated: Moose 2.0; See specific updates in the [#152](https://github.com/moderntribe/moose/pull/152).
  Design requirements in the [Jira ticket here](https://moderntribe.atlassian.net/browse/MOOSE-99).
- Chore: GitHub Action workflow updates to use the latest versions of actions/cache.
- Chore: GitHub Action tests job removed to conserve resources. Tests should be run locally.
- Updated: Composer PHP minimum requirement set to 8.2 to match expected platform version.
- Updated: GHA Code quality workflow dispatch to Pull Requests opened.

## [2024.07]

- Chore: WP version to 6.6.1
- Chore: Package updates for composer & NPM, including plugins: limit-login-attempts-reloaded (2.26.11 => 2.26.12),
  seo-by-rank-math (1.0.221 => 1.0.224), advanced-custom-fields-pro (6.3.2.1 => 6.3.4), user-switching (1.7.3 => 1.8.0).
- Removed: We were previously using some overrides to handle nested Group block layouts. With some updates in WP 6.6
  these classes are no longer necessary as WP handles this use case in core now.
- Updated: Composer PHP platform to PHP 8.2.
- Updated: Lando composer config to use 2-latest.

## [2024.06]

- Changed: Renamed and added a finish job to the Dokku Deploy App workflow so that it doesn't fail when all 3 app jobs
  are skipped.
- Changed: Renamed the code quality workflow from "Workflow" to "Code Quality Checks" and renamed the file accordingly.
- Updated: Updated GitHub default & 3rd-party action versions to
  eliminate [node version warnings](https://github.com/moderntribe/moose/actions/runs/9617664104).
- Chore: WP version to 6.5.5
- Chore: Composer updates including plugins: limit-login-attempts-reloaded (2.26.8 => 2.26.11), seo-by-rank-math (
  1.0.218 => 1.0.221), social-sharing-block (1.1.0 => 1.2.0), advanced-custom-fields-pro (6.2.9 => 6.3.2.1)
- Added: Create WP Controls script & documentation.
- Changed: Column block now uses the Create WP Controls script to create the "stacking order" controls.
- Updated: theme.json version and related adjustments for WP
  v6.6. [Reference](https://make.wordpress.org/core/2024/06/19/theme-json-version-3/)
- Updated: Remove some old, commented out css from our reset that is no longer causing issues.
- Updated: root style selector updates
  per [Core's specificity updates](https://make.wordpress.org/core/2024/06/21/wordpress-6-6-css-specificity/).
- Changed: Remove the injectChanges parameter from BrowserSync config as css injection doesn't work.
- Changed: Update the `dist` npm script to use the `production` ENV value so that assets are minified.
- Added: A `build` npm task to build non-minified assets on demand.
- Updated: `theme.json` objects should now be ordered alphabetically in order to more easily find properties.
- Updated: `theme.json` now allows you to style block style variations using the `variations` property - so some styling
  has been moved out of `.scss` files and into `theme.json` to reflect this.
- Changed: Query Pagination styles were somewhat confusing, it should now be properly nested so the hierarchy makes
  sense.
- Added: [Documentation](./docs/supported-block-features.md) surrounding what features of Gutenberg Moose disables by
  default.
- Removed: Block content filters that add the block class name to the Core List and Paragraph
  blocks. [List blocks now have their class name added via Core](https://make.wordpress.org/core/2024/06/24/miscellaneous-editor-changes-in-wordpress-6-6/#Added-wp-block-list-class-to-the-list-block)
  and Paragraphs are targeted via their element.

## [2024.05]

- Updated: Pattern definition consistency for usage of `Inserter:`
- Updated: Post pattern now shows up in the pattern selector when adding a new post.
- Updated: Post pattern should now have a layout more consistent with designs we've been seeing
- Updated: Search Result Post Card should now use the Read More block instead of the Post Title block for its link
  wrapper
- Updated: Search template has been updated to reflect this card change
- Removed: Utility that helped the Post Title block act as a link wrapper for cards. It's not being used anywhere within
  core Moose anymore, so it's not needed.
- Chore: Composer updates including plugins: seo-by-rank-math:1.0.218, block-editor-custom-alignment:1.0.7
- Chore: WP version to 6.5.2

## [2024.04]

- Removed: `example` custom block in favor of custom block generation through `npm run create-block`.
- Added: Custom block external template (+ documentation) that allows us to quickly create blocks through the command
  line using `npm run create-block`. [[MOOSE-77]](https://moderntribe.atlassian.net/browse/MOOSE-77)
- Changed: Remove Gravity Forms as a composer dependency and the respective mtribe.site composer utility. Gravity Forms
  should be added directly to a project repo when required.
- Chore: Composer updates including plugins: advanced-custom-fields-pro:6.2.9, duracelltomi-google-tag-manager:1.20.2,
  limit-login-attempts-reloaded:2.26.8, safe-svg:2.2.4, seo-by-rank-math:1.0.216, user-switching:1.7.3
- Chore: Update NPM packages, including swapping browser-sync-webpack-plugin to browser-sync-v3-webpack-plugin for
  correct version support.

## [2024.03]

- Fixed: Fixed an issue with the Terms block where if a post ID wasn't provided it would error
  out. [Panopto Slack thread.](https://tribe.slack.com/archives/C061UC7B2F9/p1710250320818599)
- Added: Styling for editor title
  bar (http://p.tri.be/i/Dszjax). [[MOOSE-111]](https://moderntribe.atlassian.net/browse/MOOSE-111)
- Added: Allow `view.js` files for blocks. [[MOOSE-86]](https://moderntribe.atlassian.net/browse/MOOSE-86)
- Changed: `render_template` function for ACF blocks should now properly pass in all block
  variables. [[MOOSE-81]](https://moderntribe.atlassian.net/browse/MOOSE-81)
- Changed: Layout styles are now properly separated between FE &
  editor. [[MOOSE-84]](https://moderntribe.atlassian.net/browse/MOOSE-84)
- Changed: `theme.json` now contains static widths for content and wide
  widths. [[MOOSE-84]](https://moderntribe.atlassian.net/browse/MOOSE-84)
- Added: `theme.json` now contains a new static "grid"
  width. [[MOOSE-84]](https://moderntribe.atlassian.net/browse/MOOSE-84)

## [2024.02]

- Chore: WordPress 6.4.3 Update
- Chore: Plugin updates: advanced-custom-fields-pro:6.2.6, gravityforms:2.8.3, duracelltomi-google-tag-manager:1.20,
  limit-login-attempts-reloaded:2.26.2,seo-by-rank-math:1.0.212

## [2023.12]

- Added: Xdebug support for WP Cli commands.
- Chore: Update WordPress Core to 6.4.2 and plugins (ACF, Gravity Forms, GTM, LLAR, RankMath, SafeSVG, User Switching)
- Chore: Update NPM engines & packages, update GitHub action for npm install, remove unused npm scripts

## [2023.11]

- Chore: WordPress 6.4.1 Update
- Chore: Plugin updates - advanced-custom-fields-pro:6.2.2, limit-login-attempts-reloaded:2.25.26, seo-by-rank-math:
  1.0.205, safe-svg:2.2.1
- Updated: Only exclude the node_modules folder if it is in the root of the project.

## [2023.10]

- Chore: Update package.json dependencies and related scripts. Update supported browsers (browserlist).
- Added: Terms block v1.0.0. Displays a set of terms for a given taxonomy. Is able to display those terms in a few
  different ways (links, pills).
- Updated: WordPress Core update to 6.3.2
- Updated: Disable Emojis to 1.7.6, Limit Login Attempts Reloaded to 2.25.25, RankMath to 1.0.203, ACF Pro to 6.2.1.1
- Adds: Lighthouse GitHub Action for automatic track of SEO, Accessibility, Performance, and Best Practices.

## [2023.08]

- Added: GTM4WP Plugin for handling Google Tag Manager.
- Updated: Deployments to use the secrets.COMPOSER_AUTH_JSON for auth.json file.
- Updated: Composer method for pulling in ACF requiring the use of an auth.json file.
- Updated: WordPress core to 6.3, ACF to 6.2, Gravity Forms to 2.7.12, Local Lando PHP version to 8.1, Yoast SEO to
  ^20.1.
- Updated: Misc composer packages updated to match local PHP version
- Added: Stacking order controls on the Column block. This allows editors to control what order columns appear in at
  mobile widths.
- Updated: Swapped Yoast SEO plugin out in favor of [Rank Math SEO](https://wordpress.org/plugins/seo-by-rank-math/)
  plugin. Remove Redirection plugin as Rank Math supports the same feature. Updated primary term helper method to
  support both plugins' primary term meta value.

## [2023.06]

- Added: Ability to hide ACF menu item using the `HIDE_ACF_MENU` constant (boolean true hides the menu item) or if we
  are in a production environment.

## [2023.05]

- Security: Removed default support for XML-RCP Authentication.

## [2023.04]

- Added: GitHub actions for Coding Standards, Static Analysis and Testing.
- Added: Default testing suite utilizing Slic.
- Updated: local-config.json & browsersync.config.js keys to work for both Lando and LocalWP.
- Updated: package.json config so npm scripts run using the config keys rather than repeated strings.
- Updated: webpack.config.js to make use of package.json config keys and fix an issue with the block.json file not being
  copied correctly on build.
- Added: PostCSS custom selectors, custom media queries, and globalCSS configs and examples.
- Chore: Updated WordPress Core to v6.2, Advanced Custom Fields Pro to v6.0.7, and `composer update` for all misc
  dependencies and plugins.
- Chore: Updated package.json dependencies and related scripts.
- Changed: Moved CHANGELOG.md from `/.github` to project root.

## [2022.10]

- Added: Initial Repo Setup.
