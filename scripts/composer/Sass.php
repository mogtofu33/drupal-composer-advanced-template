<?php

namespace DrupalProject\composer;

use Composer\Script\Event;
use DrupalFinder\DrupalFinder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use ScssPhp\ScssPhp\Compiler;

/**
 * Sass support with Php.
 *
 * @author Jean Valverde <contact@developpeur-drupal.com>
 * @link https://developpeur-drupal.com/en
 */
class Sass {

  /**
   * Compile Bootstrap Sass subtheme.
   */
  public static function compile(Event $event) {

    $fs = new Filesystem();
    $drupalFinder = new DrupalFinder();
    $drupalFinder->locateRoot(getcwd());
    $drupalRoot = $drupalFinder->getDrupalRoot();
    $finder = new Finder();

    $io = $event->getIO();

    $args = $event->getArguments();
    $themeName = (count($args)) ? implode('_', $args) : NULL;
    $themeName = self::sanitizeFilename($themeName, "bootstrap_sass");

    $customFolder = $drupalRoot . '/themes/custom/' . $themeName;
    if ($fs->exists($customFolder . '/scss/style.scss')) {
      // Process all possible files not starting with '_'.
      $finder->files()->name('*.scss')->notName('_*.scss')->in($customFolder . '/scss/');
      foreach ($finder as $file) {
        $source = $file->getPathname();
        $destination = $customFolder . '/css/' . str_replace('.scss', '', $file->getFilename()) . '.css';

        // Create empty file if didn't exist.
        if (!$fs->exists($destination)) {
          $fs->dumpFile($destination, '');
        }

        // Do not compile if scss has not been recently updated.
        if ((filemtime($source) > filemtime($destination)) || (filesize($destination) == 0)) {
          $io->write('[Info] Compiling ' . $file->getFilename());
          if ($fs->exists($customFolder . '/css/' . $file->getFilename())) {
            $fs->remove($customFolder . '/css/' . $file->getFilename());
          }

          self::compileFile($source, $destination, $event->isDevMode());
          $io->write(' -- Done');
        }
        else {
          $io->write('[Info] No change in ' . $file->getFilename());
        }

      }

    }
    else {
      $io->writeError('File style.scss not found in ' . $customFolder);
    }

  }

  /**
   * Compile .scss file.
   *
   * @param string $in
   *   Input file (.scss).
   * @param string $out
   *   (Optional) Output file (.css).
   * @param bool $isDevMode
   *   (Optional) compiling format for dev, default FALSE.
   *
   * @return string|bool
   *   The compile result.
   *
   * @throws \Composer\Exception
   */
  public static function compileFile($in, $out = NULL, $isDevMode = FALSE) {

    if (!is_readable($in)) {
      throw new Exception('load error: failed to find ' . $in);
    }

    $pi = pathinfo($in);

    $scss = new Compiler();
    if ($isDevMode) {
      $scss->setLineNumberStyle(Compiler::LINE_COMMENTS);
      // $scss->setSourceMap(Compiler::SOURCE_MAP_INLINE);
      $scss->setFormatter('ScssPhp\ScssPhp\Formatter\Nested');
    }
    else {
      // $scss->setFormatter('Leafo\ScssPhp\Formatter\Crunched');
      $scss->setFormatter('ScssPhp\ScssPhp\Formatter\Compact');
    }

    $scss->addImportPath($pi['dirname'] . '/');

    $compiled = $scss->compile(file_get_contents($in), $in);

    if ($out !== NULL) {
      return file_put_contents($out, $compiled);
    }

    return $compiled;
  }

  /**
   * Make a filename safe to use in any function.
   *
   * Clean accents, spaces, special chars...
   * The iconv function must be activated.
   *
   * @param string $fileName
   *   The filename to sanitize (with or without extension).
   * @param string $defaultIfEmpty
   *   The default string returned for a non valid filename
   *   (only special chars or separators).
   * @param string $separator
   *   The default separator.
   * @param bool $lowerCase
   *   Tells if the string must converted to lower case.
   *
   * @author COil <https://github.com/COil>
   * @see http://stackoverflow.com/questions/2668854/sanitizing-strings-to-make-them-url-and-filename-safe
   *
   * @return string
   *   The sanitized full filename.
   */
  private static function sanitizeFilename($fileName, $defaultIfEmpty = 'default', $separator = '_', $lowerCase = TRUE) {
    // Gather file information and store its extension.
    $fileInfos = pathinfo($fileName);
    $fileExt   = array_key_exists('extension', $fileInfos) ? '.' . strtolower($fileInfos['extension']) : '';

    // Removes accents.
    $fileName = @iconv('UTF-8', 'us-ascii//TRANSLIT', $fileInfos['filename']);

    // Removes all characters that are not separators, letters, numbers,
    // dots or whitespaces.
    $fileName = preg_replace("/[^ a-zA-Z" . preg_quote($separator) . "\d\.\s]/", '', $lowerCase ? strtolower($fileName) : $fileName);

    // Replaces all successive separators into a single one.
    $fileName = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $fileName);

    // Trim beginning and ending separators.
    $fileName = trim($fileName, $separator);

    // If empty use the default string.
    if (empty($fileName)) {
      $fileName = $defaultIfEmpty;
    }

    return $fileName . $fileExt;
  }

}
