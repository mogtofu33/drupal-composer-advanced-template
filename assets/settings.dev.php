<?php

// @codingStandardsIgnoreFile

/**
 * @file
 * Local development override configuration feature for DEV.
 */

// Config split compatibility, set FALSE for Prod.
$config['config_split.config_split.config_dev']['status'] = TRUE;

// Local settings.
$settings['simple_environment_indicator'] = 'DarkGreen Dev';
$settings['simple_environment_anonymous'] = TRUE;

// Webprofiler tracer
$settings['tracer_plugin'] = 'stopwatch_tracer';
// Handle errors outside of webprofiler, @see https://git.drupalcode.org/project/webprofiler/-/blob/10.1.x/README.md
// $settings['webprofiler_error_page_disabled'] = TRUE;

// Drupal default dev settings from example.settings.local.php.
if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
  include $app_root . '/' . $site_path . '/settings.local.php';
}
