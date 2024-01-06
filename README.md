# Composer template for Drupal 10 projects

[![pipeline status](https://gitlab.com/mog33/drupal-composer-advanced-template/badges/9.x/pipeline.svg)](https://gitlab.com/mog33/drupal-composer-advanced-template/commits/9.x-dev)

Enhanced Drupal 10 profile to kickstart a website.

- [What's this?](#whats-this)
- [What's included / added](#whats-included--added)
- [Install](#install)
  - [Requirements](#requirements)
  - [Grab code and libraries](#grab-code-and-libraries)
  - [Drupal installation](#drupal-installation)
    - [Server / remote installation](#server--remote-installation)
    - [Quick local setup with ddev](#quick-local-setup-with-ddev)

## What's this?

This project is meant to be a starting point for a developer, not a ready to
use Drupal with functionalities.
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
- Creates environment variables based on your .env file. See [.env.example](./.env.example), inspired from [drupal-project](https://github.com/drupal-composer/drupal-project)
- Some [patches for core](./composer.json#L260)
- A Full [Gitlab-CI support](https://gitlab.com/mog33/gitlab-ci-drupal) for build, tests, code quality, linting, metrics and deploy, see [Gitlab-CI for Drupal](https://gitlab.com/mog33/gitlab-ci-drupal)

## Install

### Requirements

Require [Composer 2+](https://getcomposer.org) with [Php 8.1+](http://php.net/) and Php modules needed for composer.

### Grab code and libraries

Get and install this project

```bash
composer create-project mog33/drupal-composer-advanced-template:10.x drupal --stability dev --no-interaction
```

Set **/web** as root of your host (Apache).

Other folders (eg: vendor) should be accessible by Webserver user and not from HTTP.

### Drupal installation

#### Server / remote installation

- Create a database and a user access to this database.

- Fix files and folder permissions of **/web** folder regardless of [Securing file permissions and ownership](https://www.drupal.org/node/244924)

- Edit `.env` and select `SETTINGS_ENVIRONMENT` value, _dev_ will enable development modules and settings

- Install Drupal and choose profile **Use existing configuration**

#### Quick local setup with ddev

This project include a simple **Docker** stack based on great project [Ddev](https://ddev.readthedocs.io/en/latest/).

Install [Ddev](https://ddev.readthedocs.io/en/latest/#installation)

**On linux:**

```bash
make install
```
