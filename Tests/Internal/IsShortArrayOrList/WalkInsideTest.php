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
 * @covers \PHPCSUtils\Internal\IsShortArrayOrList::walkInside
 *
 * @since 1.0.0
 */
final class WalkInsideTest extends UtilityMethodTestCase
{

    /**
     * Test that nested brackets with ambivalent content get passed off back to the solve() method.
     *
     * @dataProvider dataWalkInsideUndetermined
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param string $expected   The expected function output.
     *
     * @return void
     */
    public function testWalkInsideUndetermined($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [\T_OPEN_SHORT_ARRAY]);
        $solver   = new IsShortArrayOrList(self::$phpcsFile, $stackPtr);
        $type     = $solver->solve();

        $this->assertSame($expected, $type);
    }

    /**
     * Data provider.
     *
     * @see testWalkInsideUndetermined() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataWalkInsideUndetermined()
    {
        return [
            'nested-short-array-empty' => [
                'testMarker' => '/* testNestedShortArrayEmpty */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'nested-short-array' => [
                'testMarker' => '/* testNestedShortArrayUndetermined */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-with-keys' => [
                'testMarker' => '/* testNestedShortArrayWithKeysUndetermined */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-with-reference' => [
                'testMarker' => '/* testNestedShortArrayWithReferenceUndetermined */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'nested-short-list-empty' => [
                'testMarker' => '/* testNestedShortListEmpty */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],

            'nested-short-list' => [
                'testMarker' => '/* testNestedShortListUndetermined */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'nested-short-list-with-keys' => [
                'testMarker' => '/* testNestedShortListWithKeysUndetermined */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'nested-short-list-with-reference' => [
                'testMarker' => '/* testNestedShortListWithReferenceUndetermined */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],

            'nested-short-array-operator-after-var-1' => [
                'testMarker' => '/* testNestedShortArrayValueHasContentAfterVar1 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-operator-after-var-2' => [
                'testMarker' => '/* testNestedShortArrayValueHasContentAfterVar2 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-operator-after-var-3' => [
                'testMarker' => '/* testNestedShortArrayValueHasContentAfterVar3 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-operator-after-var-4' => [
                'testMarker' => '/* testNestedShortArrayValueHasContentAfterVar4 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
        ];
    }

    /**
     * Test that nested brackets are recognized as a short array when based on the contents
     * it can't be a short list.
     *
     * @dataProvider dataWalkInsideResolved
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param string $expected   The expected function output.
     *
     * @return void
     */
    public function testWalkInsideResolved($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [\T_OPEN_SHORT_ARRAY]);
        $solver   = new IsShortArrayOrList(self::$phpcsFile, $stackPtr);
        $type     = $solver->solve();

        $this->assertSame($expected, $type);
    }

    /**
     * Data provider.
     *
     * @see testWalkInsideResolved() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataWalkInsideResolved()
    {
        return [
            'nested-short-array-no-vars-or-nested-null' => [
                'testMarker' => '/* testNestedShortArrayNoVarsOrNestedNull */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-no-vars-or-nested-ints' => [
                'testMarker' => '/* testNestedShortArrayNoVarsOrNestedInts */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-with-keys-no-vars-or-nested-text-strings' => [
                'testMarker' => '/* testNestedShortArrayWithKeysNoVarsOrNestedTextStrings */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-with-keys-no-vars-or-nested-bools' => [
                'testMarker' => '/* testNestedShortArrayWithKeysNoVarsOrNestedBools */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-no-vars-or-nested-floats' => [
                'testMarker' => '/* testNestedShortArrayNoVarsOrNestedFloats */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-no-vars-or-nested-long-array' => [
                'testMarker' => '/* testNestedShortArrayNoVarsOrNestedLongArray */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-no-vars-or-nested-object' => [
                'testMarker' => '/* testNestedShortArrayNoVarsOrNestedObject */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'nested-short-array-function-call' => [
                'testMarker' => '/* testNestedShortArrayFuncCall */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-namespaced-constant' => [
                'testMarker' => '/* testNestedShortArrayNamespacedConstant */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-class-constant' => [
                'testMarker' => '/* testNestedShortArrayClassConstant1 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-class-constant-with-hierarchy-keyword' => [
                'testMarker' => '/* testNestedShortArrayClassConstant2 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-fqn-method-call' => [
                'testMarker' => '/* testNestedShortArrayMethodCall */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-mixed-content' => [
                'testMarker' => '/* testNestedShortArrayMixedContent */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'nested-short-array-content-after-short-array-1' => [
                'testMarker' => '/* testNestedShortArrayValueHasContentAfterShortArray1 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-content-after-short-array-2' => [
                'testMarker' => '/* testNestedShortArrayValueHasContentAfterShortArray2 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-content-after-short-array-3' => [
                'testMarker' => '/* testNestedShortArrayValueHasContentAfterShortArray3 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'nested-short-array-with-nested-short-array-recursion-1' => [
                'testMarker' => '/* testNestedShortArrayRecursion1 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-with-nested-short-array-recursion-2' => [
                'testMarker' => '/* testNestedShortArrayRecursion2 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-with-nested-short-array-recursion-3' => [
                'testMarker' => '/* testNestedShortArrayRecursion3 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'nested-short-list-empty-entry-at-start' => [
                'testMarker' => '/* testNestedShortListEmptyEntryAtStart */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'nested-short-list-empty-entry-in-middle' => [
                'testMarker' => '/* testNestedShortListEmptyEntryInMiddle */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'nested-short-list-empty-entry-in-middle-with-comment' => [
                'testMarker' => '/* testNestedShortListEmptyEntryInMiddleWithComment */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'nested-short-list-empty-entry-at-end' => [
                'testMarker' => '/* testNestedShortListEmptyEntryAtEnd */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
        ];
    }

    /**
     * Ensure the "cannot be determined based on the array entry sample size" condition gets hit.
     *
     * @return void
     */
    public function testSampleTooSmall()
    {
        $stackPtr = $this->getTargetToken('/* testNestedShortArraySampleTooSmall */', \T_OPEN_SHORT_ARRAY);
        $solver   = new IsShortArrayOrList(self::$phpcsFile, $stackPtr);
        $type     = $solver->solve();

        $this->assertSame(IsShortArrayOrList::SHORT_ARRAY, $type);
    }

    /**
     * Ensure the "cannot be determined due to the recursion limit" condition gets hit.
     *
     * @dataProvider dataRecursionLimit
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param string $expected   The expected function output.
     *
     * @return void
     */
    public function testRecursionLimit($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [\T_OPEN_SHORT_ARRAY]);
        $solver   = new IsShortArrayOrList(self::$phpcsFile, $stackPtr);
        $type     = $solver->solve();

        $this->assertSame($expected, $type);
    }

    /**
     * Data provider.
     *
     * @see testRecursionLimit() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataRecursionLimit()
    {
        return [
            'nested-short-array-with-nested-short-array-recursion-4' => [
                'testMarker' => '/* testNestedShortArrayRecursion4 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested-short-array-with-nested-short-array-recursion-6' => [
                'testMarker' => '/* testNestedShortArrayRecursion6 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],

            'nested-short-list-with-nested-short-list-recursion-4' => [
                'testMarker' => '/* testNestedShortListRecursion4 */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
        ];
    }
}
