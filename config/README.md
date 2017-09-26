# Workflow (With merge request)

This is a sample to manage all config settings and override by environment in config folders.

## Summary

* /dev
> * Include dev modules, contact mail and global site name and mail, performance and logging settings for dev.

* /sync
> * Include main configuration, common for all environments.

## Settings and pre requisites

<pre>
settings.php
</pre>

Must include _settings.local.php_ as a common settings for all environments.

<pre>example.settings.local.php</pre>

Must be rename _settings.local.php_

<pre>
settings.local.php
</pre>

Must include dev or prod file depending environment:
<pre>
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
</pre>

* [example.settings.local.php](../web/sites/default/example.settings.local.php)
* [settings.dev.php](../web/sites/default/settings.dev.php)
* [settings.prod.php](../web/sites/default/settings.prod.php)

## Dev > Prod

### On DEV

#### Daily export config (update both sync and dev_config)
<pre>
drush cr
drush cex
</pre>

### Push config changes
<pre>
# Create specific branch
git checkout -b dev-for-merge
# Commit config
git add config/
git commit -m "Config changes:..."
git push origin dev-for-merge
# Create a merge request
</pre>

#### Merge config

### On PROD

#### Pull changes
<pre>
git pull origin master
</pre>

#### Import configuration
<pre>
drush cr
drush cim
</pre>

## Prod > dev

### On Prod

#### Routine export of changes
<pre>
drush cr
drush cex
</pre>

### Git push config changes
<pre>
# Create specific branch
git checkout -b prod-sync-for-merge
# Commit config
git add config/
git commit -m "Prod config export:...."
git push origin prod-sync-for-merge
# Create a merge request
</pre>

### On dev

### Git pull
<pre>
git pull origin master
</pre>

#### Import to merge last prod changes
<pre>
drush cr
drush csim dev_config
</pre>

#### Check for config overrides from production, and get those back into code.
<pre>drush cex</pre>
