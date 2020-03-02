<?php

namespace DrupalProject\composer;

use Composer\Script\Event;
use DrupalFinder\DrupalFinder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ProjectHelper.
 */
class ProjectHelper {

  /**
   * Create and copy needed files.
   *
   * @param Composer\Script\Event $event
   *   The composer scripts Command Events.
   */
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
      $event->getIO()->write("Missing or already exist settings file .env");
    }

    if ($fs->exists($drupalSettings)) {
      $oldmask = umask(0);
      $fs->chmod($drupalSettings, 0750);
      umask($oldmask);
      $event->getIO()->write("Set sites/default directory to chmod 0750");
    }
    else {
      $event->getIO()->warning("Missing Drupal settings folder: $drupalSettings");
    }

    $settingFiles = [
      '/example.settings.local.php' => '/settings.local.php',
      '/example.settings.dev.php' => '/settings.dev.php',
      '/example.settings.prod.php' => '/settings.prod.php',
    ];

    foreach ($settingFiles as $src => $dest) {
      if ($fs->exists($drupalDocRoot . $src) and !$fs->exists($drupalSettings . $dest)) {
        $event->getIO()->write("Copy $src => $dest");
        $fs->copy($drupalDocRoot . $src, $drupalSettings . $dest);
      }
      else {
        if (!$fs->exists($drupalDocRoot . $src)) {
          $event->getIO()->error("Missing source file: $src");
        }
        if ($fs->exists($drupalSettings . $dest)) {
          $event->getIO()->warning("Destination file already here: $dest");
        }
      }
    }

    // Include local file.
    $settings = $drupalSettings . '/settings.php';
    if ($fs->exists($settings)) {
      $oldmask = umask(0);
      $fs->chmod($settings, 0777);
      ProjectHelper::appendToFile($settings, 'include $app_root . "/" . $site_path . "/settings.local.php";');
      $fs->chmod($settings, 0600);
      umask($oldmask);
    }
    else {
      $event->getIO()->warning("Missing settings file: $settings");
    }

    $event->getIO()->info("Project setup Done!");
  }

  /**
   * Appends content to an existing file.
   *
   * @param string $filename
   *   The file to which to append content.
   * @param string $content
   *   The content to append.
   *
   * @throws IOException
   *   If the file is not writable.
   */
  private static function appendToFile($filename, $content) {
    $dir = \dirname($filename);
    if (!is_dir($dir)) {
      $fs = new Filesystem();
      $fs->mkdir($dir);
    }
    if (!is_writable($dir)) {
      throw new IOException(sprintf('Unable to write to the "%s" directory.', $dir), 0, NULL, $dir);
    }
    if (FALSE === @file_put_contents($filename, $content, FILE_APPEND)) {
      throw new IOException(sprintf('Failed to write file "%s".', $filename), 0, NULL, $filename);
    }
  }

}
