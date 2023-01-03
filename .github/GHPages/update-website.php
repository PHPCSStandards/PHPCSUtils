#!/usr/bin/env php
<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * Website deploy preparation script.
 *
 * Grabs markdown files which will be used in the website, adjusts if needed
 * and places them in a target directory.
 *
 * {@internal This functionality has a minimum PHP requirement of PHP 7.2.}
 *
 * @internal
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\GHPages;

require_once __DIR__ . '/UpdateWebsite.php';

$phpcsutilsWebsiteUpdater       = new UpdateWebsite();
$phpcsutilsWebsiteUpdateSuccess = $phpcsutilsWebsiteUpdater->run();

if ($phpcsutilsWebsiteUpdateSuccess === 0) {
    echo 'SUCCESFULLY updated/created the website files!', \PHP_EOL;
}

exit($phpcsutilsWebsiteUpdateSuccess);
