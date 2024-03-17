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
 * Tests for the \PHPCSUtils\Internal\IsShortArrayOrList class.
 *
 * Note: these tests don't _strictly_ test any particular part of the class, they test
 * the whole class and non-method specific tests for the `IsShortArrayOrList` class
 * should be added to this file.
 *
 * @coversNothing
 *
 * @since 1.0.0
 */
final class IsShortArrayOrListTest extends UtilityMethodTestCase
{

    /**
     * Test whether a T_OPEN_SHORT_ARRAY token is correctly determined to be a short array,
     * a short list or a real square bracket.
     *
     * @dataProvider dataIsShortArrayOrList
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param string $expected   The expected return value.
     *
     * @return void
     */
    public function testIsShortArrayOrList($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_OPEN_SHORT_ARRAY);
        $solver   = new IsShortArrayOrList(self::$phpcsFile, $stackPtr);
        $type     = $solver->solve();

        $this->assertSame($expected, $type);
    }

    /**
     * Data provider.
     *
     * @see testIsShortArrayOrList() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataIsShortArrayOrList()
    {
        return [
            'short-array-comparison-no-assignment' => [
                'testMarker' => '/* testShortArrayInComparison */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-comparison-no-assignment-nested' => [
                'testMarker' => '/* testShortArrayNestedInComparison */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-union-before' => [
                'testMarker' => '/* testShortArrayUnionFirst */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-union-after' => [
                'testMarker' => '/* testShortArrayUnionSecond */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-equal-before' => [
                'testMarker' => '/* testShortArrayEqualFirst */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-equal-after' => [
                'testMarker' => '/* testShortArrayEqualSecond */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-identical-before' => [
                'testMarker' => '/* testShortArrayIdenticalFirst */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-identical-after' => [
                'testMarker' => '/* testShortArrayIdenticalSecond */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-not-equal-before' => [
                'testMarker' => '/* testShortArrayNotEqualFirst */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-not-equal-after' => [
                'testMarker' => '/* testShortArrayNotEqualSecond */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-not-equal-brackets-before' => [
                'testMarker' => '/* testShortArrayNotEqualBracketsFirst */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-not-equal-brackets-after' => [
                'testMarker' => '/* testShortArrayNotEqualBracketsSecond */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-not-identical-before' => [
                'testMarker' => '/* testShortArrayNonIdenticalFirst */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-not-identical-after' => [
                'testMarker' => '/* testShortArrayNonIdenticalSecond */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-list-multi-item' => [
                'testMarker' => '/* testShortListMultiItem */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-with-keys' => [
                'testMarker' => '/* testShortListWithKeys */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-with-nesting' => [
                'testMarker' => '/* testShortListWithNesting */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-nested' => [
                'testMarker' => '/* testShortListNested */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-in-chained-assignment' => [
                'testMarker' => '/* testShortlistMultiAssign */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-array-in-chained-assignment' => [
                'testMarker' => '/* testShortArrayMultiAssign */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-with-nesting-and-keys' => [
                'testMarker' => '/* testShortArrayWithNestingAndKeys */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-nested-with-keys-1' => [
                'testMarker' => '/* testNestedShortArrayWithKeys_1 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-nested-with-keys-2' => [
                'testMarker' => '/* testNestedShortArrayWithKeys_2 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-nested-with-keys-3' => [
                'testMarker' => '/* testNestedShortArrayWithKeys_3 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-nested-unpacking-without-keys-php74' => [
                'testMarker' => '/* testNestedShortArrayPHP74UnpackingWithoutKeys */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-nested-unpacking-with-keys-php81' => [
                'testMarker' => '/* testNestedShortArrayPHP81UnpackingWithKeys */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-list-with-nesting-and-keys' => [
                'testMarker' => '/* testShortListWithNestingAndKeys */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-nested-with-keys-1' => [
                'testMarker' => '/* testNestedShortListWithKeys_1 */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-nested-with-keys-2' => [
                'testMarker' => '/* testNestedShortListWithKeys_2 */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-nested-with-keys-3' => [
                'testMarker' => '/* testNestedShortListWithKeys_3 */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-deeply-nested' => [
                'testMarker' => '/* testDeeplyNestedShortList */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-in-braced-control-structure' => [
                'testMarker' => '/* testShortListInBracedControlStructure */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-in-alternative-control-structure' => [
                'testMarker' => '/* testShortListInAlternativeControlStructure */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-containing-short-array-as-key' => [
                'testMarker' => '/* testShortListWithShortArrayAsKey */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-array-as-key-for-nested-short-list' => [
                'testMarker' => '/* testShortArrayAsKeyForShortList */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-list-nested-with-array-key' => [
                'testMarker' => '/* testShortListWithShortArrayAsKeyNested */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-containing-short-array-in-key' => [
                'testMarker' => '/* testShortListWithShortArrayInKey */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-array-in-key-for-short-list-closure-default' => [
                'testMarker' => '/* testShortArrayInKeyForShortListA */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-in-key-for-short-list-closure-call-param' => [
                'testMarker' => '/* testShortArrayInKeyForShortListB */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-list-nested-with-array-in-key' => [
                'testMarker' => '/* testShortListWithShortArrayInKeyNested */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-nested-with-empties' => [
                'testMarker' => '/* testNestedShortListWithEmpties */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],

            'short-list-in-closure-in-short-array-value' => [
                'testMarker' => '/* testShortListInClosureInShortArrayValue */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-nested-in-closure-in-short-array-value' => [
                'testMarker' => '/* testNestedShortListInClosureInShortArrayValue */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-array-in-closure-in-short-array-value' => [
                'testMarker' => '/* testShortArrayInClosureInShortArrayValue */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-nested-in-closure-in-short-array-value' => [
                'testMarker' => '/* testNestedShortArrayInClosureInShortArrayValue */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-in-function-call-in-short-array-value' => [
                'testMarker' => '/* testShortArrayInFunctionCallInShortArrayValue */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-list-in-short-array-value' => [
                'testMarker' => '/* testPlainShortListInShortArrayValue */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-in-short-array-value-no-key' => [
                'testMarker' => '/* testPlainShortListInShortArrayValueNoKey */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-keyed-in-short-array-value-1' => [
                'testMarker' => '/* testKeyedShortListInShortArrayValue1 */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-array-as-key-for-short-list-in-array-value' => [
                'testMarker' => '/* testShortArrayInShortListAsKey */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-list-keyed-in-short-array-value-2' => [
                'testMarker' => '/* testKeyedShortListInShortArrayValue2 */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-array-in-function-call-as-key-for-short-list-in-array-value' => [
                'testMarker' => '/* testShortArrayInFunctionCallInShortListKey */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-list-nested-and-empty-in-short-array-value' => [
                'testMarker' => '/* testEmptyShortListInShortListInShortArrayValue */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],

            'outer-short-array-stop-test-square-open' => [
                'testMarker' => '/* testOuterShortArrayStopAtBrackets */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'inner-short-array-stop-test-square-open-1' => [
                'testMarker' => '/* testShortArrayInShortArrayKeyStopAtBrackets */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'inner-short-array-stop-test-square-open-2' => [
                'testMarker' => '/* testShortArrayInShortListKeyStopAtBrackets */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'short-array-comma-before-after-in-function-call' => [
                'testMarker' => '/* testRiskySyntaxCombiButNonNested-FunctionCall */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-comma-before-after-in-closure-call' => [
                'testMarker' => '/* testRiskySyntaxCombiButNonNested-ClosureCall */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-comma-before-after-in-fn-call' => [
                'testMarker' => '/* testRiskySyntaxCombiButNonNested-FnCall */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-comma-before-after-in-echo-statement' => [
                'testMarker' => '/* testRiskySyntaxCombiButNonNested-Echo */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'short-array-in-arrow-fn-return-expression' => [
                'testMarker' => '/* testShortArrayInShortArrowFunction */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'short-array-in-match-condition' => [
                'testMarker' => '/* testShortArrayConditionInMatchExpression */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-nested-in-match-condition' => [
                'testMarker' => '/* testNestedShortArrayConditionInMatchExpression */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-in-match-return-expression' => [
                'testMarker' => '/* testShortArrayReturnedFromMatchExpression */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-array-nested-in-match-return-expression' => [
                'testMarker' => '/* testNestedShortArrayReturnedFromMatchExpression */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short-list-in-match-condition' => [
                'testMarker' => '/* testShortListConditionInMatchExpression */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-nested-in-match-condition' => [
                'testMarker' => '/* testNestedShortListConditionInMatchExpression */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-in-match-return-expression' => [
                'testMarker' => '/* testShortListReturnedFromMatchExpression */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-nested-in-match-return-expression' => [
                'testMarker' => '/* testNestedShortListReturnedFromMatchExpression */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-array-nested-in-long-array' => [
                'testMarker' => '/* testShortArrayNestedInLongArray */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            // Invalid syntaxes.
            'short-list-nested-empty' => [
                'testMarker' => '/* testNestedShortListEmpty */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-without-vars' => [
                'testMarker' => '/* testShortListWithoutVars */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short-list-nested-long-list' => [
                'testMarker' => '/* testShortListNestedLongList */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'long-list-with-nested-short-list' => [
                'testMarker' => '/* testLongListNestedShortList */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'parse-error-anon-class-trait-use-as' => [
                'testMarker' => '/* testNestedAnonClassWithTraitUseAs */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'parse-error-use-as' => [
                'testMarker' => '/* testParseError */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
        ];
    }
}
