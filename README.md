# Composer template for Drupal projects

[![pipeline status](https://gitlab.com/mog33/drupal-composer-advanced-template/badges/8.x-dev/pipeline.svg)](https://gitlab.com/mog33/drupal-composer-advanced-template/commits/8.x-dev)
[![Build Status](https://travis-ci.org/Mogtofu33/drupal-composer-advanced-template.svg?branch=8.x-dev)](https://travis-ci.org/Mogtofu33/drupal-composer-advanced-template)

Based on [Composer template for Drupal projects](https://github.com/drupal-composer/drupal-project).

- [What's included / added](#whats-included--added)
- [Install](#install)
  - [Requirements](#requirements)
  - [Grab code and libraries](#grab-code-and-libraries)
  - [Drupal installation](#drupal-installation)
- [Bonus](#bonus)
  - [Using Sass with a Docker image](#using-sass-with-a-docker-image)

## What's included / added

- A bunch of interesting [contrib modules](./composer.json#L47), some [patches for core / contrib](./composer.json#L271)
- Third party libraries download with [Asset packagist](https://asset-packagist.org)
- [Bootstrap Sass](https://www.drupal.org/project/bootstrap) sub theme with sass + Sass compiler with Php
- Drupal basic configuration with Dev / Prod environment, see [Workflow readme](config/README.md)
- A Full [Gitlab-CI support](https://gitlab.com/mog33/gitlab-ci-drupal) for build, tests, code quality, linting, metrics and deploy, see [Gitlab-CI for Drupal8](https://gitlab.com/mog33/gitlab-ci-drupal)

## Install

### Requirements

Require [Composer 1.8+](https://getcomposer.org) with [Php 7+](http://php.net/) and Php modules needed for composer.

Compiling the Sass file is done through [http://leafo.github.io/scssphp](http://leafo.github.io/scssphp) but you can install [Sass](https://sass-lang.com/install) if you prefer a more advanced feature (like watch) or use a [Docker image](#using-sass-with-a-docker-image).

Recommended:

- [Composer prestissimo](https://github.com/hirak/prestissimo)

```bash
composer global require "hirak/prestissimo:^0.3"
```

### Grab code and libraries

Get and install this project

```bash
composer create-project mog33/drupal-composer-advanced-template:dev-8.x-dev drupal --stability dev --no-interaction
```

_Note_: A sub theme for Bootstrap Sass will be created and compiled during the
installation. When developing a theme and editing the scss file you can compile
with command:

```bash
composer run-script sass-compile
```

Set **/web** as root of your host (Apache).

Other folders (eg: vendor) should be accessible by Webserver user and not from HTTP.

### Drupal installation

#### Graphic installation

Visit your Drupal and choose profile a `configuration installer` with config
folder `../config/sync`.

#### Drush installation

- Create a database and a user access to this database.

- Fix files and folder permissions of **/web** folder regardless of [Securing file permissions and ownership](https://www.drupal.org/node/244924)

- Copy _.env.example_ to _.env_ and edit database values:

```bash
cp .env.example .env
vi .env
```

- Drush command installation to run from **web** folder

_Note_: Currently can not be installed from Drupal install UI (install.php), only Drush command.

```bash
cd web
../vendor/bin/drush si config_installer --yes \
    config_installer_sync_configure_form.sync_directory="../config/sync" \
    --account-name=admin \
    --account-pass=password
```

If you have a permission denied, ensure permissions on `web/sites/default` is 750.

- Copy and rename _example.settings.*.php_ at the root of this project to _web/sites/default/settings.*.php_ and edit to adapt environment switch:

```bash
cp example.settings.local.php web/sites/default/settings.local.php
cp example.settings.dev.php web/sites/default/settings.dev.php
cp example.settings.prod.php web/sites/default/settings.prod.php
```

Set dev config

```bash
../vendor/bin/drush --yes csim config_split.config_split.config_dev
```

Login to your new website with user admin / password or using _drush_:

```bash
../vendor/bin/drush uli
```

## Bonus

### Using Sass with a Docker image

If you don't want to install Sass and the provided Php script in this project do not please you, an other solution is to use a Docker image with [node-sass](https://github.com/sass/node-sass).

Create the _Dockerfile_

```dockerfile
FROM node:dubnium-alpine
RUN apk update && apk add --no-cache git shadow
RUN npm install -g node-sass bootstrap-sass postcss-cli autoprefixer watch --unsafe-perm
WORKDIR /app
RUN chown -R node:node /app
CMD [ "sass", "node-sass", "compile", "watch", "npm" ]
```

One time image build

```bash
docker build -t sass .
```

Run from your theme folder

```bash
cd web/themes/custom/bootstrap_sass
docker run --rm -v $(pwd):/app sass node-sass scss/style.scss > css/style.css
# Set format compressed.
docker run --rm -v $(pwd):/app sass node-sass --output-style compressed scss/style.scss > css/style.css
# Add comments for dev.
docker run --rm -v $(pwd):/app sass node-sass --source-comments scss/style.scss > css/style.css
# Add comments for dev.
docker run --rm sass node-sass --help
```
