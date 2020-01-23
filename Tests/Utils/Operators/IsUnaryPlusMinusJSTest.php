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

use PHPCSUtils\Tests\Utils\Operators\IsUnaryPlusMinusTest;

/**
 * Tests for the \PHPCSUtils\Utils\Operators::isUnaryPlusMinus() method.
 *
 * @covers \PHPCSUtils\Utils\Operators::isUnaryPlusMinus
 *
 * @group operators
 *
 * @since 1.0.0
 */
class IsUnaryPlusMinusJSTest extends IsUnaryPlusMinusTest
{

    /**
     * The file extension of the test case file (without leading dot).
     *
     * @var string
     */
    protected static $fileExtension = 'js';

    /**
     * Data provider.
     *
     * @see IsUnaryPlusMinusTest::testIsUnaryPlusMinus() For the array format.
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
            'unary-minus-colon' => [
                '/* testUnaryMinusColon */',
                true,
            ],
            'unary-minus-switch-case' => [
                '/* testUnaryMinusCase */',
                true,
            ],
            'unary-minus-ternary-then' => [
                '/* testUnaryMinusTernaryThen */',
                true,
            ],
            'unary-minus-ternary-else' => [
                '/* testUnaryPlusTernaryElse */',
                true,
            ],
            'unary-minus-if-condition' => [
                '/* testUnaryMinusIfCondition */',
                true,
            ],
        ];
    }
}
