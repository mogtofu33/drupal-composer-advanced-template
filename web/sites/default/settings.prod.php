<?php

/**
 * @file
 * Local development override configuration feature for PROD.
 *
 */

// Config split compatibility, set FALSE for Prod.
$config['config_split.config_split.dev_config']['status'] = FALSE;

// Local settings.
$settings['file_private_path'] = 'sites/default/files/private';
$settings['simple_environment_indicator'] = 'DarkRed PROD';
