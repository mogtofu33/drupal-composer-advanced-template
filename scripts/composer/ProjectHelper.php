<?php

namespace DrupalProject\composer;

use Composer\Script\Event;
use DrupalFinder\DrupalFinder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ProjectHelper.
 *
 * @author Jean Valverde <contact@developpeur-drupal.com>
 * @link https://developpeur-drupal.com/en
 */
final class ProjectHelper {

  /**
   * Create and copy needed files.
   *
   * @param \Composer\Script\Event $event
   *   The composer scripts Command Events.
   */
  public static function createRequiredFiles(Event $event) {
    $fs = new Filesystem();
    $drupalFinder = new DrupalFinder();
    $io = $event->getIO();

    $drupalDocRoot = getcwd();
    $drupalFinder->locateRoot(getcwd());
    $drupalRoot = $drupalFinder->getDrupalRoot();
    $drupalSettings = $drupalRoot . '/sites/default';

    $files = [
      $drupalSettings . '/default.settings.php' => $drupalSettings . '/settings.php',
      $drupalDocRoot . '/.env.example' => $drupalDocRoot . '/.env',
    ];

    foreach ($files as $src => $dest) {
      if ($fs->exists($src)) {
        if ($fs->exists($dest)) {
          $io->write(sprintf('<info>[SKIP] Existing file %s</info>', $dest));
        }
        else {
          $io->write(sprintf('<info>Create %s from %s</info>', $dest, $src));
          $fs->copy($src, $dest);
        }
      }
      else {
        $io->warning(sprintf('Missing file %s', $src));
      }
    }

    if ($fs->exists($drupalSettings)) {
      $oldmask = umask(0);
      $fs->chmod($drupalSettings, 0750);
      umask($oldmask);
      $io->write('<info>Set sites/default directory to chmod 0750</info>');
    }
    else {
      $io->warning(sprintf('Missing Drupal settings folder: %s', $drupalSettings));
    }

    $io->write('<info>Project setup Done! Happy dev!</info>');
  }

}
