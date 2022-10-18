<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Operators;

use PHPCSUtils\Tests\BackCompat\BCFile\IsReferenceTest as BCFile_IsReferenceTest;

/**
 * Tests for the \PHPCSUtils\Utils\Operators::isReference() method.
 *
 * @covers \PHPCSUtils\Utils\Operators::isReference
 *
 * @group operators
 *
 * @since 1.0.0
 */
final class IsReferenceTest extends BCFile_IsReferenceTest
{

    /**
     * The fully qualified name of the class being tested.
     *
     * This allows for the same unit tests to be run for both the BCFile functions
     * as well as for the related PHPCSUtils functions.
     *
     * @var string
     */
    const TEST_CLASS = '\PHPCSUtils\Utils\Operators';

    /**
     * Full path to the test case file associated with this test class.
     *
     * @var string
     */
    protected static $caseFile = '';

    /**
     * Initialize PHPCS & tokenize the test case file.
     *
     * Overloaded to re-use the `$caseFile` from the BCFile test.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = \dirname(\dirname(__DIR__)) . '/BackCompat/BCFile/IsReferenceTest.inc';
        parent::setUpTestFile();
    }
}
