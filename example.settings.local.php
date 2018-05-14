<?php

/**
 * @file
 * Drupal site-specific configuration file common for all environments.
 */

// Trusted host patterns settings.
$settings['trusted_host_patterns'] = [
  '^localhost$',
];

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
