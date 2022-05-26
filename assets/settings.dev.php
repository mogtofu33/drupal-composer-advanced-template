<?php

// @codingStandardsIgnoreFile

/**
 * @file
 * Local development override configuration feature for DEV.
 */

// Config split compatibility, set FALSE for Prod.
$config['config_split.config_split.config_dev']['status'] = TRUE;

// Local settings.
$settings['file_private_path'] = 'sites/default/files/private';
$settings['simple_environment_indicator'] = 'DarkGreen Dev';
$settings['simple_environment_anonymous'] = TRUE;

// Trusted host patterns settings.
// $settings['trusted_host_patterns'][] = '^MY_ENV_IP_OR_DOMAIN$';

// Drupal default dev settings from example.settings.local.php.
if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
  include $app_root . '/' . $site_path . '/settings.local.php';
}
