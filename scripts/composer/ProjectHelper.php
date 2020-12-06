<?php

declare(strict_types=1);

namespace DrupalProject\composer;

use Composer\Script\Event;
use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
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
  public static function createRequiredFiles(Event $event): void {
    $fs = new Filesystem();
    $drupalFinder = new DrupalFinder();
    $io = $event->getIO();

    $drupalDocRoot = \getcwd();
    $drupalFinder->locateRoot($drupalDocRoot);
    $drupalRoot = $drupalFinder->getDrupalRoot();
    $drupalSettings = $drupalRoot . '/sites';

    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->safeLoad();

    $files = [
      $drupalRoot . '/sites/default/default.settings.php' => $drupalRoot . '/sites/default/settings.php',
      $drupalRoot . '/.env.example' => $drupalRoot . '/.env',
    ];

    if ('dev' === $_ENV['SETTINGS_ENVIRONMENT']) {
      $io->write('<info>Dev environment detected.</info>');
      $files[$drupalRoot . '/sites/example.settings.local.php'] = $drupalRoot . '/sites/default/settings.local.php';
    }

    foreach ($files as $src => $dest) {
      if ($fs->exists($src)) {
        if ($fs->exists($dest)) {
          $io->write(\sprintf('<info>[SKIP] Existing file %s</info>', $dest));
        }
        else {
          $io->write(\sprintf('<info>Create %s from %s</info>', $dest, $src));
          $fs->copy($src, $dest);
        }
      }
      else {
        $io->warning(\sprintf('Missing file %s', $src));
      }
    }

    if ($fs->exists($drupalSettings)) {
      $oldmask = \umask(0);
      $fs->chmod($drupalSettings, 0750);
      \umask($oldmask);
      $io->write('<info>Set sites/default directory to chmod 0750</info>');
    }
    else {
      $io->warning(\sprintf('Missing Drupal settings folder: %s', $drupalSettings));
    }

    $io->write('<info>Project setup Done! Happy dev!</info>');
  }

}
