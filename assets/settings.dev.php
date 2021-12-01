<?php

// @codingStandardsIgnoreFile

/**
 * @file
 * Local development override configuration feature.
 *
 * To activate this feature, copy and rename it such that its path plus
 * filename is 'sites/default/settings.local.php'. Then, go to the bottom of
 * 'sites/default/settings.php' and uncomment the commented lines that mention
 * 'settings.local.php'.
 *
 * If you are using a site name in the path, such as 'sites/example.com', copy
 * this file to 'sites/example.com/settings.local.php', and uncomment the lines
 * at the bottom of 'sites/example.com/settings.php'.
 */

/**
 * Assertions.
 *
 * The Drupal project primarily uses runtime assertions to enforce the
 * expectations of the API by failing when incorrect calls are made by code
 * under development.
 *
 * @see http://php.net/assert
 * @see https://www.drupal.org/node/2492225
 *
 * If you are using PHP 7.0 it is strongly recommended that you set
 * zend.assertions=1 in the PHP.ini file (It cannot be changed from .htaccess
 * or runtime) on development machines and to 0 in production.
 *
 * @see https://wiki.php.net/rfc/expectations
 */
assert_options(ASSERT_ACTIVE, TRUE);
\Drupal\Component\Assertion\Handle::register();

// Webprofiler specific settings to have proper performance measure.
$class_loader->addPsr4('Drupal\\webprofiler\\', [ __DIR__ . '/../../modules/contrib/devel/webprofiler/src']);
$settings['container_base_class'] = '\Drupal\webprofiler\DependencyInjection\TraceableContainer';

// Config split compatibility, set FALSE for Prod.
$config['config_split.config_split.config_dev']['status'] = TRUE;

// Local settings.
$settings['file_private_path'] = 'sites/default/files/private';
$settings['simple_environment_indicator'] = 'DarkGreen Dev';
$settings['simple_environment_anonymous'] = TRUE;

// Trusted host patterns settings.
// $settings['trusted_host_patterns'][] = '^MY_ENV_IP_OR_DOMAIN$';

// Drupal default dev settings from example.settings.local.php.

/**
 * Enable local development services.
 */
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';

// Drupal no cache from example.settings.local.php.
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['page'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';

// Disable all other caches, can highly slow down the site.
// $settings['cache']['default'] = 'cache.backend.null';
// $settings['cache']['bins']['bootstrap'] = 'cache.backend.null';
// $settings['cache']['bins']['discovery'] = 'cache.backend.null';
// $settings['cache']['bins']['config'] = 'cache.backend.null';

// When working with migrate.
// $settings['cache']['bins']['discovery_migration'] = 'cache.backend.memory';

// $settings['extension_discovery_scan_tests'] = TRUE;
$settings['rebuild_access'] = TRUE;
$settings['skip_permissions_hardening'] = TRUE;

// Perfomance and logging is set by config_split, uncomment here to force.
// $config['system.logging']['error_level'] = 'verbose';
// $config['system.performance']['css']['preprocess'] = FALSE;
// $config['system.performance']['js']['preprocess'] = FALSE;
