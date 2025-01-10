# 1Password CLI

The 1Password CLI can be used to automate the creation of the `auth.json` file for composer. This file is used to store 
credentials used by composer to install paid plugins like Advanced Custom Fields Pro and Gravity Forms.

The 1Password CLI is used both for local development and in GitHub Actions workflows.

## Installation for Local Development

See the [1Password CLI](https://developer.1password.com/docs/cli/get-started/) installation instructions for how to 
install & authenticate with the 1Password CLI on your local machine.

## Creating an `auth.json` File via the 1Password CLI

Running `composer create-auth` on your local machine (not within a Lando container) will use the 1Password CLI to 
create or update the `auth.json`. Note that contrary to most other composer scripts, this one cannot be run inside a 
lando container because the container is not authorized to access the 1Password CLI.

### The `auth.template.json` Template File

Under the hood, the `composer create-auth` script uses the [1Password `op inject` command](https://developer.1password.com/docs/cli/reference/commands/inject)
to retrieve secrets from a 1Password vault and creates a new file by replacing references in the template file with 
those secrets.

## Adding or Updating Secrets

Here are steps for adding a new authentication or secret value to the project:
1. Add a new section to the project's 1Password item.
2. Add the new secret(s) within the new section you created in the item.
3. Update the `auth.template.json` file with the new secret key(s) and placeholder value(s).
4. Run `composer create-auth` to update the `auth.json` file with the new secret(s).

## GitHub Secrets

1Password provides [their own GitHub Action](https://github.com/1Password/install-cli-action) that is used in the 
project workflows. The following GitHub secrets are required to use the 1Password CLI in GitHub Actions:
* `OP_SERVICE_ACCOUNT_TOKEN` - (Required) A 1Password service account token.
* `OP_VAULT` - The 1Password vault where the secrets are stored. Defaults to `Engineering`.
* `OP_ITEM` - The 1Password item containing the secrets. Defaults to `MT-Composer-Auth`.

We have configured a default service account with access to Modern Tribe's Engineering vault. This service account is
sufficient for projects that are just getting started and haven't yet purchased any client-specific licenses.

> [!IMPORTANT]
> Modern Tribe's default 1Password service account and the plugin licenses in the MT Engineering vault are shared 
> across all Modern Tribe projects and are intended for local development and Dokku environments only. If a project is 
> deploying to other hosting environments, the project should be using a project-specific 1Password vault and 
> client-supplied license keys for GitHub Actions.

### 1Password Service Account Token

To use 1Password CLI with GitHub Actions, you must create a [1Password service account](https://developer.1password.com/docs/service-accounts/get-started) 
and populate the `OP_SERVICE_ACCOUNT_TOKEN` secret in the project's GitHub repository with the respective service 
account token. You may need to ask Modern Tribe leadership or the project manager to create this account for you.

When creating the service account, be sure that the account can only access the project's vault(s). Service accounts
cannot be modified once they are created and should not be shared between projects.

### 1Password Vault and Item

The `OP_VAULT` and `OP_ITEM` secrets tell the 1Password CLI which vault and item to retrieve values from. If they are
not defined, the 1Password CLI will default to the `Engineering` vault and the `MT-Composer-Auth` item.

When creating a project-specific 1Password vault be sure to follow the structure of the `MT-Composer-Auth` item in the
Engineering vault. The structure of the `auth.template.json` file expects a specific 1Password item structure.
