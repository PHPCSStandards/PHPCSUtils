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
class GetMemberPropertiesDiffTest extends UtilityMethodTestCase
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
     * @return array
     */
    public function dataNotClassPropertyException()
    {
        return [
            'interface property' => ['/* testInterfaceProperty */'],
            'enum property'      => ['/* testEnumProperty */'],
        ];
    }

    /**
     * Test the getMemberProperties() method.
     *
     * @dataProvider dataGetMemberProperties
     *
     * @param string $identifier Comment which precedes the test case.
     * @param bool   $expected   Expected function output.
     *
     * @return void
     */
    public function testGetMemberProperties($identifier, $expected)
    {
        $variable = $this->getTargetToken($identifier, \T_VARIABLE);
        $result   = Variables::getMemberProperties(self::$phpcsFile, $variable);

        if (isset($expected['type_token']) && $expected['type_token'] !== false) {
            $expected['type_token'] += $variable;
        }
        if (isset($expected['type_end_token']) && $expected['type_end_token'] !== false) {
            $expected['type_end_token'] += $variable;
        }

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetMemberProperties()
     *
     * @return array
     */
    public function dataGetMemberProperties()
    {
        return [
            'php8.2-pseudo-type-true' => [
                '/* testPHP82PseudoTypeTrue */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => 'true',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8.2-pseudo-type-true-nullable' => [
                '/* testPHP82NullablePseudoTypeTrue */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'is_readonly'     => false,
                    'type'            => '?true',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            'php8.2-pseudo-type-true-in-union' => [
                '/* testPHP82PseudoTypeTrueInUnion */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => 'int|string|true',
                    'type_token'      => -6, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8.2-pseudo-type-invalid-true-false-union' => [
                '/* testPHP82PseudoTypeFalseAndTrue */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => false,
                    'is_readonly'     => true,
                    'type'            => 'true|FALSE',
                    'type_token'      => -4, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
        ];
    }
}
