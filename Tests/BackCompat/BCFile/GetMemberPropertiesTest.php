<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 *
 * This class is imported from the PHP_CodeSniffer project.
 *
 * Copyright of the original code in this class as per the import:
 * @author    Juliette Reinders Folmer <jrf@phpcodesniffer.info>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 *
 * With documentation contributions from:
 * @author    Phil Davis <phil@jankaritech.com>
 *
 * @copyright 2017-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHPCSUtils\Tests\BackCompat\BCFile;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::getMemberProperties method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::getMemberProperties
 *
 * @group variables
 *
 * @since 1.0.0
 */
class GetMemberPropertiesTest extends UtilityMethodTestCase
{

    /**
     * The fully qualified name of the class being tested.
     *
     * This allows for the same unit tests to be run for both the BCFile functions
     * as well as for the related PHPCSUtils functions.
     *
     * @var string
     */
    const TEST_CLASS = '\PHPCSUtils\BackCompat\BCFile';

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
        $testClass = static::TEST_CLASS;

        $variable = $this->getTargetToken($identifier, T_VARIABLE);
        $result   = $testClass::getMemberProperties(self::$phpcsFile, $variable);

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
            [
                '/* testVar */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testVarType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => false,
                    'type'            => '?int',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            [
                '/* testPublic */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testPublicType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'string',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testProtected */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testProtectedType */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'bool',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testPrivate */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testPrivateType */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'array',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testStatic */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => true,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testStaticType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => true,
                    'type'            => '?string',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            [
                '/* testStaticVar */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => true,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testVarStatic */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => true,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testPublicStatic */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testProtectedStatic */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testPrivateStatic */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testNoPrefix */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testPublicStaticWithDocblock */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testProtectedStaticWithDocblock */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testPrivateStaticWithDocblock */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupType 1 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'float',
                    'type_token'      => -6, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -6, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupType 2 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'float',
                    'type_token'      => -13, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -13, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupNullableType 1 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '?string',
                    'type_token'      => -6, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -6, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            [
                '/* testGroupNullableType 2 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '?string',
                    'type_token'      => -17, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -17, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            [
                '/* testGroupProtectedStatic 1 */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupProtectedStatic 2 */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupProtectedStatic 3 */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupPrivate 1 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupPrivate 2 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupPrivate 3 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupPrivate 4 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupPrivate 5 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupPrivate 6 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupPrivate 7 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testMessyNullableType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '?array',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            [
                '/* testNamespaceType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '\MyNamespace\MyClass',
                    'type_token'      => -5, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testNullableNamespaceType 1 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '?ClassName',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            [
                '/* testNullableNamespaceType 2 */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '?Folder\ClassName',
                    'type_token'      => -4, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            [
                '/* testMultilineNamespaceType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '\MyNamespace\MyClass\Foo',
                    'type_token'      => -18, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testPropertyAfterMethod */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testInterfaceProperty */',
                [],
            ],
            [
                '/* testNestedProperty 1 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testNestedProperty 2 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
        ];
    }

    /**
     * Test receiving an expected exception when a non property is passed.
     *
     * @dataProvider dataNotClassProperty
     *
     * @param string $identifier Comment which precedes the test case.
     *
     * @return void
     */
    public function testNotClassPropertyException($identifier)
    {
        $this->expectPhpcsException('$stackPtr is not a class member var');

        $testClass = static::TEST_CLASS;

        $variable = $this->getTargetToken($identifier, T_VARIABLE);
        $testClass::getMemberProperties(self::$phpcsFile, $variable);
    }

    /**
     * Data provider.
     *
     * @see testNotClassPropertyException()
     *
     * @return array
     */
    public function dataNotClassProperty()
    {
        return [
            ['/* testMethodParam */'],
            ['/* testImportedGlobal */'],
            ['/* testLocalVariable */'],
            ['/* testGlobalVariable */'],
            ['/* testNestedMethodParam 1 */'],
            ['/* testNestedMethodParam 2 */'],
        ];
    }

    /**
     * Test receiving an expected exception when a non variable is passed.
     *
     * @return void
     */
    public function testNotAVariableException()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_VARIABLE');

        $testClass = static::TEST_CLASS;

        $next = $this->getTargetToken('/* testNotAVariable */', T_RETURN);
        $testClass::getMemberProperties(self::$phpcsFile, $next);
    }
}
