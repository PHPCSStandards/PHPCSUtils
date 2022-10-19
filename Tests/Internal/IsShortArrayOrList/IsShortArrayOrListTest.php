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
use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Arrays;
use PHPCSUtils\Utils\Lists;

/**
 * Tests for the \PHPCSUtils\Utils\Arrays::isShortArray() and
 * the \PHPCSUtils\Utils\Lists::isShortList() methods.
 *
 * @group arrays
 * @group lists
 *
 * @since 1.0.0
 */
final class IsShortArrayOrListTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @covers \PHPCSUtils\Utils\Arrays::isShortArray
     *
     * @return void
     */
    public function testNonExistentTokenIsShortArray()
    {
        $this->assertFalse(Arrays::isShortArray(self::$phpcsFile, 100000));
    }

    /**
     * Test passing a non-existent token pointer.
     *
     * @covers \PHPCSUtils\Utils\Lists::isShortList
     *
     * @return void
     */
    public function testNonExistentTokenIsShortList()
    {
        $this->assertFalse(Lists::isShortList(self::$phpcsFile, 100000));
    }

    /**
     * Test that false is returned when a non-short array/non-square bracket token is passed.
     *
     * @dataProvider dataNotABracket
     * @covers       \PHPCSUtils\Utils\Arrays::isShortArray
     *
     * @param string           $testMarker  The comment which prefaces the target token in the test file.
     * @param int|string|array $targetToken The token type(s) to look for.
     *
     * @return void
     */
    public function testNotShortArrayBracket($testMarker, $targetToken)
    {
        $target = $this->getTargetToken($testMarker, $targetToken);
        $this->assertFalse(Arrays::isShortArray(self::$phpcsFile, $target));
    }

    /**
     * Test that false is returned when a non-short array/non-square bracket token is passed.
     *
     * @dataProvider dataNotABracket
     * @covers       \PHPCSUtils\Utils\Lists::isShortList
     *
     * @param string           $testMarker  The comment which prefaces the target token in the test file.
     * @param int|string|array $targetToken The token type(s) to look for.
     *
     * @return void
     */
    public function testNotShortListBracket($testMarker, $targetToken)
    {
        $target = $this->getTargetToken($testMarker, $targetToken);
        $this->assertFalse(Lists::isShortList(self::$phpcsFile, $target));
    }

    /**
     * Data provider.
     *
     * @see testNotShortArrayBracket() For the array format.
     * @see testNotShortListBracket()  For the array format.
     *
     * @return array
     */
    public function dataNotABracket()
    {
        return [
            'long-array' => [
                '/* testLongArray */',
                \T_ARRAY,
            ],
            'long-list' => [
                '/* testLongList */',
                \T_LIST,
            ],
        ];
    }

    /**
     * Test that short array/square bracket, which are in actual fact not short arrays/short list tokens,
     * are correctly identified as real square brackets.
     *
     * @dataProvider dataNotShortArrayShortListBracket
     * @covers       \PHPCSUtils\Internal\IsShortArrayOrList
     *
     * @param string           $testMarker  The comment which prefaces the target token in the test file.
     * @param int|string|array $targetToken The token type(s) to look for.
     *
     * @return void
     */
    public function testNotShortArrayShortListBracket($testMarker, $targetToken)
    {
        $stackPtr = $this->getTargetToken($testMarker, $targetToken);
        $solver   = new IsShortArrayOrList(self::$phpcsFile, $stackPtr);
        $type     = $solver->solve();

        $this->assertSame(IsShortArrayOrList::SQUARE_BRACKETS, $type);
    }

    /**
     * Data provider.
     *
     * @see testNotShortArrayShortListBracket() For the array format.
     *
     * @return array
     */
    public function dataNotShortArrayShortListBracket()
    {
        return [
            'array-assignment-no-key' => [
                '/* testArrayAssignmentEmpty */',
                \T_CLOSE_SQUARE_BRACKET,
            ],
            'array-assignment-string-key' => [
                '/* testArrayAssignmentStringKey */',
                \T_OPEN_SQUARE_BRACKET,
            ],
            'array-assignment-int-key' => [
                '/* testArrayAssignmentIntKey */',
                \T_OPEN_SQUARE_BRACKET,
            ],
            'array-assignment-var-key' => [
                '/* testArrayAssignmentVarKey */',
                \T_OPEN_SQUARE_BRACKET,
            ],
            'array-access-string-key' => [
                '/* testArrayAccessStringKey */',
                \T_CLOSE_SQUARE_BRACKET,
            ],
            'array-access-int-key-1' => [
                '/* testArrayAccessIntKey1 */',
                \T_OPEN_SQUARE_BRACKET,
            ],
            'array-access-int-key-2' => [
                '/* testArrayAccessIntKey2 */',
                \T_OPEN_SQUARE_BRACKET,
            ],
            'array-access-function-call' => [
                '/* testArrayAccessFunctionCall */',
                \T_OPEN_SQUARE_BRACKET,
            ],
            'array-access-constant' => [
                '/* testArrayAccessConstant */',
                \T_OPEN_SQUARE_BRACKET,
            ],
            'array-access-nullsafe-method-call' => [
                '/* testNullsafeMethodCallDereferencing */',
                \T_OPEN_SQUARE_BRACKET,
            ],
            'live-coding' => [
                '/* testLiveCoding */',
                \T_OPEN_SQUARE_BRACKET,
            ],
        ];
    }

    /**
     * Test whether a T_OPEN_SHORT_ARRAY token is correctly determined to be a short array,
     * a short list or a real square bracket.
     *
     * @dataProvider dataIsShortArrayOrList
     * @covers       \PHPCSUtils\Internal\IsShortArrayOrList
     *
     * @param string           $testMarker  The comment which prefaces the target token in the test file.
     * @param string           $expected    The expected return value.
     * @param int|string|array $targetToken The token type(s) to test. Defaults to T_OPEN_SHORT_ARRAY.
     *
     * @return void
     */
    public function testIsShortArrayOrList($testMarker, $expected, $targetToken = \T_OPEN_SHORT_ARRAY)
    {
        $stackPtr = $this->getTargetToken($testMarker, $targetToken);
        $solver   = new IsShortArrayOrList(self::$phpcsFile, $stackPtr);
        $type     = $solver->solve();

        $this->assertSame($expected, $type);
    }

    /**
     * Data provider.
     *
     * @see testIsShortArrayOrList() For the array format.
     *
     * @return array
     */
    public function dataIsShortArrayOrList()
    {
        return [
            'short-array-not-nested' => [
                '/* testShortArrayNonNested */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-comparison-no-assignment' => [
                '/* testShortArrayInComparison */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-comparison-no-assignment-nested' => [
                '/* testShortArrayNestedInComparison */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-union-before' => [
                '/* testShortArrayUnionFirst */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-union-after' => [
                '/* testShortArrayUnionSecond */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-equal-before' => [
                '/* testShortArrayEqualFirst */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-equal-after' => [
                '/* testShortArrayEqualSecond */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-identical-before' => [
                '/* testShortArrayIdenticalFirst */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-identical-after' => [
                '/* testShortArrayIdenticalSecond */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-not-equal-before' => [
                '/* testShortArrayNotEqualFirst */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-not-equal-after' => [
                '/* testShortArrayNotEqualSecond */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-not-equal-brackets-before' => [
                '/* testShortArrayNotEqualBracketsFirst */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-not-equal-brackets-after' => [
                '/* testShortArrayNotEqualBracketsSecond */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-not-identical-before' => [
                '/* testShortArrayNonIdenticalFirst */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-not-identical-after' => [
                '/* testShortArrayNonIdenticalSecond */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-in-foreach' => [
                '/* testShortArrayInForeach */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-list-in-foreach' => [
                '/* testShortListInForeach */',
                IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list' => [
                '/* testShortList */',
                IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-detect-on-close-bracket' => [
                '/* testShortListDetectOnCloseBracket */',
                IsShortArrayOrList::SHORT_LIST,
                \T_CLOSE_SHORT_ARRAY,
            ],
            'short-list-with-keys' => [
                '/* testShortListWithKeys */',
                IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-with-nesting' => [
                '/* testShortListWithNesting */',
                IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-nested' => [
                '/* testShortListNested */',
                IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-in-foreach-with-key' => [
                '/* testShortListInForeachWithKey */',
                IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-in-foreach-nested' => [
                '/* testShortListInForeachNested */',
                IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-in-foreach-with-keys-detect-on-close-bracket' => [
                '/* testShortListInForeachWithKeysDetectOnCloseBracket */',
                IsShortArrayOrList::SHORT_LIST,
                \T_CLOSE_SHORT_ARRAY,
            ],

            'short-list-in-chained-assignment' => [
                '/* testShortlistMultiAssign */',
                IsShortArrayOrList::SHORT_LIST,
            ],
            'short-array-in-chained-assignment' => [
                '/* testShortArrayMultiAssign */',
                IsShortArrayOrList::SHORT_ARRAY,
                \T_CLOSE_SHORT_ARRAY,
            ],
            'short-array-with-nesting-and-keys' => [
                '/* testShortArrayWithNestingAndKeys */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-nested-with-keys-1' => [
                '/* testNestedShortArrayWithKeys_1 */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-nested-with-keys-2' => [
                '/* testNestedShortArrayWithKeys_2 */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-nested-with-keys-3' => [
                '/* testNestedShortArrayWithKeys_3 */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-list-with-nesting-and-keys' => [
                '/* testShortListWithNestingAndKeys */',
                IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-nested-with-keys-1' => [
                '/* testNestedShortListWithKeys_1 */',
                IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-nested-with-keys-2' => [
                '/* testNestedShortListWithKeys_2 */',
                IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-nested-with-keys-3' => [
                '/* testNestedShortListWithKeys_3 */',
                IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-deeply-nested' => [
                '/* testDeeplyNestedShortList */',
                IsShortArrayOrList::SHORT_LIST,
            ],

            // Invalid syntaxes.
            'short-list-nested-empty' => [
                '/* testNestedShortListEmpty */',
                IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-without-vars' => [
                '/* testShortListWithoutVars */',
                IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-nested-long-list' => [
                '/* testShortListNestedLongList */',
                IsShortArrayOrList::SHORT_LIST,
            ],
            'parse-error-foreach-without-as' => [
                '/* testForeachWithoutAs */',
                // Unknown from a foreach perspective, but then the "normal" rules kick in.
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'parse-error-anon-class-trait-use-as' => [
                '/* testNestedAnonClassWithTraitUseAs */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'parse-error-use-as' => [
                '/* testParseError */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'parse-error-live-coding' => [
                '/* testLiveCodingNested */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
        ];
    }

    /**
     * Verify that the build-in caching is used when caching is enabled.
     *
     * @covers \PHPCSUtils\Utils\Arrays::isShortArray
     *
     * @return void
     */
    public function testIsShortArrayResultIsCached()
    {
        $methodName = 'PHPCSUtils\\Utils\\Arrays::isShortArray';
        $testMarker = '/* testShortArrayUnionFirst */';
        $expected   = true;

        $stackPtr = $this->getTargetToken($testMarker, \T_OPEN_SHORT_ARRAY);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = Arrays::isShortArray(self::$phpcsFile, $stackPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, $stackPtr);
        $resultSecondRun = Arrays::isShortArray(self::$phpcsFile, $stackPtr);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }

    /**
     * Verify that the build-in caching is used when caching is enabled.
     *
     * @covers \PHPCSUtils\Utils\Lists::isShortList
     *
     * @return void
     */
    public function testIsShortListResultIsCached()
    {
        $methodName = 'PHPCSUtils\\Utils\\Lists::isShortList';
        $testMarker = '/* testShortListWithNestingAndKeys */';
        $expected   = true;

        $stackPtr = $this->getTargetToken($testMarker, \T_OPEN_SHORT_ARRAY);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = Lists::isShortList(self::$phpcsFile, $stackPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, $stackPtr);
        $resultSecondRun = Lists::isShortList(self::$phpcsFile, $stackPtr);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }
}
