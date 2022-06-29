<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Operators;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Numbers;
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
     * @param bool   $maybeSkip  Whether the "should this test be skipped" check should be executed.
     *                           Defaults to false.
     *
     * @return void
     */
    public function testIsUnaryPlusMinus($testMarker, $expected, $maybeSkip = false)
    {
        if ($maybeSkip === true) {
            /*
             * Skip the test if this is PHP 7.4 or a PHPCS version which backfills the token sequence
             * to one token as in that case, the plus/minus token won't exist
             */
            if (\version_compare(\PHP_VERSION_ID, '70399', '>') === true) {
                $this->markTestSkipped('Test irrelevant as the target token won\'t exist when on PHP >= 7.4');
            }

            if (\version_compare(static::$phpcsVersion, Numbers::UNSUPPORTED_PHPCS_VERSION, '>=') === true) {
                $this->markTestSkipped(
                    'Test irrelevant as the target token won\'t exist when on PHPCS >= '
                    . Numbers::UNSUPPORTED_PHPCS_VERSION
                );
            }
        }

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
                'testMarker' => '/* testNonUnaryPlus */',
                'expected'   => false,
            ],
            'non-unary-minus' => [
                'testMarker' => '/* testNonUnaryMinus */',
                'expected'   => false,
            ],
            'non-unary-plus-arrays' => [
                'testMarker' => '/* testNonUnaryPlusArrays */',
                'expected'   => false,
            ],
            'unary-minus-arithmetic' => [
                'testMarker' => '/* testUnaryMinusArithmetic */',
                'expected'   => true,
            ],
            'unary-plus-arithmetic' => [
                'testMarker' => '/* testUnaryPlusArithmetic */',
                'expected'   => true,
            ],
            'unary-minus-concatenation' => [
                'testMarker' => '/* testUnaryMinusConcatenation */',
                'expected'   => true,
            ],
            'unary-plus-int-assignment' => [
                'testMarker' => '/* testUnaryPlusIntAssignment */',
                'expected'   => true,
            ],
            'unary-minus-variable-assignment' => [
                'testMarker' => '/* testUnaryMinusVariableAssignment */',
                'expected'   => true,
            ],
            'unary-plus-float-assignment' => [
                'testMarker' => '/* testUnaryPlusFloatAssignment */',
                'expected'   => true,
            ],
            'unary-minus-bool-assignment' => [
                'testMarker' => '/* testUnaryMinusBoolAssignment */',
                'expected'   => true,
            ],
            'unary-plus-string-assignment-with-comment' => [
                'testMarker' => '/* testUnaryPlusStringAssignmentWithComment */',
                'expected'   => true,
            ],
            'unary-minus-string-assignment' => [
                'testMarker' => '/* testUnaryMinusStringAssignment */',
                'expected'   => true,
            ],
            'unary-plus-plus-null-assignment' => [
                'testMarker' => '/* testUnaryPlusNullAssignment */',
                'expected'   => true,
            ],
            'unary-minus-variable-variable-assignment' => [
                'testMarker' => '/* testUnaryMinusVariableVariableAssignment */',
                'expected'   => true,
            ],
            'unary-plus-int-comparison' => [
                'testMarker' => '/* testUnaryPlusIntComparison */',
                'expected'   => true,
            ],
            'unary-plus-int-comparison-yoda' => [
                'testMarker' => '/* testUnaryPlusIntComparisonYoda */',
                'expected'   => true,
            ],
            'unary-minus-float-comparison' => [
                'testMarker' => '/* testUnaryMinusFloatComparison */',
                'expected'   => true,
            ],
            'unary-minus-string-comparison-yoda' => [
                'testMarker' => '/* testUnaryMinusStringComparisonYoda */',
                'expected'   => true,
            ],
            'unary-plus-variable-boolean' => [
                'testMarker' => '/* testUnaryPlusVariableBoolean */',
                'expected'   => true,
            ],
            'unary-minus-variable-boolean' => [
                'testMarker' => '/* testUnaryMinusVariableBoolean */',
                'expected'   => true,
            ],
            'unary-plus-logical-xor' => [
                'testMarker' => '/* testUnaryPlusLogicalXor */',
                'expected'   => true,
            ],
            'unary-minus-ternary-then' => [
                'testMarker' => '/* testUnaryMinusTernaryThen */',
                'expected'   => true,
            ],
            'unary-plus-ternary-else' => [
                'testMarker' => '/* testUnaryPlusTernaryElse */',
                'expected'   => true,
            ],
            'unary-minus-coalesce' => [
                'testMarker' => '/* testUnaryMinusCoalesce */',
                'expected'   => true,
            ],
            'unary-plus-int-return' => [
                'testMarker' => '/* testUnaryPlusIntReturn */',
                'expected'   => true,
            ],
            'unary-minus-float-return' => [
                'testMarker' => '/* testUnaryMinusFloatReturn */',
                'expected'   => true,
            ],
            'unary-minus-int-exit' => [
                'testMarker' => '/* testUnaryPlusIntExit */',
                'expected'   => true,
            ],
            'unary-plus-print' => [
                'testMarker' => '/* testUnaryPlusPrint */',
                'expected'   => true,
            ],
            'unary-minus-echo' => [
                'testMarker' => '/* testUnaryMinusEcho */',
                'expected'   => true,
            ],
            'unary-plus-yield' => [
                'testMarker' => '/* testUnaryPlusYield */',
                'expected'   => true,
            ],
            'unary-plus-array-access' => [
                'testMarker' => '/* testUnaryPlusArrayAccess */',
                'expected'   => true,
            ],
            'unary-minus-string-array-access' => [
                'testMarker' => '/* testUnaryMinusStringArrayAccess */',
                'expected'   => true,
            ],
            'unary-plus-long-array-assignment' => [
                'testMarker' => '/* testUnaryPlusLongArrayAssignment */',
                'expected'   => true,
            ],
            'unary-minus-long-array-assignment-key' => [
                'testMarker' => '/* testUnaryMinusLongArrayAssignmentKey */',
                'expected'   => true,
            ],
            'unary-plus-long-array-assignment-value' => [
                'testMarker' => '/* testUnaryPlusLongArrayAssignmentValue */',
                'expected'   => true,
            ],
            'unary-plus-short-array-assignment' => [
                'testMarker' => '/* testUnaryPlusShortArrayAssignment */',
                'expected'   => true,
            ],
            'non-unary-minus-short-array-assignment' => [
                'testMarker' => '/* testNonUnaryMinusShortArrayAssignment */',
                'expected'   => false,
            ],
            'unary-minus-casts' => [
                'testMarker' => '/* testUnaryMinusCast */',
                'expected'   => true,
            ],
            'unary-plus-function-call-param' => [
                'testMarker' => '/* testUnaryPlusFunctionCallParam */',
                'expected'   => true,
            ],
            'unary-minus-function-call-param' => [
                'testMarker' => '/* testUnaryMinusFunctionCallParam */',
                'expected'   => true,
            ],
            'unary-plus-declare' => [
                'testMarker' => '/* testUnaryPlusDeclare */',
                'expected'   => true,
            ],
            'unary-plus-switch-case' => [
                'testMarker' => '/* testUnaryPlusCase */',
                'expected'   => true,
            ],
            'unary-plus-continue' => [
                'testMarker' => '/* testUnaryPlusContinue */',
                'expected'   => true,
            ],
            'unary-minus-switch-case' => [
                'testMarker' => '/* testUnaryMinusCase */',
                'expected'   => true,
            ],
            'unary-plus-break' => [
                'testMarker' => '/* testUnaryPlusBreak */',
                'expected'   => true,
            ],
            'unary-minus-arrow-function' => [
                'testMarker' => '/* testUnaryMinusArrowFunction */',
                'expected'   => true,
            ],
            'operator-sequence-non-unary-1' => [
                'testMarker' => '/* testSequenceNonUnary1 */',
                'expected'   => false,
            ],
            'operator-sequence-non-unary-2' => [
                'testMarker' => '/* testSequenceNonUnary2 */',
                'expected'   => false,
            ],
            'operator-sequence-non-unary-3' => [
                'testMarker' => '/* testSequenceNonUnary3 */',
                'expected'   => false,
            ],
            'operator-sequence-unary-end' => [
                'testMarker' => '/* testSequenceUnaryEnd */',
                'expected'   => true,
            ],
            'php-7.4-underscore-float-containing-plus' => [
                'testMarker' => '/* testPHP74NumericLiteralFloatContainingPlus */',
                'expected'   => false,
                'maybeSkip'  => true, // Skip for PHP 7.4 & PHPCS 3.5.3+.
            ],
            'php-7.4-underscore-float-containing-minus' => [
                'testMarker' => '/* testPHP74NumericLiteralFloatContainingMinus */',
                'expected'   => false,
                'maybeSkip'  => true, // Skip for PHP 7.4 & PHPCS 3.5.3+.
            ],
            'php-7.4-underscore-int-calculation-1' => [
                'testMarker' => '/* testPHP74NumericLiteralIntCalc1 */',
                'expected'   => false,
            ],
            'php-7.4-underscore-int-calculation-2' => [
                'testMarker' => '/* testPHP74NumericLiteralIntCalc2 */',
                'expected'   => false,
            ],
            'php-7.4-underscore-float-calculation-1' => [
                'testMarker' => '/* testPHP74NumericLiteralFloatCalc1 */',
                'expected'   => false,
            ],
            'php-7.4-underscore-float-calculation-2' => [
                'testMarker' => '/* testPHP74NumericLiteralFloatCalc2 */',
                'expected'   => false,
            ],

            'parse-error' => [
                'testMarker' => '/* testParseError */',
                'expected'   => false,
            ],
        ];
    }
}
