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
     * @return void
     */
    public function testNotAFunctionException()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_FUNCTION or T_CLOSURE');

        $next = $this->getTargetToken('/* testNotAFunction */', T_RETURN);
        BCFile::getMethodProperties(self::$phpcsFile, $next);
    }

    /**
     * Test a basic function.
     *
     * @return void
     */
    public function testBasicFunction()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '',
            'return_type_token'    => false,
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
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
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => 'array',
            'return_type_token'    => 11, // Offset from the T_FUNCTION token.
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
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
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => 'int',
            'return_type_token'    => 8, // Offset from the T_FUNCTION token.
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
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
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '',
            'return_type_token'    => false,
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
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
            'scope'                => 'private',
            'scope_specified'      => true,
            'return_type'          => '',
            'return_type_token'    => false,
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => true,
            'has_body'             => true,
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
            'scope'                => 'public',
            'scope_specified'      => true,
            'return_type'          => '',
            'return_type_token'    => false,
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => true,
            'is_static'            => false,
            'has_body'             => true,
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
            'scope'                => 'protected',
            'scope_specified'      => true,
            'return_type'          => 'int',
            'return_type_token'    => 8, // Offset from the T_FUNCTION token.
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
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
            'scope'                => 'public',
            'scope_specified'      => true,
            'return_type'          => 'array',
            'return_type_token'    => 7, // Offset from the T_FUNCTION token.
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
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
            'scope'                => 'public',
            'scope_specified'      => true,
            'return_type'          => '?array',
            'return_type_token'    => 8, // Offset from the T_FUNCTION token.
            'nullable_return_type' => true,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
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
            'scope'                => 'public',
            'scope_specified'      => true,
            'return_type'          => '?array',
            'return_type_token'    => 18, // Offset from the T_FUNCTION token.
            'nullable_return_type' => true,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
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
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '\MyNamespace\MyClass',
            'return_type_token'    => 7, // Offset from the T_FUNCTION token.
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
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
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '\MyNamespace\MyClass\Foo',
            'return_type_token'    => 7, // Offset from the T_FUNCTION token.
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
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
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '',
            'return_type_token'    => false,
            'nullable_return_type' => false,
            'is_abstract'          => true,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => false,
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
            'scope'                => 'protected',
            'scope_specified'      => true,
            'return_type'          => 'bool',
            'return_type_token'    => 7, // Offset from the T_FUNCTION token.
            'nullable_return_type' => false,
            'is_abstract'          => true,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => false,
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
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '',
            'return_type_token'    => false,
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => false,
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
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => 'array',
            'return_type_token'    => 8, // Offset from the T_FUNCTION token.
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getMethodPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test helper.
     *
     * @param string $commentString The comment which preceeds the test.
     * @param array  $expected      The expected function output.
     *
     * @return void
     */
    protected function getMethodPropertiesTestHelper($commentString, $expected)
    {
        $function = $this->getTargetToken($commentString, [T_FUNCTION, T_CLOSURE]);
        $found    = BCFile::getMethodProperties(self::$phpcsFile, $function);

        if ($expected['return_type_token'] !== false) {
            $expected['return_type_token'] += $function;
        }

        $this->assertSame($expected, $found);
    }
}
