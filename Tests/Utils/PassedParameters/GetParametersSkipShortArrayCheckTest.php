<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\PassedParameters;

use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Utils\PassedParameters;

/**
 * Tests for the \PHPCSUtils\Utils\PassedParameters::hasParameters() and
 * the \PHPCSUtils\Utils\PassedParameters::getParameters() methods for
 * when the $isShortArray parameter has been passed.
 *
 * @covers \PHPCSUtils\Utils\PassedParameters::hasParameters
 * @covers \PHPCSUtils\Utils\PassedParameters::getParameters
 *
 * @since 1.0.0
 */
final class GetParametersSkipShortArrayCheckTest extends PolyfilledTestCase
{

    /**
     * Test receiving an expected exception when passed an invalid (non short array) token when the
     * `$isShortArray` parameter is NOT passed (or not set to true).
     *
     * Also verify that for valid constructs, the method still behaves like normal.
     *
     * @dataProvider dataHasParametersDontSkipShortArrayCheck
     *
     * @param string     $testMarker      The comment which prefaces the target token in the test file.
     * @param int|string $targetType      The type of token to look for.
     * @param bool       $expectException Whether or not to expect an exception.
     *
     * @return void
     */
    public function testHasParametersDontSkipShortArrayCheck($testMarker, $targetType, $expectException)
    {
        if ($expectException === true) {
            $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
            $this->expectExceptionMessage(
                'Argument #2 ($stackPtr) must be of type function call, array, isset, unset or exit;'
            );
        }

        $target    = $this->getTargetToken($testMarker, [$targetType]);
        $hasParams = PassedParameters::hasParameters(self::$phpcsFile, $target);

        // Will only be asserted when no exception is expected/caught.
        $this->assertIsBool($hasParams);
    }

    /**
     * Data provider.
     *
     * @see testHasParametersDontSkipShortArrayCheck() For the array format.
     *
     * @return array<string, array<string, int|string|bool|array<int, array<string, int|string>>>>
     */
    public static function dataHasParametersDontSkipShortArrayCheck()
    {
        $data = self::dataTestCases();

        foreach ($data as $name => $dataset) {
            unset($data[$name]['expected']);
        }

        return $data;
    }

    /**
     * Test retrieving the parameter details for valid and invalid constructs when the
     * `$isShortArray` parameter is set to TRUE.
     *
     * @dataProvider dataGetParametersSkipShortArrayCheck
     *
     * @param string                                $testMarker The comment which prefaces the target token in the test file.
     * @param int|string                            $targetType The type of token to look for.
     * @param array<int, array<string, int|string>> $expected   The expected return value.
     *
     * @return void
     */
    public function testGetParametersSkipShortArrayCheck($testMarker, $targetType, $expected)
    {
        /*
         * Expect an exception on PHPCS versions when square brackets will never be a short array.
         * Note: this also means that the "$expected" value will not be tested as the exception
         * will be received before the code reaches that point.
         */
        if ($targetType === \T_OPEN_SQUARE_BRACKET) {
            $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
            $this->expectExceptionMessage(
                'Argument #2 ($stackPtr) must be of type function call, array, isset, unset or exit;'
            );
        }

        $stackPtr = $this->getTargetToken($testMarker, [$targetType]);
        $result   = PassedParameters::getParameters(self::$phpcsFile, $stackPtr, 0, true);

        $this->assertIsArray($result);

        // Start/end token position values in the expected array are set as offsets
        // in relation to the target token.
        // Change these to exact positions based on the retrieved stackPtr.
        foreach ($expected as $key => $value) {
            $expected[$key]['start'] = ($stackPtr + $value['start']);
            $expected[$key]['end']   = ($stackPtr + $value['end']);
        }

        foreach ($result as $key => $value) {
            // The GetTokensAsString functions have their own tests, no need to duplicate it here.
            unset($result[$key]['clean']);
        }

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetParametersSkipShortArrayCheck() For the array format.
     *
     * @return array<string, array<string, int|string|bool|array<int, array<string, int|string>>>>
     */
    public static function dataGetParametersSkipShortArrayCheck()
    {
        $data = self::dataTestCases();

        foreach ($data as $name => $dataset) {
            unset($data[$name]['expectException']);
        }

        return $data;
    }

    /**
     * Data provider.
     *
     * @see testGetParametersSkipShortArrayCheck()     For the array format.
     * @see testHasParametersDontSkipShortArrayCheck() For the array format.
     *
     * @return array<string, array<string, int|string|bool|array<int, array<string, int|string>>>>
     */
    public static function dataTestCases()
    {
        return [
            'no-params' => [
                'testMarker'      => '/* testNoParams */',
                'targetType'      => \T_OPEN_SHORT_ARRAY,
                'expectException' => false,
                'expected'        => [],
            ],
            'long-array' => [
                'testMarker'      => '/* testLongArray */',
                'targetType'      => \T_ARRAY,
                'expectException' => false,
                'expected'        => [
                    1 => [
                        'start' => 2,
                        'end'   => 3,
                        'raw'   => '1',
                    ],
                    2 => [
                        'start' => 5,
                        'end'   => 7,
                        'raw'   => '2',
                    ],
                ],
            ],
            'short-array' => [
                'testMarker'      => '/* testShortArray */',
                'targetType'      => \T_OPEN_SHORT_ARRAY,
                'expectException' => false,
                'expected'        => [
                    1 => [
                        'start' => 1,
                        'end'   => 6,
                        'raw'   => "'a' => 1",
                    ],
                    2 => [
                        'start' => 8,
                        'end'   => 14,
                        'raw'   => "'b' => 2",
                    ],
                ],
            ],
            'short-list' => [
                'testMarker'      => '/* testShortList */',
                'targetType'      => \T_OPEN_SHORT_ARRAY,
                'expectException' => true,
                'expected'        => [
                    1 => [
                        'start' => 1,
                        'end'   => 6,
                        'raw'   => '\'a\' => $a',
                    ],
                    2 => [
                        'start' => 8,
                        'end'   => 14,
                        'raw'   => '\'b\' => $b',
                    ],
                ],
            ],
            /*
             * This test will result in an (expected) Exception when run on PHPCS versions which
             * correctly tokenize short arrays.
             * The `T_OPEN_SQUARE_BRACKET` will (correctly) not be supported by the PassedParameters
             * class for those PHPCS versions.
             */
            'array-assign' => [
                'testMarker'      => '/* testArrayAssign */',
                'targetType'      => \T_OPEN_SQUARE_BRACKET,
                'expectException' => true,
                'expected'        => [],
            ],
            /*
             * This test will result in an (expected) Exception when run on PHPCS versions which
             * correctly tokenize short arrays.
             * The `T_OPEN_SQUARE_BRACKET` will (correctly) not be supported by the PassedParameters
             * class for those PHPCS versions.
             */
            'array-access' => [
                'testMarker'      => '/* testArrayAccess */',
                'targetType'      => \T_OPEN_SQUARE_BRACKET,
                'expectException' => true,
                'expected'        => [
                    1 => [
                        'start' => 1,
                        'end'   => 4,
                        'raw'   => '$keys[\'key\']',
                    ],
                ],
            ],
            'short-list-with-empties-before' => [
                'testMarker'      => '/* testShortListWithEmptyItemsBefore */',
                'targetType'      => \T_OPEN_SHORT_ARRAY,
                'expectException' => true,
                'expected'        => [
                    1 => [
                        'start' => 1,
                        'end'   => 0,
                        'raw'   => '',
                    ],
                    2 => [
                        'start' => 2,
                        'end'   => 2,
                        'raw'   => '',
                    ],
                    3 => [
                        'start' => 4,
                        'end'   => 5,
                        'raw'   => '$a',
                    ],
                ],
            ],
            'short-list-with-empties-after' => [
                'testMarker'      => '/* testShortListWithEmptyItemsAfter */',
                'targetType'      => \T_OPEN_SHORT_ARRAY,
                'expectException' => true,
                'expected'        => [
                    1 => [
                        'start' => 1,
                        'end'   => 1,
                        'raw'   => '$a',
                    ],
                    2 => [
                        'start' => 3,
                        'end'   => 2,
                        'raw'   => '',
                    ],
                ],
            ],
            'short-list-with-all-empties' => [
                'testMarker'      => '/* testShortListWithAllEmptyItems */',
                'targetType'      => \T_OPEN_SHORT_ARRAY,
                'expectException' => true,
                'expected'        => [
                    1 => [
                        'start' => 1,
                        'end'   => 0,
                        'raw'   => '',
                    ],
                    2 => [
                        'start' => 2,
                        'end'   => 1,
                        'raw'   => '',
                    ],
                ],
            ],
        ];
    }
}
