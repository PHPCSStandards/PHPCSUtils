<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
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
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'var-modifier' => [
                '/* testVar */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'var-modifier-and-type' => [
                '/* testVarType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '?int',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            'public-modifier' => [
                '/* testPublic */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'public-modifier-and-type' => [
                '/* testPublicType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => 'string',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'protected-modifier' => [
                '/* testProtected */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'protected-modifier-and-type' => [
                '/* testProtectedType */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => 'bool',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'private-modifier' => [
                '/* testPrivate */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'private-modifier-and-type' => [
                '/* testPrivateType */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => 'array',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'static-modifier' => [
                '/* testStatic */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => true,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'static-modifier-and-type' => [
                '/* testStaticType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => true,
                    'is_readonly'     => false,
                    'type'            => '?string',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            'static-and-var-modifier' => [
                '/* testStaticVar */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => true,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'var-and-static-modifier' => [
                '/* testVarStatic */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => true,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'public-static-modifiers' => [
                '/* testPublicStatic */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'protected-static-modifiers' => [
                '/* testProtectedStatic */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'private-static-modifiers' => [
                '/* testPrivateStatic */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'no-modifier' => [
                '/* testNoPrefix */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'public-and-static-modifier-with-docblock' => [
                '/* testPublicStaticWithDocblock */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'protected-and-static-modifier-with-docblock' => [
                '/* testProtectedStaticWithDocblock */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'private-and-static-modifier-with-docblock' => [
                '/* testPrivateStaticWithDocblock */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'property-group-simple-type-prop-1' => [
                '/* testGroupType 1 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => 'float',
                    'type_token'      => -6, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -6, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'property-group-simple-type-prop-2' => [
                '/* testGroupType 2 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => 'float',
                    'type_token'      => -13, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -13, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'property-group-nullable-type-prop-1' => [
                '/* testGroupNullableType 1 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'is_readonly'     => false,
                    'type'            => '?string',
                    'type_token'      => -6, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -6, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            'property-group-nullable-type-prop-2' => [
                '/* testGroupNullableType 2 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'is_readonly'     => false,
                    'type'            => '?string',
                    'type_token'      => -17, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -17, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            'property-group-protected-static-prop-1' => [
                '/* testGroupProtectedStatic 1 */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'property-group-protected-static-prop-2' => [
                '/* testGroupProtectedStatic 2 */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'property-group-protected-static-prop-3' => [
                '/* testGroupProtectedStatic 3 */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'property-group-private-prop-1' => [
                '/* testGroupPrivate 1 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'property-group-private-prop-2' => [
                '/* testGroupPrivate 2 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'property-group-private-prop-3' => [
                '/* testGroupPrivate 3 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'property-group-private-prop-4' => [
                '/* testGroupPrivate 4 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'property-group-private-prop-5' => [
                '/* testGroupPrivate 5 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'property-group-private-prop-6' => [
                '/* testGroupPrivate 6 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'property-group-private-prop-7' => [
                '/* testGroupPrivate 7 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'messy-nullable-type' => [
                '/* testMessyNullableType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '?array',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            'fqn-type' => [
                '/* testNamespaceType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '\MyNamespace\MyClass',
                    'type_token'      => ($php8Names === true) ? -2 : -5, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'nullable-classname-type' => [
                '/* testNullableNamespaceType 1 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '?ClassName',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            'nullable-namespace-relative-class-type' => [
                '/* testNullableNamespaceType 2 */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '?Folder\ClassName',
                    'type_token'      => ($php8Names === true) ? -2 : -4, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            'multiline-namespaced-type' => [
                '/* testMultilineNamespaceType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '\MyNamespace\MyClass\Foo',
                    'type_token'      => ($php8Names === true) ? -15 : -18, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'property-after-method' => [
                '/* testPropertyAfterMethod */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'invalid-property-in-interface' => [
                '/* testInterfaceProperty */',
                [],
            ],
            'property-in-nested-class-1' => [
                '/* testNestedProperty 1 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'property-in-nested-class-2' => [
                '/* testNestedProperty 2 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'php8-mixed-type' => [
                '/* testPHP8MixedTypeHint */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'is_readonly'     => false,
                    'type'            => 'miXed',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8-nullable-mixed-type' => [
                '/* testPHP8MixedTypeHintNullable */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '?mixed',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            'namespace-operator-type-declaration' => [
                '/* testNamespaceOperatorTypeHint */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '?namespace\Name',
                    'type_token'      => ($php8Names === true) ? -2 : -4, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            'php8-union-types-simple' => [
                '/* testPHP8UnionTypesSimple */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
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
                    'is_readonly'     => false,
                    'type'            => 'MyClassA|\Package\MyClassB',
                    'type_token'      => ($php8Names === true) ? -4 : -7, // Offset from the T_VARIABLE token.
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
                    'is_readonly'     => false,
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
                    'is_readonly'     => false,
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
                    'is_readonly'     => false,
                    // Missing static, but that's OK as not an allowed syntax.
                    'type'            => 'callable||void',
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
                    'is_readonly'     => false,
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
                    'is_readonly'     => false,
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
                    'is_readonly'     => false,
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
                    'is_readonly'     => false,
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
                    'is_readonly'     => false,
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
                    'is_readonly'     => false,
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
                    'is_readonly'     => false,
                    'type'            => 'int|string|INT',
                    'type_token'      => -10, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8.1-readonly-property' => [
                '/* testPHP81Readonly */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => true,
                    'type'            => 'int',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8.1-readonly-property-with-nullable-type' => [
                '/* testPHP81ReadonlyWithNullableType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => true,
                    'type'            => '?array',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            'php8.1-readonly-property-with-union-type' => [
                '/* testPHP81ReadonlyWithUnionType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => true,
                    'type'            => 'string|int',
                    'type_token'      => -4, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8.1-readonly-property-with-union-type-with-null' => [
                '/* testPHP81ReadonlyWithUnionTypeWithNull */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => true,
                    'type'            => 'string|null',
                    'type_token'      => -4, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8.1-readonly-property-with-union-type-no-visibility' => [
                '/* testPHP81OnlyReadonlyWithUnionType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => false,
                    'is_readonly'     => true,
                    'type'            => 'string|int',
                    'type_token'      => -4, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8.1-readonly-property-with-multi-union-type-no-visibility' => [
                '/* testPHP81OnlyReadonlyWithUnionTypeMultiple */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => false,
                    'is_readonly'     => true,
                    'type'            => '\InterfaceA|\Sub\InterfaceB|false',
                    'type_token'      => ($php8Names === true) ? -7 : -11, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -3, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8.1-readonly-and-static-property' => [
                '/* testPHP81ReadonlyAndStatic */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'is_readonly'     => true,
                    'type'            => '?string',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            'php8.1-readonly-mixed-case-keyword' => [
                '/* testPHP81ReadonlyMixedCase */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'is_readonly'     => true,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                ],
            ],
            'php8-property-with-single-attribute' => [
                '/* testPHP8PropertySingleAttribute */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => 'string',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8-property-with-multiple-attributes' => [
                '/* testPHP8PropertyMultipleAttributes */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '?int|float',
                    'type_token'      => -4, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
                ],
            ],
            'php8-property-with-multiline-attribute' => [
                '/* testPHP8PropertyMultilineAttribute */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => 'mixed',
                    'type_token'      => -2, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'invalid-property-in-enum' => [
                '/* testEnumProperty */',
                [],
            ],
            'php8.1-single-intersection-type' => [
                '/* testPHP81IntersectionTypes */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => 'Foo&Bar',
                    'type_token'      => -4, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8.1-multi-intersection-type' => [
                '/* testPHP81MoreIntersectionTypes */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => 'Foo&Bar&Baz',
                    'type_token'      => -6, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8.1-illegal-intersection-type' => [
                '/* testPHP81IllegalIntersectionTypes */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => 'int&string',
                    'type_token'      => -4, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => false,
                ],
            ],
            'php8.1-nullable-intersection-type' => [
                '/* testPHP81NullableIntersectionType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'is_readonly'     => false,
                    'type'            => '?Foo&Bar',
                    'type_token'      => -4, // Offset from the T_VARIABLE token.
                    'type_end_token'  => -2, // Offset from the T_VARIABLE token.
                    'nullable_type'   => true,
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
            ['/* testEnumMethodParamNotProperty */'],
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
