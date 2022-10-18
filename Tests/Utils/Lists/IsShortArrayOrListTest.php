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

use PHPCSUtils\Internal\Cache;
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
     * Test that false is returned when a non-short array token is passed which isn't incorrectly tokenized.
     *
     * @dataProvider dataNotShortArrayShortListBracket
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
     * Test that false is returned when a non-short array token is passed which isn't incorrectly tokenized.
     *
     * @dataProvider dataNotShortArrayShortListBracket
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
    public function dataNotShortArrayShortListBracket()
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
            'live-coding' => [
                '/* testLiveCoding */',
                \T_OPEN_SQUARE_BRACKET,
            ],
        ];
    }

    /**
     * Data integrity test. Verify that the data provider is consistent.
     *
     * Possibly a bit over the top, but better safe than sorry in this case.
     *
     * Something is either a short array or a short list or neither. It can never be both at the same time.
     *
     * @dataProvider  dataIsShortArrayOrList
     * @coversNothing
     *
     * @param string $ignore Unused.
     * @param bool[] $data   The expected boolean return value for list and array.
     *
     * @return void
     */
    public function testValidDataProvider($ignore, $data)
    {
        $forbidden = [
            'array' => true,
            'list'  => true,
        ];

        $this->assertNotSame($forbidden, $data);
    }

    /**
     * Test whether a T_OPEN_SHORT_ARRAY token is a short array.
     *
     * @dataProvider dataIsShortArrayOrList
     * @covers       \PHPCSUtils\Utils\Arrays::isShortArray
     *
     * @param string           $testMarker  The comment which prefaces the target token in the test file.
     * @param bool[]           $expected    The expected boolean return value for list and array.
     * @param int|string|array $targetToken The token type(s) to test. Defaults to T_OPEN_SHORT_ARRAY.
     *
     * @return void
     */
    public function testIsShortArray($testMarker, $expected, $targetToken = \T_OPEN_SHORT_ARRAY)
    {
        $stackPtr = $this->getTargetToken($testMarker, $targetToken);
        $result   = Arrays::isShortArray(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected['array'], $result);
    }

    /**
     * Test whether a T_OPEN_SHORT_ARRAY token is a short list.
     *
     * @dataProvider dataIsShortArrayOrList
     * @covers       \PHPCSUtils\Utils\Lists::isShortList
     *
     * @param string           $testMarker  The comment which prefaces the target token in the test file.
     * @param bool[]           $expected    The expected boolean return value for list and array.
     * @param int|string|array $targetToken The token type(s) to test. Defaults to T_OPEN_SHORT_ARRAY.
     *
     * @return void
     */
    public function testIsShortList($testMarker, $expected, $targetToken = \T_OPEN_SHORT_ARRAY)
    {
        $stackPtr = $this->getTargetToken($testMarker, $targetToken);
        $result   = Lists::isShortList(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected['list'], $result);
    }

    /**
     * Data provider.
     *
     * @see testIsShortArray() For the array format.
     * @see testIsShortList()  For the array format.
     *
     * @return array
     */
    public function dataIsShortArrayOrList()
    {
        return [
            'short-array-not-nested' => [
                '/* testShortArrayNonNested */',
                [
                    'array' => true,
                    'list'  => false,
                ],
            ],
            'comparison-no-assignment' => [
                '/* testShortArrayInComparison */',
                [
                    'array' => true,
                    'list'  => false,
                ],
            ],
            'comparison-no-assignment-nested' => [
                '/* testShortArrayNestedInComparison */',
                [
                    'array' => true,
                    'list'  => false,
                ],
            ],
            'short-array-in-foreach' => [
                '/* testShortArrayInForeach */',
                [
                    'array' => true,
                    'list'  => false,
                ],
            ],
            'short-list-in-foreach' => [
                '/* testShortListInForeach */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'short-list' => [
                '/* testShortList */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'short-list-detect-on-close-bracket' => [
                '/* testShortListDetectOnCloseBracket */',
                [
                    'array' => false,
                    'list'  => true,
                ],
                \T_CLOSE_SHORT_ARRAY,
            ],
            'short-list-with-keys' => [
                '/* testShortListWithKeys */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'short-list-with-nesting' => [
                '/* testShortListWithNesting */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'short-list-nested' => [
                '/* testShortListNested */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'short-list-in-foreach-with-key' => [
                '/* testShortListInForeachWithKey */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'short-list-in-foreach-nested' => [
                '/* testShortListInForeachNested */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'short-list-in-foreach-with-keys-detect-on-close-bracket' => [
                '/* testShortListInForeachWithKeysDetectOnCloseBracket */',
                [
                    'array' => false,
                    'list'  => true,
                ],
                \T_CLOSE_SHORT_ARRAY,
            ],

            'chained-assignment-short-list' => [
                '/* testShortlistMultiAssign */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'chained-assignment-short-array' => [
                '/* testShortArrayMultiAssign */',
                [
                    'array' => true,
                    'list'  => false,
                ],
                \T_CLOSE_SHORT_ARRAY,
            ],
            'short-array-with-nesting-and-keys' => [
                '/* testShortArrayWithNestingAndKeys */',
                [
                    'array' => true,
                    'list'  => false,
                ],
            ],
            'short-array-nested-with-keys-1' => [
                '/* testNestedShortArrayWithKeys_1 */',
                [
                    'array' => true,
                    'list'  => false,
                ],
            ],
            'short-array-nested-with-keys-2' => [
                '/* testNestedShortArrayWithKeys_2 */',
                [
                    'array' => true,
                    'list'  => false,
                ],
            ],
            'short-array-nested-with-keys-3' => [
                '/* testNestedShortArrayWithKeys_3 */',
                [
                    'array' => true,
                    'list'  => false,
                ],
            ],
            'short-list-with-nesting-and-keys' => [
                '/* testShortListWithNestingAndKeys */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'short-list-nested-with-keys-1' => [
                '/* testNestedShortListWithKeys_1 */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'short-list-nested-with-keys-2' => [
                '/* testNestedShortListWithKeys_2 */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'short-list-nested-with-keys-3' => [
                '/* testNestedShortListWithKeys_3 */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'short-list-nested-empty' => [
                '/* testNestedShortListEmpty */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'short-list-deeply-nested' => [
                '/* testDeeplyNestedShortList */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'short-list-without-vars' => [
                '/* testShortListWithoutVars */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'short-list-nested-long-list' => [
                '/* testShortListNestedLongList */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'parse-error-anon-class-trait-use-as' => [
                '/* testNestedAnonClassWithTraitUseAs */',
                [
                    'array' => true,
                    'list'  => false,
                ],
            ],
            'parse-error-use-as' => [
                '/* testParseError */',
                [
                    'array' => true,
                    'list'  => false,
                ],
            ],
            'parse-error-live-coding' => [
                '/* testLiveCodingNested */',
                [
                    'array' => true,
                    'list'  => false,
                ],
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
        $cases      = $this->dataIsShortArrayOrList();
        $testMarker = $cases['short-array-in-foreach'][0];
        $expected   = $cases['short-array-in-foreach'][1]['array'];

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
        $cases      = $this->dataIsShortArrayOrList();
        $testMarker = $cases['short-list-with-nesting-and-keys'][0];
        $expected   = $cases['short-list-with-nesting-and-keys'][1]['list'];

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
