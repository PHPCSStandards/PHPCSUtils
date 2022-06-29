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
 * @group passedparameters
 *
 * @since 1.0.0
 */
class GetParametersSkipShortArrayCheckTest extends PolyfilledTestCase
{

    /**
     * Test receiving an expected exception when passed an invalid (non short array) token when the
     * `$isShortArray` parameter is NOT passed (or not set to true).
     *
     * Also verify that for valid constructs, the method still behaves like normal.
     *
     * @dataProvider dataTestCases
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
            $this->expectPhpcsException(
                'The hasParameters() method expects a function call, array, isset or unset token to be passed.'
            );
        }

        $target    = $this->getTargetToken($testMarker, [$targetType]);
        $hasParams = PassedParameters::hasParameters(self::$phpcsFile, $target);

        if ($expectException === false) {
            $this->assertIsBool($hasParams);
        }
    }

    /**
     * Test retrieving the parameter details for valid and invalid constructs when the
     * `$isShortArray` parameter is set to TRUE.
     *
     * @dataProvider dataTestCases
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType The type of token to look for.
     * @param bool       $ignore     Not used in this test.
     * @param array      $expected   The expected return value.
     *
     * @return void
     */
    public function testGetParametersSkipShortArrayCheck($testMarker, $targetType, $ignore, $expected)
    {
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
     * @see testGetParametersSkipShortArrayCheck()     For the array format.
     * @see testHasParametersDontSkipShortArrayCheck() For the array format.
     *
     * @return array
     */
    public function dataTestCases()
    {
        return [
            'no-params' => [
                'testMarker' => '/* testNoParams */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'ignore'     => false,
                'expected'   => [],
            ],
            'long-array' => [
                'testMarker' => '/* testLongArray */',
                'targetType' => \T_ARRAY,
                'ignore'     => false,
                'expected'   => [
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
                'testMarker' => '/* testShortArray */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'ignore'     => false,
                'expected'   => [
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
                'testMarker' => '/* testShortList */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'ignore'     => true,
                'expected'   => [
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
            'array-assign' => [
                'testMarker' => '/* testArrayAssign */',
                'targetType' => \T_OPEN_SQUARE_BRACKET,
                'ignore'     => true,
                'expected'   => [],
            ],
            'array-access' => [
                'testMarker' => '/* testArrayAccess */',
                'targetType' => \T_OPEN_SQUARE_BRACKET,
                'ignore'     => true,
                'expected'   => [
                    1 => [
                        'start' => 1,
                        'end'   => 4,
                        'raw'   => '$keys[\'key\']',
                    ],
                ],
            ],
            'short-list-with-empties-before' => [
                'testMarker' => '/* testShortListWithEmptyItemsBefore */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'ignore'     => true,
                'expected'   => [
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
                'testMarker' => '/* testShortListWithEmptyItemsAfter */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'ignore'     => true,
                'expected'   => [
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
                'testMarker' => '/* testShortListWithAllEmptyItems */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'ignore'     => true,
                'expected'   => [
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
