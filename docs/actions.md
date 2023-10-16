# GitHub Actions

We use GitHub Actions for many automated tasks. Some actions run on push, some run when creating a release and others might only run when selecting them in the UI (workflow_dispatch). You can learn more about GitHub Actions through the documentation located at [docs.github.com/en/actions](https://docs.github.com/en/actions). All of these actions are a starting point for your project and you should be adjusting/adding/removing actions to best fit your project needs.

## Deployments

### Dokku Deployment Workflows

The [Dokku](https://dokku.com/) deployments are for internal qa and development testing. These include the dokku-dev.yml, dokku-review-app.yml, and pipeline-dokku.yml workflows. To use these deployments you need to update the `app_name` from `moose-dev` to the unique name for your projects environment and setup the environment in our private dokku-ansible repo to accept the connection.

### Production Deployment Workflows

We have 3 deployment workflows to interface with whatever hosting environment is needed (deploy-dev.yml, deploy-stage.yml, deploy-prod.yml). You will need to update the `[DEV|STAGE|PROD]_DEPLOY_REPO`, `DEPLOY_PRIVATE_SSH_KEY`, `COMPOSER_AUTH_JSON`, and `COMPOSER_ENV` secrets to use these deployments in your project. These are intended to be deploying to the hosting service where the site will live. Most hosting companies work with `git` making it the default push we currently use.

## Static Analysis and Testing

There are a series of workflows that must pass in order to allow a pull request to be merged.  These are highlighted in the `workflows.yml` file and include the coding standards, static analysis, linting, and the php test suite using [Slic](https://github.com/stellarwp/slic).

## Lighthouse Testing

We are using the [Lighthouse CI](https://github.com/treosh/lighthouse-ci-action/tree/main) for testing list of production urls that we would like to run lighthouse tests on and stores the results as artifacts of the action. There are parameters that we set in the [lighthouserc.json](../.github/lighthouse/lighthouserc.json) file allowing us to set the minimum values for each of the lighthouse matrix. There are minimum values set as a baseline but each value should be updated once a project is live in order to track that updates made do not effect the results over time.

## Modern Tribe

[![Modern Tribe](https://moderntribe-common.s3.us-west-2.amazonaws.com/marketing/ModernTribe-Banner.png)](https://tri.be/contact/)
