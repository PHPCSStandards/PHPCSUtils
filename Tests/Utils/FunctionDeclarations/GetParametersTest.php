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
use PHPCSUtils\Tests\BackCompat\BCFile\GetMethodParametersTest as BCFile_GetMethodParametersTest;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\FunctionDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\FunctionDeclarations::getParameters method.
 *
 * @covers \PHPCSUtils\Utils\FunctionDeclarations::getParameters
 *
 * @group functiondeclarations
 *
 * @since 1.0.0
 */
final class GetParametersTest extends BCFile_GetMethodParametersTest
{

    /**
     * Full path to the test case file associated with this test class.
     *
     * @var string
     */
    protected static $caseFile = '';

    /**
     * Initialize PHPCS & tokenize the test case file.
     *
     * Overloaded to re-use the `$caseFile` from the BCFile test.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = \dirname(\dirname(__DIR__)) . '/BackCompat/BCFile/GetMethodParametersTest.inc';
        parent::setUpTestFile();
    }

    /**
     * Test receiving an expected exception when a non function/use token is passed.
     *
     * @dataProvider dataUnexpectedTokenException
     *
     * @param string $commentString   The comment which preceeds the test.
     * @param array  $targetTokenType The token type to search for after $commentString.
     *
     * @return void
     */
    public function testUnexpectedTokenException($commentString, $targetTokenType)
    {
        $this->expectPhpcsException('$stackPtr must be of type T_FUNCTION, T_CLOSURE or T_USE or an arrow function');

        $next = $this->getTargetToken($commentString, $targetTokenType);
        FunctionDeclarations::getParameters(self::$phpcsFile, $next);
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
        $this->expectPhpcsException('$stackPtr was not a valid closure T_USE');

        $use = $this->getTargetToken($identifier, [\T_USE]);
        FunctionDeclarations::getParameters(self::$phpcsFile, $use);
    }

    /**
     * Test receiving an empty array when there are no parameters.
     *
     * @dataProvider dataNoParams
     *
     * @param string $commentString   The comment which preceeds the test.
     * @param array  $targetTokenType Optional. The token type to search for after $commentString.
     *                                Defaults to the function/closure/arrow tokens.
     *
     * @return void
     */
    public function testNoParams($commentString, $targetTokenType = [\T_FUNCTION, \T_CLOSURE, \T_FN])
    {
        $target = $this->getTargetToken($commentString, $targetTokenType);
        $result = FunctionDeclarations::getParameters(self::$phpcsFile, $target);

        $this->assertSame([], $result);
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
     * @param int                              $targetPtr The token pointer to the target token from which
     *                                                    the offset is calculated.
     * @param array<int, array<string, mixed>> $expected  The expected function output containing offsets.
     *
     * @return array<int, array<string, mixed>>
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
