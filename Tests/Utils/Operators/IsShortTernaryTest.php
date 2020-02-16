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
use PHPCSUtils\Utils\Operators;

/**
 * Tests for the \PHPCSUtils\Utils\Operators::isShortTernary() method.
 *
 * @covers \PHPCSUtils\Utils\Operators::isShortTernary
 *
 * @group operators
 *
 * @since 1.0.0
 */
class IsShortTernaryTest extends UtilityMethodTestCase
{

    /**
     * Test that false is returned when a non-existent token is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(Operators::isShortTernary(self::$phpcsFile, 10000));
    }

    /**
     * Test that false is returned when a non-ternary then/else token is passed.
     *
     * @return void
     */
    public function testNotTernaryToken()
    {
        $target = $this->getTargetToken('/* testNotATernaryToken */', \T_ECHO);
        $this->assertFalse(Operators::isShortTernary(self::$phpcsFile, $target));
    }

    /**
     * Test whether a T_INLINE_THEN or T_INLINE_ELSE token is correctly identified for being a short ternary.
     *
     * @dataProvider dataIsShortTernary
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   The expected boolean return value.
     *
     * @return void
     */
    public function testIsShortTernary($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_INLINE_THEN);
        $result   = Operators::isShortTernary(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result, 'Test failed with inline then');

        $stackPtr = $this->getTargetToken($testMarker, \T_INLINE_ELSE);
        $result   = Operators::isShortTernary(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result, 'Test failed with inline else');
    }

    /**
     * Data provider.
     *
     * @see testIsShortTernary() For the array format.
     *
     * @return array
     */
    public function dataIsShortTernary()
    {
        return [
            'long-ternary' => [
                '/* testLongTernary */',
                false,
            ],
            'short-ternary-no-space' => [
                '/* testShortTernaryNoSpace */',
                true,
            ],
            'short-ternary-long-space' => [
                '/* testShortTernaryLongSpace */',
                true,
            ],
            'short-ternary-comments-annotations' => [
                '/* testShortTernaryWithCommentAndAnnotations */',
                true,
            ],
        ];
    }

    /**
     * Safeguard that incorrectly tokenized T_INLINE_THEN or T_INLINE_ELSE tokens are correctly
     * rejected as not short ternary.
     *
     * {@internal None of these are really problematic, but better to be safe than sorry.}
     *
     * @dataProvider dataIsShortTernaryTokenizerIssues
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $tokenType  The token code to look for.
     *
     * @return void
     */
    public function testIsShortTernaryTokenizerIssues($testMarker, $tokenType = \T_INLINE_THEN)
    {
        $stackPtr = $this->getTargetToken($testMarker, $tokenType);
        $result   = Operators::isShortTernary(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result);
    }

    /**
     * Data provider.
     *
     * @see testIsShortTernaryTokenizerIssues() For the array format.
     *
     * @return array
     */
    public function dataIsShortTernaryTokenizerIssues()
    {
        $targetCoalesce = [\T_INLINE_THEN];
        if (\defined('T_COALESCE')) {
            $targetCoalesce[] = \T_COALESCE;
        }

        $targetCoalesceAndEquals = $targetCoalesce;
        if (\defined('T_COALESCE_EQUAL')) {
            $targetCoalesceAndEquals[] = \T_COALESCE_EQUAL;
        }

        $targetNullable = [\T_INLINE_THEN];
        if (\defined('T_NULLABLE')) {
            $targetNullable[] = \T_NULLABLE;
        }

        return [
            'null-coalesce' => [
                '/* testDontConfuseWithNullCoalesce */',
                $targetCoalesce,
            ],
            'null-coalesce-equals' => [
                '/* testDontConfuseWithNullCoalesceEquals */',
                $targetCoalesceAndEquals,
            ],
            'nullable-property' => [
                '/* testDontConfuseWithNullable1 */',
                $targetNullable,
            ],
            'nullable-param-type' => [
                '/* testDontConfuseWithNullable2 */',
                $targetNullable,
            ],
            'nullable-return-type' => [
                '/* testDontConfuseWithNullable3 */',
                $targetNullable,
            ],
            'parse-error' => [
                '/* testParseError */',
            ],
        ];
    }
}
