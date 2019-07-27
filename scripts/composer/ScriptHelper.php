<?php

/**
 * @file
 * Contains \DrupalProject\composer\ScriptHelper.
 */

namespace DrupalProject\composer;

use Composer\Script\Event;
use DrupalFinder\DrupalFinder;
use Symfony\Component\Filesystem\Filesystem;

class ScriptHelper {

  public static function createRequiredFiles(Event $event) {
    $fs = new Filesystem();
    $drupalFinder = new DrupalFinder();
    $drupalDocRoot = getcwd();
    $drupalFinder->locateRoot(getcwd());
    $drupalRoot = $drupalFinder->getDrupalRoot();
    $drupalSettings = $drupalRoot . '/sites/default';

    // Copy .env file.
    if ($fs->exists($drupalDocRoot . '/.env.example') and !$fs->exists($drupalDocRoot . '/.env')) {
      $event->getIO()->write("Create .env");
      $fs->copy($drupalDocRoot . '/.env.example', $drupalDocRoot . '/.env');
    }
    else {
      $event->getIO()->warning("Missing or already exist settings file .env");
    }

    $settingFiles = [
      '/example.settings.local.php' => '/settings.local.php',
      '/example.settings.dev.php' => '/settings.dev.php',
      '/example.settings.prod.php' => '/settings.prod.php',
    ];

    if ($fs->exists($drupalSettings)) {
      $oldmask = umask(0);
      $fs->chmod($drupalSettings, 0750);
      umask($oldmask);
      $event->getIO()->write("Set sites/default directory to chmod 0750");
    }
    else {
      $event->getIO()->warning("Missing Drupal settings folder: $drupalSettings");
    }

    foreach ($settingFiles as $src => $dest) {
      if ($fs->exists($drupalDocRoot . $src) and !$fs->exists($drupalSettings . $dest)) {
        $event->getIO()->write("Copy $src => $dest");
        $fs->copy($drupalDocRoot . $src, $drupalSettings . $dest);
      }
      else {
        $event->getIO()->warning("Missing file or already exist: $src => $dest");
      }
    }

    // Include local file.
    $settings = $drupalSettings . '/settings.php';
    if ($fs->exists($settings)) {
      $oldmask = umask(0);
      $fs->chmod($settings, 0777);
      ScriptHelper::appendToFile($settings, 'include $app_root . "/" . $site_path . "/settings.local.php";');
      $fs->chmod($settings, 0600);
      umask($oldmask);
    }
    else {
      $event->getIO()->warning("Missing settings file: $settings");
    }
  }

  /**
   * Appends content to an existing file.
   *
   * @param string $filename The file to which to append content
   * @param string $content  The content to append
   *
   * @throws IOException If the file is not writable
   */
  private static function appendToFile($filename, $content) {
    $dir = \dirname($filename);
    if (!is_dir($dir)) {
      $this->mkdir($dir);
    }
    if (!is_writable($dir)) {
      throw new IOException(sprintf('Unable to write to the "%s" directory.', $dir), 0, null, $dir);
    }
    if (false === @file_put_contents($filename, $content, FILE_APPEND)) {
      throw new IOException(sprintf('Failed to write file "%s".', $filename), 0, null, $filename);
    }
  }
}