<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Lists;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Lists;

/**
 * Tests for the \PHPCSUtils\Utils\Lists::getOpenClose() method.
 *
 * @covers \PHPCSUtils\Utils\Lists::getOpenClose
 *
 * @since 1.0.0
 */
final class GetOpenCloseTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(Lists::getOpenClose(self::$phpcsFile, 100000));
    }

    /**
     * Test that false is returned when a non-(short) list token is passed.
     *
     * @dataProvider dataNotListOpenToken
     *
     * @param string     $testMarker  The comment which prefaces the target token in the test file.
     * @param int|string $targetToken The token type(s) to look for.
     *
     * @return void
     */
    public function testNotListOpenToken($testMarker, $targetToken)
    {
        $target = $this->getTargetToken($testMarker, $targetToken);
        $this->assertFalse(Lists::getOpenClose(self::$phpcsFile, $target));
    }

    /**
     * Data provider.
     *
     * @see testNotListOpenToken() For the array format.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataNotListOpenToken()
    {
        return [
            'short-array' => [
                'testMarker'  => '/* testShortArray */',
                'targetToken' => \T_OPEN_SHORT_ARRAY,
            ],
            'array-access-square-bracket' => [
                'testMarker'  => '/* testArrayAccess */',
                'targetToken' => \T_OPEN_SQUARE_BRACKET,
            ],
            'short-array-closer' => [
                'testMarker'  => '/* testShortArray */',
                'targetToken' => \T_CLOSE_SHORT_ARRAY,
            ],
            'short-list-closer' => [
                'testMarker'  => '/* testNestedShortList */',
                'targetToken' => \T_CLOSE_SHORT_ARRAY,
            ],
            'array-access-square-bracket-closer' => [
                'testMarker'  => '/* testArrayAccess */',
                'targetToken' => \T_CLOSE_SQUARE_BRACKET,
            ],
        ];
    }

    /**
     * Test retrieving the open/close tokens for a list.
     *
     * @dataProvider dataGetOpenClose
     *
     * @param string                   $testMarker  The comment which prefaces the target token in the test file.
     * @param int|string               $targetToken The token type(s) to look for.
     * @param array<string, int>|false $expected    The expected function return value.
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

        $result = Lists::getOpenClose(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * The opener/closer positions are provided as offsets from the target stackPtr.
     *
     * @see testGetOpenClose() For the array format.
     *
     * @return array<string, array<string, int|string|array<string, int>|false>>
     */
    public static function dataGetOpenClose()
    {
        return [
            'long-list' => [
                'testMarker'  => '/* testLongList */',
                'targetToken' => \T_LIST,
                'expected'    => [
                    'opener' => 1,
                    'closer' => 14,
                ],
            ],
            'long-list-nested' => [
                'testMarker'  => '/* testNestedLongList */',
                'targetToken' => \T_LIST,
                'expected'    => [
                    'opener' => 2,
                    'closer' => 6,
                ],
            ],
            'short-list' => [
                'testMarker'  => '/* testShortList */',
                'targetToken' => \T_OPEN_SHORT_ARRAY,
                'expected'    => [
                    'opener' => 0,
                    'closer' => 9,
                ],
            ],
            'short-list-nested' => [
                'testMarker'  => '/* testNestedShortList */',
                'targetToken' => \T_OPEN_SHORT_ARRAY,
                'expected'    => [
                    'opener' => 0,
                    'closer' => 2,
                ],
            ],
            'long-list-with-comments-and-annotations' => [
                'testMarker'  => '/* testListWithCommentsAndAnnotations */',
                'targetToken' => \T_LIST,
                'expected'    => [
                    'opener' => 7,
                    'closer' => 18,
                ],
            ],
            'parse-error' => [
                'testMarker'  => '/* testParseError */',
                'targetToken' => \T_LIST,
                'expected'    => false,
            ],
        ];
    }

    /**
     * Test retrieving the open/close tokens for a nested list, skipping the short list check.
     *
     * @return void
     */
    public function testGetOpenCloseThirdParam()
    {
        $stackPtr = $this->getTargetToken('/* testNestedShortList */', \T_OPEN_SHORT_ARRAY);
        $expected = [
            'opener' => $stackPtr,
            'closer' => ($stackPtr + 2),
        ];

        $result = Lists::getOpenClose(self::$phpcsFile, $stackPtr, true);
        $this->assertSame($expected, $result);
    }
}
