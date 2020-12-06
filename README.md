# Composer template for Drupal 9 projects

[![pipeline status](https://gitlab.com/mog33/drupal-composer-advanced-template/badges/9.x-dev/pipeline.svg)](https://gitlab.com/mog33/drupal-composer-advanced-template/commits/9.x-dev)
[![Build Status](https://travis-ci.org/Mogtofu33/drupal-composer-advanced-template.svg?branch=9.x-dev)](https://travis-ci.org/Mogtofu33/drupal-composer-advanced-template)

Enhanced Drupal 9 profile to kickstart a website.

- [What's this?](#whats-this)
- [What's included / added](#whats-included--added)
- [Install](#install)
  - [Requirements](#requirements)
  - [Grab code and libraries](#grab-code-and-libraries)
  - [Drupal installation](#drupal-installation)
    - [Server / remote installation](#server--remote-installation)
    - [Local setup with ddev](#local-setup-with-ddev)
- [Project metrics](#project-metrics)

## What's this?

This project is meant to be a starting point for a developper, not a ready to
use Drupal with functionnalities.  
For more advanced profiles see:
  - [Varbase](https://www.drupal.org/project/varbase)
  - [Lightning](https://www.drupal.org/project/lightning)
  - [Thunder](https://www.drupal.org/project/thunder)
  - [Social](https://www.drupal.org/project/social)
  - [Commerce](https://www.drupal.org/project/commerce)
  - [and more...](https://www.drupal.org/project/project_distribution?f%5B0%5D=&f%5B1%5D=&f%5B2%5D=sm_core_compatibility%3A9&f%5B3%5D=sm_field_project_type%3Afull&f%5B4%5D=&f%5B5%5D=&text=&solrsort=iss_project_release_usage+desc&op=Search)

## What's included / added

- Third party libraries download with [Asset packagist](https://asset-packagist.org)
- Drupal basic configuration with Dev / Prod environment, see [Workflow readme](config/README.md)
  - Local logging with [monolog](https://github.com/Seldaek/monolog) and [chrome
    logger](https://craig.is/writing/chrome-logger) support
- Creates environment variables based on your .env file. See [.env.example](./.env.example), inspired from [drupal-project](https://github.com/drupal-composer/drupal-project)
- Some [patches for core](./composer.json#L258)
- A Full [Gitlab-CI support](https://gitlab.com/mog33/gitlab-ci-drupal) for build, tests, code quality, linting, metrics and deploy, see [Gitlab-CI for Drupal](https://gitlab.com/mog33/gitlab-ci-drupal)

## Install

### Requirements

Require [Composer 2+](https://getcomposer.org) with [Php 7.3+](http://php.net/) and Php modules needed for composer.

Compiling the Sass file is done through [https://scssphp.github.io/scssphp/](https://scssphp.github.io/scssphp/) but you can install [Sass](https://sass-lang.com/install) if you prefer a more advanced feature (like watch) or use a [Docker image](#using-sass-with-a-docker-image).

### Grab code and libraries

Get and install this project

```bash
composer create-project mog33/drupal-composer-advanced-template:9.x-dev drupal --stability dev --no-interaction
```
Set **/web** as root of your host (Apache).

Other folders (eg: vendor) should be accessible by Webserver user and not from HTTP.

### Drupal installation

#### Server / remote installation

- Create a database and a user access to this database.

- Fix files and folder permissions of **/web** folder regardless of [Securing file permissions and ownership](https://www.drupal.org/node/244924)

- Edit `.env` and select `SETTINGS_ENVIRONMENT` value, _dev_ will enable development modules and settings

- Install Drupal and choose profile **Use existing configuration**

#### Local setup with ddev

This project include a simple **Docker** stack based on great project [Ddev](https://ddev.readthedocs.io/en/latest/).

Install [Ddev](https://ddev.readthedocs.io/en/latest/#installation)

Then launch the ddev stack:

```bash
ddev start
```

- Edit `.env` and select `SETTINGS_ENVIRONMENT` value, _dev_ will enable development modules and settings


- Use Drush command installation to run from **web** folder

```bash
ddev exec drush -y si --existing-config --account-name=admin --account-pass=password
```

_Note_: If you have a permission denied, ensure permissions on `web/sites/default` is 750.

Composer install script will create `web/sites/default/settings.php`, `settings.dev.php` and `settings.prod.php`, adapt if you need.

- Login to your new website with user admin / password OR using _drush_:

```bash
../vendor/bin/drush uli
```

## Project metrics

You want an idea of what's in this project ?

Just take a peek at [Phpmetrics for this project](https://mog33.gitlab.io/-/drupal-composer-advanced-template/-/jobs/265433512/artifacts/reports/phpmetrics/index.html)
