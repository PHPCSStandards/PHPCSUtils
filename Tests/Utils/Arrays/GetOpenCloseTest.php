<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Arrays;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Arrays;

/**
 * Tests for the \PHPCSUtils\Utils\Arrays::getOpenClose() method.
 *
 * @covers \PHPCSUtils\Utils\Arrays::getOpenClose
 *
 * @group arrays
 *
 * @since 1.0.0
 */
class GetOpenCloseTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(Arrays::getOpenClose(self::$phpcsFile, 100000));
    }

    /**
     * Test that false is returned when a non-(short) array token is passed.
     *
     * @dataProvider dataNotArrayToken
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @return void
     */
    public function testNotArrayToken($testMarker)
    {
        $target = $this->getTargetToken($testMarker, [\T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET]);
        $this->assertFalse(Arrays::getOpenClose(self::$phpcsFile, $target));
    }

    /**
     * Data provider.
     *
     * @see testNotArrayToken() For the array format.
     *
     * @return array
     */
    public function dataNotArrayToken()
    {
        return [
            'short-list'                  => ['/* testShortList */'],
            'array-access-square-bracket' => ['/* testArrayAccess */'],
        ];
    }

    /**
     * Test retrieving the open/close tokens for an array.
     *
     * @dataProvider dataGetOpenClose
     *
     * @param string           $testMarker  The comment which prefaces the target token in the test file.
     * @param int|string|array $targetToken The token type(s) to look for.
     * @param array|false      $expected    The expected function return value.
     *
     * @return void
     */
    public function testGetOpenClose($testMarker, $targetToken, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, $targetToken);

        // Convert offsets to absolute positions.
        if (isset($expected['opener'], $expected['closer'])) {
            $expected['opener'] += $stackPtr;
            $expected['closer'] += $stackPtr;
        }

        $result = Arrays::getOpenClose(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * The opener/closer positions are provided as offsets from the target stackPtr.
     *
     * @see testGetOpenClose() For the array format.
     *
     * @return array
     */
    public function dataGetOpenClose()
    {
        return [
            'long-array' => [
                '/* testLongArray */',
                \T_ARRAY,
                [
                    'opener' => 1,
                    'closer' => 14,
                ],
            ],
            'long-array-nested' => [
                '/* testNestedLongArray */',
                \T_ARRAY,
                [
                    'opener' => 2,
                    'closer' => 6,
                ],
            ],
            'short-array' => [
                '/* testShortArray */',
                \T_OPEN_SHORT_ARRAY,
                [
                    'opener' => 0,
                    'closer' => 9,
                ],
            ],
            'short-array-nested' => [
                '/* testNestedShortArray */',
                \T_OPEN_SHORT_ARRAY,
                [
                    'opener' => 0,
                    'closer' => 2,
                ],
            ],
            'long-array-with-comments-and-annotations' => [
                '/* testArrayWithCommentsAndAnnotations */',
                \T_ARRAY,
                [
                    'opener' => 4,
                    'closer' => 26,
                ],
            ],
            'parse-error' => [
                '/* testParseError */',
                \T_ARRAY,
                false,
            ],
        ];
    }

    /**
     * Test retrieving the open/close tokens for a nested array, skipping the short array check.
     *
     * @return void
     */
    public function testGetOpenCloseThirdParam()
    {
        $stackPtr = $this->getTargetToken('/* testNestedShortArray */', \T_OPEN_SHORT_ARRAY);
        $expected = [
            'opener' => $stackPtr,
            'closer' => ($stackPtr + 2),
        ];

        $result = Arrays::getOpenClose(self::$phpcsFile, $stackPtr, true);
        $this->assertSame($expected, $result);
    }
}
