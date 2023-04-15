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

use PHPCSUtils\Internal\Cache;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Tokens\Collections;
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
     * Verify recognition of PHP8 constructor with property promotion using PHP 8.1 readonly
     * keyword without explicit visibility.
     *
     * @return void
     */
    public function testPHP81ConstructorPropertyPromotionWithOnlyReadOnly()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 10, // Offset from the T_FUNCTION token.
            'name'                => '$promotedProp',
            'content'             => 'readonly Foo&Bar $promotedProp',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'Foo&Bar',
            'type_hint_token'     => 6, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 8, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'property_visibility' => 'public',
            'visibility_token'    => false,
            'property_readonly'   => true,
            'readonly_token'      => 4, // Offset from the T_FUNCTION token.
            'comma_token'         => 11,
        ];
        $expected[1] = [
            'token'               => 18, // Offset from the T_FUNCTION token.
            'name'                => '$promotedToo',
            'content'             => 'readonly ?bool $promotedToo',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?bool',
            'type_hint_token'     => 16, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 16, // Offset from the T_FUNCTION token.
            'nullable_type'       => true,
            'property_visibility' => 'public',
            'visibility_token'    => false,
            'property_readonly'   => true,
            'readonly_token'      => 13, // Offset from the T_FUNCTION token.
            'comma_token'         => 19,
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
        $target   = $this->getTargetToken($marker, $targetType);
        $found    = FunctionDeclarations::getParameters(self::$phpcsFile, $target);
        $expected = $this->updateExpectedTokenPositions($target, $expected);

        $this->assertSame($expected, $found);
    }

    /**
     * Test helper to translate token offsets to absolute positions in an "expected" array.
     *
     * @param string $targetPtr The token pointer to the target token from which the offset is calculated.
     * @param array  $expected  The expected function output containing offsets.
     *
     * @return array
     */
    private function updateExpectedTokenPositions($targetPtr, $expected)
    {
        foreach ($expected as $key => $param) {
            $expected[$key]['token'] += $targetPtr;

            if ($param['reference_token'] !== false) {
                $expected[$key]['reference_token'] += $targetPtr;
            }
            if ($param['variadic_token'] !== false) {
                $expected[$key]['variadic_token'] += $targetPtr;
            }
            if ($param['type_hint_token'] !== false) {
                $expected[$key]['type_hint_token'] += $targetPtr;
            }
            if ($param['type_hint_end_token'] !== false) {
                $expected[$key]['type_hint_end_token'] += $targetPtr;
            }
            if ($param['comma_token'] !== false) {
                $expected[$key]['comma_token'] += $targetPtr;
            }
            if (isset($param['default_token'])) {
                $expected[$key]['default_token'] += $targetPtr;
            }
            if (isset($param['default_equal_token'])) {
                $expected[$key]['default_equal_token'] += $targetPtr;
            }
            if (isset($param['visibility_token']) && $param['visibility_token'] !== false) {
                $expected[$key]['visibility_token'] += $targetPtr;
            }
            if (isset($param['readonly_token'])) {
                $expected[$key]['readonly_token'] += $targetPtr;
            }
        }

        return $expected;
    }

    /**
     * Verify that the build-in caching is used when caching is enabled.
     *
     * @return void
     */
    public function testResultIsCached()
    {
        // The test case used is specifically selected to be one which will always reach the cache check.
        $methodName = 'PHPCSUtils\\Utils\\FunctionDeclarations::getParameters';
        $testMarker = '/* testPHP82PseudoTypeTrue */';
        $expected   = [
            0 => [
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
            ],
        ];

        $stackPtr = $this->getTargetToken($testMarker, Collections::functionDeclarationTokens());
        $expected = $this->updateExpectedTokenPositions($stackPtr, $expected);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = FunctionDeclarations::getParameters(self::$phpcsFile, $stackPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, $stackPtr);
        $resultSecondRun = FunctionDeclarations::getParameters(self::$phpcsFile, $stackPtr);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }
}
