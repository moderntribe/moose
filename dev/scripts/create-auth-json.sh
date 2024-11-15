#!/bin/bash

# Create auth.json file for Composer using the 1Password CLI

# Check if 1Password CLI is installed
if ! command -v op > /dev/null 2>&1; then
  echo >&2 "It appears that the 1Password CLI is not installed. Skipping.";
  exit 0;
fi

# Exit if there is an existing auth.json file
if [ -f auth.json ]; then
  echo >&2 "auth.json already exists. Skipping.";
  exit 0;
fi

# Create auth.json file using 1Password CLI
echo "Creating auth.json file for Composer using 1Password CLI...";
op inject -i auth.template.json -o auth.json

exit 0;
