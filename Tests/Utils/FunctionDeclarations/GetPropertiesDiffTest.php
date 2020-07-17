<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\FunctionDeclarations;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\FunctionDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\FunctionDeclarations::getProperties method.
 *
 * The tests in this class cover the differences between the PHPCS native method and the PHPCSUtils
 * version. These tests would fail when using the BCFile `getMethodProperties()` method.
 *
 * @covers \PHPCSUtils\Utils\FunctionDeclarations::getProperties
 *
 * @group functiondeclarations
 *
 * @since 1.0.0
 */
class GetPropertiesDiffTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_FUNCTION or T_CLOSURE or an arrow function');

        FunctionDeclarations::getProperties(self::$phpcsFile, 10000);
    }

    /**
     * Test handling of the PHPCS 3.2.0+ annotations between the keywords.
     *
     * @return void
     */
    public function testMessyPhpcsAnnotationsMethod()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => true,
            'return_type'           => '',
            'return_type_token'     => false,
            'return_type_end_token' => false,
            'nullable_return_type'  => false,
            'is_abstract'           => true,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test handling of the PHPCS 3.2.0+ annotations between the keywords with a static closure.
     *
     * @return void
     */
    public function testMessyPhpcsAnnotationsStaticClosure()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '',
            'return_type_token'     => false,
            'return_type_end_token' => false,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => true,
            'has_body'              => true,
        ];

        $this->getPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test that the new "return_type_end_token" index is set correctly.
     *
     * @return void
     */
    public function testReturnTypeEndTokenIndex()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '?\MyNamespace\MyClass\Foo',
            'return_type_token'     => 8, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 20, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 union type declaration.
     *
     * @return void
     */
    public function testPHP8UnionTypesSimple()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'int|float',
            'return_type_token'     => 9, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 11, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 union type declaration with two classes.
     *
     * @return void
     */
    public function testPHP8UnionTypesTwoClasses()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'MyClassA|\Package\MyClassB',
            'return_type_token'     => 6, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 11, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $arrowTokenTypes = Collections::arrowFunctionTokensBC();

        $this->getPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected, $arrowTokenTypes);
    }

    /**
     * Verify recognition of PHP8 union type declaration with all base types.
     *
     * @return void
     */
    public function testPHP8UnionTypesAllBaseTypes()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'array|bool|callable|int|float|null|Object|string',
            'return_type_token'     => 8, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 22, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 union type declaration with all pseudo types.
     *
     * Note: "Resource" is not a type, but seen as a class name.
     *
     * @return void
     */
    public function testPHP8UnionTypesAllPseudoTypes()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'false|MIXED|self|parent|static|iterable|Resource|void',
            'return_type_token'     => 9, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 23, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 union type declaration with (illegal) nullability.
     *
     * @return void
     */
    public function testPHP8UnionTypesNullable()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '?int|float',
            'return_type_token'     => 12, // Offset from the T_CLOSURE token.
            'return_type_end_token' => 14, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 type declaration with (illegal) single type null.
     *
     * @return void
     */
    public function testPHP8PseudoTypeNull()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'null',
            'return_type_token'     => 7, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 7, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 type declaration with (illegal) single type false.
     *
     * @return void
     */
    public function testPHP8PseudoTypeFalse()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'false',
            'return_type_token'     => 7, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 7, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 type declaration with (illegal) type false combined with type bool.
     *
     * @return void
     */
    public function testPHP8PseudoTypeFalseAndBool()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'bool|false',
            'return_type_token'     => 7, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 9, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 type declaration with (illegal) type object combined with a class name.
     *
     * @return void
     */
    public function testPHP8ObjectAndClass()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'object|ClassName',
            'return_type_token'     => 7, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 9, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 type declaration with (illegal) type iterable combined with array/Traversable.
     *
     * @return void
     */
    public function testPHP8PseudoTypeIterableAndArray()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => true,
            'return_type'           => 'iterable|array|Traversable',
            'return_type_token'     => 7, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 11, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => false,
        ];

        $this->getPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 type declaration with (illegal) duplicate types.
     *
     * @return void
     */
    public function testPHP8DuplicateTypeInUnionWhitespaceAndComment()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'int|string|INT',
            'return_type_token'     => 7, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 17, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test helper.
     *
     * @param string $commentString The comment which preceeds the test.
     * @param array  $expected      The expected function output.
     * @param array  $targetType    Optional. The token type to search for after $commentString.
     *                              Defaults to the function/closure tokens.
     *
     * @return void
     */
    protected function getPropertiesTestHelper($commentString, $expected, $targetType = [\T_FUNCTION, \T_CLOSURE])
    {
        $function = $this->getTargetToken($commentString, $targetType);
        $found    = FunctionDeclarations::getProperties(self::$phpcsFile, $function);

        if ($expected['return_type_token'] !== false) {
            $expected['return_type_token'] += $function;
        }
        if ($expected['return_type_end_token'] !== false) {
            $expected['return_type_end_token'] += $function;
        }

        $this->assertSame($expected, $found);
    }
}
