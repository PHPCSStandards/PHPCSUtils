<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
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
final class GetMemberPropertiesDiffTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_VARIABLE');

        Variables::getMemberProperties(self::$phpcsFile, 10000);
    }

    /**
     * Test receiving an expected exception when an (invalid) interface or enum property is passed.
     *
     * @dataProvider dataNotClassPropertyException
     *
     * @param string $testMarker Comment which precedes the test case.
     *
     * @return void
     */
    public function testNotClassPropertyException($testMarker)
    {
        $this->expectPhpcsException('$stackPtr is not a class member var');

        $variable = $this->getTargetToken($testMarker, \T_VARIABLE);
        Variables::getMemberProperties(self::$phpcsFile, $variable);
    }

    /**
     * Data provider.
     *
     * @see testNotClassPropertyException()
     *
     * @return array<string, array<string>>
     */
    public static function dataNotClassPropertyException()
    {
        return [
            'interface property' => ['/* testInterfaceProperty */'],
            'enum property'      => ['/* testEnumProperty */'],
        ];
    }
}
