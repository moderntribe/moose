#!/bin/bash
# Simple bash script to check the current version of WordPress and update it if necessary.

CURRENT_VERSION=$(wp core version)
REQUESTED_VERSION=$(composer config extra.wordpress-version)

if [ "$CURRENT_VERSION" == "$REQUESTED_VERSION" ]; then
  echo "WordPress is already at version $REQUESTED_VERSION. Skipping install."
  exit 0
fi

echo "Updating WordPress to version $REQUESTED_VERSION..."
wp core download --version=$REQUESTED_VERSION --skip-content --force
exit 0;
