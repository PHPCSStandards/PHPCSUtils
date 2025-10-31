#!/usr/bin/env php
<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * Update the phpDocumentor configuration file.
 *
 * {@internal This functionality has a minimum PHP requirement of PHP 7.2.}
 *
 * @internal
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 *
 * @phpcs:disable PHPCompatibility.FunctionUse.NewFunctionParameters.getenv_local_onlyFound
 * @phpcs:disable PHPCompatibility.FunctionUse.NewFunctionParameters.dirname_levelsFound
 */

namespace PHPCSUtils\GHPages;

$phpcsutilsPhpdocVersionUpdater = static function () {
    $tagname = \getenv('TAG', true);
    if ($tagname === false) {
        echo 'ERROR: No TAG environment variable found.', \PHP_EOL;
        exit(1);
    }

    $tagname = \trim($tagname);
    if ($tagname === '' || \preg_match('`^[0-9]+\.[0-9]+\.[0-9]+(?:-(?:alpha|beta|rc)[0-9]*)?$`', $tagname) !== 1) {
        echo "ERROR: \"$tagname\" is not a valid tag.", \PHP_EOL;
        exit(1);
    }

    $projectRoot = \dirname(__DIR__, 2);
    $source      = '.phpdoc.xml.dist';
    $destination = 'phpdoc.xml';
    $count       = 0;

    if (\file_exists($projectRoot . '/' . $destination)) {
        echo "WARNING: Detected pre-existing \"$destination\" file.", \PHP_EOL;
        echo "Please make sure that this overload file is in sync with the \"$source\" file.", \PHP_EOL;
        echo 'This is your own responsibility!' . \PHP_EOL, \PHP_EOL;

        $config = \file_get_contents($projectRoot . '/' . $destination);
        if (\is_string($config) === false) {
            echo "ERROR: Failed to read phpDocumentor $destination configuration file.", \PHP_EOL;
            exit(1);
        }

        // Replace the previous version nr in the API doc title with the latest version number.
        $config = \preg_replace(
            '`<title>PHPCSUtils ([\#0-9\.]+)</title>`',
            "<title>PHPCSUtils {$tagname}</title>",
            $config,
            -1,
            $count
        );
    } else {
        $config = \file_get_contents($projectRoot . '/' . $source);
        if (\is_string($config) === false) {
            echo "ERROR: Failed to read phpDocumentor $source configuration template file.", \PHP_EOL;
            exit(1);
        }

        // Replace the "#.#.#" placeholder in the API doc title with the latest version number.
        $config = \str_replace(
            '<title>PHPCSUtils</title>',
            "<title>PHPCSUtils {$tagname}</title>",
            $config,
            $count
        );
    }

    if ($count !== 1) {
        echo "ERROR: Version number text replacement failed. Made $count replacements.", \PHP_EOL;
        exit(1);
    }

    if (\file_put_contents($projectRoot . '/' . $destination, $config) === false) {
        echo "ERROR: Failed to write phpDocumentor $destination configuration file.", \PHP_EOL;
        exit(1);
    } else {
        echo "SUCCESFULLY updated/created the $destination file!", \PHP_EOL;
    }

    exit(0);
};

$phpcsutilsPhpdocVersionUpdater();
