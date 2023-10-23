# Multisite Setup

## Dev Environment Updates

### Lando

Lando requires an additional config file to allow multisite to work correctly when using a subdirectory. In order to reference the `multisite.conf` update the `.lando.yml` file to include the `vhosts: dev/config/multisite.conf` in the config settings. After updating the lando file, run `lando rebuild -y` to rebuild the container utilizing the new nginx config settings.

```yml
...
config:
  ...
  config:
    vhosts: dev/config/multisite.conf
...
```

## WordPress Multisite Setup

To activate multisite, set the `WP_ALLOW_MULTISITE` constant to true and open the Network Settings under the Settings menu item in the WordPress admin. Select the multisite network type and update the required constants in your `local-config.php` as instructed. 