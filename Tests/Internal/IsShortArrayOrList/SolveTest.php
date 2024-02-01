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
use PHPCSUtils\Internal\StableCollections;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\Internal\IsShortArrayOrList class.
 *
 * @covers \PHPCSUtils\Internal\IsShortArrayOrList::solve
 *
 * @since 1.0.0
 */
final class SolveTest extends UtilityMethodTestCase
{

    /**
     * Test the logic of the solve() method.
     *
     * @dataProvider dataSolve
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param string $expected   The expected function output.
     *
     * @return void
     */
    public function testSolve($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, StableCollections::$shortArrayListOpenTokensBC);
        $solver   = new IsShortArrayOrList(self::$phpcsFile, $stackPtr);
        $type     = $solver->solve();

        $this->assertSame($expected, $type);
    }

    /**
     * Data provider.
     *
     * @see testSolve() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataSolve()
    {
        return [
            'real square brackets' => [
                'testMarker' => '/* testSquareBrackets */',
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'unambiguous short list' => [
                'testMarker' => '/* testShortList */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short array in attribute' => [
                'testMarker' => '/* testShortArrayInAttribute */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short array in foreach' => [
                'testMarker' => '/* testShortArrayInForeach */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short list in foreach' => [
                'testMarker' => '/* testShortListInForeach */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'unambiguous short array' => [
                'testMarker' => '/* testShortArray */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested short array, first item in parent' => [
                'testMarker' => '/* testNestedShortArrayParentBracketBefore */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested short list, last item in parent' => [
                'testMarker' => '/* testNestedShortListParentBracketAfter */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'nested short list, last item in parent, trailing comma' => [
                'testMarker' => '/* testNestedShortListParentBracketAfterWithTrailingComma */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'nested short array, content unambiguous' => [
                'testMarker' => '/* testNestedShortArrayContentNonAmbiguous */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'nested short list, content unambiguous' => [
                'testMarker' => '/* testNestedShortListContentNonAmbiguous */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'nested short array via outer brackets' => [
                'testMarker' => '/* testOuterShortArray */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short array, parse error' => [
                'testMarker' => '/* testLiveCodingNested */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
        ];
    }
}
