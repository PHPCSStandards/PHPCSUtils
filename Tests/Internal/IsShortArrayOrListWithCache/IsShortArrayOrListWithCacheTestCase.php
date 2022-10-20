<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Internal\IsShortArrayOrListWithCache;

use PHPCSUtils\Tests\PolyfilledTestCase;

/**
 * Base test case for testing the IsShortArrayOrListWithCache class.
 *
 * @since 1.0.0
 */
abstract class IsShortArrayOrListWithCacheTestCase extends PolyfilledTestCase
{

    /**
     * Initialize PHPCS & tokenize the test case file.
     *
     * Overloaded to use a generic test `$caseFile` used by multiple tests for this class.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = __DIR__ . '/IsShortArrayOrListWithCacheTest.inc';
        parent::setUpTestFile();
    }
}
