<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Constants;

use PHPCSUtils\Internal\Cache;
use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Utils\Constants;

/**
 * Tests for the \PHPCSUtils\Utils\Constants::getProperties method.
 *
 * @covers \PHPCSUtils\Utils\Constants::getProperties
 *
 * @group constants
 *
 * @since 1.1.0
 */
final class GetPropertiesTest extends PolyfilledTestCase
{

    /**
     * Test receiving an exception when passing a non-integer token pointer.
     *
     * @return void
     */
    public function testNonIntegerToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type integer, boolean given');

        Constants::getProperties(self::$phpcsFile, false);
    }

    /**
     * Test receiving an exception when passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\OutOfBoundsStackPtr');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be a stack pointer which exists in the $phpcsFile object, 100000 given'
        );

        Constants::getProperties(self::$phpcsFile, 100000);
    }

    /**
     * Test receiving an expected exception when a non const token is passed.
     *
     * @return void
     */
    public function testNotAConstException()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type T_CONST;');

        $define = $this->getTargetToken('/* testNotAConstToken */', \T_STRING);
        Constants::getProperties(self::$phpcsFile, $define);
    }

    /**
     * Test receiving an expected exception when a non OO constant is passed.
     *
     * @dataProvider dataNotOOConstantException
     *
     * @param string $identifier Comment which precedes the test case.
     *
     * @return void
     */
    public function testNotOOConstantException($identifier)
    {
        $this->expectException('PHPCSUtils\Exceptions\ValueError');
        $this->expectExceptionMessage('The value of argument #2 ($stackPtr) must be the pointer to an OO constant');

        $const = $this->getTargetToken($identifier, \T_CONST);
        Constants::getProperties(self::$phpcsFile, $const);
    }

    /**
     * Data provider.
     *
     * @see testNotOOConstantException()
     *
     * @return array<string, array<string>>
     */
    public static function dataNotOOConstantException()
    {
        return [
            'global constant'                          => ['/* testGlobalConstantCannotHaveModifiersOrType */'],
            'constant declared in OO method (illegal)' => ['/* testConstInMethodIsNotOO */'],
        ];
    }

    /**
     * Test the getProperties() method.
     *
     * @dataProvider dataGetProperties
     *
     * @param string                         $identifier Comment which precedes the test case.
     * @param array<string, string|int|bool> $expected   Expected function output.
     *
     * @return void
     */
    public function testGetProperties($identifier, $expected)
    {
        $const    = $this->getTargetToken($identifier, \T_CONST);
        $expected = $this->updateExpectedTokenPositions($const, $expected);
        $result   = Constants::getProperties(self::$phpcsFile, $const);

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * Note: all indexes containing token positions should contain either `false` (no position)
     * or the _offset_ of the token in relation to the `T_CONST` token which is passed
     * to the getProperties() method.
     *
     * @see testGetProperties()
     *
     * @return array<string, array<string|array<string, string|int|bool>>>
     */
    public static function dataGetProperties()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'no modifiers, no type, with docblock' => [
                'identifier' => '/* testNoModifiersNoTypesWithDocblock */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => false,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                    'name_token'      => 2,
                    'equal_token'     => 4,
                ],
            ],

            // Testing modifier keyword recognition.
            'final, no type' => [
                'identifier' => '/* testFinalNoTypesConstAsName */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => false,
                    'is_final'        => true,
                    'final_token'     => -2,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                    'name_token'      => 2,
                    'equal_token'     => 4,
                ],
            ],
            'public, no type' => [
                'identifier' => '/* testPublicNoTypes */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => -2,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                    'name_token'      => 2,
                    'equal_token'     => 4,
                ],
            ],
            'protected, no type, with comment' => [
                'identifier' => '/* testProtectedNoTypesWithComment */',
                'expected'   => [
                    'scope'           => 'protected',
                    'scope_token'     => -4,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                    'name_token'      => 2,
                    'equal_token'     => 4,
                ],
            ],
            'private, no type, comments and whitespace' => [
                'identifier' => '/* testPrivateNoTypesWithCommentAndWhitespace */',
                'expected'   => [
                    'scope'           => 'private',
                    'scope_token'     => -6,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                    'name_token'      => 5,
                    'equal_token'     => 7,
                ],
            ],
            'final public, no type' => [
                'identifier' => '/* testFinalPublicNoTypes */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => -2,
                    'is_final'        => true,
                    'final_token'     => -4,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                    'name_token'      => 2,
                    'equal_token'     => 4,
                ],
            ],
            'protected final, no type' => [
                'identifier' => '/* testProtectedFinalNoTypes */',
                'expected'   => [
                    'scope'           => 'protected',
                    'scope_token'     => -4,
                    'is_final'        => true,
                    'final_token'     => -2,
                    'type'            => '',
                    'type_token'      => false,
                    'type_end_token'  => false,
                    'nullable_type'   => false,
                    'name_token'      => 2,
                    'equal_token'     => 4,
                ],
            ],

            // Testing typed constants.
            'no modifiers, typed: true' => [
                'identifier' => '/* testTypedTrue */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => false,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => 'true',
                    'type_token'      => 2,
                    'type_end_token'  => 2,
                    'nullable_type'   => false,
                    'name_token'      => 4,
                    'equal_token'     => 6,
                ],
            ],
            'final, typed: false' => [
                'identifier' => '/* testTypedFalse */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => false,
                    'is_final'        => true,
                    'final_token'     => -2,
                    'type'            => 'false',
                    'type_token'      => 2,
                    'type_end_token'  => 2,
                    'nullable_type'   => false,
                    'name_token'      => 4,
                    'equal_token'     => 6,
                ],
            ],
            'public, typed: null' => [
                'identifier' => '/* testTypedNull */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => -2,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => 'null',
                    'type_token'      => 2,
                    'type_end_token'  => 2,
                    'nullable_type'   => false,
                    'name_token'      => 4,
                    'equal_token'     => 6,
                ],
            ],
            'final protected, typed: bool, with comment' => [
                'identifier' => '/* testTypedBoolWihComment */',
                'expected'   => [
                    'scope'           => 'protected',
                    'scope_token'     => -2,
                    'is_final'        => true,
                    'final_token'     => -4,
                    'type'            => 'bool',
                    'type_token'      => 2,
                    'type_end_token'  => 2,
                    'nullable_type'   => false,
                    'name_token'      => 4,
                    'equal_token'     => 6,
                ],
            ],
            'private, typed: ?int, with docblock' => [
                'identifier' => '/* testTypedNullableIntWithDocblock */',
                'expected'   => [
                    'scope'           => 'private',
                    'scope_token'     => -2,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => '?int',
                    'type_token'      => 3,
                    'type_end_token'  => 3,
                    'nullable_type'   => true,
                    'name_token'      => 5,
                    'equal_token'     => 7,
                ],
            ],
            'no modifiers, typed: float, with attribute' => [
                'identifier' => '/* testTypedFloatWithAttribute */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => false,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => 'float',
                    'type_token'      => 2,
                    'type_end_token'  => 2,
                    'nullable_type'   => false,
                    'name_token'      => 4,
                    'equal_token'     => 6,
                ],
            ],
            'public final, typed: ?string, with comment' => [
                'identifier' => '/* testTypedNullableStringWithComment */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => -6,
                    'is_final'        => true,
                    'final_token'     => -2,
                    'type'            => '?string',
                    'type_token'      => 3,
                    'type_end_token'  => 3,
                    'nullable_type'   => true,
                    'name_token'      => 5,
                    'equal_token'     => 7,
                ],
            ],
            'private final, typed: array' => [
                'identifier' => '/* testTypedArray */',
                'expected'   => [
                    'scope'           => 'private',
                    'scope_token'     => -4,
                    'is_final'        => true,
                    'final_token'     => -2,
                    'type'            => 'array',
                    'type_token'      => 2,
                    'type_end_token'  => 2,
                    'nullable_type'   => false,
                    'name_token'      => 4,
                    'equal_token'     => 6,
                ],
            ],
            'no modifiers, typed: object, extra whitespace' => [
                'identifier' => '/* testTypedObjectWithExtraWhitespace */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => false,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => 'object',
                    'type_token'      => 3,
                    'type_end_token'  => 3,
                    'nullable_type'   => false,
                    'name_token'      => 6,
                    'equal_token'     => 11,
                ],
            ],
            'no modifiers, typed: ?iterable, lowercase constant name' => [
                'identifier' => '/* testTypedNullableIterableLowercaseName */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => false,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => '?iterable',
                    'type_token'      => 3,
                    'type_end_token'  => 3,
                    'nullable_type'   => true,
                    'name_token'      => 5,
                    'equal_token'     => 7,
                ],
            ],
            'no modifiers, typed: mixed' => [
                'identifier' => '/* testTypedMixed */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => false,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => 'mixed',
                    'type_token'      => 2,
                    'type_end_token'  => 2,
                    'nullable_type'   => false,
                    'name_token'      => 4,
                    'equal_token'     => 6,
                ],
            ],
            'no modifiers, typed: nullable unqualified name, comment in type' => [
                'identifier' => '/* testTypedClassUnqualifiedWithComment */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => false,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => '?MyClass',
                    'type_token'      => 6,
                    'type_end_token'  => 6,
                    'nullable_type'   => true,
                    'name_token'      => 8,
                    'equal_token'     => 10,
                ],
            ],
            'public, typed: fully qualified name, with docblock' => [
                'identifier' => '/* testTypedClassFullyQualifiedWithDocblock */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => -2,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => '\MyClass',
                    'type_token'      => 2,
                    'type_end_token'  => ($php8Names === true) ? 2 : 3,
                    'nullable_type'   => false,
                    'name_token'      => ($php8Names === true) ? 4 : 5,
                    'equal_token'     => ($php8Names === true) ? 6 : 7,
                ],
            ],
            'protected, typed: namespace relative name' => [
                'identifier' => '/* testTypedClassNamespaceRelative */',
                'expected'   => [
                    'scope'           => 'protected',
                    'scope_token'     => -2,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => 'namespace\MyClass',
                    'type_token'      => 2,
                    'type_end_token'  => ($php8Names === true) ? 2 : 4,
                    'nullable_type'   => false,
                    'name_token'      => ($php8Names === true) ? 4 : 6,
                    'equal_token'     => ($php8Names === true) ? 6 : 8,
                ],
            ],
            'private, typed: partially qualified, with multi-attribute' => [
                'identifier' => '/* testTypedClassPartiallyQualifiedWithMultipleAttributes */',
                'expected'   => [
                    'scope'           => 'private',
                    'scope_token'     => -2,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => 'Partial\MyClass',
                    'type_token'      => 2,
                    'type_end_token'  => ($php8Names === true) ? 2 : 4,
                    'nullable_type'   => false,
                    'name_token'      => ($php8Names === true) ? 4 : 6,
                    'equal_token'     => ($php8Names === true) ? 6 : 8,
                ],
            ],
            'no modifiers, typed: ?parent, with comments, messy' => [
                'identifier' => '/* testTypedNullableParentMessy */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => false,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => '?parent',
                    'type_token'      => 9,
                    'type_end_token'  => 9,
                    'nullable_type'   => true,
                    'name_token'      => 11,
                    'equal_token'     => 13,
                ],
            ],
            'public, typed: bool, multi-constant, single line' => [
                'identifier' => '/* testMultiConstSingleLine */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => -2,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => 'bool',
                    'type_token'      => 2,
                    'type_end_token'  => 2,
                    'nullable_type'   => false,
                    'name_token'      => 4,
                    'equal_token'     => 6,
                ],
            ],
            'public, typed: ?array, multi-constant, multi-line' => [
                'identifier' => '/* testMultiConstMultiLine */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => -2,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => '?array',
                    'type_token'      => 3,
                    'type_end_token'  => 3,
                    'nullable_type'   => true,
                    'name_token'      => 8,
                    'equal_token'     => 10,
                ],
            ],

            // Types which are only legal in enums.
            'final, typed: self' => [
                'identifier' => '/* testEnumConstTypedSelf */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => false,
                    'is_final'        => true,
                    'final_token'     => -2,
                    'type'            => 'self',
                    'type_token'      => 2,
                    'type_end_token'  => 2,
                    'nullable_type'   => false,
                    'name_token'      => 4,
                    'equal_token'     => 6,
                ],
            ],
            'no modifiers, typed: static' => [
                'identifier' => '/* testEnumConstTypedNullableStatic */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => false,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => '?static',
                    'type_token'      => 3,
                    'type_end_token'  => 3,
                    'nullable_type'   => true,
                    'name_token'      => 5,
                    'equal_token'     => 7,
                ],
            ],

            // Illegal types, but that's not the concern of this method.
            'protected, typed: ?callable (not supported in PHP)' => [
                'identifier' => '/* testTypedNullableCallable */',
                'expected'   => [
                    'scope'           => 'protected',
                    'scope_token'     => -2,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => '?callable',
                    'type_token'      => 3,
                    'type_end_token'  => 3,
                    'nullable_type'   => true,
                    'name_token'      => 5,
                    'equal_token'     => 7,
                ],
            ],
            'final public, typed: void (not supported in PHP)' => [
                'identifier' => '/* testTypedVoid */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => -2,
                    'is_final'        => true,
                    'final_token'     => -4,
                    'type'            => 'void',
                    'type_token'      => 2,
                    'type_end_token'  => 2,
                    'nullable_type'   => false,
                    'name_token'      => 4,
                    'equal_token'     => 6,
                ],
            ],
            'private, typed: never (not supported in PHP)' => [
                'identifier' => '/* testTypedNever */',
                'expected'   => [
                    'scope'           => 'private',
                    'scope_token'     => -2,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => 'never',
                    'type_token'      => 2,
                    'type_end_token'  => 2,
                    'nullable_type'   => false,
                    'name_token'      => 4,
                    'equal_token'     => 6,
                ],
            ],

            // Union types.
            'no modifiers, typed: true|null' => [
                'identifier' => '/* testTypedUnionTrueNull */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => false,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => 'true|null',
                    'type_token'      => 2,
                    'type_end_token'  => 4,
                    'nullable_type'   => false,
                    'name_token'      => 8,
                    'equal_token'     => 10,
                ],
            ],
            'final, typed: array|object, with multi-line atribute' => [
                'identifier' => '/* testTypedUnionArrayObjectWithMultilineAttribute */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => false,
                    'is_final'        => true,
                    'final_token'     => -2,
                    'type'            => 'array|object',
                    'type_token'      => 2,
                    'type_end_token'  => 4,
                    'nullable_type'   => false,
                    'name_token'      => 6,
                    'equal_token'     => 8,
                ],
            ],
            'no modifiers, typed: string|array|int, with whitespace in type' => [
                'identifier' => '/* testTypedUnionStringArrayInt */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => false,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => 'string|array|int',
                    'type_token'      => 2,
                    'type_end_token'  => 10,
                    'nullable_type'   => false,
                    'name_token'      => 12,
                    'equal_token'     => 14,
                ],
            ],
            'public final, typed: ?float|bool|array, illegal nullable union type' => [
                'identifier' => '/* testTypedUnionFloatBoolArrayIllegalNullable */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => -4,
                    'is_final'        => true,
                    'final_token'     => -2,
                    'type'            => '?float|bool|array',
                    'type_token'      => 3,
                    'type_end_token'  => 10,
                    'nullable_type'   => true,
                    'name_token'      => 12,
                    'equal_token'     => 14,
                ],
            ],
            'no modifiers, typed: iterable|false' => [
                'identifier' => '/* testTypedUnionIterableFalse */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => false,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => 'iterable|false',
                    'type_token'      => 2,
                    'type_end_token'  => 4,
                    'nullable_type'   => false,
                    'name_token'      => 6,
                    'equal_token'     => 8,
                ],
            ],
            'final protected, typed: Unqualified|namespace\Relative, with whitespace and comments in type' => [
                'identifier' => '/* testTypedUnionUnqualifiedNamespaceRelativeWithWhiteSpaceAndComments */',
                'expected'   => [
                    'scope'           => 'protected',
                    'scope_token'     => -2,
                    'is_final'        => true,
                    'final_token'     => -4,
                    'type'            => 'Unqualified|namespace\Relative',
                    'type_token'      => 2,
                    'type_end_token'  => ($php8Names === true) ? 8 : 10,
                    'nullable_type'   => false,
                    'name_token'      => ($php8Names === true) ? 10 : 12,
                    'equal_token'     => ($php8Names === true) ? 12 : 14,
                ],
            ],
            'private, typed: \Fully\Qualified|Partially\Qualified' => [
                'identifier' => '/* testTypedUnionFullyQualifiedPartiallyQualified */',
                'expected'   => [
                    'scope'           => 'private',
                    'scope_token'     => -4,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => '\Fully\Qualified|Partially\Qualified',
                    'type_token'      => 2,
                    'type_end_token'  => ($php8Names === true) ? 4 : 9,
                    'nullable_type'   => false,
                    'name_token'      => ($php8Names === true) ? 6 : 11,
                    'equal_token'     => ($php8Names === true) ? 8 : 13,
                ],
            ],

            // Intersection types.
            'final, typed: Unqualified|namespace\Relative, with whitespace and comments in type' => [
                'identifier' => '/* testTypedIntersectUnqualifiedNamespaceRelative */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => false,
                    'is_final'        => true,
                    'final_token'     => -2,
                    'type'            => 'Unqualified&namespace\Relative',
                    'type_token'      => 2,
                    'type_end_token'  => ($php8Names === true) ? 4 : 6,
                    'nullable_type'   => false,
                    'name_token'      => ($php8Names === true) ? 6 : 8,
                    'equal_token'     => ($php8Names === true) ? 8 : 10,
                ],
            ],
            'protected, typed: \Fully\Qualified|Partially\Qualified' => [
                'identifier' => '/* testTypedIntersectFullyQualifiedPartiallyQualified */',
                'expected'   => [
                    'scope'           => 'protected',
                    'scope_token'     => -2,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => '\Fully\Qualified&Partially\Qualified',
                    'type_token'      => 2,
                    'type_end_token'  => ($php8Names === true) ? 4 : 9,
                    'nullable_type'   => false,
                    'name_token'      => ($php8Names === true) ? 6 : 11,
                    'equal_token'     => ($php8Names === true) ? 8 : 13,
                ],
            ],

            // DNF types.
            'protected, DNF typed: float|(Partially\Qualified&Traversable)' => [
                'identifier' => '/* testPHP82DNFType */',
                'expected'   => [
                    'scope'           => 'protected',
                    'scope_token'     => -2,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => 'float|(Partially\Qualified&Traversable)',
                    'type_token'      => 2,
                    'type_end_token'  => ($php8Names === true) ? 8 : 10,
                    'nullable_type'   => false,
                    'name_token'      => ($php8Names === true) ? 10 : 12,
                    'equal_token'     => ($php8Names === true) ? 12 : 14,
                ],
            ],
            'public final, DNF typed: (Unqualified&namespace\Relative)|bool, with whitespace and comments in type' => [
                'identifier' => '/* testPHP82DNFTypeWithWhiteSpaceAndComment */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => -4,
                    'is_final'        => true,
                    'final_token'     => -2,
                    'type'            => '(Unqualified&namespace\Relative)|bool',
                    'type_token'      => 2,
                    'type_end_token'  => ($php8Names === true) ? 16 : 18,
                    'nullable_type'   => false,
                    'name_token'      => ($php8Names === true) ? 18 : 20,
                    'equal_token'     => ($php8Names === true) ? 20 : 22,
                ],
            ],
            'no modifiers, DNF typed: ?(A&\Pck\B)|bool, with whitespace and comments in type' => [
                'identifier' => '/* testPHP82DNFTypeIllegalNullable */',
                'expected'   => [
                    'scope'           => 'public',
                    'scope_token'     => false,
                    'is_final'        => false,
                    'final_token'     => false,
                    'type'            => '?(A&\Pck\B)|bool',
                    'type_token'      => 3,
                    'type_end_token'  => ($php8Names === true) ? 9 : 12,
                    'nullable_type'   => true,
                    'name_token'      => ($php8Names === true) ? 11 : 14,
                    'equal_token'     => ($php8Names === true) ? 13 : 16,
                ],
            ],
        ];
    }

    /**
     * Verify that the build-in caching is used when caching is enabled.
     *
     * @return void
     */
    public function testResultIsCached()
    {
        $methodName = 'PHPCSUtils\\Utils\\Constants::getProperties';
        $cases      = self::dataGetProperties();
        $identifier = $cases['public final, typed: ?float|bool|array, illegal nullable union type']['identifier'];
        $expected   = $cases['public final, typed: ?float|bool|array, illegal nullable union type']['expected'];

        $const    = $this->getTargetToken($identifier, \T_CONST);
        $expected = $this->updateExpectedTokenPositions($const, $expected);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = Constants::getProperties(self::$phpcsFile, $const);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, $const);
        $resultSecondRun = Constants::getProperties(self::$phpcsFile, $const);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }

    /**
     * Test helper to translate token offsets to absolute positions in an "expected" array.
     *
     * @param int                            $targetPtr The token pointer to the target token from which
     *                                                  the offset is calculated.
     * @param array<string, string|int|bool> $expected  The expected function output containing offsets.
     *
     * @return array<string, string|int|bool>
     */
    private function updateExpectedTokenPositions($targetPtr, $expected)
    {
        if (\is_int($expected['scope_token']) === true) {
            $expected['scope_token'] += $targetPtr;
        }
        if (\is_int($expected['final_token']) === true) {
            $expected['final_token'] += $targetPtr;
        }
        if (\is_int($expected['type_token']) === true) {
            $expected['type_token'] += $targetPtr;
        }
        if (\is_int($expected['type_end_token']) === true) {
            $expected['type_end_token'] += $targetPtr;
        }
        if (\is_int($expected['name_token']) === true) {
            $expected['name_token'] += $targetPtr;
        }
        if (\is_int($expected['equal_token'])) {
            $expected['equal_token'] += $targetPtr;
        }

        return $expected;
    }
}
