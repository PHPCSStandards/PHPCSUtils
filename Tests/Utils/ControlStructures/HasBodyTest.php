<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\ControlStructures;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\ControlStructures;

/**
 * Tests for the \PHPCSUtils\Utils\ControlStructures::hasBody() method.
 *
 * @covers \PHPCSUtils\Utils\ControlStructures::hasBody
 *
 * @group controlstructures
 *
 * @since 1.0.0
 */
class HasBodyTest extends UtilityMethodTestCase
{

    /**
     * Test that false is returned when a non-existent token is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(ControlStructures::hasBody(self::$phpcsFile, 10000));
    }

    /**
     * Test that false is returned when a non-control structure token is passed.
     *
     * @return void
     */
    public function testNotControlStructure()
    {
        $target = $this->getTargetToken('/* testNotControlStructure */', \T_ECHO);
        $this->assertFalse(ControlStructures::hasBody(self::$phpcsFile, $target));
    }

    /**
     * Test whether the function correctly identifies whether a control structure has a body.
     *
     * @dataProvider dataHasBody
     *
     * @param string $testMarker      The comment which prefaces the target token in the test file.
     * @param bool   $hasBody         The expected boolean return value when the function is called
     *                                with `$allowEmpty = true`.
     * @param bool   $hasNonEmptyBody The expected boolean return value when the function is called
     *                                with `$allowEmpty = false`.
     *
     * @return void
     */
    public function testHasBody($testMarker, $hasBody, $hasNonEmptyBody)
    {
        $stackPtr = $this->getTargetToken($testMarker, Collections::controlStructureTokens());

        $result = ControlStructures::hasBody(self::$phpcsFile, $stackPtr);
        $this->assertSame($hasBody, $result, 'Failed hasBody check with $allowEmpty = true');

        $result = ControlStructures::hasBody(self::$phpcsFile, $stackPtr, false);
        $this->assertSame($hasNonEmptyBody, $result, 'Failed hasBody check with $allowEmpty = false');
    }

    /**
     * Data provider.
     *
     * @see testHasBody() For the array format.
     *
     * @return array
     */
    public function dataHasBody()
    {
        return [
            'if-without-body' => [
                '/* testIfWithoutBody */',
                false,
                false,
            ],
            'if-empty-body' => [
                '/* testIfEmptyBody */',
                true,
                false,
            ],
            'elseif-empty-body' => [
                '/* testElseIfEmptyBody */',
                true,
                false,
            ],
            'else-if-empty-body' => [
                '/* testElseSpaceIfEmptyBody */',
                true,
                false,
            ],
            'else-empty-body' => [
                '/* testElseEmptyBody */',
                true,
                false,
            ],
            'if-with-code' => [
                '/* testIfWithCode */',
                true,
                true,
            ],
            'elseif-with-code' => [
                '/* testElseIfWithCode */',
                true,
                true,
            ],
            'else-if-with-code' => [
                '/* testElseSpaceIfWithCode */',
                true,
                true,
            ],
            'else-with-code' => [
                '/* testElseWithCode */',
                true,
                true,
            ],
            'for-without-body' => [
                '/* testForWithoutBody */',
                false,
                false,
            ],
            'for-empty-body' => [
                '/* testForEmptyBody */',
                true,
                false,
            ],
            'for-with-code' => [
                '/* testForWithCode */',
                true,
                true,
            ],
            'foreach-without-body' => [
                '/* testForEachWithoutBody */',
                false,
                false,
            ],
            'foreach-empty-body' => [
                '/* testForEachEmptyBody */',
                true,
                false,
            ],
            'foreach-with-code' => [
                '/* testForEachWithCode */',
                true,
                true,
            ],
            'while-without-body' => [
                '/* testWhileWithoutBody */',
                false,
                false,
            ],
            'while-empty-body' => [
                '/* testWhileEmptyBody */',
                true,
                false,
            ],
            'while-with-code' => [
                '/* testWhileWithCode */',
                true,
                true,
            ],
            'do-while-empty-body' => [
                '/* testDoWhileEmptyBody */',
                true,
                false,
            ],
            'do-while-with-code' => [
                '/* testDoWhileWithCode */',
                true,
                true,
            ],
            'switch-without-body' => [
                '/* testSwitchWithoutBody */',
                false,
                false,
            ],
            'switch-empty-body' => [
                '/* testSwitchEmptyBody */',
                true,
                false,
            ],
            'switch-with-code' => [
                '/* testSwitchWithCode */',
                true,
                true,
            ],
            'declare-without-body' => [
                '/* testDeclareWithoutBody */',
                false,
                false,
            ],
            'declare-empty-body' => [
                '/* testDeclareEmptyBody */',
                true,
                false,
            ],
            'declare-with-code' => [
                '/* testDeclareWithCode */',
                true,
                true,
            ],
            'alternative-syntax-if-empty-body' => [
                '/* testAlternativeIfEmptyBody */',
                true,
                false,
            ],
            'alternative-syntax-elseif-with-code' => [
                '/* testAlternativeElseIfWithCode */',
                true,
                true,
            ],
            'alternative-syntax-else-with-code' => [
                '/* testAlternativeElseWithCode */',
                true,
                true,
            ],
            'alternative-syntax-for-empty-body' => [
                '/* testAlternativeForEmptyBody */',
                true,
                false,
            ],
            'alternative-syntax-for-with-code' => [
                '/* testAlternativeForWithCode */',
                true,
                true,
            ],
            'alternative-syntax-foreach-empty-body' => [
                '/* testAlternativeForeachEmptyBody */',
                true,
                false,
            ],
            'alternative-syntax-foreach-with-code' => [
                '/* testAlternativeForeachWithCode */',
                true,
                true,
            ],
            'alternative-syntax-while-empty-body' => [
                '/* testAlternativeWhileEmptyBody */',
                true,
                false,
            ],
            'alternative-syntax-while-with-code' => [
                '/* testAlternativeWhileWithCode */',
                true,
                true,
            ],
            'alternative-syntax-switch-empty-body' => [
                '/* testAlternativeSwitchEmptyBody */',
                true,
                false,
            ],
            'alternative-syntax-switch-with-code' => [
                '/* testAlternativeSwitchWithCode */',
                true,
                true,
            ],
            'alternative-syntax-declare-empty-body' => [
                '/* testAlternativeDeclareEmptyBody */',
                true,
                false,
            ],
            'alternative-syntax-declare-with-code' => [
                '/* testAlternativeDeclareWithCode */',
                true,
                true,
            ],
            'inline-if-with-code' => [
                '/* testInlineIfWithCode */',
                true,
                true,
            ],
            'inline-elseif-with-code' => [
                '/* testInlineElseIfWithCode */',
                true,
                true,
            ],
            'inline-else-with-code' => [
                '/* testInlineElseWithCode */',
                true,
                true,
            ],
            'inline-for-with-code' => [
                '/* testInlineForWithCode */',
                true,
                true,
            ],
            'inline-foreach-with-code' => [
                '/* testInlineForEachWithCode */',
                true,
                true,
            ],
            'inline-while-with-code' => [
                '/* testInlineWhileWithCode */',
                true,
                true,
            ],
            'inline-do-while-with-code' => [
                '/* testInlineDoWhileWithCode */',
                true,
                true,
            ],
            'else-live-coding' => [
                '/* testElseLiveCoding */',
                true,
                false,
            ],
        ];
    }
}
