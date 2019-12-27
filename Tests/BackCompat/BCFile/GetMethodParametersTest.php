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
 * @author    Juliette Reinders Folmer <jrf@phpcodesniffer.info>
 *
 * With documentation contributions from:
 * @author    Diogo Oliveira de Melo <dmelo87@gmail.com>
 * @author    George Mponos <gmponos@gmail.com>
 *
 * @copyright 2010-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHPCSUtils\Tests\BackCompat\BCFile;

use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::getMethodParameters method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::getMethodParameters
 *
 * @since 1.0.0
 */
class GetMethodParametersTest extends UtilityMethodTestCase
{

    /**
     * Test receiving an expected exception when a non function/use token is passed.
     *
     * @return void
     */
    public function testUnexpectedTokenException()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_FUNCTION or T_CLOSURE or T_USE');

        $next = $this->getTargetToken('/* testNotAFunction */', [T_INTERFACE]);
        BCFile::getMethodParameters(self::$phpcsFile, $next);
    }

    /**
     * Test receiving an expected exception when a non-closure use token is passed.
     *
     * @dataProvider dataInvalidUse
     *
     * @param string $identifier The comment which preceeds the test.
     *
     * @return void
     */
    public function testInvalidUse($identifier)
    {
        $this->expectPhpcsException('$stackPtr was not a valid T_USE');

        $use = $this->getTargetToken($identifier, [T_USE]);
        BCFile::getMethodParameters(self::$phpcsFile, $use);
    }

    /**
     * Data Provider.
     *
     * @see testInvalidUse() For the array format.
     *
     * @return array
     */
    public function dataInvalidUse()
    {
        return [
            'ImportUse'      => ['/* testImportUse */'],
            'ImportGroupUse' => ['/* testImportGroupUse */'],
            'TraitUse'       => ['/* testTraitUse */'],
            'InvalidUse'     => ['/* testInvalidUse */'],
        ];
    }

    /**
     * Test receiving an empty array when there are no parameters.
     *
     * @dataProvider dataNoParams
     *
     * @param string $commentString   The comment which preceeds the test.
     * @param array  $targetTokenType Optional. The token type to search for after $commentString.
     *                                Defaults to the function/closure tokens.
     *
     * @return void
     */
    public function testNoParams($commentString, $targetTokenType = [T_FUNCTION, T_CLOSURE])
    {
        $target = $this->getTargetToken($commentString, $targetTokenType);
        $result = BCFile::getMethodParameters(self::$phpcsFile, $target);

        $this->assertSame([], $result);
    }

    /**
     * Data Provider.
     *
     * @see testNoParams() For the array format.
     *
     * @return array
     */
    public function dataNoParams()
    {
        return [
            'FunctionNoParams'   => ['/* testFunctionNoParams */'],
            'ClosureNoParams'    => ['/* testClosureNoParams */'],
            'ClosureUseNoParams' => ['/* testClosureUseNoParams */', T_USE],
        ];
    }

    /**
     * Verify pass-by-reference parsing.
     *
     * @return void
     */
    public function testPassByReference()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 5, // Offset from the T_FUNCTION token.
            'name'                => '$var',
            'content'             => '&$var',
            'pass_by_reference'   => true,
            'reference_token'     => 4, // Offset from the T_FUNCTION token.
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify array hint parsing.
     *
     * @return void
     */
    public function testArrayHint()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 6, // Offset from the T_FUNCTION token.
            'name'                => '$var',
            'content'             => 'array $var',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'array',
            'type_hint_token'     => 4, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 4, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify type hint parsing.
     *
     * @return void
     */
    public function testTypeHint()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 6, // Offset from the T_FUNCTION token.
            'name'                => '$var1',
            'content'             => 'foo $var1',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'foo',
            'type_hint_token'     => 4, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 4, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => 7, // Offset from the T_FUNCTION token.
        ];

        $expected[1] = [
            'token'               => 11, // Offset from the T_FUNCTION token.
            'name'                => '$var2',
            'content'             => 'bar $var2',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'bar',
            'type_hint_token'     => 9, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 9, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify self type hint parsing.
     *
     * @return void
     */
    public function testSelfTypeHint()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 6, // Offset from the T_FUNCTION token.
            'name'                => '$var',
            'content'             => 'self $var',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'self',
            'type_hint_token'     => 4, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 4, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify nullable type hint parsing.
     *
     * @return void
     */
    public function testNullableTypeHint()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 7, // Offset from the T_FUNCTION token.
            'name'                => '$var1',
            'content'             => '?int $var1',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?int',
            'type_hint_token'     => 5, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 5, // Offset from the T_FUNCTION token.
            'nullable_type'       => true,
            'comma_token'         => 8, // Offset from the T_FUNCTION token.
        ];

        $expected[1] = [
            'token'               => 14, // Offset from the T_FUNCTION token.
            'name'                => '$var2',
            'content'             => '?\bar $var2',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?\bar',
            'type_hint_token'     => 11, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 12, // Offset from the T_FUNCTION token.
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify variable.
     *
     * @return void
     */
    public function testVariable()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 4, // Offset from the T_FUNCTION token.
            'name'                => '$var',
            'content'             => '$var',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify default value parsing with a single function param.
     *
     * @return void
     */
    public function testSingleDefaultValue()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 4, // Offset from the T_FUNCTION token.
            'name'                => '$var1',
            'content'             => '$var1=self::CONSTANT',
            'default'             => 'self::CONSTANT',
            'default_token'       => 6, // Offset from the T_FUNCTION token.
            'default_equal_token' => 5, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify default value parsing.
     *
     * @return void
     */
    public function testDefaultValues()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 4, // Offset from the T_FUNCTION token.
            'name'                => '$var1',
            'content'             => '$var1=1',
            'default'             => '1',
            'default_token'       => 6, // Offset from the T_FUNCTION token.
            'default_equal_token' => 5, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 7, // Offset from the T_FUNCTION token.
        ];
        $expected[1] = [
            'token'               => 9, // Offset from the T_FUNCTION token.
            'name'                => '$var2',
            'content'             => "\$var2='value'",
            'default'             => "'value'",
            'default_token'       => 11, // Offset from the T_FUNCTION token.
            'default_equal_token' => 10, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify "bitwise and" in default value !== pass-by-reference.
     *
     * @return void
     */
    public function testBitwiseAndConstantExpressionDefaultValue()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 4, // Offset from the T_FUNCTION token.
            'name'                => '$a',
            'content'             => '$a = 10 & 20',
            'default'             => '10 & 20',
            'default_token'       => 8, // Offset from the T_FUNCTION token.
            'default_equal_token' => 6, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify default value parsing with array values.
     *
     * @return void
     */
    public function testArrayDefaultValues()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 4, // Offset from the T_FUNCTION token.
            'name'                => '$var1',
            'content'             => '$var1 = []',
            'default'             => '[]',
            'default_token'       => 8, // Offset from the T_FUNCTION token.
            'default_equal_token' => 6, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 10, // Offset from the T_FUNCTION token.
        ];
        $expected[1] = [
            'token'               => 12, // Offset from the T_FUNCTION token.
            'name'                => '$var2',
            'content'             => '$var2 = array(1, 2, 3)',
            'default'             => 'array(1, 2, 3)',
            'default_token'       => 16, // Offset from the T_FUNCTION token.
            'default_equal_token' => 14, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify having a T_STRING constant as a default value for the second parameter.
     *
     * @return void
     */
    public function testConstantDefaultValueSecondParam()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 4, // Offset from the T_FUNCTION token.
            'name'                => '$var1',
            'content'             => '$var1',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 5, // Offset from the T_FUNCTION token.
        ];
        $expected[1] = [
            'token'               => 7, // Offset from the T_FUNCTION token.
            'name'                => '$var2',
            'content'             => '$var2 = M_PI',
            'default'             => 'M_PI',
            'default_token'       => 11, // Offset from the T_FUNCTION token.
            'default_equal_token' => 9, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify distinquishing between a nullable type and a ternary within a default expression.
     *
     * @return void
     */
    public function testScalarTernaryExpressionInDefault()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 5, // Offset from the T_FUNCTION token.
            'name'                => '$a',
            'content'             => '$a = FOO ? \'bar\' : 10',
            'default'             => 'FOO ? \'bar\' : 10',
            'default_token'       => 9, // Offset from the T_FUNCTION token.
            'default_equal_token' => 7, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 18, // Offset from the T_FUNCTION token.
        ];
        $expected[1] = [
            'token'               => 24, // Offset from the T_FUNCTION token.
            'name'                => '$b',
            'content'             => '? bool $b',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?bool',
            'type_hint_token'     => 22, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 22, // Offset from the T_FUNCTION token.
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify a variadic parameter being recognized correctly.
     *
     * @return void
     */
    public function testVariadicFunction()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 9, // Offset from the T_FUNCTION token.
            'name'                => '$a',
            'content'             => 'int ... $a',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => true,
            'variadic_token'      => 7, // Offset from the T_FUNCTION token.
            'type_hint'           => 'int',
            'type_hint_token'     => 5, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 5, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify a variadic parameter passed by reference being recognized correctly.
     *
     * @return void
     */
    public function testVariadicByRefFunction()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 7, // Offset from the T_FUNCTION token.
            'name'                => '$a',
            'content'             => '&...$a',
            'pass_by_reference'   => true,
            'reference_token'     => 5, // Offset from the T_FUNCTION token.
            'variable_length'     => true,
            'variadic_token'      => 6, // Offset from the T_FUNCTION token.
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify handling of a variadic parameter with a class based type declaration.
     *
     * @return void
     */
    public function testVariadicFunctionClassType()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 4, // Offset from the T_FUNCTION token.
            'name'                => '$unit',
            'content'             => '$unit',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 5, // Offset from the T_FUNCTION token.
        ];
        $expected[1] = [
            'token'               => 10, // Offset from the T_FUNCTION token.
            'name'                => '$intervals',
            'content'             => 'DateInterval ...$intervals',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => true,
            'variadic_token'      => 9,
            'type_hint'           => 'DateInterval',
            'type_hint_token'     => 7, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 7, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify distinquishing between a nullable type and a ternary within a default expression.
     *
     * @return void
     */
    public function testNameSpacedTypeDeclaration()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 12, // Offset from the T_FUNCTION token.
            'name'                => '$a',
            'content'             => '\Package\Sub\ClassName $a',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '\Package\Sub\ClassName',
            'type_hint_token'     => 5, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 10, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => 13, // Offset from the T_FUNCTION token.
        ];
        $expected[1] = [
            'token'               => 20, // Offset from the T_FUNCTION token.
            'name'                => '$b',
            'content'             => '?Sub\AnotherClass $b',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?Sub\AnotherClass',
            'type_hint_token'     => 16, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 18, // Offset from the T_FUNCTION token.
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify correctly recognizing all type declarations supported by PHP.
     *
     * @return void
     */
    public function testWithAllTypes()
    {
        $expected     = [];
        $expected[0]  = [
            'token'               => 9, // Offset from the T_FUNCTION token.
            'name'                => '$a',
            'content'             => '?ClassName $a',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?ClassName',
            'type_hint_token'     => 7, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 7, // Offset from the T_FUNCTION token.
            'nullable_type'       => true,
            'comma_token'         => 10, // Offset from the T_FUNCTION token.
        ];
        $expected[1]  = [
            'token'               => 15, // Offset from the T_FUNCTION token.
            'name'                => '$b',
            'content'             => 'self $b',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'self',
            'type_hint_token'     => 13, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 13, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => 16, // Offset from the T_FUNCTION token.
        ];
        $expected[2]  = [
            'token'               => 21, // Offset from the T_FUNCTION token.
            'name'                => '$c',
            'content'             => 'parent $c',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'parent',
            'type_hint_token'     => 19, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 19, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => 22, // Offset from the T_FUNCTION token.
        ];
        $expected[3]  = [
            'token'               => 27, // Offset from the T_FUNCTION token.
            'name'                => '$d',
            'content'             => 'object $d',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'object',
            'type_hint_token'     => 25, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 25, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => 28, // Offset from the T_FUNCTION token.
        ];
        $expected[4]  = [
            'token'               => 34, // Offset from the T_FUNCTION token.
            'name'                => '$e',
            'content'             => '?int $e',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?int',
            'type_hint_token'     => 32, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 32, // Offset from the T_FUNCTION token.
            'nullable_type'       => true,
            'comma_token'         => 35, // Offset from the T_FUNCTION token.
        ];
        $expected[5]  = [
            'token'               => 41, // Offset from the T_FUNCTION token.
            'name'                => '$f',
            'content'             => 'string &$f',
            'pass_by_reference'   => true,
            'reference_token'     => 40, // Offset from the T_FUNCTION token.
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'string',
            'type_hint_token'     => 38, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 38, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => 42, // Offset from the T_FUNCTION token.
        ];
        $expected[6]  = [
            'token'               => 47, // Offset from the T_FUNCTION token.
            'name'                => '$g',
            'content'             => 'iterable $g',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'iterable',
            'type_hint_token'     => 45, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 45, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => 48, // Offset from the T_FUNCTION token.
        ];
        $expected[7]  = [
            'token'               => 53, // Offset from the T_FUNCTION token.
            'name'                => '$h',
            'content'             => 'bool $h = true',
            'default'             => 'true',
            'default_token'       => 57, // Offset from the T_FUNCTION token.
            'default_equal_token' => 55, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'bool',
            'type_hint_token'     => 51, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 51, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => 58, // Offset from the T_FUNCTION token.
        ];
        $expected[8]  = [
            'token'               => 63, // Offset from the T_FUNCTION token.
            'name'                => '$i',
            'content'             => 'callable $i = \'is_null\'',
            'default'             => "'is_null'",
            'default_token'       => 67, // Offset from the T_FUNCTION token.
            'default_equal_token' => 65, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'callable',
            'type_hint_token'     => 61, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 61, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => 68, // Offset from the T_FUNCTION token.
        ];
        $expected[9]  = [
            'token'               => 73, // Offset from the T_FUNCTION token.
            'name'                => '$j',
            'content'             => 'float $j = 1.1',
            'default'             => '1.1',
            'default_token'       => 77, // Offset from the T_FUNCTION token.
            'default_equal_token' => 75, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'float',
            'type_hint_token'     => 71, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 71, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => 78, // Offset from the T_FUNCTION token.
        ];
        $expected[10] = [
            'token'               => 84, // Offset from the T_FUNCTION token.
            'name'                => '$k',
            'content'             => 'array ...$k',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => true,
            'variadic_token'      => 83, // Offset from the T_FUNCTION token.
            'type_hint'           => 'array',
            'type_hint_token'     => 81, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 81, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify handling of a closure.
     *
     * @return void
     */
    public function testMessyDeclaration()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 25, // Offset from the T_FUNCTION token.
            'name'                => '$a',
            'content'             => '// comment
    ?\MyNS /* comment */
        \ SubCat // phpcs:ignore Standard.Cat.Sniff -- for reasons.
            \  MyClass $a',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?\MyNS\SubCat\MyClass',
            'type_hint_token'     => 9,
            'type_hint_end_token' => 23,
            'nullable_type'       => true,
            'comma_token'         => 26, // Offset from the T_FUNCTION token.
        ];
        $expected[1] = [
            'token'               => 29, // Offset from the T_FUNCTION token.
            'name'                => '$b',
            'content'             => "\$b /* test */ = /* test */ 'default' /* test*/",
            'default'             => "'default' /* test*/",
            'default_token'       => 37, // Offset from the T_FUNCTION token.
            'default_equal_token' => 33, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 40, // Offset from the T_FUNCTION token.
        ];
        $expected[2] = [
            'token'               => 62, // Offset from the T_FUNCTION token.
            'name'                => '$c',
            'content'             => '// phpcs:ignore Stnd.Cat.Sniff -- For reasons.
    ? /*comment*/
        bool // phpcs:disable Stnd.Cat.Sniff -- For reasons.
        & /*test*/ ... /* phpcs:ignore */ $c',
            'pass_by_reference'   => true,
            'reference_token'     => 54, // Offset from the T_FUNCTION token.
            'variable_length'     => true,
            'variadic_token'      => 58, // Offset from the T_FUNCTION token.
            'type_hint'           => '?bool',
            'type_hint_token'     => 50, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 50, // Offset from the T_FUNCTION token.
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify handling of a closure.
     *
     * @return void
     */
    public function testClosure()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 3, // Offset from the T_FUNCTION token.
            'name'                => '$a',
            'content'             => '$a = \'test\'',
            'default'             => "'test'",
            'default_token'       => 7, // Offset from the T_FUNCTION token.
            'default_equal_token' => 5, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify handling of a closure T_USE token correctly.
     *
     * @return void
     */
    public function testClosureUse()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 3, // Offset from the T_USE token.
            'name'                => '$foo',
            'content'             => '$foo',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => 4, // Offset from the T_USE token.
        ];
        $expected[1] = [
            'token'               => 6, // Offset from the T_USE token.
            'name'                => '$bar',
            'content'             => '$bar',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected, [T_USE]);
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
    private function getMethodParametersTestHelper($commentString, $expected, $targetType = [T_FUNCTION, T_CLOSURE])
    {
        $target = $this->getTargetToken($commentString, $targetType);
        $found  = BCFile::getMethodParameters(self::$phpcsFile, $target);

        foreach ($expected as $key => $param) {
            $expected[$key]['token'] += $target;

            if ($param['reference_token'] !== false) {
                $expected[$key]['reference_token'] += $target;
            }
            if ($param['variadic_token'] !== false) {
                $expected[$key]['variadic_token'] += $target;
            }
            if ($param['type_hint_token'] !== false) {
                $expected[$key]['type_hint_token'] += $target;
            }
            if ($param['type_hint_end_token'] !== false) {
                $expected[$key]['type_hint_end_token'] += $target;
            }
            if ($param['comma_token'] !== false) {
                $expected[$key]['comma_token'] += $target;
            }
            if (isset($param['default_token'])) {
                $expected[$key]['default_token'] += $target;
            }
            if (isset($param['default_equal_token'])) {
                $expected[$key]['default_equal_token'] += $target;
            }
        }

        $this->assertSame($expected, $found);
    }
}
