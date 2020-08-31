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
            'php8-union-types-simple' => [
                '/* testPHP8UnionTypesSimple */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'int|float',
                    'type_token'      => -4, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8-union-types-two-classes' => [
                '/* testPHP8UnionTypesTwoClasses */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'MyClassA|\Package\MyClassB',
                    'type_token'      => -7, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8-union-types-all-base-types' => [
                '/* testPHP8UnionTypesAllBaseTypes */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'array|bool|int|float|NULL|object|string',
                    'type_token'      => -14, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8-union-types-all-pseudo-types' => [
                '/* testPHP8UnionTypesAllPseudoTypes */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => false,
                    'type'            => 'false|mixed|self|parent|iterable|Resource',
                    'type_token'      => -12, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8-union-types-illegal-types' => [
                '/* testPHP8UnionTypesIllegalTypes */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'callable||void', // Missing static, but that's OK as not an allowed syntax.
                    'type_token'      => -6, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8-union-types-nullable' => [
                '/* testPHP8UnionTypesNullable */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '?int|float',
                    'type_token'      => -4, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            'php8-union-types-pseudo-type-null' => [
                '/* testPHP8PseudoTypeNull */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'null',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8-union-types-pseudo-type-false' => [
                '/* testPHP8PseudoTypeFalse */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'false',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8-union-types-pseudo-type-false-and-bool' => [
                '/* testPHP8PseudoTypeFalseAndBool */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'bool|FALSE',
                    'type_token'      => -4, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8-union-types-object-and-class' => [
                '/* testPHP8ObjectAndClass */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'object|ClassName',
                    'type_token'      => -4, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8-union-types-pseudo-type-iterable-and-array' => [
                '/* testPHP8PseudoTypeIterableAndArray */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'iterable|array|Traversable',
                    'type_token'      => -6, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8-union-types-duplicate-type-with-whitespace-and-comments' => [
                '/* testPHP8DuplicateTypeInUnionWhitespaceAndComment */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'int|string|INT',
                    'type_token'      => -10, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'namespace-operator-type-declaration' => [
                '/* testNamespaceOperatorTypeHint */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '?namespace\Name',
                    'type_token'      => -4, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
        ];
    }
}
