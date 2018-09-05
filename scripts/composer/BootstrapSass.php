<?php

/**
 * @file
 * Contains \DrupalProject\composer\ScriptHandler.
 */

namespace DrupalProject\composer;

use Composer\Script\Event;
use DrupalFinder\DrupalFinder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class BootstrapSass {

  /**
   * Create Bootstrap Sass subtheme.
   *
   * see https://drupal-bootstrap.org/api/bootstrap/starterkits%21sass%21README.md/group/sub_theming_sass/8
   * Assume we already have bootstrap sources from composer.
   */
  public static function create(Event $event) {
    $fs = new Filesystem();
    $drupalFinder = new DrupalFinder();
    $drupalFinder->locateRoot(getcwd());
    $drupalRoot = $drupalFinder->getDrupalRoot();
    $finder = new Finder();

    $themeName = "bootstrap_sass";
    $themeTitle = "Bootstrap Sass";
    $bootstrapVersion = "3.3.7";
    $configRb = "https://gitlab.com/mog33/gitlab-ci-drupal/snippets/1751092/raw";
    $customFolder = $drupalRoot . '/themes/custom/' . $themeName;
    $contribFolder = $drupalRoot . '/themes/contrib/bootstrap';

    // If config.rb exist mean we can stop here.
    if ($fs->exists($customFolder . '/config.rb')) {
      echo "[info] Bootstrap Sass theme exist, skip.\n";
      return;
    }

    // Create Bootstrap subtheme from starterkit.
    $fs->mkdir($customFolder);
    $fs->mirror($contribFolder . '/starterkits/sass', $customFolder);

    // Copy and adapt config to get a default block position.
    $fs->mkdir($customFolder . '/config/optional');
    $fs->mirror($contribFolder . '/config/optional', $customFolder . '/config/optional');
    $finder->files()->in($customFolder . '/config/optional/');
    foreach ($finder as $file) {
      $content = $file->getContents();
      $content = str_replace('id: bootstrap', 'id: ' . $themeName, $content);
      $content = str_replace('theme: bootstrap', 'theme: ' . $themeName, $content);
      $content = str_replace('- bootstrap', '- ' . $themeName, $content);
      $fs->dumpFile($file->getPathname(), $content);
      $new_filename = str_replace('block.bootstrap', 'block.' . $themeName, $file->getFilename());
      $fs->rename($file->getPathname(), $file->getPath() . '/' . $new_filename);
    }

    // Adapt files names and content for the starterkit.
    $files = [
      '/THEMENAME.starterkit.yml' => '/' . $themeName . '.info.yml',
      '/THEMENAME.libraries.yml' => '/' . $themeName . '.libraries.yml',
      '/THEMENAME.theme' => '/' . $themeName . '.theme',
      '/config/install/THEMENAME.settings.yml' => '/config/install/' . $themeName . '.settings.yml',
      '/config/schema/THEMENAME.schema.yml' => '/config/schema/' . $themeName . '.schema.yml',
    ];
    foreach ($files as $source => $target) {
      $content = file_get_contents($customFolder . $source);
      $content = str_replace('THEMETITLE', $themeTitle, $content);
      $content = str_replace('THEMENAME', $themeName, $content);
      $fs->dumpFile($customFolder . $target, $content);
      $fs->remove($customFolder . $source);
    }

    // Copy Boostrap sass framework.
    $fs->mirror($drupalRoot . '/libraries/bootstrap-sass', $customFolder . '/bootstrap');

    // We need a config file for compiling.
    $config = file_get_contents($configRb);
    $fs->dumpFile($customFolder . '/config.rb', $config);

    // Inform about next steps.
    echo "[info] You need to run compass compile, ie:\n  compass compile " . $customFolder . "\n\n";
    echo "And enable the new theme Boostrap Sass after Drupal installtion.\n";

  }

}
