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
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Chris Wilkinson <c.wilkinson@elifesciences.org>
 * @author    Juliette Reinders Folmer <jrf@phpcodesniffer.info>
 *
 * @copyright 2018-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHPCSUtils\Tests\BackCompat\BCFile;

use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::getMethodProperties method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::getMethodProperties
 *
 * @group functiondeclarations
 *
 * @since 1.0.0
 */
class GetMethodPropertiesTest extends UtilityMethodTestCase
{

    /**
     * Test receiving an expected exception when a non function token is passed.
     *
     * @dataProvider dataNotAFunctionException
     *
     * @param string $commentString   The comment which preceeds the test.
     * @param array  $targetTokenType The token type to search for after $commentString.
     *
     * @return void
     */
    public function testNotAFunctionException($commentString, $targetTokenType)
    {
        $this->expectPhpcsException('$stackPtr must be of type T_FUNCTION or T_CLOSURE or T_FN');

        $next = $this->getTargetToken($commentString, $targetTokenType);
        BCFile::getMethodProperties(self::$phpcsFile, $next);
    }

    /**
     * Data Provider.
     *
     * @see testNotAFunctionException() For the array format.
     *
     * @return array
     */
    public function dataNotAFunctionException()
    {
        return [
            'return' => [
                '/* testNotAFunction */',
                T_RETURN,
            ],
            'function-call-fn-phpcs-3.5.3-3.5.4' => [
                '/* testFunctionCallFnPHPCS353-354 */',
                [T_FN, T_STRING],
            ],
            'fn-live-coding' => [
                '/* testArrowFunctionLiveCoding */',
                [T_FN, T_STRING],
            ],
        ];
    }

    /**
     * Test a basic function.
     *
     * @return void
     */
    public function testBasicFunction()
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
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a function with a return type.
     *
     * @return void
     */
    public function testReturnFunction()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'array',
            'return_type_token'     => 11, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 11, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a closure used as a function argument.
     *
     * @return void
     */
    public function testNestedClosure()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'int',
            'return_type_token'     => 8, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 8, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a basic method.
     *
     * @return void
     */
    public function testBasicMethod()
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
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a private static method.
     *
     * @return void
     */
    public function testPrivateStaticMethod()
    {
        $expected = [
            'scope'                 => 'private',
            'scope_specified'       => true,
            'return_type'           => '',
            'return_type_token'     => false,
            'return_type_end_token' => false,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => true,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a basic final method.
     *
     * @return void
     */
    public function testFinalMethod()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => true,
            'return_type'           => '',
            'return_type_token'     => false,
            'return_type_end_token' => false,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => true,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a protected method with a return type.
     *
     * @return void
     */
    public function testProtectedReturnMethod()
    {
        $expected = [
            'scope'                 => 'protected',
            'scope_specified'       => true,
            'return_type'           => 'int',
            'return_type_token'     => 8, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 8, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a public method with a return type.
     *
     * @return void
     */
    public function testPublicReturnMethod()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => true,
            'return_type'           => 'array',
            'return_type_token'     => 7, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 7, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a public method with a nullable return type.
     *
     * @return void
     */
    public function testNullableReturnMethod()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => true,
            'return_type'           => '?array',
            'return_type_token'     => 8, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 8, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a public method with a nullable return type.
     *
     * @return void
     */
    public function testMessyNullableReturnMethod()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => true,
            'return_type'           => '?array',
            'return_type_token'     => 18, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 18, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a method with a namespaced return type.
     *
     * @return void
     */
    public function testReturnNamespace()
    {
        $php8Names = parent::usesPhp8NameTokens();

        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '\MyNamespace\MyClass',
            'return_type_token'     => 7, // Offset from the T_FUNCTION token.
            'return_type_end_token' => ($php8Names === true) ? 7 : 10, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a method with a messy namespaces return type.
     *
     * @return void
     */
    public function testReturnMultilineNamespace()
    {
        $php8Names = parent::usesPhp8NameTokens();

        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '\MyNamespace\MyClass\Foo',
            'return_type_token'     => 7, // Offset from the T_FUNCTION token.
            'return_type_end_token' => ($php8Names === true) ? 20 : 23, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a method with an unqualified named return type.
     *
     * @return void
     */
    public function testReturnUnqualifiedName()
    {
        $expected = [
            'scope'                 => 'private',
            'scope_specified'       => true,
            'return_type'           => '?MyClass',
            'return_type_token'     => 8, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 8, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a method with a partially qualified namespaced return type.
     *
     * @return void
     */
    public function testReturnPartiallyQualifiedName()
    {
        $php8Names = parent::usesPhp8NameTokens();

        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'Sub\Level\MyClass',
            'return_type_token'     => 7, // Offset from the T_FUNCTION token.
            'return_type_end_token' => ($php8Names === true) ? 7 : 11, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a basic abstract method.
     *
     * @return void
     */
    public function testAbstractMethod()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '',
            'return_type_token'     => false,
            'return_type_end_token' => false,
            'nullable_return_type'  => false,
            'is_abstract'           => true,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => false,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test an abstract method with a return type.
     *
     * @return void
     */
    public function testAbstractReturnMethod()
    {
        $expected = [
            'scope'                 => 'protected',
            'scope_specified'       => true,
            'return_type'           => 'bool',
            'return_type_token'     => 7, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 7, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => true,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => false,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a basic interface method.
     *
     * @return void
     */
    public function testInterfaceMethod()
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
            'is_static'             => false,
            'has_body'              => false,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a static arrow function.
     *
     * @return void
     */
    public function testArrowFunction()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'int',
            'return_type_token'     => 9, // Offset from the T_FN token.
            'return_type_end_token' => 9, // Offset from the T_FN token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => true,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a function with return type "static".
     *
     * @return void
     */
    public function testReturnTypeStatic()
    {
        $expected = [
            'scope'                 => 'private',
            'scope_specified'       => true,
            'return_type'           => 'static',
            'return_type_token'     => 7, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 7, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a function with return type "mixed".
     *
     * @return void
     */
    public function testPHP8MixedTypeHint()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'mixed',
            'return_type_token'     => 7, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 7, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a function with return type "mixed" and nullability.
     *
     * @return void
     */
    public function testPHP8MixedTypeHintNullable()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '?mixed',
            'return_type_token'     => 8, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 8, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test a function with return type using the namespace operator.
     *
     * @return void
     */
    public function testNamespaceOperatorTypeHint()
    {
        $php8Names = parent::usesPhp8NameTokens();

        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '?namespace\Name',
            'return_type_token'     => 9, // Offset from the T_FUNCTION token.
            'return_type_end_token' => ($php8Names === true) ? 9 : 11, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
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

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 union type declaration with two classes.
     *
     * @return void
     */
    public function testPHP8UnionTypesTwoClasses()
    {
        $php8Names = parent::usesPhp8NameTokens();

        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'MyClassA|\Package\MyClassB',
            'return_type_token'     => 6, // Offset from the T_FUNCTION token.
            'return_type_end_token' => ($php8Names === true) ? 8 : 11, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
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

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
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

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
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
            'return_type_end_token' => 14, // Offset from the T_CLOSURE token.
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
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

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
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

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
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

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
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

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
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

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
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

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8.1 type "never".
     *
     * @return void
     */
    public function testPHP81NeverType()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'never',
            'return_type_token'     => 7, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 7, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8.1 type "never"  with (illegal) nullability.
     *
     * @return void
     */
    public function testPHP81NullableNeverType()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '?never',
            'return_type_token'     => 8, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 8, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test for incorrect tokenization of array return type declarations in PHPCS < 2.8.0.
     *
     * @link https://github.com/squizlabs/PHP_CodeSniffer/pull/1264
     *
     * @return void
     */
    public function testPhpcsIssue1264()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'array',
            'return_type_token'     => 8, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 8, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test handling of incorrect tokenization of array return type declarations for arrow functions
     * in a very specific code sample in PHPCS < 3.5.4.
     *
     * @link https://github.com/squizlabs/PHP_CodeSniffer/issues/2773
     *
     * @return void
     */
    public function testArrowFunctionArrayReturnValue()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'array',
            'return_type_token'     => 5, // Offset from the T_FN token.
            'return_type_end_token' => 5, // Offset from the T_FN token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test handling of an arrow function returning by reference.
     *
     * @return void
     */
    public function testArrowFunctionReturnByRef()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '?string',
            'return_type_token'     => 12, // Offset from the T_FN token.
            'return_type_end_token' => 12, // Offset from the T_FN token.
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test handling of function declaration nested in a ternary, where the colon for the
     * return type was incorrectly tokenized as T_INLINE_ELSE prior to PHPCS 3.5.7.
     *
     * @return void
     */
    public function testFunctionDeclarationNestedInTernaryPHPCS2975()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => true,
            'return_type'           => 'c',
            'return_type_token'     => 7, // Offset from the T_FN token.
            'return_type_end_token' => 7, // Offset from the T_FN token.
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
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
    protected function getMethodPropertiesTestHelper(
        $commentString,
        $expected,
        $targetType = [T_FUNCTION, T_CLOSURE, T_FN]
    ) {
        $function = $this->getTargetToken($commentString, $targetType);
        $found    = BCFile::getMethodProperties(self::$phpcsFile, $function);

        if ($expected['return_type_token'] !== false) {
            $expected['return_type_token'] += $function;
        }
        if ($expected['return_type_end_token'] !== false) {
            $expected['return_type_end_token'] += $function;
        }

        $this->assertSame($expected, $found);
    }
}
