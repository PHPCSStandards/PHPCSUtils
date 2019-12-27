<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Conditions;

use PHPCSUtils\Tests\BackCompat\BCFile\GetConditionTest as BCFile_GetConditionTest;

/**
 * Tests for various methods in the \PHPCSUtils\Utils\Conditions class.
 *
 * @covers \PHPCSUtils\Utils\Conditions::getCondition
 * @covers \PHPCSUtils\Utils\Conditions::hasCondition
 *
 * @group conditions
 *
 * @since 1.0.0
 */
class GetConditionTest extends BCFile_GetConditionTest
{

    /**
     * The fully qualified name of the class being tested.
     *
     * This allows for the same unit tests to be run for both the BCFile functions
     * as well as for the related PHPCSUtils functions.
     *
     * @var string
     */
    const TEST_CLASS = '\PHPCSUtils\Utils\Conditions';

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
        self::$caseFile = \dirname(\dirname(__DIR__)) . '/BackCompat/BCFile/GetConditionTest.inc';
        parent::setUpTestFile();
    }
}
