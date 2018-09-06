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

```
composer global require "hirak/prestissimo:^0.3"
```

### Grab code and libraries

Clone this project locally in your web root folder.

    git clone https://gitlab.com/mog33/drupal-composer-advanced-template.git -b 8.x-dev drupal
    # Or with a Gitlab account:
    git clone git@gitlab.com:mog33/drupal-composer-advanced-template.git -b 8.x-dev drupal

Download project code, from this folder run

    cd drupal
    composer install

Optional with Bootstrap Sass

    composer install-boostrap-sass
    compass compile drupal/web/themes/custom/bootstrap_sass

Set **/web** as root of your host (Apache).

Other folders (eg: vendor) should be accessible by Webserver user and not from HTTP.

### Drupal installation

* Create a database and a user access to this database.

* Fix files and folder permissions of **/web** folder regardless of [Securing file permissions and ownership](https://www.drupal.org/node/244924)

* Drush command installation to run from **web** folder, change uppercase variables to match your environment:

    ../vendor/bin/drush si config_installer \
        config_installer_sync_configure_form.sync_directory="../config/sync" \
        --db-url=mysql://DB_USER:DB_PASS@DB_HOST/DB_NAME \
        --account-name=admin \
        --account-pass=ADMIN_PASSWORD \
        --account-mail=ADMIN_MAIL@MAIL.COM

* Edit _web/sites/default/settings.php_, at the end of the file uncomment settings.local.php inclusion.

    if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
        include $app_root . '/' . $site_path . '/settings.local.php';
    }

* Copy and rename _web/sites/default/example.settings.local.php_ to _web/sites/default/example.settings.local.php_ and edit to adapt environment switch.

    sudo cp web/sites/default/example.settings.local.php web/sites/default/settings.local.php
