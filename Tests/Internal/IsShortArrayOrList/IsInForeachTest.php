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

use PHPCSUtils\Internal\IsShortArrayOrList;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests to make sure that brackets inside foreach conditions are recognized correctly as short array/short lists or
 * real square brackets.
 *
 * @covers \PHPCSUtils\Internal\IsShortArrayOrList::isInForeach
 *
 * @since 1.0.0
 */
final class IsInForeachTest extends UtilityMethodTestCase
{

    /**
     * Test that a short array which is in a control structure, but not in a foreach bows out
     * of the `IsShortArrayOrList::isInForeach()` function.
     *
     * @return void
     */
    public function testNotInForeach()
    {
        $stackPtr = $this->getTargetToken('/* testNotInForeach */', \T_OPEN_SHORT_ARRAY);
        $solver   = new IsShortArrayOrList(self::$phpcsFile, $stackPtr);
        $type     = $solver->solve();

        $this->assertSame(IsShortArrayOrList::SHORT_ARRAY, $type);
    }

    /**
     * Test that brackets inside foreach conditions are recognized correctly as short array/short lists.
     *
     * @dataProvider dataIsInForeachResolved
     * @dataProvider dataIsInForeachNestedResolvedViaOuter
     * @dataProvider dataIsInForeachUndetermined
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param string $expected   The expected function output.
     *
     * @return void
     */
    public function testIsInForeach($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [\T_OPEN_SHORT_ARRAY]);
        $solver   = new IsShortArrayOrList(self::$phpcsFile, $stackPtr);
        $type     = $solver->solve();

        $this->assertSame($expected, $type);
    }

    /**
     * Data provider 1.
     *
     * These test cases should get resolved DIRECTLY via the `isInForeach()` method.
     *
     * @see testIsInForeach() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataIsInForeachResolved()
    {
        return [
            'resolved: short array in foreach' => [
                'testMarker' => '/* testShortArrayInForeach */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'resolved: short array in foreach with nested shortlist' => [
                'testMarker' => '/* testShortArrayInForeachWithNestedShortList */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'resolved: short array in foreach with assignment' => [
                'testMarker' => '/* testShortArrayInForeachWithAssignment */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'resolved: short list in foreach' => [
                'testMarker' => '/* testShortListInForeach */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'resolved: short list in foreach with key' => [
                'testMarker' => '/* testShortListInForeachWithKey */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'resolved: short list in foreach with list keys' => [
                'testMarker' => '/* testShortListInForeachWithListKeys */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'resolved: short list in foreach with reference' => [
                'testMarker' => '/* testShortlistInForeachWithReference */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
        ];
    }

    /**
     * Data provider 2.
     *
     * These test cases should get resolved INDIRECTLY once the `isInForeach()` method
     * is called for the OUTER set of brackets.
     *
     * @see testIsInForeach() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataIsInForeachNestedResolvedViaOuter()
    {
        return [
            'resolved-on-outer: short array in foreach nested at start' => [
                'testMarker' => '/* testShortArrayInForeachNestedAtStart */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'resolved-on-outer: short array in foreach nested in middle' => [
                'testMarker' => '/* testShortArrayInForeachNestedInMiddle */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'resolved-on-outer: short array in foreach nested at end' => [
                'testMarker' => '/* testShortArrayInForeachNestedAtEnd */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'resolved-on-outer: short array empty in foreach nested in middle' => [
                'testMarker' => '/* testShortArrayEmptyInForeachNestedInMiddle */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'resolved-on-outer: short list in foreach nested at start' => [
                'testMarker' => '/* testShortListInForeachNestedAtStart */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'resolved-on-outer: short list in foreach nested in middle' => [
                'testMarker' => '/* testShortListInForeachNestedInMiddle */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'resolved-on-outer: short list in foreach nested at end' => [
                'testMarker' => '/* testShortListInForeachNestedAtEnd */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
        ];
    }

    /**
     * Data provider 3.
     *
     * These are test cases, which do involve a `foreach`, but will not get resolved via the `isInForeach()` method.
     *
     * @see testIsInForeach() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataIsInForeachUndetermined()
    {
        return [
            'undetermined: short array in function call in foreach' => [
                'testMarker' => '/* testShortArrayInFunctionCallInForeach */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'undetermined: short array in foreach as key in shortlist 1' => [
                'testMarker' => '/* testShortArrayAsKeyInShortList1 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'undetermined: short array in foreach as key in shortlist 2' => [
                'testMarker' => '/* testShortArrayAsKeyInShortList2 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'undetermined: short list nested in short array in foreach' => [
                'testMarker' => '/* testShortListNestedInShortArrayInForeach */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'undetermined: nested short list nested in short array in foreach' => [
                'testMarker' => '/* testNestedShortListNestedInShortArrayInForeach */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],

            'undetermined: short array in foreach as key after as' => [
                'testMarker' => '/* testShortArrayAsKeyAfterAs */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'undetermined: parse error missing bracket' => [
                'testMarker' => '/* testParseError */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'undetermined: parse error foreach without as' => [
                'testMarker' => '/* testForeachWithoutAs */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
        ];
    }
}
