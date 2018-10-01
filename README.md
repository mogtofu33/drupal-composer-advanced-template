# Composer template for Drupal projects

Based on [Composer template for Drupal projects](https://github.com/drupal-composer/drupal-project).

## What's added?

* Base contrib modules, Core patches
* Third party libraries download with [Asset packagist](https://asset-packagist.org)
* Bootstrap Sass sub theme
* Drupal basic config with Dev / Prod environment, see [Workflow readme](config/README.md)

## Install

### Requirements

Package creation require [Composer 1.6+](https://getcomposer.org) with [Php 7+](http://php.net/) and Php modules needed for composer. To create the styles files you need if using Bootsrap Sass  [Compass 1+](http://compass-style.org/install)

Recommended:

* [Composer prestissimo](https://github.com/hirak/prestissimo)

```bash
composer global require "hirak/prestissimo:^0.3"
```

### Grab code and libraries

Clone this project locally in your web root folder.

```bash
git clone https://gitlab.com/mog33/drupal-composer-advanced-template.git -b 8.x-dev drupal
# Or with a Gitlab account:
git clone git@gitlab.com:mog33/drupal-composer-advanced-template.git -b 8.x-dev drupal
```

Download project code, from this folder run

```bash
cd drupal
composer install
```

Optional with Bootstrap Sass

```bash
composer install-boostrap-sass
compass compile web/themes/custom/bootstrap_sass
```

Set **/web** as root of your host (Apache).

Other folders (eg: vendor) should be accessible by Webserver user and not from HTTP.

### Drupal installation

* Create a database and a user access to this database.

* Fix files and folder permissions of **/web** folder regardless of [Securing file permissions and ownership](https://www.drupal.org/node/244924)

* Drush command installation to run from **web** folder, change uppercase variables to match your environment:
* 
_Note_: Currently can not be installed from Drupal install.php, only Drush command.

```bash
cd web
../vendor/bin/drush si config_installer \
    config_installer_sync_configure_form.sync_directory="../config/sync" \
    --account-name=admin \
    --account-pass=password \
    --db-url=DB_TYPE://DB_USER:DB_PASS@DB_HOST/DB_NAME
```

* Edit _web/sites/default/settings.php_, at the end of the file uncomment settings.local.php inclusion.

```php
if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
    include $app_root . '/' . $site_path . '/settings.local.php';
}
```

* Copy and rename _example.settings.*.php_ at the root of this project to _web/sites/default/example.settings.*.php_ and edit to adapt environment switch.

```bash
cd ..
sudo cp example.settings.local.php web/sites/default/settings.local.php
sudo cp example.settings.dev.php web/sites/default/settings.dev.php
sudo cp example.settings.prod.php web/sites/default/settings.prod.php
```
