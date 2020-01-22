<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Operators;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Operators;

/**
 * Tests for the \PHPCSUtils\Utils\Operators::isUnaryPlusMinus() method.
 *
 * @covers \PHPCSUtils\Utils\Operators::isUnaryPlusMinus
 *
 * @group operators
 *
 * @since 1.0.0
 */
class IsUnaryPlusMinusTest extends UtilityMethodTestCase
{

    /**
     * Test that false is returned when a non-existent token is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(Operators::isUnaryPlusMinus(self::$phpcsFile, 10000));
    }

    /**
     * Test that false is returned when a non-plus/minus token is passed.
     *
     * @return void
     */
    public function testNotPlusMinusToken()
    {
        $target = $this->getTargetToken('/* testNonUnaryPlus */', \T_LNUMBER);
        $this->assertFalse(Operators::isUnaryPlusMinus(self::$phpcsFile, $target));
    }

    /**
     * Test whether a T_PLUS or T_MINUS token is a unary operator.
     *
     * @dataProvider dataIsUnaryPlusMinus
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   The expected boolean return value.
     *
     * @return void
     */
    public function testIsUnaryPlusMinus($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [\T_PLUS, \T_MINUS]);
        $result   = Operators::isUnaryPlusMinus(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testIsUnaryPlusMinus() For the array format.
     *
     * @return array
     */
    public function dataIsUnaryPlusMinus()
    {
        return [
            'non-unary-plus' => [
                '/* testNonUnaryPlus */',
                false,
            ],
            'non-unary-minus' => [
                '/* testNonUnaryMinus */',
                false,
            ],
            'non-unary-plus-arrays' => [
                '/* testNonUnaryPlusArrays */',
                false,
            ],
            'unary-minus-arithmetic' => [
                '/* testUnaryMinusArithmetic */',
                true,
            ],
            'unary-plus-arithmetic' => [
                '/* testUnaryPlusArithmetic */',
                true,
            ],
            'unary-minus-concatenation' => [
                '/* testUnaryMinusConcatenation */',
                true,
            ],
            'unary-plus-int-assignment' => [
                '/* testUnaryPlusIntAssignment */',
                true,
            ],
            'unary-minus-variable-assignment' => [
                '/* testUnaryMinusVariableAssignment */',
                true,
            ],
            'unary-plus-float-assignment' => [
                '/* testUnaryPlusFloatAssignment */',
                true,
            ],
            'unary-minus-bool-assignment' => [
                '/* testUnaryMinusBoolAssignment */',
                true,
            ],
            'unary-plus-string-assignment-with-comment' => [
                '/* testUnaryPlusStringAssignmentWithComment */',
                true,
            ],
            'unary-minus-string-assignment' => [
                '/* testUnaryMinusStringAssignment */',
                true,
            ],
            'unary-plus-plus-null-assignment' => [
                '/* testUnaryPlusNullAssignment */',
                true,
            ],
            'unary-minus-variable-variable-assignment' => [
                '/* testUnaryMinusVariableVariableAssignment */',
                true,
            ],
            'unary-plus-int-comparison' => [
                '/* testUnaryPlusIntComparison */',
                true,
            ],
            'unary-plus-int-comparison-yoda' => [
                '/* testUnaryPlusIntComparisonYoda */',
                true,
            ],
            'unary-minus-float-comparison' => [
                '/* testUnaryMinusFloatComparison */',
                true,
            ],
            'unary-minus-string-comparison-yoda' => [
                '/* testUnaryMinusStringComparisonYoda */',
                true,
            ],
            'unary-plus-variable-boolean' => [
                '/* testUnaryPlusVariableBoolean */',
                true,
            ],
            'unary-minus-variable-boolean' => [
                '/* testUnaryMinusVariableBoolean */',
                true,
            ],
            'unary-plus-logical-xor' => [
                '/* testUnaryPlusLogicalXor */',
                true,
            ],
            'unary-minus-ternary-then' => [
                '/* testUnaryMinusTernaryThen */',
                true,
            ],
            'unary-plus-ternary-else' => [
                '/* testUnaryPlusTernaryElse */',
                true,
            ],
            'unary-minus-coalesce' => [
                '/* testUnaryMinusCoalesce */',
                true,
            ],
            'unary-plus-int-return' => [
                '/* testUnaryPlusIntReturn */',
                true,
            ],
            'unary-minus-float-return' => [
                '/* testUnaryMinusFloatReturn */',
                true,
            ],
            'unary-plus-print' => [
                '/* testUnaryPlusPrint */',
                true,
            ],
            'unary-minus-echo' => [
                '/* testUnaryMinusEcho */',
                true,
            ],
            'unary-plus-yield' => [
                '/* testUnaryPlusYield */',
                true,
            ],
            'unary-plus-array-access' => [
                '/* testUnaryPlusArrayAccess */',
                true,
            ],
            'unary-minus-string-array-access' => [
                '/* testUnaryMinusStringArrayAccess */',
                true,
            ],
            'unary-plus-long-array-assignment' => [
                '/* testUnaryPlusLongArrayAssignment */',
                true,
            ],
            'unary-minus-long-array-assignment-key' => [
                '/* testUnaryMinusLongArrayAssignmentKey */',
                true,
            ],
            'unary-plus-long-array-assignment-value' => [
                '/* testUnaryPlusLongArrayAssignmentValue */',
                true,
            ],
            'unary-plus-short-array-assignment' => [
                '/* testUnaryPlusShortArrayAssignment */',
                true,
            ],
            'non-unary-minus-short-array-assignment' => [
                '/* testNonUnaryMinusShortArrayAssignment */',
                false,
            ],
            'unary-minus-casts' => [
                '/* testUnaryMinusCast */',
                true,
            ],
            'unary-plus-function-call-param' => [
                '/* testUnaryPlusFunctionCallParam */',
                true,
            ],
            'unary-minus-function-call-param' => [
                '/* testUnaryMinusFunctionCallParam */',
                true,
            ],
            'unary-plus-declare' => [
                '/* testUnaryPlusDeclare */',
                true,
            ],
            'unary-plus-switch-case' => [
                '/* testUnaryPlusCase */',
                true,
            ],
            'unary-minus-switch-case' => [
                '/* testUnaryMinusCase */',
                true,
            ],
            'operator-sequence-non-unary-1' => [
                '/* testSequenceNonUnary1 */',
                false,
            ],
            'operator-sequence-non-unary-2' => [
                '/* testSequenceNonUnary2 */',
                false,
            ],
            'operator-sequence-non-unary-3' => [
                '/* testSequenceNonUnary3 */',
                false,
            ],
            'operator-sequence-unary-end' => [
                '/* testSequenceUnaryEnd */',
                true,
            ],
            'parse-error' => [
                '/* testParseError */',
                false,
            ],
        ];
    }
}
