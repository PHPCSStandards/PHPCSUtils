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
 * Tests for the \PHPCSUtils\Utils\Lists::isShortList() method.
 *
 * @covers \PHPCSUtils\Utils\Lists::isShortList
 *
 * @group lists
 *
 * @since 1.0.0
 */
class IsShortListTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(Lists::isShortList(self::$phpcsFile, 100000));
    }

    /**
     * Test that false is returned when a non-short array token is passed.
     *
     * @dataProvider dataNotShortArrayToken
     *
     * @param string           $testMarker  The comment which prefaces the target token in the test file.
     * @param int|string|array $targetToken The token type(s) to look for.
     *
     * @return void
     */
    public function testNotShortArrayToken($testMarker, $targetToken)
    {
        $target = $this->getTargetToken($testMarker, $targetToken);
        $this->assertFalse(Lists::isShortList(self::$phpcsFile, $target));
    }

    /**
     * Data provider.
     *
     * @see testNotShortArrayToken() For the array format.
     *
     * @return array
     */
    public function dataNotShortArrayToken()
    {
        return [
            'long-list' => [
                '/* testLongList */',
                \T_LIST,
            ],
            'array-assignment' => [
                '/* testArrayAssignment */',
                \T_CLOSE_SQUARE_BRACKET,
            ],
            'live-coding' => [
                '/* testLiveCoding */',
                \T_OPEN_SQUARE_BRACKET,
            ],
        ];
    }

    /**
     * Test whether a T_OPEN_SHORT_ARRAY token is a short list.
     *
     * @dataProvider dataIsShortList
     *
     * @param string           $testMarker  The comment which prefaces the target token in the test file.
     * @param bool             $expected    The expected boolean return value.
     * @param int|string|array $targetToken The token type(s) to test. Defaults to T_OPEN_SHORT_ARRAY.
     *
     * @return void
     */
    public function testIsShortList($testMarker, $expected, $targetToken = \T_OPEN_SHORT_ARRAY)
    {
        $stackPtr = $this->getTargetToken($testMarker, $targetToken);
        $result   = Lists::isShortList(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testIsShortList() For the array format.
     *
     * @return array
     */
    public function dataIsShortList()
    {
        return [
            'short-array-not-nested' => [
                '/* testNonNestedShortArray */',
                false,
            ],
            'comparison-no-assignment' => [
                '/* testNoAssignment */',
                false,
            ],
            'comparison-no-assignment-nested' => [
                '/* testNestedNoAssignment */',
                false,
            ],
            'short-array-in-foreach' => [
                '/* testShortArrayInForeach */',
                false,
            ],
            'short-list' => [
                '/* testShortList */',
                true,
            ],
            'short-list-detect-on-close-bracket' => [
                '/* testShortListDetectOnCloseBracket */',
                true,
                \T_CLOSE_SHORT_ARRAY,
            ],
            'short-list-with-nesting' => [
                '/* testShortListWithNesting */',
                true,
            ],
            'short-list-nested' => [
                '/* testNestedShortList */',
                true,
            ],
            'short-list-in-foreach' => [
                '/* testShortListInForeach */',
                true,
            ],
            'short-list-in-foreach-with-key' => [
                '/* testShortListInForeachWithKey */',
                true,
            ],
            'short-list-in-foreach-nested' => [
                '/* testShortListInForeachNested */',
                true,
            ],
            'short-list-chained-assignment' => [
                '/* testMultiAssignShortlist */',
                true,
            ],
            'short-list-with-keys' => [
                '/* testShortListWithKeys */',
                true,
            ],
            'short-list-in-foreach-with-keys-detect-on-close-bracket' => [
                '/* testShortListInForeachWithKeysDetectOnCloseBracket */',
                true,
                \T_CLOSE_SHORT_ARRAY,
            ],
            'short-list-nested-empty' => [
                '/* testNestedShortListEmpty */',
                true,
            ],
            'short-list-deeply-nested' => [
                '/* testDeeplyNestedShortList */',
                true,
            ],
            'short-list-with-nesting-and-keys' => [
                '/* testShortListWithNestingAndKeys */',
                true,
            ],
            'short-list-nested-with-keys-1' => [
                '/* testNestedShortListWithKeys_1 */',
                true,
            ],
            'short-list-nested-with-keys-2' => [
                '/* testNestedShortListWithKeys_2 */',
                true,
            ],
            'short-list-nested-with-keys-3' => [
                '/* testNestedShortListWithKeys_3 */',
                true,
            ],
            'short-list-without-vars' => [
                '/* testShortListWithoutVars */',
                true,
            ],
            'short-list-nested-long-list' => [
                '/* testShortListNestedLongList */',
                true,
            ],
            'parse-error-anon-class-trait-use-as' => [
                '/* testNestedAnonClassWithTraitUseAs */',
                false,
            ],
            'parse-error-use-as' => [
                '/* testParseError */',
                false,
            ],
        ];
    }
}
