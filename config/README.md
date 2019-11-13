# Workflow (With merge request)

This is a sample to manage all config settings and override by environment in config folders.

More information on the offical documentation [Creating a simple split configuration: Dev modules only in dev environments](https://www.drupal.org/docs/8/modules/configuration-split/creating-a-simple-split-configuration-dev-modules-only-in-dev)

## Summary

* /dev
> * Include dev modules, contact mail and global site name and mail, performance and logging settings for dev.

* /sync
> * Include main configuration, common for all environments.

## Settings and pre requisites

```settings.php```

Must include _settings.local.php_ as a common settings for all environments.

```example.settings.local.php```

Must be rename _settings.local.php_

```settings.local.php```

Must include dev or prod file depending environment:

```php
// Switch comment for env. Adapt to switch based on something else.
switch ($_SERVER['HTTP_HOST']) {
  // Dev
  case 'localhost':
    if (file_exists($app_root . '/' . $site_path . '/settings.dev.php')) {
      include $app_root . '/' . $site_path . '/settings.dev.php';
    }
  break;
  // Default is Prod.
  default;
  if (file_exists($app_root . '/' . $site_path . '/settings.prod.php')) {
    include $app_root . '/' . $site_path . '/settings.prod.php';
  }
}
```

* [example.settings.local.php](../example.settings.local.php)
* [example.settings.dev.phpp](../example.settings.dev.php)
* [example.settings.prod.php](../example.settings.prod.phpp)

## Dev > Prod

### On DEV

#### Daily export config (update both sync and dev_config)

```bash
drush cr
drush cex
```

### Push config changes

```bash
# Create specific branch
git checkout -b dev-for-merge
# Commit config
git add config/
git commit -m "Config changes:..."
git push origin dev-for-merge
# Create a merge request
```

#### Merge config

### On PROD

#### Pull changes

```bash
git pull origin master
```

#### Import configuration

```bash
drush cr
drush cim
```

## Prod > dev

### On Prod

#### Routine export of changes

```bash
drush cr
drush cex
```

### Git push config changes

```bash
# Create specific branch
git checkout -b prod-sync-for-merge
# Commit config
git add config/
git commit -m "Prod config export:...."
git push origin prod-sync-for-merge
# Create a merge request
```

### On dev

### Git pull

```bash
git pull origin master
```

#### Import to merge last prod changes

```bash
drush cr
drush csim config_dev
```

#### Check for config overrides from production, and get those back into code.

```drush cex```
