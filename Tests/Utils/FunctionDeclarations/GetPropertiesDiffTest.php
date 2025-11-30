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
use PHPCSUtils\Tests\PolyfilledTestCase;
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
final class GetPropertiesDiffTest extends PolyfilledTestCase
{

    /**
     * Test passing a non-integer token pointer.
     *
     * @return void
     */
    public function testNonIntegerToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type integer, boolean given');

        FunctionDeclarations::getProperties(self::$phpcsFile, false);
    }

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\OutOfBoundsStackPtr');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be a stack pointer which exists in the $phpcsFile object'
        );

        FunctionDeclarations::getProperties(self::$phpcsFile, 10000);
    }

    /**
     * Test that for invalid code, the return type parsing doesn't grab too many tokens.
     *
     * @return void
     */
    public function testInvalidTypeParseError()
    {
        $php8Names = parent::usesPhp8NameTokens();

        // Offsets are relative to the T_FUNCTION token.
        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => ($php8Names === true) ? '?\\\\SomeType' : '?\\&\\SomeType',
            'return_type_token'     => 9,
            'return_type_end_token' => ($php8Names === true) ? 11 : 12,
            'nullable_return_type'  => true,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => true,
        ];

        $this->getPropertiesTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
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
     * Test helper.
     *
     * @param string                         $commentString The comment which preceeds the test.
     * @param array<string, int|string|bool> $expected      The expected function output.
     * @param int|string|array<int|string>   $targetType    Optional. The token type to search for after $commentString.
     *                                                      Defaults to the function/closure tokens.
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

        // Convert offsets to absolute positions in the token stream.
        if (\is_int($expected['return_type_token']) === true) {
            $expected['return_type_token'] += $function;
        }
        if (\is_int($expected['return_type_end_token']) === true) {
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
