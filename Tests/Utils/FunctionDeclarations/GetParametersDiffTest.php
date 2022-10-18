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
use PHPCSUtils\Utils\FunctionDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\FunctionDeclarations::getParameters method.
 *
 * The tests in this class cover the differences between the PHPCS native method and the PHPCSUtils
 * version. These tests would fail when using the BCFile `getParameters()` method.
 *
 * @covers \PHPCSUtils\Utils\FunctionDeclarations::getParameters
 *
 * @group functiondeclarations
 *
 * @since 1.0.0
 */
final class GetParametersDiffTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_FUNCTION, T_CLOSURE or T_USE or an arrow function');

        FunctionDeclarations::getParameters(self::$phpcsFile, 10000);
    }

    /**
     * Verify recognition of PHP 8.2 stand-alone `true` type.
     *
     * @return void
     */
    public function testPHP82PseudoTypeTrue()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 7, // Offset from the T_FUNCTION token.
            'name'                => '$var',
            'content'             => '?true $var = true',
            'default'             => 'true',
            'default_token'       => 11, // Offset from the T_FUNCTION token.
            'default_equal_token' => 9,  // Offset from the T_FUNCTION token.
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?true',
            'type_hint_token'     => 5, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 5, // Offset from the T_FUNCTION token.
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP 8.2 type declaration with (illegal) type false combined with type true.
     *
     * @return void
     */
    public function testPHP82PseudoTypeFalseAndTrue()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 8, // Offset from the T_FUNCTION token.
            'name'                => '$var',
            'content'             => 'true|false $var = true',
            'default'             => 'true',
            'default_token'       => 12, // Offset from the T_FUNCTION token.
            'default_equal_token' => 10, // Offset from the T_FUNCTION token.
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'true|false',
            'type_hint_token'     => 4, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 6, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getMethodParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test helper.
     *
     * @param string $marker     The comment which preceeds the test.
     * @param array  $expected   The expected function output.
     * @param array  $targetType Optional. The token type to search for after $marker.
     *                           Defaults to the function/closure/arrow tokens.
     *
     * @return void
     */
    protected function getMethodParametersTestHelper($marker, $expected, $targetType = [\T_FUNCTION, \T_CLOSURE, \T_FN])
    {
        $target = $this->getTargetToken($marker, $targetType);
        $found  = FunctionDeclarations::getParameters(self::$phpcsFile, $target);

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
            if (isset($param['visibility_token'])) {
                $expected[$key]['visibility_token'] += $target;
            }
            if (isset($param['readonly_token'])) {
                $expected[$key]['readonly_token'] += $target;
            }
        }

        $this->assertSame($expected, $found);
    }
}
