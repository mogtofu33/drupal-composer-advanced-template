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
use Leafo\ScssPhp\Compiler;

class BootstrapSass {

  /**
   * Create Bootstrap Sass subtheme.
   *
   * Accept a theme name as argument.
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
    $io = $event->getIO();

    $args = $event->getArguments();
    $themeName = (count($args)) ? implode('_', $args) : NULL;
    $themeName = self::sanitizeFilename($themeName, "bootstrap_sass");

    $themeTitle = "Bootstrap Sass";
    $customFolder = $drupalRoot . '/themes/custom/' . $themeName;
    $contribFolder = $drupalRoot . '/themes/contrib/bootstrap';

    // If info.yml exist mean we can stop here.
    if ($fs->exists($customFolder . '/' . $themeName . '.info.yml')) {
      $io->writeError('Theme already exist in ' . $customFolder);
      if($io->askConfirmation('Delete and replace? (y/n) ', false)) {
        $fs->remove($customFolder);
      }
      else {
        $io->write('[Info] Process stopped.');
        return;
      }
    }

    // Create Bootstrap subtheme from starterkit.
    $fs->mkdir($customFolder);

    if (!$fs->exists($contribFolder . '/starterkits/THEMENAME/THEMENAME.theme')) {
      throw new Exception('Missing Bootstrap starterkit: ' . $contribFolder . '/starterkits/THEMENAME');
    }

    $fs->mirror($contribFolder . '/starterkits/THEMENAME', $customFolder);

    // Empty style.css
    $fs->remove($customFolder . '/css/style.css');
    $fs->dumpFile($customFolder . '/css/style.css', '');

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

    # Switch starterkit libraries to use Bootstrap Sass javascript files.
    $target = $customFolder . '/' . $themeName . '.libraries.yml';
    $content = file_get_contents($target);
    $content = str_replace('#', '', $content);
    $lines = explode(PHP_EOL, $content);
    $num = 1;
    foreach ($lines as $line) {
      if ($num < 6 || $num > 21) {
        $linesArray[$num] = $line;
      }
      $num++;
    }
    $content = implode("\n", $linesArray);
    $fs->dumpFile($target, $content);

    // Copy Bootstrap sass framework.
    $fs->mirror($drupalRoot . '/libraries/bootstrap-sass', $customFolder . '/bootstrap');

    $io->write("\n[Success] Theme created in $customFolder\n");
    $io->write('You can enable this theme after Drupal installation.');

  }

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
     * Compile .scss file
     *
     * @param string $in  Input file (.scss)
     * @param string $out Output file (.css) optional
     *
     * @return string|bool
     *
     * @throws \Composer\Exception
     */
    public static function compileFile($in, $out = null, $isDevMode = FALSE) {

        if (!is_readable($in)) {
          throw new Exception('load error: failed to find ' . $in);
        }

        $pi = pathinfo($in);
  
        $scss = new Compiler();
        if ($isDevMode) {
          $scss->setLineNumberStyle(Compiler::LINE_COMMENTS);
          // $scss->setSourceMap(Compiler::SOURCE_MAP_INLINE);
          $scss->setFormatter('Leafo\ScssPhp\Formatter\Nested');
        }
        else {
          // $scss->setFormatter('Leafo\ScssPhp\Formatter\Crunched');
          $scss->setFormatter('Leafo\ScssPhp\Formatter\Compact');
        }

        $scss->addImportPath($pi['dirname'] . '/');

        $compiled = $scss->compile(file_get_contents($in), $in);

        if ($out !== null) {
          return file_put_contents($out, $compiled);
        }

        return $compiled;
    }
  /**
   * Make a filename safe to use in any function. (Accents, spaces, special chars...)
   * The iconv function must be activated.
   *
   * @param string  $fileName       The filename to sanitize (with or without extension)
   * @param string  $defaultIfEmpty The default string returned for a non valid filename (only special chars or separators)
   * @param string  $separator      The default separator
   * @param boolean $lowerCase      Tells if the string must converted to lower case
   *
   * @author COil <https://github.com/COil>
   * @see    http://stackoverflow.com/questions/2668854/sanitizing-strings-to-make-them-url-and-filename-safe
   *
   * @return string
   */
  private static function sanitizeFilename($fileName, $defaultIfEmpty = 'default', $separator = '_', $lowerCase = true) {
    // Gather file information and store its extension
    $fileInfos = pathinfo($fileName);
    $fileExt   = array_key_exists('extension', $fileInfos) ? '.'. strtolower($fileInfos['extension']) : '';

    // Removes accents
    $fileName = @iconv('UTF-8', 'us-ascii//TRANSLIT', $fileInfos['filename']);

    // Removes all characters that are not separators, letters, numbers, dots or whitespaces
    $fileName = preg_replace("/[^ a-zA-Z". preg_quote($separator). "\d\.\s]/", '', $lowerCase ? strtolower($fileName) : $fileName);

    // Replaces all successive separators into a single one
    $fileName = preg_replace('!['. preg_quote($separator).'\s]+!u', $separator, $fileName);

    // Trim beginning and ending separators
    $fileName = trim($fileName, $separator);

    // If empty use the default string
    if (empty($fileName)) {
        $fileName = $defaultIfEmpty;
    }

    return $fileName. $fileExt;
  }
}
