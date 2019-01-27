<?php

/**
 * @file
 * Drupal site-specific configuration file common for all environments.
 */

// Switch comment for env. Adapt to switch based on something else.
// Default environment is prod.
$environment = "prod";

if (!empty(getenv('SETTINGS_ENVIRONMENT'))) {
  $environment = getenv('SETTINGS_ENVIRONMENT');
}

if (file_exists($app_root . '/' . $site_path . '/settings.' . $environment . '.php')) {
  include $app_root . '/' . $site_path . '/settings.' . $environment . '.php';
}
