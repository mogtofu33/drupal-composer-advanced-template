<?php

/**
 * @file
 * Local development override configuration feature for DEV.
 */

// Config split compatibility, set FALSE for Prod.
$config['config_split.config_split.dev_config']['status'] = TRUE;

// Local settings.
$settings['file_private_path'] = 'sites/default/files/private';
$settings['simple_environment_indicator'] = 'DarkGreen Dev';


// Proxy settings.
// $settings['http_client_config']['proxy']['http'] = 'http://My_WEB_PROXY:8080';
// $settings['http_client_config']['proxy']['https'] = 'http://My_WEB_PROXY:8080';
// $settings['http_client_config']['proxy']['no'] = [
//   '127.0.0.1',
//   'localhost',
//   'SPECIFIC_IP',
// ];

// Add specific trusted host
// $settings['trusted_host_patterns'][] = '^MY_OTHER_NAME_OR_IP$';

// Drupal default dev settings from example.settings.local.php.

// Assertions
assert_options(ASSERT_ACTIVE, TRUE);
\Drupal\Component\Assertion\Handle::register();

// Drupal no cache from example.settings.local.php.
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
$settings['extension_discovery_scan_tests'] = TRUE;
$settings['rebuild_access'] = TRUE;
$settings['skip_permissions_hardening'] = TRUE;

// Webprofiler specific settings
$class_loader->addPsr4('Drupal\\webprofiler\\', [ __DIR__ . '/../../modules/contrib/devel/webprofiler/src']);
$settings['container_base_class'] = '\Drupal\webprofiler\DependencyInjection\TraceableContainer';
