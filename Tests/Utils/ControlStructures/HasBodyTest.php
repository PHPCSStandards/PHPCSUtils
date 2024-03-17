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
 * @since 1.0.0
 */
final class HasBodyTest extends UtilityMethodTestCase
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
     * @return array<string, array<string, string|bool>>
     */
    public static function dataHasBody()
    {
        return [
            'if-without-body' => [
                'testMarker'      => '/* testIfWithoutBody */',
                'hasBody'         => false,
                'hasNonEmptyBody' => false,
            ],
            'if-without-body-due-to-php-close-tag' => [
                'testMarker'      => '/* testIfWithoutBodyDueToCloseTag */',
                'hasBody'         => false,
                'hasNonEmptyBody' => false,
            ],
            'elseif-without-body-due-to-php-close-tag' => [
                'testMarker'      => '/* testElseIfWithoutBodyDueToCloseTag */',
                'hasBody'         => false,
                'hasNonEmptyBody' => false,
            ],
            'else-without-body-due-to-php-close-tag' => [
                'testMarker'      => '/* testElseWithoutBodyDueToCloseTag */',
                'hasBody'         => false,
                'hasNonEmptyBody' => false,
            ],
            'if-empty-body' => [
                'testMarker'      => '/* testIfEmptyBody */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
            'elseif-empty-body' => [
                'testMarker'      => '/* testElseIfEmptyBody */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
            'else-if-empty-body' => [
                'testMarker'      => '/* testElseSpaceIfEmptyBody */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
            'else-empty-body' => [
                'testMarker'      => '/* testElseEmptyBody */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
            'if-with-code' => [
                'testMarker'      => '/* testIfWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'elseif-with-code' => [
                'testMarker'      => '/* testElseIfWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'else-if-with-code' => [
                'testMarker'      => '/* testElseSpaceIfWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'else-with-code' => [
                'testMarker'      => '/* testElseWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'for-without-body' => [
                'testMarker'      => '/* testForWithoutBody */',
                'hasBody'         => false,
                'hasNonEmptyBody' => false,
            ],
            'for-without-body-due-to-php-close-tag' => [
                'testMarker'      => '/* testForWithoutBodyDueToCloseTag */',
                'hasBody'         => false,
                'hasNonEmptyBody' => false,
            ],
            'for-empty-body' => [
                'testMarker'      => '/* testForEmptyBody */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
            'for-with-code' => [
                'testMarker'      => '/* testForWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'foreach-without-body' => [
                'testMarker'      => '/* testForEachWithoutBody */',
                'hasBody'         => false,
                'hasNonEmptyBody' => false,
            ],
            'foreach-without-body-due-to-php-close-tag' => [
                'testMarker'      => '/* testForEachWithoutBodyDueToCloseTag */',
                'hasBody'         => false,
                'hasNonEmptyBody' => false,
            ],
            'foreach-empty-body' => [
                'testMarker'      => '/* testForEachEmptyBody */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
            'foreach-with-code' => [
                'testMarker'      => '/* testForEachWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'while-without-body' => [
                'testMarker'      => '/* testWhileWithoutBody */',
                'hasBody'         => false,
                'hasNonEmptyBody' => false,
            ],
            'while-without-body-due-to-php-close-tag' => [
                'testMarker'      => '/* testWhileWithoutBodyDueToCloseTag */',
                'hasBody'         => false,
                'hasNonEmptyBody' => false,
            ],
            'while-empty-body' => [
                'testMarker'      => '/* testWhileEmptyBody */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
            'while-with-code' => [
                'testMarker'      => '/* testWhileWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'do-while-empty-body' => [
                'testMarker'      => '/* testDoWhileEmptyBody */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
            'do-while-with-code' => [
                'testMarker'      => '/* testDoWhileWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'switch-without-body' => [
                'testMarker'      => '/* testSwitchWithoutBody */',
                'hasBody'         => false,
                'hasNonEmptyBody' => false,
            ],
            'switch-without-body-due-to-php-close-tag' => [
                'testMarker'      => '/* testSwitchWithoutBodyDueToCloseTag */',
                'hasBody'         => false,
                'hasNonEmptyBody' => false,
            ],
            'switch-empty-body' => [
                'testMarker'      => '/* testSwitchEmptyBody */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
            'switch-with-code' => [
                'testMarker'      => '/* testSwitchWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'declare-without-body' => [
                'testMarker'      => '/* testDeclareWithoutBody */',
                'hasBody'         => false,
                'hasNonEmptyBody' => false,
            ],
            'declare-without-body-due-to-php-close-tag' => [
                'testMarker'      => '/* testDeclareWithoutBodyDueToCloseTag */',
                'hasBody'         => false,
                'hasNonEmptyBody' => false,
            ],
            'declare-empty-body' => [
                'testMarker'      => '/* testDeclareEmptyBody */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
            'declare-with-code' => [
                'testMarker'      => '/* testDeclareWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'alternative-syntax-if-empty-body' => [
                'testMarker'      => '/* testAlternativeIfEmptyBody */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
            'alternative-syntax-elseif-with-code' => [
                'testMarker'      => '/* testAlternativeElseIfWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'alternative-syntax-else-with-code' => [
                'testMarker'      => '/* testAlternativeElseWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'alternative-syntax-for-empty-body' => [
                'testMarker'      => '/* testAlternativeForEmptyBody */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
            'alternative-syntax-for-with-code' => [
                'testMarker'      => '/* testAlternativeForWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'alternative-syntax-foreach-empty-body' => [
                'testMarker'      => '/* testAlternativeForeachEmptyBody */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
            'alternative-syntax-foreach-with-code' => [
                'testMarker'      => '/* testAlternativeForeachWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'alternative-syntax-while-empty-body' => [
                'testMarker'      => '/* testAlternativeWhileEmptyBody */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
            'alternative-syntax-while-with-code' => [
                'testMarker'      => '/* testAlternativeWhileWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'alternative-syntax-switch-empty-body' => [
                'testMarker'      => '/* testAlternativeSwitchEmptyBody */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
            'alternative-syntax-switch-with-code' => [
                'testMarker'      => '/* testAlternativeSwitchWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'alternative-syntax-declare-empty-body' => [
                'testMarker'      => '/* testAlternativeDeclareEmptyBody */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
            'alternative-syntax-declare-with-code' => [
                'testMarker'      => '/* testAlternativeDeclareWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'inline-if-with-code' => [
                'testMarker'      => '/* testInlineIfWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'inline-elseif-with-code' => [
                'testMarker'      => '/* testInlineElseIfWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'inline-else-with-code' => [
                'testMarker'      => '/* testInlineElseWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'inline-for-with-code' => [
                'testMarker'      => '/* testInlineForWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'inline-foreach-with-code' => [
                'testMarker'      => '/* testInlineForEachWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'inline-while-with-code' => [
                'testMarker'      => '/* testInlineWhileWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],
            'inline-do-while-with-code' => [
                'testMarker'      => '/* testInlineDoWhileWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],

            /*
             * Match without body cannot be tested as, in that case, `match` will tokenize as `T_STRING`.
             * Without body (`match();`), match will either yield a parse error
             * or be interpreted as a function call (`\match();` or `self::match()` etc).
             */

            'match-empty-body' => [
                'testMarker'      => '/* testMatchEmptyBody */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
            'match-empty-body-comment-only' => [
                'testMarker'      => '/* testMatchEmptyBodyWithComment */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
            'match-with-code' => [
                'testMarker'      => '/* testMatchWithCode */',
                'hasBody'         => true,
                'hasNonEmptyBody' => true,
            ],

            'else-live-coding' => [
                'testMarker'      => '/* testElseLiveCoding */',
                'hasBody'         => true,
                'hasNonEmptyBody' => false,
            ],
        ];
    }
}
