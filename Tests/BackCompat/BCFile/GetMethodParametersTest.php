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
     * Test helper.
     *
     * @param string $commentString The comment which preceeds the test.
     * @param array  $expected      The expected function output.
     *
     * @return void
     */
    private function getMethodParametersTestHelper($commentString, $expected)
    {
        $function = $this->getTargetToken($commentString, [T_FUNCTION]);
        $found    = BCFile::getMethodParameters(self::$phpcsFile, $function);

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
