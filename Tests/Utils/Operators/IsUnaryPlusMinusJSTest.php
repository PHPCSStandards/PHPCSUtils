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

use PHPCSUtils\Tests\Utils\Operators\IsUnaryPlusMinusTestCase;

/**
 * Tests for the \PHPCSUtils\Utils\Operators::isUnaryPlusMinus() method.
 *
 * @covers \PHPCSUtils\Utils\Operators::isUnaryPlusMinus
 *
 * @group operators
 *
 * @since 1.0.0
 */
final class IsUnaryPlusMinusJSTest extends IsUnaryPlusMinusTestCase
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
     * @return array<string, array<string, string|bool>>
     */
    public static function dataIsUnaryPlusMinus()
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
            'unary-minus-colon' => [
                'testMarker' => '/* testUnaryMinusColon */',
                'expected'   => true,
            ],
            'unary-minus-switch-case' => [
                'testMarker' => '/* testUnaryMinusCase */',
                'expected'   => true,
            ],
            'unary-minus-ternary-then' => [
                'testMarker' => '/* testUnaryMinusTernaryThen */',
                'expected'   => true,
            ],
            'unary-minus-ternary-else' => [
                'testMarker' => '/* testUnaryPlusTernaryElse */',
                'expected'   => true,
            ],
            'unary-minus-if-condition' => [
                'testMarker' => '/* testUnaryMinusIfCondition */',
                'expected'   => true,
            ],
        ];
    }
}
