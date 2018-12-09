<?php

$settings['hash_salt'] = '3U5rP_ZgCTzvGt4sfWvVTL6H2kvtBkYaU8ExLxrobmvV-hBh-ZaQ_PgEAANkZq--LiBsQ60M_6w';
$settings['update_free_access'] = FALSE;
$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.yml';
$settings['file_scan_ignore_directories'] = [
  'node_modules',
  'bower_components',
];
$settings['entity_update_batch_size'] = 50;

$config_directories['sync'] = '../config/sync';

$databases['default']['default'] = array (
  'database' => 'drupal',
  'username' => 'root',
  'password' => '',
  'prefix' => '',
  'host' => 'mariadb',
  'port' => '',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

# https://www.drupal.org/project/drupal/issues/2867042
$settings['file_chmod_directory'] = 02775;
