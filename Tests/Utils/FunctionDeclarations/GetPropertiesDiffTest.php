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
final class GetPropertiesDiffTest extends UtilityMethodTestCase
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
     * Verify recognition of PHP 8.2 stand-alone `true` type.
     *
     * @return void
     */
    public function testPHP82PseudoTypeTrue()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '?true',
            'return_type_token'     => 8, // Offset from the T_FUNCTION token.
            'return_type_end_token' => 8, // Offset from the T_FUNCTION token.
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP 8.2 type declaration with (illegal) type false combined with type true.
     *
     * @return void
     */
    public function testPHP82PseudoTypeFalseAndTrue()
    {
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => 'true|false',
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
     * Test helper.
     *
     * @param string $commentString The comment which preceeds the test.
     * @param array  $expected      The expected function output.
     * @param array  $targetType    Optional. The token type to search for after $commentString.
     *                              Defaults to the function/closure tokens.
     *
     * @return void
     */
    protected function getPropertiesTestHelper(
        $commentString,
        $expected,
        $targetType = [\T_FUNCTION, \T_CLOSURE, \T_FN]
    ) {
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

    /**
     * Verify that the build-in caching is used when caching is enabled.
     *
     * @return void
     */
    public function testResultIsCached()
    {
        // The test case used is specifically selected to be one which will always reach the cache check.
        $methodName = 'PHPCSUtils\\Utils\\FunctionDeclarations::getProperties';
        $testMarker = '/* testMessyPhpcsAnnotationsStaticClosure */';
        $expected   = [
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

        $stackPtr = $this->getTargetToken($testMarker, Collections::functionDeclarationTokens());

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = FunctionDeclarations::getProperties(self::$phpcsFile, $stackPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, $stackPtr);
        $resultSecondRun = FunctionDeclarations::getProperties(self::$phpcsFile, $stackPtr);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }
}
