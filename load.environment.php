<?php // phpcs:disable

/**
 * This file is included very early. See autoload.files in composer.json and
 * https://getcomposer.org/doc/04-schema.md#files
 */

use Dotenv\Dotenv;
// use Dotenv\Exception\InvalidPathException;

/**
 * Load any .env file. See /.env.example.
 *
 * Drupal has no official method for loading environment variables and uses
 * getenv() in some places.
 */
if (!getenv('CI', TRUE)) {
  $dotenv = Dotenv::createImmutable(__DIR__);
  $dotenv->safeLoad();
}
