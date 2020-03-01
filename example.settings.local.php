<?php

/**
 * @file
 * Drupal site-specific configuration file common for all environments.
 */

// This specifies the default configuration sync directory.
// $config_directories (pre-Drupal 8.8) and
// $settings['config_sync_directory'] are supported
// so it should work on any Drupal 8 or 9 version.
if (defined('CONFIG_SYNC_DIRECTORY') && empty($config_directories[CONFIG_SYNC_DIRECTORY])) {
  $config_directories[CONFIG_SYNC_DIRECTORY] = '../config/sync';
} else if (empty($settings['config_sync_directory'])) {
  $settings['config_sync_directory'] = '../config/sync';
}

// Switch comment for env. Adapt to switch based on something else.
// Default environment is prod.
$environment = "prod";

if (!empty(getenv('SETTINGS_ENVIRONMENT'))) {
  $environment = getenv('SETTINGS_ENVIRONMENT');
}

if (file_exists($app_root . '/' . $site_path . '/settings.' . $environment . '.php')) {
  include $app_root . '/' . $site_path . '/settings.' . $environment . '.php';
}
