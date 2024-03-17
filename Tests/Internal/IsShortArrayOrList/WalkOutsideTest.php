<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Internal\IsShortArrayOrList;

use PHPCSUtils\Internal\Cache;
use PHPCSUtils\Internal\IsShortArrayOrList;
use PHPCSUtils\Internal\IsShortArrayOrListWithCache;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\Internal\IsShortArrayOrList class.
 *
 * @covers \PHPCSUtils\Internal\IsShortArrayOrList::walkOutside
 *
 * @since 1.0.0
 */
final class WalkOutsideTest extends UtilityMethodTestCase
{

    /**
     * Test that the "search for outer brackets" is done in as efficient a way as possible,
     * while still yielding correct results.
     *
     * @dataProvider dataWalkOutside
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param string $expected   The expected function output.
     *
     * @return void
     */
    public function testWalkOutside($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [\T_OPEN_SHORT_ARRAY]);
        $solver   = new IsShortArrayOrList(self::$phpcsFile, $stackPtr);
        $type     = $solver->solve();

        $this->assertSame($expected, $type);
    }

    /**
     * Data provider.
     *
     * @see testWalkOutside() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataWalkOutside()
    {
        return [
            'nested-short-array-start-of-file' => [
                'testMarker' => '/* testShortArrayStopAtStartOfFile */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'nested-short-array-jump-test' => [
                'testMarker' => '/* testShortArrayJumpingOver */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-list-jump-test' => [
                'testMarker' => '/* testShortListJumpingOver */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],

            'outer-short-array-stop-test-semicolon' => [
                'testMarker' => '/* testOuterShortArrayStopAtSemicolon */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'outer-short-array-stop-test-open-tag' => [
                'testMarker' => '/* testOuterShortArrayStopAtOpenTag */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'outer-short-array-stop-test-short-open-echo' => [
                'testMarker' => '/* testOuterShortArrayStopAtOpenEchoTag */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'outer-short-array-stop-test-curly-open' => [
                'testMarker' => '/* testOuterShortArrayStopAtCurly */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'outer-short-array-stop-test-parens-open-1' => [
                'testMarker' => '/* testOuterShortArrayStopAtParensFuncCall */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'outer-short-array-stop-test-parens-open-2' => [
                'testMarker' => '/* testOuterShortArrayStopAtParensClosureCall */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'outer-short-array-stop-test-parens-open-3' => [
                'testMarker' => '/* testOuterShortArrayStopAtParensFnCall */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'inner-short-list-stop-test-parens-open-4' => [
                'testMarker' => '/* testInnerShortListStopAtParensLongList */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],

            'inner-short-array-stop-test-curly-open' => [
                'testMarker' => '/* testShortArrayInShortArrayStopAtCurly */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'inner-short-list-stop-test-semicolon' => [
                'testMarker' => '/* testShortArrayInShortListStopAtSemicolon */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'inner-short-array-stop-test-parens-open-1' => [
                'testMarker' => '/* testShortArrayInShortArrayStopAtParensFuncCall */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'inner-short-array-stop-test-parens-open-2' => [
                'testMarker' => '/* testShortArrayInShortArrayStopAtParensClosureCall */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'inner-short-array-stop-test-parens-open-3' => [
                'testMarker' => '/* testShortArrayInShortArrayStopAtParensFnCall */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'inner-short-array-stop-test-parens-open-4' => [
                'testMarker' => '/* testShortArrayInShortListStopAtParensFuncCall */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'inner-short-array-stop-test-parens-open-5' => [
                'testMarker' => '/* testShortArrayInShortListStopAtParensClosureCall */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'inner-short-array-stop-test-parens-open-6' => [
                'testMarker' => '/* testShortArrayInShortListStopAtParensFnCall */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'parse-error-live-coding' => [
                'testMarker' => '/* testLiveCodingNested */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
        ];
    }

    /**
     * Test that the method correctly re-uses cached information for adjacent sets of brackets.
     *
     * @dataProvider dataReuseCacheFromAdjacent
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param string $adjacent1  The comment which prefaces the matching adjacent bracket set in the test file.
     * @param string $adjacent2  The comment which prefaces the non-matching adjacent bracket set in the test file.
     * @param string $expected   The expected function output.
     *
     * @return void
     */
    public function testReuseCacheFromAdjacent($testMarker, $adjacent1, $adjacent2, $expected)
    {
        $adjacentPtr1 = $this->getTargetToken($adjacent1, \T_OPEN_SHORT_ARRAY);
        $adjacentPtr2 = $this->getTargetToken($adjacent2, \T_OPEN_SHORT_ARRAY);
        $target       = $this->getTargetToken($testMarker, \T_OPEN_SHORT_ARRAY);

        // Verify the cache of the adjacent bracket set is re-used.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        // Make sure the type of the adjacent bracket sets is cached.
        IsShortArrayOrListWithCache::getType(self::$phpcsFile, $adjacentPtr1); // Same code pattern.
        IsShortArrayOrListWithCache::getType(self::$phpcsFile, $adjacentPtr2); // Not the same code pattern.

        // Check the type of the current target set of brackets.
        $solver = new IsShortArrayOrList(self::$phpcsFile, $target);
        $type   = $solver->solve();

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $type);
    }

    /**
     * Data provider.
     *
     * @see testReuseCacheFromAdjacent() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataReuseCacheFromAdjacent()
    {
        return [
            'nested-short-array' => [
                'testMarker' => '/* testShortArrayReuseTypeOfAdjacent */',
                'adjacent1'  => '/* testShortArrayAdjacent1 */',
                'adjacent2'  => '/* testShortArrayAdjacent2 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'nested-short-list' => [
                'testMarker' => '/* testShortListReuseTypeOfAdjacent */',
                'adjacent1'  => '/* testShortListAdjacent1 */',
                'adjacent2'  => '/* testShortListAdjacent2 */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
        ];
    }
}
