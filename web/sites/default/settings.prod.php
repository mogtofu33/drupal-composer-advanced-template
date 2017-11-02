<?php

/**
 * @file
 * Local development override configuration feature for PROD.
 */

// Config split compatibility, set FALSE for Prod.
$config['config_split.config_split.dev_config']['status'] = FALSE;

// Local settings.
$settings['file_private_path'] = 'sites/default/files/private';
$settings['simple_environment_indicator'] = 'DarkRed PROD';

// Trusted host patterns settings.
// $settings['trusted_host_patterns'][] = '^MY_ENV_IP_OR_DOMAIN$';

// Proxy settings.
// $settings['http_client_config']['proxy']['http'] = 'http://My_WEB_PROXY:8080';
// $settings['http_client_config']['proxy']['https'] = 'http://My_WEB_PROXY:8080';
// $settings['http_client_config']['proxy']['no'] = [
//   '127.0.0.1',
//   'localhost',
//   'SPECIFIC_IP',
// ];
