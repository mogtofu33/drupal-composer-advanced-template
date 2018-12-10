# Composer template for Drupal projects

[![pipeline status](https://gitlab.org/mog33/drupal-composer-advanced-template/badges/8.x-dev/pipeline.svg)](https://gitlab.org/mog33/drupal-composer-advanced-template/commits/8.x-dev)
[![Build Status](https://travis-ci.org/Mogtofu33/drupal-composer-advanced-template.svg?branch=8.x-dev)](https://travis-ci.org/Mogtofu33/drupal-composer-advanced-template)

Based on [Composer template for Drupal projects](https://github.com/drupal-composer/drupal-project).

## What's added?

* Base contrib modules, Core patches
* Third party libraries download with [Asset packagist](https://asset-packagist.org)
* Bootstrap Sass sub theme
* Drupal basic config with Dev / Prod environment, see [Workflow readme](config/README.md)

## Install

### Requirements

Package creation require [Composer 1.7+](https://getcomposer.org) with [Php 7+](http://php.net/) and Php modules needed for composer. To create the styles files you need if using Bootsrap Sass  [Compass 1+](http://compass-style.org/install)

Recommended:

* [Composer prestissimo](https://github.com/hirak/prestissimo)

```shell
composer global require "hirak/prestissimo:^0.3"
```

### Grab code and libraries

Grab this project locally in your web root folder:

```shell
curl -fSl https://gitlab.com/mog33/drupal-composer-advanced-template/-/archive/8.x-dev/drupal-composer-advanced-template-8.x-dev.tar.gz -o drupal.tar.gz
tar xf drupal.tar.gz && mv drupal-composer-advanced-template-8.x-dev drupal
```

Download project code, from this folder run:

```shell
cd drupal
composer install
```

Optional with Bootstrap Sass:

```shell
composer install-boostrap-sass
compass compile web/themes/custom/bootstrap_sass
```

Set **/web** as root of your host (Apache).

Other folders (eg: vendor) should be accessible by Webserver user and not from HTTP.

### Drupal installation

* Create a database and a user access to this database.

* Fix files and folder permissions of **/web** folder regardless of [Securing file permissions and ownership](https://www.drupal.org/node/244924)

* Copy _.env.example_ to _.env_ and edit database values:

```shell
cp .env.example .env
```

Set your database connection values.

* Edit _web/sites/default/settings.php_ and uncomment _settings.local.php_ inclusion at the end and add config variable:

```php
if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
  include $app_root . '/' . $site_path . '/settings.local.php';
}
$config_directories['sync'] = '../config/sync';
```

* Copy and rename _example.settings.*.php_ at the root of this project to _web/sites/default/settings.*.php_ and edit to adapt environment switch:

```shell
cp example.settings.local.php web/sites/default/settings.local.php
cp example.settings.dev.php web/sites/default/settings.dev.php
cp example.settings.prod.php web/sites/default/settings.prod.php
```

Edit _web/sites/default/settings.local.php_ to match _trusted_host_patterns_ and your env switch.

* Drush command installation to run from **web** folder, change uppercase variables to match your environment:

_Note_: Currently can not be installed from Drupal install.php, only Drush command.

```shell
cd web
../vendor/bin/drush si config_installer --yes \
    config_installer_sync_configure_form.sync_directory="../config/sync" \
    --account-name=admin \
    --account-pass=password
```

Login to your new website with user admin / password or using drush:

```shell
../vendor/bin/drush uli
```
