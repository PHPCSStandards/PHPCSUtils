<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Variables;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Variables;

/**
 * Tests for the \PHPCSUtils\Utils\Variables::getMemberProperties method.
 *
 * The tests in this class cover the differences between the PHPCS native method and the PHPCSUtils
 * version. These tests would fail when using the BCFile `getMemberProperties()` method.
 *
 * @covers \PHPCSUtils\Utils\Variables::getMemberProperties
 *
 * @group variables
 *
 * @since 1.0.0
 */
class GetMemberPropertiesDiffTest extends UtilityMethodTestCase
{

    /**
     * Test receiving an expected exception when an (invalid) interface property is passed.
     *
     * @return void
     */
    public function testNotClassPropertyException()
    {
        $this->expectPhpcsException('$stackPtr is not a class member var');

        $variable = $this->getTargetToken('/* testInterfaceProperty */', \T_VARIABLE);
        Variables::getMemberProperties(self::$phpcsFile, $variable);
    }
}
